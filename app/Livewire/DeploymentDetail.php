<?php

namespace App\Livewire;

use App\Jobs\FulfillProductDeployment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Wave\Deployment;

class DeploymentDetail extends Component
{
    public int $deploymentId;

    public function mount(int $deploymentId): void
    {
        $this->deploymentId = $deploymentId;
    }

    public function getDeploymentProperty(): Deployment
    {
        return Deployment::with(['product', 'userProduct.pricing', 'userProduct.orderAddons.addon'])
            ->findOrFail($this->deploymentId);
    }

    public function getIsPollingProperty(): bool
    {
        return in_array($this->deployment->status, ['pending', 'provisioning']);
    }

    public function retry(): void
    {
        $deployment = Deployment::find($this->deploymentId);

        if (! $deployment || $deployment->user_id !== Auth::id() || $deployment->status !== 'failed') {
            return;
        }

        $deployment->update(['status' => 'pending', 'failure_reason' => null]);

        FulfillProductDeployment::dispatch($deployment);
    }

    public function render()
    {
        return view('livewire.deployment-detail', [
            'deployment' => $this->deployment,
            'isPolling' => $this->isPolling,
        ]);
    }
}
