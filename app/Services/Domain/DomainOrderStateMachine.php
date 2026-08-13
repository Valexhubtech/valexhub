<?php

namespace App\Services\Domain;

use App\Mail\AdminAlertMail;
use App\Models\DomainOrder;
use App\Models\Domain;
use App\Services\Dns\DesecDnsProvider;
use App\Services\Dns\RecordConflictResolver;
use App\Services\Go54\Go54RegistrarProvider;
use App\Services\Go54\InsufficientWalletBalanceException;
use App\Services\Plume\PlumeClient;
use App\Services\Plume\PlumeNotVerifiedException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DomainOrderStateMachine
{
    private const DESEC_NAMESERVERS = ['ns1.desec.io', 'ns2.desec.org'];
    private const STALL_HOURS = 48;

    public function __construct(
        private Go54RegistrarProvider $go54,
        private DesecDnsProvider $dns,
        private RecordConflictResolver $conflictResolver,
        private PlumeClient $plume,
        private EmailInjector $injector,
    ) {}

    /**
     * Advance a single domain order by one step.
     * Idempotent — calling it twice in the same state is safe.
     */
    public function tick(DomainOrder $order): void
    {
        try {
            match ($order->state) {
                'pending'          => $this->doPurchase($order),
                'awaiting_wallet'  => null, // human action required — admin presses Retry
                'registered'       => $this->doDnsSetup($order),
                'dns_ready'        => $this->doVerify($order),
                'active'           => null, // terminal state
                default            => null,
            };
        } catch (InsufficientWalletBalanceException $e) {
            $order->advanceTo('awaiting_wallet', $e->getMessage());
            $this->alertAdmin("GO54 wallet insufficient for {$order->domain}", $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('DomainOrder tick failed', [
                'order_id' => $order->id,
                'state'    => $order->state,
                'error'    => $e->getMessage(),
            ]);
            $order->last_error = $e->getMessage();
            $order->save();
        }
    }

    /** Admin pressed Retry after funding the wallet. */
    public function retryFromAwaitingWallet(DomainOrder $order): void
    {
        if ($order->state !== 'awaiting_wallet') {
            return;
        }

        $order->advanceTo('pending');
        $this->tick($order);
    }

    private function doPurchase(DomainOrder $order): void
    {
        $order->advanceTo('purchasing');

        $result = $this->go54->register($order->domain, self::DESEC_NAMESERVERS);

        $order->go54_reference = $result['reference'] ?? $result['id'] ?? null;
        $order->advanceTo('registered');
    }

    private function doDnsSetup(DomainOrder $order): void
    {
        // Create deSEC zone (idempotent)
        $this->dns->createZone($order->domain);

        // Fetch any junk records the registrar pre-seeded
        $existing = $this->dns->listRecords($order->domain);

        // Get Plume's required DNS records for this domain
        $plumeData = $this->plume->registerDomain($order->domain);
        $plumeRecords = $plumeData['dns_records'] ?? [];

        // Apply conflict rule then push
        $resolved = $this->conflictResolver->resolve($order->domain, $existing, $plumeRecords);
        $this->dns->pushRecords($order->domain, $resolved);

        // Mark domain as managed
        Domain::updateOrCreate(
            ['domain' => $order->domain],
            ['owner' => "instance_{$order->instance_id}", 'registrar' => 'go54', 'dns_host' => 'desec', 'managed' => true],
        );

        $order->advanceTo('dns_ready');
    }

    private function doVerify(DomainOrder $order): void
    {
        // Check for stall
        if ($order->updated_at->diffInHours(now()) >= self::STALL_HOURS) {
            $this->alertAdmin("Domain {$order->domain} stalled at dns_ready for 48h", 'Manual check required.');

            return;
        }

        try {
            $this->plume->verifyDomain($order->domain);
        } catch (PlumeNotVerifiedException) {
            // Not yet — tick again later
            return;
        }

        // Verified — create domain-scoped key and inject to instance
        $keyData = $this->plume->createApiKey($order->domain, "instance-{$order->instance_id}");

        $this->injector->injectEmailDomain(
            instanceId: $order->instance_id,
            domain: $order->domain,
            apiKey: $keyData['key'],
        );

        $order->advanceTo('active');
    }

    private function alertAdmin(string $subject, string $body): void
    {
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new AdminAlertMail($subject, $body));
    }
}
