<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DeploymentAlertMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Wave\Deployment;

class CoolifyWebhookController extends Controller
{
    /**
     * Receives POST /api/coolify-webhook?token=SECRET from Coolify's notification system.
     *
     * Events handled:
     *   deployment_success   — container started; save URL, update env vars in Coolify
     *   deployment_failed    — container failed to start; notify support + user
     *   status_changed       — running app stopped unexpectedly; notify support + user
     *   restart_limit_reached — app is crash-looping; notify support + user
     */
    public function handle(Request $request, string $token): JsonResponse
    {
        $secret = config('services.coolify.webhook_secret');

        if (! $secret || ! hash_equals($secret, $token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->json()->all();
        $event = $data['event'] ?? null;
        $appUuid = $data['application_uuid'] ?? null;
        $fqdn = $data['fqdn'] ?? null;

        Log::info('Coolify webhook received', ['event' => $event, 'app_uuid' => $appUuid]);

        if (! $appUuid) {
            return response()->json(['ok' => true]);
        }

        $deployment = Deployment::where('coolify_app_id', $appUuid)->first();

        if (! $deployment) {
            return response()->json(['ok' => true]);
        }

        match ($event) {
            'deployment_success' => $this->onDeploymentSuccess($deployment, $fqdn),
            'deployment_failed' => $this->onDeploymentFailed($deployment),
            'status_changed' => $this->onStatusChanged($deployment),
            'restart_limit_reached' => $this->onRestartLimitReached($deployment),
            default => null,
        };

        return response()->json(['ok' => true]);
    }

    // ── Event handlers ────────────────────────────────────────────────────────

    private function onDeploymentSuccess(Deployment $deployment, ?string $fqdn): void
    {
        // Container is up. Bootstrap is running inside it.
        // Status stays "provisioning" — setup-complete webhook flips it to "active".
        if ($fqdn) {
            $appUrl = str_starts_with($fqdn, 'http') ? $fqdn : 'https://'.ltrim($fqdn, '/');
            $deployment->update(['deployment_url' => $appUrl]);

            // Push BETTER_AUTH_URL and NEXT_PUBLIC_APP_URL into Coolify now that we know the URL.
            // These take effect on the next container restart / redeploy.
            $this->pushUrlEnvVars($deployment, $appUrl);
        }
    }

    private function onDeploymentFailed(Deployment $deployment): void
    {
        if (in_array($deployment->status, ['failed', 'terminated'])) {
            return;
        }

        $deployment->update([
            'status' => 'failed',
            'failure_reason' => 'Container failed to start. Check Coolify deployment logs.',
        ]);

        $this->notifySupport($deployment, 'Deployment failed — container did not start. Check Coolify logs.');
        $this->notifyUser($deployment, 'failed');
    }

    private function onStatusChanged(Deployment $deployment): void
    {
        // Coolify fires this when a running container stops unexpectedly.
        if ($deployment->status !== 'active') {
            return;
        }

        $deployment->update(['status' => 'failed']);

        $this->notifySupport($deployment, 'Active deployment stopped unexpectedly.');
        $this->notifyUser($deployment, 'stopped');
    }

    private function onRestartLimitReached(Deployment $deployment): void
    {
        if (in_array($deployment->status, ['failed', 'terminated'])) {
            return;
        }

        $deployment->update([
            'status' => 'failed',
            'failure_reason' => 'Container reached its restart limit — crash-looping. Manual intervention required.',
        ]);

        $this->notifySupport($deployment, 'Deployment is crash-looping and has reached the restart limit.');
        $this->notifyUser($deployment, 'crashed');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function pushUrlEnvVars(Deployment $deployment, string $appUrl): void
    {
        $server = $deployment->coolifyServer;
        if (! $server || ! $deployment->coolify_app_id) {
            return;
        }

        $baseUrl = rtrim($server->api_url, '/');
        $token = $server->api_token;

        foreach (['BETTER_AUTH_URL', 'NEXT_PUBLIC_APP_URL'] as $key) {
            try {
                Http::withToken($token)
                    ->baseUrl($baseUrl)
                    ->acceptJson()
                    ->timeout(10)
                    ->post("/api/v1/applications/{$deployment->coolify_app_id}/envs", [
                        'key' => $key,
                        'value' => $appUrl,
                        'is_preview' => false,
                        'is_secret' => false,
                    ]);
            } catch (\Throwable) {
                Log::warning("Failed to update {$key} in Coolify", ['deployment_id' => $deployment->id]);
            }
        }
    }

    private function notifySupport(Deployment $deployment, string $reason): void
    {
        $email = config('services.support.email');
        if (! $email) {
            return;
        }

        Mail::to($email)->queue(new DeploymentAlertMail($deployment, $reason, 'support'));
    }

    private function notifyUser(Deployment $deployment, string $type): void
    {
        if (! $deployment->user?->email) {
            return;
        }

        Mail::to($deployment->user->email)->queue(new DeploymentAlertMail($deployment, $type, 'user'));
    }
}
