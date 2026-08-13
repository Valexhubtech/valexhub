<?php

namespace App\Services\Dns;

interface DnsProvider
{
    public function zoneExists(string $domain): bool;

    public function createZone(string $domain): void;

    /** @return array<int, array{subname: string, type: string, ttl: int, records: string[]}> */
    public function listRecords(string $domain): array;

    /**
     * Push (create or update) a set of rrsets into the zone.
     * Each item: ['subname' => '', 'type' => 'A', 'ttl' => 3600, 'records' => ['1.2.3.4']]
     *
     * @param  array<int, array{subname: string, type: string, ttl: int, records: string[]}>  $rrsets
     */
    public function pushRecords(string $domain, array $rrsets): void;

    public function updateRecord(string $domain, string $subname, string $type, int $ttl, array $records): void;

    public function deleteRecord(string $domain, string $subname, string $type): void;
}
