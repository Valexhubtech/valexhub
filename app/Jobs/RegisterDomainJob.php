<?php

namespace App\Jobs;

use App\Services\Hostinger\HostingerDomainService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Wave\Deployment;
use Wave\DomainPurchase;

class RegisterDomainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly int $domainPurchaseId
    ) {}

    public function handle(HostingerDomainService $hostinger): void
    {
        $purchase = DomainPurchase::findOrFail($this->domainPurchaseId);

        if ($purchase->registration_status === 'registered') {
            return; // Already done — idempotent
        }

        // Local dev: skip real Hostinger API calls to avoid registering live domains
        if (app()->environment('local')) {
            Log::info('RegisterDomainJob: local env — simulating registration', [
                'domain'      => $purchase->domain,
                'purchase_id' => $purchase->id,
            ]);
            $purchase->update([
                'registration_status' => 'registered',
                'dns_status'          => 'configured',
            ]);
            if ($purchase->deployment_id) {
                Deployment::where('id', $purchase->deployment_id)
                    ->update(['custom_domain' => $purchase->domain]);
            }
            return;
        }

        // ── 1. Register domain on Hostinger ───────────────────────────────────
        $purchase->update(['registration_status' => 'processing']);

        $registered = false;
        if ($purchase->hostinger_item_id) {
            $registered = $hostinger->purchaseDomain($purchase->domain, $purchase->hostinger_item_id);
        } else {
            // item_id was not resolved at checkout time — log and mark failed
            Log::error('RegisterDomainJob: no hostinger_item_id', ['purchase_id' => $purchase->id]);
        }

        if (! $registered) {
            $purchase->update(['registration_status' => 'failed']);
            Log::error('RegisterDomainJob: Hostinger registration failed', [
                'domain'      => $purchase->domain,
                'purchase_id' => $purchase->id,
            ]);
            return;
        }

        $purchase->update(['registration_status' => 'registered']);

        // ── 2. Configure DNS ──────────────────────────────────────────────────
        $serverIp = $this->resolveServerIp($purchase);

        if (! $serverIp) {
            Log::warning('RegisterDomainJob: no server IP available for DNS setup', [
                'purchase_id'   => $purchase->id,
                'deployment_id' => $purchase->deployment_id,
            ]);
            $purchase->update(['dns_status' => 'failed']);
            return;
        }

        $dnsOk = $hostinger->configureDomainForDeployment($purchase->domain, $serverIp);
        $purchase->update(['dns_status' => $dnsOk ? 'configured' : 'failed']);

        // ── 3. Update deployment with custom domain ───────────────────────────
        if ($dnsOk && $purchase->deployment_id) {
            Deployment::where('id', $purchase->deployment_id)
                ->update(['custom_domain' => $purchase->domain]);
        }

        Log::info('RegisterDomainJob: completed', [
            'domain'      => $purchase->domain,
            'dns_ok'      => $dnsOk,
            'purchase_id' => $purchase->id,
        ]);
    }

    private function resolveServerIp(DomainPurchase $purchase): ?string
    {
        if (! $purchase->deployment_id) {
            return null;
        }

        $deployment = Deployment::with('coolifyServer')->find($purchase->deployment_id);

        return $deployment?->coolifyServer?->ip_address;
    }
}
