<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DeploymentCredentialsMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Wave\Deployment;

class InstanceWebhookController extends Controller
{
    /**
     * Receives POST /api/instance-webhook/setup-complete from the deployed instance
     * once the bootstrap has finished (DB created, migrations run, admin seeded).
     *
     * Auth: Authorization: Bearer {central_api_key}
     *       X-Valexhub-Signature: hmac(sha256, key, raw_body)
     */
    public function setupComplete(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Accept any non-terminated status — bootstrap re-runs on every container restart,
        // so the deployment may already be 'active' after a restart.
        $deployment = Deployment::where('central_api_key', $token)
            ->whereNotIn('status', ['terminated'])
            ->first();

        if (! $deployment) {
            Log::warning('setup-complete webhook: no matching deployment', ['token_prefix' => substr($token, 0, 8)]);

            return response()->json(['error' => 'Not found'], 404);
        }

        // Verify HMAC signature
        $rawBody = $request->getContent();
        $expected = hash_hmac('sha256', $rawBody, $token);
        $received = $request->header('X-Valexhub-Signature', '');

        if (! hash_equals($expected, $received)) {
            Log::warning('setup-complete webhook: invalid signature', ['deployment_id' => $deployment->id]);

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->json()->all();
        $appUrl = $data['app_url'] ?? null;

        $isFirstActivation = in_array($deployment->status, ['provisioning', 'pending']);

        $updates = ['deployed_at' => now()];
        if ($appUrl) {
            $updates['deployment_url'] = $appUrl;
        }
        if ($isFirstActivation) {
            $updates['status'] = 'active';
        }

        $deployment->update($updates);

        // Only trigger activation side-effects on first transition to active.
        // On subsequent re-pings (container restarts) we skip the email so the
        // customer doesn't receive duplicate credentials.
        if ($isFirstActivation) {
            $deployment->userProduct?->markAsActive();

            if ($deployment->user?->email) {
                Mail::to($deployment->user->email)->queue(new DeploymentCredentialsMail($deployment));
            }
        }

        Log::info('setup-complete webhook received', [
            'deployment_id' => $deployment->id,
            'app_url' => $appUrl,
            'first_activation' => $isFirstActivation,
        ]);

        return response()->json(['ok' => true]);
    }
}
