<?php

namespace App\Services\Domain;

use App\Models\EmailDomain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wave\Deployment;

/**
 * Injects Plume email env vars into a Coolify-hosted instance and triggers a redeploy.
 * Replaces the shared-domain vars with custom-domain vars (or vice-versa).
 * Always exactly ONE key + ONE domain per instance.
 */
class EmailInjector
{
    public function injectEmailDomain(string $instanceId, string $domain, string $apiKey): void
    {
        $deployment = Deployment::findOrFail($instanceId);
        $appUuid    = $deployment->coolify_app_id;

        if (! $appUuid || ! $deployment->coolifyServer) {
            Log::error('EmailInjector: deployment has no Coolify app ID or server', [
                'instance_id' => $instanceId,
            ]);

            return;
        }

        $server  = $deployment->coolifyServer;
        $baseUrl = rtrim($server->api_url, '/');
        $token   = $server->api_token;

        $businessName = $deployment->business_name ?? config('app.name');

        $vars = [
            ['key' => 'PLUME_BASE_URL',       'value' => config('services.deploy.plume_base_url'), 'secret' => false],
            ['key' => 'PLUME_API_KEY',         'value' => $apiKey,                           'secret' => true],
            ['key' => 'MAIL_FROM_DOMAIN',      'value' => $domain,                           'secret' => false],
            ['key' => 'MAIL_PROVIDER',         'value' => 'plume',                           'secret' => false],
            ['key' => 'SENDER_NAME',           'value' => "{$businessName} via Valexhub",    'secret' => false],
        ];

        $http = Http::withToken($token)->baseUrl($baseUrl)->acceptJson()->timeout(30);

        foreach ($vars as $var) {
            $payload  = ['key' => $var['key'], 'value' => $var['value']];
            $response = $http->post("/api/v1/applications/{$appUuid}/envs", $payload);

            if ($response->status() === 409) {
                $response = $http->patch("/api/v1/applications/{$appUuid}/envs", $payload);
            }

            if ($response->failed()) {
                Log::error('EmailInjector: env injection failed', [
                    'key'      => $var['key'],
                    'instance' => $instanceId,
                    'status'   => $response->status(),
                ]);
            }
        }

        // Trigger redeploy so new env vars take effect
        $http->post('/api/v1/deploy', ['uuid' => $appUuid, 'force' => false]);

        // Record in email_domains table (upsert — keyed by instance + domain)
        EmailDomain::updateOrCreate(
            ['instance_id' => $instanceId, 'domain' => $domain],
            [
                'domain'       => $domain,
                'is_shared'    => $domain === config('services.plume.mail_domain'),
                'stage'        => $domain === config('services.plume.mail_domain') ? 'default_only' : 'custom_active',
                'status'       => 'active',
                'plume_api_key' => encrypt($apiKey),
            ]
        );

        Log::info('EmailInjector: domain injected', ['instance' => $instanceId, 'domain' => $domain]);
    }
}
