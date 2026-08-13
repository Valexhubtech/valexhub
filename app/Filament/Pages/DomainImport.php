<?php

namespace App\Filament\Pages;

use App\Services\Domain\DomainImportService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Pages\Page;

class DomainImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.domain-import';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-arrow-down-tray'; }
    public static function getNavigationGroup(): ?string { return 'Deployments & Services'; }
    public static function getNavigationLabel(): string { return 'Import / Adopt Domain'; }
    public static function getNavigationSort(): int { return 6; }

    public string $domain = '';
    public array $extraSubnames = [];
    public string $step = 'query'; // query | review | ns_switch | done
    public array $records = [];
    public array $staleFlags = [];
    public string $registrar = 'go54';

    private const DESEC_NAMESERVERS = ['ns1.desec.io', 'ns2.desec.org'];

    public function form(Schema $form): Schema
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
