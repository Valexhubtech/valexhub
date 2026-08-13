<?php

namespace App\Services\Domain;

use App\Models\Domain;
use App\Services\Dns\DesecDnsProvider;
use App\Services\Dns\RecordConflictResolver;
use App\Services\Plume\PlumeClient;
use App\Services\Plume\PlumeDomainExistsException;
use App\Services\Plume\PlumeNotVerifiedException;
use Illuminate\Support\Facades\Log;

/**
 * One-time admin action: register notif.PLATFORM_MAIL_DOMAIN in Plume,
 * push its DNS records to deSEC, and verify. Idempotent.
 */
class PlatformDomainProvisioner
{
    public function __construct(
        private PlumeClient $plume,
        private DesecDnsProvider $dns,
        private RecordConflictResolver $conflictResolver,
    ) {}

    /**
     * @return array{domain: string, status: string, records: array}
     */
    public function provision(): array
    {
        $domain = config('services.plume.mail_domain');

        if (! $domain) {
            throw new \RuntimeException('PLATFORM_MAIL_DOMAIN env var is not set.');
        }

        // 1. Register domain in Plume (idempotent on 409)
        try {
            $plumeData = $this->plume->registerDomain($domain);
        } catch (PlumeDomainExistsException) {
            $plumeData = $this->plume->getDomain($domain);
        }

        $plumeRecords = $plumeData['dns_records'] ?? [];

        Log::info('PlatformDomainProvisioner: Plume domain registered', ['domain' => $domain]);

        // 2. Ensure deSEC zone exists
        $this->dns->createZone($domain);

        // 3. Apply conflict rule then push records
        $existing  = $this->dns->listRecords($domain);
        $resolved  = $this->conflictResolver->resolve($domain, $existing, $plumeRecords);
        $this->dns->pushRecords($domain, $resolved);

        Log::info('PlatformDomainProvisioner: DNS records pushed', ['domain' => $domain, 'count' => count($resolved)]);

        // 4. Mark domain as managed
        Domain::updateOrCreate(
            ['domain' => $domain],
            ['owner' => 'us', 'registrar' => 'elsewhere', 'dns_host' => 'desec', 'managed' => true],
        );

        // 5. Attempt verification (may need propagation time — that's fine)
        $status = 'dns_pushed';
        try {
            $this->plume->verifyDomain($domain);
            $status = 'verified';
            Log::info('PlatformDomainProvisioner: domain verified', ['domain' => $domain]);
        } catch (PlumeNotVerifiedException) {
            $status = 'dns_pushed_awaiting_propagation';
            Log::info('PlatformDomainProvisioner: DNS pushed, not yet verified — re-run after propagation', ['domain' => $domain]);
        }

        return [
            'domain'  => $domain,
            'status'  => $status,
            'records' => $resolved,
        ];
    }

    /** Re-run verification only (call after DNS has propagated). */
    public function reverify(): array
    {
        $domain = config('services.plume.mail_domain');

        try {
            $this->plume->verifyDomain($domain);

            return ['domain' => $domain, 'status' => 'verified'];
        } catch (PlumeNotVerifiedException) {
            return ['domain' => $domain, 'status' => 'not_yet_verified'];
        }
    }
}
