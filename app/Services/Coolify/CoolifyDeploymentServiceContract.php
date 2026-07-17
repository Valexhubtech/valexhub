<?php

namespace App\Services\Coolify;

use App\Models\User;
use Wave\CoolifyServer;
use Wave\Deployment;
use Wave\Product;

interface CoolifyDeploymentServiceContract
{
    /**
     * Provision a new instance of $product for $user and return its access details.
     */
    public function deploy(Product $product, User $user, array $options = []): CoolifyDeploymentResult;

    /**
     * Look up the current status of a previously created deployment.
     * Pass the server to route the request to the correct Coolify instance.
     */
    public function getStatus(string $appId, ?CoolifyServer $server = null): CoolifyDeploymentResult;

    /**
     * Pull the latest Docker image and redeploy an existing instance.
     * Used for rolling software updates across customer deployments.
     */
    public function pushUpdate(Deployment $deployment): bool;
}
