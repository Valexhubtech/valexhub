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
                . 'Please add a new server in the admin panel and re-queue this deployment.';

            $deployment->update([
                'status'         => 'failed',
                'failure_reason' => $reason,
            ]);

            Log::critical('Deployment failed: no available Coolify server', [
                'deployment_id' => $deployment->id,
                'product_id'    => $deployment->product_id,
                'user_id'       => $deployment->user_id,
            ]);

            return;
        }

        // ── Provision ─────────────────────────────────────────────────────────
        $deployment->update([
            'status'            => 'provisioning',
            'coolify_server_id' => $server->id,
        ]);

        $result = $this->coolify->deploy($deployment->product, $deployment->user, [
            'deploy_for'  => $deployment->deploy_for,
            'client_name' => $deployment->client_name,
            'client_email'=> $deployment->client_email,
            'server'      => $server,
        ]);

        if ($result->success) {
            $deployment->update([
                'status'                 => 'active',
                'coolify_app_id'         => $result->appId,
                'deployment_url'         => $result->deploymentUrl,
                'credentials_encrypted'  => [
                    'username'  => $result->loginUsername,
                    'password'  => $result->loginPassword,
                    'admin_url' => $result->adminUrl,
                ],
                'deployed_at' => now(),
            ]);

            $deployment->userProduct?->markAsActive();

            Mail::to($deployment->user->email)->queue(new DeploymentCredentialsMail($deployment));
        } else {
            $deployment->update([
                'status'         => 'failed',
                'failure_reason' => $result->failureReason,
            ]);

            Log::error('Coolify deployment failed', [
                'deployment_id' => $deployment->id,
                'server_id'     => $server->id,
                'reason'        => $result->failureReason,
            ]);
        }
    }
}
