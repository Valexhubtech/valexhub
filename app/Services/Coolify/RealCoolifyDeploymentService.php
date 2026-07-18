<?php

namespace App\Services\Coolify;

use App\Models\User;
use App\Services\BunnyStream\BunnyStreamService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Wave\CoolifyServer;
use Wave\Deployment;
use Wave\OrderAddon;
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

        $baseUrl = rtrim($server->api_url, '/');
        $token = $server->api_token;
        $serverUuid = $server->coolify_server_uuid;
        $projectUuid = $server->coolify_project_uuid;
        $environmentName = $server->coolify_environment_name ?? 'production';

        try {
            $appName = Str::slug($product->name).'-'.Str::lower(Str::random(6));
            $password = Str::password(16, symbols: false);
            $dbName = 'db_'.Str::lower(Str::random(20));
            $betterAuthSecret = Str::random(64);
            $centralApiKey = Str::random(64);
            $encryptionKey = bin2hex(random_bytes(32));
            $businessName = $options['business_name'] ?? $user->name;

            $basePayload = [
                'project_uuid' => $projectUuid,
                'server_uuid' => $serverUuid,
                'environment_name' => $environmentName,
                'name' => $appName,
                'instant_deploy' => false, // we inject env vars first, then trigger
            ];

            $deployType = $product->coolify_deploy_type;

            if ($deployType === 'docker_image') {
                $endpoint = '/api/v1/applications/dockerimage';
                $payload = array_merge($basePayload, [
                    'docker_registry_image_name' => $product->coolify_docker_image,
                ]);
            } elseif ($deployType === 'git_repo') {
                $endpoint = '/api/v1/applications/public';
                $payload = array_merge($basePayload, [
                    'git_repository' => $product->coolify_git_repo,
                    'git_branch' => $product->coolify_git_branch ?? 'main',
                ]);
            } else {
                return new CoolifyDeploymentResult(
                    success: false,
                    failureReason: 'Unknown coolify_deploy_type: '.($deployType ?? 'null').'. Set it to "docker_image" or "git_repo" on the product.',
                );
            }

            $response = $this->http($baseUrl, $token)
                ->post($endpoint, $payload)
                ->throw();

            $responseData = $response->json();
            $appUuid = $responseData['uuid'] ?? null;

            if (! $appUuid) {
                return new CoolifyDeploymentResult(
                    success: false,
                    failureReason: 'Coolify did not return an application UUID.',
                );
            }

            // Fetch the FQDN Coolify auto-assigned to this app right after creation.
            // It's available immediately (before deploy) as a sslip.io URL.
            // We need it now so BETTER_AUTH_URL can be injected on the first deploy.
            $appFqdn = $responseData['fqdn'] ?? null;
            if (! $appFqdn) {
                try {
                    $info = $this->http($baseUrl, $token)->get("/api/v1/applications/{$appUuid}")->json();
                    $appFqdn = $info['fqdn'] ?? null;
                } catch (\Throwable) {
                }
            }
            $appUrl = $appFqdn
                ? (str_starts_with($appFqdn, 'http') ? $appFqdn : 'https://'.ltrim($appFqdn, '/'))
                : null;

            // Create a per-deployment Bunny Stream library so each business gets
            // isolated video storage. Falls back gracefully if API key is absent.
            $bunnyLibraryId = null;
            $bunnyApiKey    = null;
            try {
                $bunny = app(BunnyStreamService::class);
                $bunnyLib = $bunny->createLibrary($businessName);
                $bunnyLibraryId = $bunnyLib['library_id'];
                $bunnyApiKey    = $bunnyLib['api_key'];
            } catch (\Throwable $e) {
                Log::warning('Bunny library creation failed — skipping Bunny env vars', [
                    'error' => $e->getMessage(),
                ]);
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
                appUrl: $appUrl,
                extraEnvVars: $options['extra_env_vars'] ?? [],
                standaloneMode: ($options['env_inject_mode'] ?? 'alongside') === 'standalone',
                bunnyLibraryId: $bunnyLibraryId,
                bunnyApiKey: $bunnyApiKey,
            );

            // Now trigger the actual deployment
            $this->http($baseUrl, $token)
                ->get('/api/v1/deploy', ['uuid' => $appUuid, 'force' => false]);

            Log::info('Coolify deployment triggered', ['app_uuid' => $appUuid, 'product' => $product->id]);

            return new CoolifyDeploymentResult(
                success: true,
                appId: $appUuid,
                deploymentUrl: $appUrl,
                loginUsername: $user->email,
                loginPassword: $password,
                dbName: $dbName,
                betterAuthSecret: $betterAuthSecret,
                centralApiKey: $centralApiKey,
                isProvisional: true,
                bunnyLibraryId: $bunnyLibraryId,
            );
        } catch (RequestException $e) {
            $responseBody = $e->response->json();
            $message = $responseBody['message'] ?? $e->getMessage();
            $errors = $responseBody['errors'] ?? [];
            $detail = $errors ? ' — '.collect($errors)->map(fn ($v, $k) => "$k: ".implode(', ', (array) $v))->implode('; ') : '';

            Log::error('Coolify deploy failed', ['product' => $product->id, 'status' => $e->response->status()]);

            return new CoolifyDeploymentResult(success: false, failureReason: $message.$detail);
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
        $token = $server->api_token;

        try {
            $response = $this->http($baseUrl, $token)
                ->get("/api/v1/applications/{$appId}")
                ->throw();

            $data = $response->json();
            $status = $data['status'] ?? 'unknown';
            $fqdn = $data['fqdn'] ?? null;
            $url = $fqdn ? 'https://'.ltrim($fqdn, '/') : null;

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
        ?string $appUrl = null,
        array $extraEnvVars = [],
        bool $standaloneMode = false,
        ?int $bunnyLibraryId = null,
        ?string $bunnyApiKey = null,
    ): void {
        $d = config('services.deploy');

        $enabledModules = $userProductId
            ? OrderAddon::where('user_product_id', $userProductId)
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
            // Database — full URL (required by service app) + individual parts (fallback)
            // Note: NODE_ENV is intentionally omitted — it is baked into the Docker image
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

            // Bunny Stream
            // Bunny Stream — per-deployment library created via API at deploy time
            ['key' => 'BUNNY_API_KEY',                       'value' => $bunnyApiKey,    'secret' => true],
            ['key' => 'BUNNY_STREAM_TOKEN_KEY',              'value' => $bunnyApiKey,    'secret' => true],
            ['key' => 'BUNNY_STREAM_LIBRARY_ID',             'value' => $bunnyLibraryId, 'secret' => false],
            ['key' => 'BUNNY_WEBHOOK_SECRET',                'value' => $bunnyApiKey,    'secret' => true],
            ['key' => 'NEXT_PUBLIC_BUNNY_STREAM_LIBRARY_ID', 'value' => $bunnyLibraryId, 'secret' => false],

            // Per-deployment identity
            ['key' => 'APP_BUSINESS_NAME',                  'value' => $businessName,                     'secret' => false],
            ['key' => 'APP_ADMIN_EMAIL',                    'value' => $user->email,                      'secret' => false],
            ['key' => 'APP_ADMIN_PASSWORD',                 'value' => $password,                         'secret' => true],
            ['key' => 'BETTER_AUTH_SECRET',                 'value' => $betterAuthSecret,                 'secret' => true],
            ['key' => 'ENABLED_MODULES',                    'value' => $enabledModules,                   'secret' => false],

            // App URL — injected at creation time so Better Auth accepts requests on first boot.
            // BETTER_AUTH_URL must match the origin exactly or Better Auth rejects all logins.
            ['key' => 'BETTER_AUTH_URL',                    'value' => $appUrl,                           'secret' => false],
            ['key' => 'NEXT_PUBLIC_APP_URL',                'value' => $appUrl,                           'secret' => false],

            // Platform comms — bootstrap uses these to phone home on setup-complete
            ['key' => 'CENTRAL_API_URL',                    'value' => $d['central_api_url'],             'secret' => false],
            ['key' => 'CENTRAL_API_KEY',                    'value' => $centralApiKey,                    'secret' => true],

            // Encryption key — unique per instance, protects sensitive DB values
            ['key' => 'ENCRYPTION_KEY',                     'value' => $encryptionKey,                    'secret' => true],
        ];

        // In standalone mode only inject the custom vars — skip the standard set entirely.
        // In alongside mode (default) merge custom vars on top of the standard set.
        $customVars = collect($extraEnvVars)
            ->filter(fn ($item) => ! empty($item['key']) && isset($item['value']))
            ->map(fn ($item) => ['key' => $item['key'], 'value' => $item['value'], 'secret' => true])
            ->values()
            ->all();

        $vars = $standaloneMode ? $customVars : array_merge($vars, $customVars);

        $injected = [];
        $failed = [];

        foreach ($vars as $var) {
            if ($var['value'] === null || $var['value'] === '') {
                continue; // skip unconfigured optional vars
            }

            try {
                $payload = ['key' => $var['key'], 'value' => $var['value']];

                $response = $this->http($baseUrl, $token)
                    ->post("/api/v1/applications/{$appUuid}/envs", $payload);

                // 409 = var already exists — switch to PATCH to update it
                if ($response->status() === 409) {
                    $response = $this->http($baseUrl, $token)
                        ->patch("/api/v1/applications/{$appUuid}/envs", $payload);
                }

                if ($response->failed()) {
                    $failed[] = $var['key'];
                    if (count($failed) === 1) {
                        Log::error('Env var injection HTTP error', [
                            'app_uuid' => $appUuid,
                            'key' => $var['key'],
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                    }
                } else {
                    $injected[] = $var['key'];
                }
            } catch (\Throwable $e) {
                $failed[] = $var['key'];
                if (count($failed) === 1) {
                    Log::error('Env var injection exception', [
                        'app_uuid' => $appUuid,
                        'key' => $var['key'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        if ($failed) {
            Log::warning('Some env vars failed to inject', ['app_uuid' => $appUuid, 'failed_keys' => $failed]);
        } else {
            Log::info('Env vars injected', ['app_uuid' => $appUuid, 'keys' => $injected]);
        }
    }

    // ── Admin operations ──────────────────────────────────────────────────────

    /**
     * Pull the latest Docker image and redeploy. Sets force=true so Coolify
     * always re-pulls from the registry regardless of local cache.
     */
    public function pushUpdate(Deployment $deployment): bool
    {
        [$baseUrl, $token] = $this->serverCredentials($deployment);
        if (! $baseUrl) {
            return false;
        }

        try {
            $this->http($baseUrl, $token)
                ->get('/api/v1/deploy', ['uuid' => $deployment->coolify_app_id, 'force' => true])
                ->throw();

            Log::info('Rolling update triggered', ['app_uuid' => $deployment->coolify_app_id]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Rolling update failed', ['app_uuid' => $deployment->coolify_app_id, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Restart the running container in-place. UUID and domain stay the same.
     * Use when the app is misbehaving but env vars are correct.
     */
    public function restartApp(Deployment $deployment): bool
    {
        [$baseUrl, $token] = $this->serverCredentials($deployment);
        if (! $baseUrl) {
            return false;
        }

        try {
            $this->http($baseUrl, $token)
                ->post("/api/v1/applications/{$deployment->coolify_app_id}/restart")
                ->throw();

            Log::info('Coolify restart triggered', ['app_uuid' => $deployment->coolify_app_id]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Coolify restart failed', ['app_uuid' => $deployment->coolify_app_id, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Re-inject all env vars into the existing Coolify app and trigger a fresh deploy.
     * UUID stays the same, so domain config is preserved.
     * Credentials are reused from the deployment record; only encryption key is regenerated.
     */
    public function redeployWithEnvFix(Deployment $deployment): bool
    {
        [$baseUrl, $token] = $this->serverCredentials($deployment);
        if (! $baseUrl) {
            return false;
        }

        $deployment->loadMissing('user');
        $creds = $deployment->credentials_encrypted ?? [];

        $password = $creds['password'] ?? Str::password(16, symbols: false);
        $dbName = $creds['db_name'] ?? 'db_'.Str::lower(Str::random(20));
        $betterAuthSecret = $creds['better_auth_secret'] ?? Str::random(64);
        $centralApiKey = $deployment->central_api_key ?? Str::random(64);
        $encryptionKey = bin2hex(random_bytes(32)); // always fresh — old value is unrecoverable
        $businessName = $deployment->business_name ?? $deployment->user?->name ?? 'Business';

        // Use the stored URL if we have one; otherwise fetch from Coolify
        $appUrl = $deployment->deployment_url;
        if (! $appUrl) {
            try {
                $info = $this->http($baseUrl, $token)->get("/api/v1/applications/{$deployment->coolify_app_id}")->json();
                $appFqdn = $info['fqdn'] ?? null;
                $appUrl = $appFqdn
                    ? (str_starts_with($appFqdn, 'http') ? $appFqdn : 'https://'.ltrim($appFqdn, '/'))
                    : null;
            } catch (\Throwable) {
            }
        }

        $this->injectEnvVars(
            appUuid: $deployment->coolify_app_id,
            user: $deployment->user,
            password: $password,
            dbName: $dbName,
            betterAuthSecret: $betterAuthSecret,
            centralApiKey: $centralApiKey,
            encryptionKey: $encryptionKey,
            businessName: $businessName,
            baseUrl: $baseUrl,
            token: $token,
            userProductId: $deployment->user_product_id,
            appUrl: $appUrl,
            extraEnvVars: $deployment->extra_env_vars ?? [],
            standaloneMode: ($deployment->env_inject_mode ?? 'alongside') === 'standalone',
        );

        // Persist updated credentials so the setup-complete webhook and client area stay in sync
        $deployment->update([
            'central_api_key' => $centralApiKey,
            'credentials_encrypted' => array_merge($creds, [
                'username' => $deployment->user?->email,
                'password' => $password,
                'db_name' => $dbName,
                'better_auth_secret' => $betterAuthSecret,
            ]),
        ]);

        try {
            $this->http($baseUrl, $token)
                ->get('/api/v1/deploy', ['uuid' => $deployment->coolify_app_id, 'force' => false])
                ->throw();

            Log::info('Coolify redeploy triggered', ['app_uuid' => $deployment->coolify_app_id]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Coolify redeploy trigger failed', ['app_uuid' => $deployment->coolify_app_id, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Permanently delete the Coolify application and its volumes.
     * Used before a full Wipe & Recreate so the admin can provision a brand-new app.
     */
    public function deleteApp(Deployment $deployment): bool
    {
        [$baseUrl, $token] = $this->serverCredentials($deployment);
        if (! $baseUrl) {
            return false;
        }

        try {
            $this->http($baseUrl, $token)
                ->delete("/api/v1/applications/{$deployment->coolify_app_id}", [
                    'delete_configurations' => true,
                    'delete_volumes' => true,
                ])
                ->throw();

            Log::info('Coolify app deleted', ['app_uuid' => $deployment->coolify_app_id]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Coolify delete failed', ['app_uuid' => $deployment->coolify_app_id, 'error' => $e->getMessage()]);

            return false; // non-fatal — Coolify may have already cleaned it up
        }
    }

    private function serverCredentials(Deployment $deployment): array
    {
        $deployment->loadMissing('coolifyServer');
        $server = $deployment->coolifyServer;

        if (! $server || ! $deployment->coolify_app_id) {
            return [null, null];
        }

        return [rtrim($server->api_url, '/'), $server->api_token];
    }

    private function http(string $baseUrl, string $token)
    {
        return Http::withToken($token)
            ->baseUrl($baseUrl)
            ->acceptJson()
            ->timeout(30);
    }
}
