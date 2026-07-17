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

        $deployment = Deployment::where('central_api_key', $token)
            ->whereIn('status', ['provisioning', 'pending'])
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

        $updates = [
            'status' => 'active',
            'deployed_at' => now(),
        ];

        if ($appUrl) {
            $updates['deployment_url'] = $appUrl;
        }

        $deployment->update($updates);
        $deployment->userProduct?->markAsActive();

        if ($deployment->user?->email) {
            Mail::to($deployment->user->email)->queue(new DeploymentCredentialsMail($deployment));
        }

        Log::info('Deployment marked active via setup-complete webhook', [
            'deployment_id' => $deployment->id,
            'app_url' => $appUrl,
        ]);

        return response()->json(['ok' => true]);
    }
}
