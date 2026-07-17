<?php

namespace App\Services\Coolify;

use App\Models\User;
use Illuminate\Support\Str;
use Wave\CoolifyServer;
use Wave\Product;

/**
 * In-process fake for Coolify.io. There is no real Coolify integration yet,
 * so this never makes an HTTP call - it fabricates a plausible deployment
 * result so the rest of the pay-and-deploy pipeline can be built and tested
 * end to end. Swap the binding in AppServiceProvider for a real
 * Guzzle-backed implementation once Coolify credentials exist.
 */
class MockCoolifyDeploymentService implements CoolifyDeploymentServiceContract
{
    public function deploy(Product $product, User $user, array $options = []): CoolifyDeploymentResult
    {
        if ($this->shouldSimulateFailure()) {
            return new CoolifyDeploymentResult(
                success: false,
                failureReason: 'Mock Coolify deployment failed (simulated failure for testing).',
            );
        }

        $appId = (string) Str::uuid();
        $shortId = Str::lower(Str::random(6));
        $slug = $product->slug ?: Str::slug($product->name);
        $appUrl = "https://{$slug}-{$shortId}.mock-coolify.app";

        return new CoolifyDeploymentResult(
            success: true,
            appId: $appId,
            deploymentUrl: $appUrl,
            loginUsername: $user->email,
            loginPassword: Str::password(16, symbols: false),
            adminUrl: "{$appUrl}/admin",
            dbName: 'db_'.Str::lower(Str::random(20)),
            betterAuthSecret: Str::random(64),
        );
    }

    public function getStatus(string $appId, ?CoolifyServer $server = null): CoolifyDeploymentResult
    {
        return new CoolifyDeploymentResult(success: true, appId: $appId);
    }

    protected function shouldSimulateFailure(): bool
    {
        $failureRate = (float) config('services.coolify.mock_failure_rate', 0);

        return $failureRate > 0 && (mt_rand() / mt_getrandmax()) < $failureRate;
    }
}
