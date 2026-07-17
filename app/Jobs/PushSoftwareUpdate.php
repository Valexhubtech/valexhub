<?php

namespace App\Jobs;

use App\Services\Coolify\RealCoolifyDeploymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Wave\Deployment;

class PushSoftwareUpdate implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(public Deployment $deployment) {}

    public function handle(RealCoolifyDeploymentService $coolify): void
    {
        $coolify->pushUpdate($this->deployment);
    }
}
