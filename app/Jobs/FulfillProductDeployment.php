<?php

namespace App\Jobs;

use App\Services\ProductDeploymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Wave\Deployment;

class FulfillProductDeployment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Deployment $deployment) {}

    public function handle(ProductDeploymentService $service): void
    {
        $service->fulfill($this->deployment);
    }
}
