<?php

namespace App\Services\Coolify;

use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Wave\CoolifyServer;
use Wave\Product;

class RealCoolifyDeploymentService implements CoolifyDeploymentServiceContract
{
    public function deploy(Product $product, User $user, array $options = []): CoolifyDeploymentResult
    {
        $server = $options['server'] ?? null;

        if (! $server instanceof CoolifyServer) {
            return new CoolifyDeploymentResult(
                success: false,
                failureReason: 'No Coolify server was passed to deploy(). Ensure a server is selected before deploying.',
            );
        }

        $baseUrl         = rtrim($server->api_url, '/');
        $token           = $server->api_token;
        $serverUuid      = $server->coolify_server_uuid;
        $projectUuid     = $server->coolify_project_uuid;
        $environmentName = $server->coolify_environment_name ?? 'production';

        try {
            $appName          = Str::slug($product->name) . '-' . Str::lower(Str::random(6));
            $password         = Str::password(16, symbols: false);
            $dbName           = 'db_' . Str::lower(Str::random(20));
            $betterAuthSecret = Str::random(64);
            $centralApiKey    = Str::random(64);
            $encryptionKey    = bin2hex(random_bytes(32));
            $businessName     = $options['business_name'] ?? $user->name;

            $basePayload = [
                'project_uuid'     => $projectUuid,
                'server_uuid'      => $serverUuid,
                'environment_name' => $environmentName,
                'name'             => $appName,
                'instant_deploy'   => false, // we inject env vars first, then trigger
            ];

            $deployType = $product->coolify_deploy_type;

            if ($deployType === 'docker_image') {
                $endpoint = '/api/v1/applications/dockerimage';
                $payload  = array_merge($basePayload, [
                    'docker_registry_image_name' => $product->coolify_docker_image,
                ]);
            } elseif ($deployType === 'git_repo') {
                $endpoint = '/api/v1/applications/public';
                $payload  = array_merge($basePayload, [
                    'git_repository' => $product->coolify_git_repo,
                    'git_branch'     => $product->coolify_git_branch ?? 'main',
                ]);
            } else {
                return new CoolifyDeploymentResult(
                    success: false,
                    failureReason: 'Unknown coolify_deploy_type: ' . ($deployType ?? 'null') . '. Set it to "docker_image" or "git_repo" on the product.',
                );
            }

            $response = $this->http($baseUrl, $token)
                ->post($endpoint, $payload)
                ->throw();

            $responseData = $response->json();
            $appUuid      = $responseData['uuid'] ?? null;

            if (! $appUuid) {
                return new CoolifyDeploymentResult(
                    success: false,
                    failureReason: 'Coolify did not return an application UUID.',
                );
            }

            // Inject all env vars before triggering the deploy
            $this->injectEnvVars(
                appUuid: $appUuid,
                user: $user,
                password: $password,
                dbName: $dbName,
                betterAuthSecret: $betterAuthSecret,
                centralApiKey: $centralApiKey,
                encryptionKey: $encryptionKey,
                businessName: $businessName,
                baseUrl: $baseUrl,
                token: $token,
                userProductId: $options['user_product_id'] ?? null,
            );

            // Now trigger the actual deployment
            $this->http($baseUrl, $token)
                ->post("/api/v1/deployments", ['uuid' => $appUuid]);

            Log::info('Coolify deployment triggered', ['app_uuid' => $appUuid, 'product' => $product->id]);

            return new CoolifyDeploymentResult(
                success: true,
                appId: $appUuid,
                loginUsername: $user->email,
                loginPassword: $password,
                dbName: $dbName,
                betterAuthSecret: $betterAuthSecret,
                centralApiKey: $centralApiKey,
                isProvisional: true, // status comes via webhook, not immediately
            );
        } catch (RequestException $e) {
            $responseBody = $e->response->json();
            $message = $responseBody['message'] ?? $e->getMessage();
            $errors  = $responseBody['errors'] ?? [];
            $detail  = $errors ? ' — ' . collect($errors)->map(fn ($v, $k) => "$k: " . implode(', ', (array) $v))->implode('; ') : '';

            Log::error('Coolify deploy failed', ['product' => $product->id, 'status' => $e->response->status()]);

            return new CoolifyDeploymentResult(success: false, failureReason: $message . $detail);
        } catch (\Throwable $e) {
            Log::error('Coolify deploy exception', ['error' => $e->getMessage()]);

            return new CoolifyDeploymentResult(success: false, failureReason: $e->getMessage());
        }
    }

