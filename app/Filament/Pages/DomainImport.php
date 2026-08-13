<?php

namespace App\Filament\Pages;

use App\Services\Domain\DomainImportService;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DomainImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static ?string $navigationGroup = 'Email & Domains';
    protected static ?string $navigationLabel = 'Import / Adopt Domain';
    protected static ?int $navigationSort = 20;
    protected static string $view = 'filament.pages.domain-import';

    public string $domain = '';
    public array $extraSubnames = [];
    public string $step = 'query'; // query | review | ns_switch | done
    public array $records = [];
    public array $staleFlags = [];
    public string $registrar = 'go54';

    private const DESEC_NAMESERVERS = ['ns1.desec.io', 'ns2.desec.org'];

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('domain')
                ->label('Domain to import')
                ->placeholder('valexhub.com')
                ->required(),

            TagsInput::make('extraSubnames')
                ->label('Extra subnames to query (optional)')
                ->placeholder('Add subname'),

            Select::make('registrar')
                ->label('Registrar')
                ->options(['go54' => 'GO54', 'elsewhere' => 'Other registrar'])
                ->default('go54'),
        ])->statePath('data');
    }

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('query')
                ->label('Reconstruct DNS Records')
                ->color('primary')
                ->action('queryRecords'),

            Action::make('approve')
                ->label('Approve & Push to deSEC')
                ->color('success')
                ->visible(fn () => $this->step === 'review')
                ->requiresConfirmation()
                ->modalDescription('This will push the approved records to deSEC and set dns_host=desec for this domain.')
                ->action('approveAndPush'),

            Action::make('switchNs')
                ->label('Switch NS to deSEC')
                ->color('warning')
                ->visible(fn () => $this->step === 'ns_switch' && ($this->data['registrar'] ?? 'elsewhere') === 'go54')
                ->requiresConfirmation()
                ->modalDescription('This will call GO54 API to point ' . ($this->data['domain'] ?? '') . ' at ns1.desec.io / ns2.desec.org.')
                ->action('switchNameservers'),

            Action::make('markManaged')
                ->label('Confirm NS Live → Mark Managed')
                ->color('gray')
                ->visible(fn () => $this->step === 'ns_switch')
                ->action('confirmManaged'),
        ];
    }

    public function queryRecords(): void
    {
        $data   = $this->form->getState();
        $domain = strtolower(trim($data['domain'] ?? ''));

        if (! $domain) {
            Notification::make()->title('Enter a domain first.')->warning()->send();

            return;
        }

        try {
            $result           = app(DomainImportService::class)->reconstruct($domain, $data['extraSubnames'] ?? []);
            $this->records    = $result['records'];
            $this->staleFlags = array_column($result['stale'], 'subname');
            $this->step       = 'review';
            $this->data       = $data;

            Notification::make()->title('Records reconstructed — review below.')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Query failed: ' . $e->getMessage())->danger()->send();
        }
    }

    public function approveAndPush(): void
    {
        $domain = strtolower(trim($this->data['domain'] ?? ''));

        try {
            app(DomainImportService::class)->applyApproved($domain, $this->records);
            $this->step = 'ns_switch';
            Notification::make()->title('Records pushed to deSEC.')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Push failed: ' . $e->getMessage())->danger()->send();
        }
    }

    public function switchNameservers(): void
    {
        $domain = strtolower(trim($this->data['domain'] ?? ''));

        try {
            app(DomainImportService::class)->switchNsViaGo54($domain, self::DESEC_NAMESERVERS);
            Notification::make()->title('NS switch sent to GO54. Propagation takes up to 24h.')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('NS switch failed: ' . $e->getMessage())->danger()->send();
        }
    }

    public function confirmManaged(): void
    {
        $domain = strtolower(trim($this->data['domain'] ?? ''));
        app(DomainImportService::class)->markManaged($domain);
        $this->step = 'done';
        Notification::make()->title($domain . ' is now fully managed.')->success()->send();
    }

    public function removeRecord(int $index): void
    {
        array_splice($this->records, $index, 1);
    }
}
