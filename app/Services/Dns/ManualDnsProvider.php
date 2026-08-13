<?php

namespace App\Services\Dns;

/**
 * Used when the domain's nameservers are NOT deSEC-managed.
 * Records are stored in the email_domains table for the manual copy-button screen.
 * No actual DNS mutations happen here.
 */
class ManualDnsProvider implements DnsProvider
{
    private array $pendingRecords = [];

    public function zoneExists(string $domain): bool
    {
        return false;
    }

    public function createZone(string $domain): void
    {
        // no-op — manual flow has no zone to create
    }

    public function listRecords(string $domain): array
    {
        return [];
    }

    public function pushRecords(string $domain, array $rrsets): void
    {
        $this->pendingRecords = $rrsets;
    }

    public function updateRecord(string $domain, string $subname, string $type, int $ttl, array $records): void
    {
        // no-op
    }

    public function deleteRecord(string $domain, string $subname, string $type): void
    {
        // no-op
    }

    /** Returns the records staged by pushRecords() for storage in email_domains.records. */
    public function getPendingRecords(): array
    {
        return $this->pendingRecords;
    }
}