    public function getStatus(string $appId, ?CoolifyServer $server = null): CoolifyDeploymentResult
    {
        if (! $server instanceof CoolifyServer) {
            return new CoolifyDeploymentResult(success: false, appId: $appId);
        }

        $baseUrl = rtrim($server->api_url, '/');
        $token   = $server->api_token;

        try {
            $response = $this->http($baseUrl, $token)
                ->get("/api/v1/applications/{$appId}")
                ->throw();

            $data   = $response->json();
            $status = $data['status'] ?? 'unknown';
            $fqdn   = $data['fqdn'] ?? null;
            $url    = $fqdn ? 'https://' . ltrim($fqdn, '/') : null;

            return new CoolifyDeploymentResult(
                success: $status === 'running',
                appId: $appId,
                deploymentUrl: $url,
            );
        } catch (RequestException $e) {
            Log::error('Coolify getStatus failed', ['appId' => $appId, 'status' => $e->response->status()]);

            return new CoolifyDeploymentResult(success: false, appId: $appId);
        }
    }

    private function injectEnvVars(
        string $appUuid,
        User $user,
        string $password,
        string $dbName,
        string $betterAuthSecret,
        string $centralApiKey,
        string $encryptionKey,
        string $businessName,
        string $baseUrl,
        string $token,
        ?int $userProductId = null,
    ): void {
        $d = config('services.deploy');

        $enabledModules = $userProductId
            ? \Wave\OrderAddon::where('user_product_id', $userProductId)
                ->with('addon:id,module_key')
                ->get()
                ->pluck('addon.module_key')
                ->filter()
                ->unique()
                ->implode(',')
            : '';

        // Construct the full DATABASE_URL from individual parts.
        // The service app requires this directly. Individual parts are also injected
        // as a fallback so the app can construct it itself if DATABASE_URL is ever missing.
        $databaseUrl = ($d['db_host'] && $d['db_user'] && $d['db_password'])
            ? sprintf(
                'postgresql://%s:%s@%s:%s/%s',
                rawurlencode($d['db_user']),
                rawurlencode($d['db_password']),
                $d['db_host'],
                $d['db_port'] ?? '5432',
                $dbName,
            )
            : null;

        // All vars to inject. Mark secrets so Coolify masks them in its UI.
        // Values are never logged — only keys appear in log output.
        $vars = [
            // Runtime
            ['key' => 'NODE_ENV',                            'value' => $d['node_env'] ?? 'production',   'secret' => false],

            // Database — full URL (required by service app) + individual parts (fallback)
            ['key' => 'DATABASE_URL',                       'value' => $databaseUrl,                      'secret' => true],
            ['key' => 'DB_HOST',                            'value' => $d['db_host'],                     'secret' => false],
            ['key' => 'DB_PORT',                            'value' => $d['db_port'] ?? '5432',           'secret' => false],
            ['key' => 'DB_USER',                            'value' => $d['db_user'],                     'secret' => false],
            ['key' => 'DB_PASSWORD',                        'value' => $d['db_password'],                 'secret' => true],
            ['key' => 'DB_NAME',                            'value' => $dbName,                           'secret' => false],

            // Redis
            ['key' => 'REDIS_URL',                          'value' => $d['redis_url'],                   'secret' => false],

            // Storage
            ['key' => 'STORAGE_TYPE',                       'value' => $d['storage_type'] ?? 'b2',       'secret' => false],
            ['key' => 'B2_APPLICATION_KEY_ID',              'value' => $d['b2_key_id'],                   'secret' => false],
            ['key' => 'B2_APPLICATION_KEY',                 'value' => $d['b2_key'],                      'secret' => true],
            ['key' => 'B2_BUCKET_ID',                       'value' => $d['b2_bucket_id'],                'secret' => false],
            ['key' => 'B2_BUCKET_NAME',                     'value' => $d['b2_bucket_name'],              'secret' => false],
            ['key' => 'B2_CDN_BASE_URL',                    'value' => $d['b2_cdn_base_url'],             'secret' => false],
            ['key' => 'STORAGE_PREFIX',                     'value' => $dbName,                           'secret' => false], // unique per instance

            // Email
            ['key' => 'RESEND_API_KEY',                     'value' => $d['resend_api_key'],              'secret' => true],

            // Payments
            ['key' => 'PAYSTACK_SECRET_KEY',                'value' => $d['paystack_secret'],             'secret' => true],
            ['key' => 'NEXT_PUBLIC_PAYSTACK_PUBLIC_KEY',    'value' => $d['paystack_public'],             'secret' => false],

            // Bunny Stream
            ['key' => 'BUNNY_API_KEY',                      'value' => $d['bunny_api_key'],               'secret' => true],
            ['key' => 'BUNNY_STREAM_TOKEN_KEY',             'value' => $d['bunny_stream_token'],          'secret' => true],
            ['key' => 'BUNNY_STREAM_LIBRARY_ID',            'value' => $d['bunny_library_id'],            'secret' => false],
            ['key' => 'BUNNY_STREAM_CDN_HOSTNAME',          'value' => $d['bunny_cdn_hostname'],          'secret' => false],
            ['key' => 'BUNNY_WEBHOOK_SECRET',               'value' => $d['bunny_webhook_secret'],        'secret' => true],
            ['key' => 'NEXT_PUBLIC_BUNNY_STREAM_LIBRARY_ID','value' => $d['bunny_library_id'],            'secret' => false],

            // Per-deployment identity
            ['key' => 'APP_BUSINESS_NAME',                  'value' => $businessName,                     'secret' => false],
            ['key' => 'APP_ADMIN_EMAIL',                    'value' => $user->email,                      'secret' => false],
            ['key' => 'APP_ADMIN_PASSWORD',                 'value' => $password,                         'secret' => true],
            ['key' => 'BETTER_AUTH_SECRET',                 'value' => $betterAuthSecret,                 'secret' => true],
            ['key' => 'ENABLED_MODULES',                    'value' => $enabledModules,                   'secret' => false],

            // Platform comms — bootstrap uses these to phone home on setup-complete
            ['key' => 'CENTRAL_API_URL',                    'value' => $d['central_api_url'],             'secret' => false],
            ['key' => 'CENTRAL_API_KEY',                    'value' => $centralApiKey,                    'secret' => true],

            // Encryption key — unique per instance, protects sensitive DB values
            ['key' => 'ENCRYPTION_KEY',                     'value' => $encryptionKey,                    'secret' => true],
        ];

        $injected = [];
        $failed   = [];

        foreach ($vars as $var) {
            if ($var['value'] === null || $var['value'] === '') {
                continue; // skip unconfigured optional vars
            }

            try {
                $this->http($baseUrl, $token)->post("/api/v1/applications/{$appUuid}/envs", [
                    'key'        => $var['key'],
                    'value'      => $var['value'],
                    'is_preview' => false,
                    'is_secret'  => $var['secret'],
                ])->throw();

                $injected[] = $var['key'];
            } catch (\Throwable) {
                $failed[] = $var['key'];
            }
        }

        if ($failed) {
            Log::warning('Some env vars failed to inject', ['app_uuid' => $appUuid, 'failed_keys' => $failed]);
        } else {
            Log::info('Env vars injected', ['app_uuid' => $appUuid, 'keys' => $injected]);
        }
    }

    private function http(string $baseUrl, string $token)
    {
        return Http::withToken($token)
            ->baseUrl($baseUrl)
            ->acceptJson()
            ->timeout(30);
    }
}
