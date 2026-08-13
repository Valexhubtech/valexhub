<?php

namespace App\Services\Dns;

use App\Models\DnsChange;

/**
 * Applies the record-conflict rule before every DNS push:
 *   - Conflicting same-name+type records → replaced by our value
 *   - Second SPF TXT → our ip4 merged into the existing legitimate one
 *   - Parking A/CNAME on names we manage → removed
 *   - Default MX when we control mail → removed
 *
 * Returns the final rrset array ready to push, and logs every change.
 */
class RecordConflictResolver
{
    private const PARKING_IPS = [
        // Common registrar/host parking IPs — extend as needed
        '1.2.3.4', '66.96.149.0', '208.91.197.27', '198.185.159.144',
        '198.185.159.145', '198.49.23.144', '198.49.23.145',
    ];

    public function resolve(string $domain, array $existing, array $desired): array
    {
        $keyed = $this->keyByNameType($existing);
        $result = [];

        foreach ($desired as $rrset) {
            $key = $this->key($rrset['subname'], $rrset['type']);

            if (isset($keyed[$key])) {
                $existing_rrset = $keyed[$key];

                if ($rrset['type'] === 'TXT' && $this->isSpf($rrset)) {
                    $merged = $this->mergeSpf($existing_rrset, $rrset, $domain);
                    $result[] = $merged;
                    unset($keyed[$key]);
                    continue;
                }

                // Conflict — our value wins; log the replacement
                DnsChange::log(
                    domain: $domain,
                    actor: 'system',
                    action: 'update',
                    type: $rrset['type'],
                    subname: $rrset['subname'],
                    before: $existing_rrset,
                    after: $rrset,
                );
                $result[] = $rrset;
                unset($keyed[$key]);
                continue;
            }

            $result[] = $rrset;
        }

        // Remove leftovers that conflict with what we're managing (parking records, stray MX)
        foreach ($keyed as $key => $leftover) {
            if ($this->isParking($leftover) || $this->isDefaultMx($leftover)) {
                DnsChange::log(
                    domain: $domain,
                    actor: 'system',
                    action: 'delete',
                    type: $leftover['type'],
                    subname: $leftover['subname'],
                    before: $leftover,
                    after: null,
                );

                // Push an empty records array to delete via deSEC PATCH
                $result[] = [
                    'subname' => $leftover['subname'],
                    'type'    => $leftover['type'],
                    'ttl'     => $leftover['ttl'] ?? 3600,
                    'records' => [],
                ];
            }
        }

        return $result;
    }

    private function mergeSpf(array $existing, array $desired, string $domain): array
    {
        $existingSpf = $this->firstSpfRecord($existing['records'] ?? []);
        $desiredSpf  = $this->firstSpfRecord($desired['records'] ?? []);

        if ($existingSpf === null || $desiredSpf === null) {
            return $desired;
        }

        // Extract ip4/ip6/include terms from desired SPF and graft onto existing
        preg_match_all('/(ip[46]:[^\s]+|include:[^\s]+)/', $desiredSpf, $desiredTerms);
        $merged = $existingSpf;

        foreach ($desiredTerms[0] as $term) {
            if (! str_contains($merged, $term)) {
                // Insert before the trailing ~all / -all / ?all
                $merged = preg_replace('/([~\-?]all)/', "{$term} $1", $merged, 1);
            }
        }

        $mergedRrset = array_merge($existing, ['records' => ["\"{$merged}\""]]);

        DnsChange::log(
            domain: $domain,
            actor: 'system',
            action: 'update',
            type: 'TXT',
            subname: $existing['subname'],
            before: $existing,
            after: $mergedRrset,
        );

        return $mergedRrset;
    }

    private function isSpf(array $rrset): bool
    {
        if ($rrset['type'] !== 'TXT') {
            return false;
        }

        foreach ($rrset['records'] as $record) {
            if (str_contains($record, 'v=spf1')) {
                return true;
            }
        }

        return false;
    }

    private function firstSpfRecord(array $records): ?string
    {
        foreach ($records as $record) {
            $unquoted = trim($record, '"');
            if (str_starts_with($unquoted, 'v=spf1')) {
                return $unquoted;
            }
        }

        return null;
    }

    private function isParking(array $rrset): bool
    {
        if ($rrset['type'] === 'A') {
            foreach ($rrset['records'] as $ip) {
                if (in_array(trim($ip), self::PARKING_IPS, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isDefaultMx(array $rrset): bool
    {
        return $rrset['type'] === 'MX' && $rrset['subname'] === '';
    }

    private function keyByNameType(array $rrsets): array
    {
        $keyed = [];
        foreach ($rrsets as $rrset) {
            $keyed[$this->key($rrset['subname'], $rrset['type'])] = $rrset;
        }

        return $keyed;
    }

    private function key(string $subname, string $type): string
    {
        return "{$subname}::{$type}";
    }
}
