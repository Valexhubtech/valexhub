<?php

namespace App\Services\Domain;

use App\Models\Domain;
use App\Services\Dns\DesecDnsProvider;
use App\Services\Dns\RecordConflictResolver;
use App\Services\Go54\Go54RegistrarProvider;
use Illuminate\Support\Facades\Log;

class DomainImportService
{
    private const COMMON_SUBNAMES = [
        '', 'www', 'mail', 'webmail', 'ftp', 'smtp', 'pop', 'imap',
        'autodiscover', 'autoconfig', 'cpanel', 'api', 'app', 'auth',
        '_dmarc', 'default._domainkey', 'mail._domainkey',
    ];

    private const QUERY_TYPES = ['A', 'AAAA', 'CNAME', 'MX', 'TXT', 'SRV'];

    public function __construct(
        private DesecDnsProvider $desec,
        private RecordConflictResolver $conflictResolver,
        private Go54RegistrarProvider $go54,
    ) {}

    /**
     * Query the current authoritative nameservers for all common records.
     * Returns rrsets in deSEC format, ready for admin sign-off.
     *
     * @param  string[]  $extraSubnames  Admin-supplied additional subnames to query
     */
    public function reconstruct(string $domain, array $extraSubnames = []): array
    {
        $subnames = array_unique(array_merge(self::COMMON_SUBNAMES, $extraSubnames));
        $records  = [];
        $stale    = [];

        foreach ($subnames as $subname) {
            $host = $subname ? "{$subname}.{$domain}" : $domain;

            foreach (self::QUERY_TYPES as $type) {
                $found = $this->queryDns($host, $type);

                if (empty($found)) {
                    continue;
                }

                $rrset = [
                    'subname' => $subname,
                    'type'    => $type,
                    'ttl'     => $found[0]['ttl'] ?? 3600,
                    'records' => array_map(fn ($r) => $this->formatRecord($r, $type), $found),
                    'stale'   => false,
                ];

                // Flag records that look stale (parking IPs, self-referential MX, etc.)
                if ($this->looksStale($rrset)) {
                    $rrset['stale'] = true;
                    $stale[] = $rrset;
                }

                $records[] = $rrset;
            }
        }

        return ['records' => $records, 'stale' => $stale];
    }

    /**
     * Apply approved records: create deSEC zone, apply conflict rule, push.
     * Called after admin signs off on the reconstructed record list.
     *
     * @param  array  $approvedRrsets  Subset of records from reconstruct(), possibly edited
     */
    public function applyApproved(string $domain, array $approvedRrsets, string $actor = 'system'): void
    {
        $this->desec->createZone($domain);
        $existing = $this->desec->listRecords($domain);
        $resolved = $this->conflictResolver->resolve($domain, $existing, $approvedRrsets);
        $this->desec->pushRecords($domain, $resolved);

        Domain::updateOrCreate(
            ['domain' => $domain],
            ['dns_host' => 'desec'],
        );

        Log::info('DomainImportService: records pushed', ['domain' => $domain, 'count' => count($resolved)]);
    }

    /**
     * Switch nameservers via GO54 (for GO54-registered domains).
     */
    public function switchNsViaGo54(string $domain, array $nameservers): void
    {
        $this->go54->setNameservers($domain, $nameservers);

        Domain::updateOrCreate(
            ['domain' => $domain],
            ['registrar' => 'go54'],
        );
    }

    /**
     * Mark domain as fully managed after NS switch confirmed.
     */
    public function markManaged(string $domain): void
    {
        Domain::updateOrCreate(
            ['domain' => $domain],
            ['managed' => true, 'dns_host' => 'desec'],
        );
    }

    private function queryDns(string $host, string $type): array
    {
        $phpType = match ($type) {
            'A'     => DNS_A,
            'AAAA'  => DNS_AAAA,
            'CNAME' => DNS_CNAME,
            'MX'    => DNS_MX,
            'TXT'   => DNS_TXT,
            'SRV'   => DNS_SRV,
            default => DNS_ANY,
        };

        try {
            return @dns_get_record($host, $phpType) ?: [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function formatRecord(array $record, string $type): string
    {
        return match ($type) {
            'A', 'AAAA' => $record['ip'] ?? $record['ipv6'] ?? '',
            'CNAME'     => rtrim($record['target'] ?? '', '.') . '.',
            'MX'        => ($record['pri'] ?? 10) . ' ' . rtrim($record['target'] ?? '', '.') . '.',
            'TXT'       => '"' . implode('', (array) ($record['txt'] ?? $record['entries'] ?? [])) . '"',
            'SRV'       => sprintf('%s %s %s %s.', $record['pri'] ?? 0, $record['weight'] ?? 0, $record['port'] ?? 0, rtrim($record['target'] ?? '', '.')),
            default     => '',
        };
    }

    private function looksStale(array $rrset): bool
    {
        // Self-referential MX (domain pointing to itself)
        if ($rrset['type'] === 'MX' && $rrset['subname'] === '') {
            return true;
        }

        // Bare parking CNAME on root (rare but happens)
        if ($rrset['type'] === 'CNAME' && $rrset['subname'] === '') {
            return true;
        }

        return false;
    }
}
