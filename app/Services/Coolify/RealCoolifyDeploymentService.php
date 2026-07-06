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
            $appName  = Str::slug($product->name) . '-' . Str::lower(Str::random(6));
            $password = Str::password(16, symbols: false);

            $basePayload = [
                'project_uuid'     => $projectUuid,
                'server_uuid'      => $serverUuid,
                'environment_name' => $environmentName,
                'name'             => $appName,
                'instant_deploy'   => true,
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

            // Coolify returns the assigned domain(s) in the create response
            $rawDomains    = $responseData['domains'] ?? null;
            $deploymentUrl = $rawDomains ? explode(',', $rawDomains)[0] : null;

            $this->injectEnvVars($appUuid, $product, $user, $password, $baseUrl, $token);

            return new CoolifyDeploymentResult(
                success: true,
                appId: $appUuid,
                deploymentUrl: $deploymentUrl,
                loginUsername: $user->email,
                loginPassword: $password,
            );
        } catch (RequestException $e) {
            $responseBody = $e->response->json();
            $message = $responseBody['message'] ?? $e->getMessage();
            $errors  = $responseBody['errors'] ?? [];
            $detail  = $errors ? ' — ' . collect($errors)->map(fn ($v, $k) => "$k: " . implode(', ', (array) $v))->implode('; ') : '';

            Log::error('Coolify deploy failed', ['response' => $responseBody, 'product' => $product->id]);

            return new CoolifyDeploymentResult(success: false, failureReason: $message . $detail);
        } catch (\Throwable $e) {
            Log::error('Coolify deploy exception', ['error' => $e->getMessage()]);

            return new CoolifyDeploymentResult(success: false, failureReason: $e->getMessage());
        }
    }

    public function getStatus(string $appId, ?CoolifyServer $server = null): CoolifyDeploymentResult
    {
        if (! $server instanceof CoolifyServer) {
            Log::warning('Coolify getStatus called without a server — returning unknown status', ['appId' => $appId]);

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

            if ($status === 'running') {
                $url = $fqdn ? 'https://' . ltrim($fqdn, '/') : null;

                return new CoolifyDeploymentResult(
                    success: true,
                    appId: $appId,
                    deploymentUrl: $url,
                );
            }

            return new CoolifyDeploymentResult(success: false, appId: $appId);
        } catch (RequestException $e) {
            Log::error('Coolify getStatus failed', ['appId' => $appId, 'error' => $e->getMessage()]);

            return new CoolifyDeploymentResult(success: false, appId: $appId);
        }
    }

    private function injectEnvVars(string $appUuid, Product $product, User $user, string $password, string $baseUrl, string $token): void
    {
        $template = $product->coolify_env_template ?? [];

        $vars = array_merge($template, [
            ['key' => 'APP_ADMIN_EMAIL',    'value' => $user->email],
            ['key' => 'APP_ADMIN_PASSWORD', 'value' => $password],
        ]);

        foreach ($vars as $var) {
            if (empty($var['key'])) {
                continue;
            }

            $this->http($baseUrl, $token)->post("/api/v1/applications/{$appUuid}/envs", [
                'key'        => $var['key'],
                'value'      => $var['value'] ?? '',
                'is_preview' => false,
            ]);
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
