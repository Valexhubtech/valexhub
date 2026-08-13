<?php

namespace App\Services\Domain;

use App\Models\Domain;
use App\Models\EmailDomain;
use App\Services\Dns\DesecDnsProvider;
use App\Services\Dns\ManualDnsProvider;
use App\Services\Dns\RecordConflictResolver;
use App\Services\Plume\PlumeClient;
use App\Services\Plume\PlumeDomainExistsException;
use App\Services\Plume\PlumeNotVerifiedException;
use Illuminate\Support\Facades\Log;

class BringYourOwnDomainService
{
    private const DESEC_NAMESERVERS = ['ns1.desec.io', 'ns2.desec.org'];

    public function __construct(
        private PlumeClient $plume,
        private DesecDnsProvider $desec,
        private RecordConflictResolver $conflictResolver,
        private EmailInjector $injector,
    ) {}

    /**
     * Attach a client's own domain to an instance.
     * Returns the email_domains record with current status.
     */
    public function attach(string $instanceId, string $domain): EmailDomain
    {
        // 1. Register on Plume (idempotent)
        try {
            $plumeData = $this->plume->registerDomain($domain);
        } catch (PlumeDomainExistsException) {
            $plumeData = $this->plume->getDomain($domain);
        }

        $plumeRecords = $plumeData['dns_records'] ?? [];

        // 2. Determine if we manage DNS for this domain
        $domainRecord = Domain::where('domain', $domain)->first();
        $weManageDns  = $domainRecord?->isDesecManaged() || $this->namepointsToDesec($domain);

        if ($weManageDns) {
            // We control DNS — push records directly
            $this->desec->createZone($domain);
            $existing = $this->desec->listRecords($domain);
            $resolved = $this->conflictResolver->resolve($domain, $existing, $plumeRecords);
            $this->desec->pushRecords($domain, $resolved);

            $status = 'verifying';

            Domain::updateOrCreate(
                ['domain' => $domain],
                ['dns_host' => 'desec', 'managed' => true],
            );
        } else {
            // Client controls DNS — store records for the manual copy screen
            $manual = new ManualDnsProvider();
            $manual->pushRecords($domain, $plumeRecords);
            $status = 'manual';
        }

        $emailDomain = EmailDomain::updateOrCreate(
            ['instance_id' => $instanceId, 'domain' => $domain],
            [
                'is_shared' => false,
                'stage'     => 'custom_pending',
                'status'    => $status,
                'records'   => $plumeRecords,
            ]
        );

        Log::info('BringYourOwnDomainService: attached', ['instance' => $instanceId, 'domain' => $domain, 'status' => $status]);

        return $emailDomain;
    }

    /**
     * Re-check DNS and Plume verification. Advances status on success.
     * Called by the "I've added the records — re-check" button.
     */
    public function recheck(EmailDomain $emailDomain): EmailDomain
    {
        $domain     = $emailDomain->domain;
        $instanceId = $emailDomain->instance_id;

        try {
            $this->plume->verifyDomain($domain);
        } catch (PlumeNotVerifiedException) {
            // Still not verified
            return $emailDomain;
        }

        // Verified — create a domain-scoped key and inject to instance
        $keyData = $this->plume->createApiKey($domain, "instance-{$instanceId}");

        $this->injector->injectEmailDomain(
            instanceId: $instanceId,
            domain: $domain,
            apiKey: $keyData['key'],
        );

        $emailDomain->update([
            'stage'        => 'custom_active',
            'status'       => 'active',
            'plume_api_key' => encrypt($keyData['key']),
        ]);

        Log::info('BringYourOwnDomainService: verified and injected', ['domain' => $domain]);

        return $emailDomain->fresh();
    }

    /** Check if current NS records point to deSEC's nameservers. */
    private function namepointsToDesec(string $domain): bool
    {
        try {
            $ns = dns_get_record($domain, DNS_NS);

            foreach ($ns as $record) {
                $target = strtolower($record['target'] ?? '');
                if (str_ends_with($target, 'desec.io') || str_ends_with($target, 'desec.org')) {
                    return true;
                }
            }
        } catch (\Throwable) {
            // DNS lookup failed — assume not managed
        }

        return false;
    }
}
