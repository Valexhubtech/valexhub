<?php

namespace App\Services;

use App\Mail\DeploymentCredentialsMail;
use App\Services\Coolify\CoolifyDeploymentServiceContract;
use App\Services\Coolify\CoolifyServerSelector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Wave\Deployment;

class ProductDeploymentService
{
    public function __construct(
        protected CoolifyDeploymentServiceContract $coolify,
        protected CoolifyServerSelector $serverSelector,
    ) {}

    /**
     * Provision the deployment via Coolify and persist the outcome.
     * Safe to call more than once (only `pending` deployments are processed).
     */
    public function fulfill(Deployment $deployment): void
    {
        if ($deployment->status !== 'pending') {
            return;
        }

        // ── Server selection ──────────────────────────────────────────────────
        $server = $this->serverSelector->findAvailableServer();

        if ($server === null) {
            $reason = 'No Coolify server is available. All servers are full or none are configured. '
                .'Please add a new server in the admin panel and re-queue this deployment.';

            $deployment->update([
                'status' => 'failed',
                'failure_reason' => $reason,
            ]);

            Log::critical('Deployment failed: no available Coolify server', [
                'deployment_id' => $deployment->id,
                'product_id' => $deployment->product_id,
                'user_id' => $deployment->user_id,
            ]);

            return;
        }

        // ── Provision ─────────────────────────────────────────────────────────
        $deployment->update([
            'status' => 'provisioning',
            'coolify_server_id' => $server->id,
        ]);

        $result = $this->coolify->deploy($deployment->product, $deployment->user, [
            'deploy_for' => $deployment->deploy_for,
            'business_name' => $deployment->business_name,
            'client_name' => $deployment->client_name,
            'client_email' => $deployment->client_email,
            'server' => $server,
            'user_product_id' => $deployment->user_product_id,
            'deployment_id' => $deployment->id,
            'extra_env_vars' => $deployment->extra_env_vars ?? [],
            'env_inject_mode' => $deployment->env_inject_mode ?? 'alongside',
        ]);

        if ($result->success) {
            $deployment->update([
                'coolify_app_id' => $result->appId,
                'deployment_url' => $result->deploymentUrl, // known at creation time from Coolify
                'central_api_key' => $result->centralApiKey,
                'auth_relay_secret' => $result->authRelaySecret,
                'bunny_library_id' => $result->bunnyLibraryId,
                'bunny_api_key_encrypted' => $result->bunnyApiKey,
                'credentials_encrypted' => [
                    'username' => $result->loginUsername,
                    'password' => $result->loginPassword,
                    'db_name' => $result->dbName,
                    'better_auth_secret' => $result->betterAuthSecret,
                ],
            ]);

            if ($result->isProvisional) {
                // Real Coolify deploy — container is booting. Active status comes via setup-complete webhook.
                $deployment->update(['status' => 'provisioning']);
            } else {
                // Mock deploy — treat as immediately active (no real container).
                $deployment->update([
                    'status' => 'active',
                    'deployment_url' => $result->deploymentUrl,
                    'deployed_at' => now(),
                ]);
                $deployment->userProduct?->markAsActive();
                Mail::to($deployment->user->email)->queue(new DeploymentCredentialsMail($deployment));
            }
        } else {
            $deployment->update([
                'status' => 'failed',
                'failure_reason' => $result->failureReason,
            ]);

            Log::error('Coolify deployment failed', [
                'deployment_id' => $deployment->id,
                'server_id' => $server->id,
                'reason' => $result->failureReason,
            ]);
        }
    }
}
