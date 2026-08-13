<?php

namespace App\Filament\Resources\DomainManagement\Pages;

use App\Filament\Resources\DomainManagement\DomainManagerResource;
use App\Models\DnsChange;
use App\Models\Domain;
use App\Services\Dns\DesecDnsProvider;
use App\Services\Dns\RecordConflictResolver;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ManageDomainRecords extends Page
{
    protected static string $resource = DomainManagerResource::class;
    protected string $view = 'filament.resources.domain-management.pages.manage-domain-records';

    /** System-managed record subname+type combos that require typed confirmation */
    private const LOCKED_KEYS = [
        // Plume email records
        '_domainkey' => 'DKIM for Plume email — editing breaks DKIM signing',
        '_dmarc'     => 'DMARC policy — editing changes email rejection rules',
        'MX::'       => 'MX record — editing redirects incoming mail',
    ];

    public Domain $record;
    public array $rrsets = [];
    public array $changelog = [];

    // New record form
    public string $newSubname = '';
    public string $newType    = 'A';
    public int    $newTtl     = 3600;
    public string $newValue   = '';

    // Confirmation for locked records
    public ?int    $pendingEditIndex = null;
    public string  $confirmationText = '';
    public string  $requiredConfirmation = '';

    public function mount(Domain $record): void
    {
        $this->record    = $record;
        $this->rrsets    = $this->fetchRrsets();
        $this->changelog = DnsChange::where('domain', $record->domain)
            ->latest()
            ->limit(50)
            ->get()
            ->toArray();
    }

    public function addRecord(): void
    {
        $this->validate([
            'newSubname' => ['nullable', 'max:255'],
            'newType'    => ['required', 'in:A,AAAA,CNAME,MX,TXT,SRV'],
            'newTtl'     => ['required', 'integer', 'min:60'],
            'newValue'   => ['required', 'max:1000'],
        ]);

        try {
            $desec = app(DesecDnsProvider::class);

            $desec->updateRecord(
                domain: $this->record->domain,
                subname: $this->newSubname,
                type: $this->newType,
                ttl: $this->newTtl,
                records: [$this->newValue],
            );

            DnsChange::log(
                domain: $this->record->domain,
                actor: auth()->id() ?? 'admin',
                action: 'create',
                type: $this->newType,
                subname: $this->newSubname,
                before: null,
                after: ['records' => [$this->newValue], 'ttl' => $this->newTtl],
            );

            $this->reset(['newSubname', 'newType', 'newTtl', 'newValue']);
            $this->rrsets    = $this->fetchRrsets();
            $this->changelog = $this->freshChangelog();

            Notification::make()->title('Record added.')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Failed: ' . $e->getMessage())->danger()->send();
        }
    }

    public function deleteRecord(string $subname, string $type): void
    {
        if ($this->isLocked($subname, $type)) {
            $this->pendingEditIndex     = -1;
            $this->requiredConfirmation = "delete {$subname} {$type}";
            $this->confirmationText     = '';

            return;
        }

        $this->doDelete($subname, $type);
    }

    public function confirmLockedEdit(): void
    {
        if (strtolower($this->confirmationText) !== strtolower($this->requiredConfirmation)) {
            Notification::make()->title('Confirmation text did not match.')->warning()->send();

            return;
        }

        // Pull subname + type from the confirmation text "delete {subname} {type}"
        $parts = explode(' ', $this->requiredConfirmation);
        if (count($parts) === 3) {
            $this->doDelete($parts[1], $parts[2]);
        }

        $this->pendingEditIndex = null;
    }

    public function cancelLockedEdit(): void
    {
        $this->pendingEditIndex = null;
    }

    private function doDelete(string $subname, string $type): void
    {
        $before = collect($this->rrsets)->first(fn ($r) => $r['subname'] === $subname && $r['type'] === $type);

        try {
            app(DesecDnsProvider::class)->deleteRecord($this->record->domain, $subname, $type);

            DnsChange::log(
                domain: $this->record->domain,
                actor: auth()->id() ?? 'admin',
                action: 'delete',
                type: $type,
                subname: $subname,
                before: $before,
                after: null,
            );

            $this->rrsets    = $this->fetchRrsets();
            $this->changelog = $this->freshChangelog();

            Notification::make()->title('Record deleted.')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Failed: ' . $e->getMessage())->danger()->send();
        }
    }

    public function isLocked(string $subname, string $type): bool
    {
        if (str_contains($subname, '_domainkey') || $subname === '_dmarc') {
            return true;
        }

        if ($type === 'MX' && $subname === '') {
            return true;
        }

        return false;
    }

    public function lockReason(string $subname, string $type): string
    {
        if (str_contains($subname, '_domainkey')) {
            return self::LOCKED_KEYS['_domainkey'];
        }
        if ($subname === '_dmarc') {
            return self::LOCKED_KEYS['_dmarc'];
        }
        if ($type === 'MX') {
            return self::LOCKED_KEYS['MX::'];
        }

        return '';
    }

    private function fetchRrsets(): array
    {
        try {
            return app(DesecDnsProvider::class)->listRecords($this->record->domain);
        } catch (\Throwable) {
            return [];
        }
    }

    private function freshChangelog(): array
    {
        return DnsChange::where('domain', $this->record->domain)
            ->latest()
            ->limit(50)
            ->get()
            ->toArray();
    }
}
