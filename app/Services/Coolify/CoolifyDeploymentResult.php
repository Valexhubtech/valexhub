<?php

namespace App\Services\Coolify;

final class CoolifyDeploymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $appId = null,
        public readonly ?string $deploymentUrl = null,
        public readonly ?string $loginUsername = null,
        public readonly ?string $loginPassword = null,
        public readonly ?string $adminUrl = null,
        public readonly ?string $dbName = null,
        public readonly ?string $betterAuthSecret = null,
        public readonly ?string $centralApiKey = null,
        // true = real Coolify deploy, status comes via webhook; false = mock, mark active immediately
        public readonly bool $isProvisional = false,
        public readonly ?string $failureReason = null,
    ) {}
}
