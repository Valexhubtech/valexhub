<?php

namespace App\Livewire;

use App\Jobs\FulfillProductDeployment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeploymentStatus extends Component
{
    private function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    public function getDeploymentsProperty()
    {
        return $this->currentUser()->deployments()->latest()->with([
            'product',
            'userProduct',
            'domainPurchases' => fn ($q) => $q->where('payment_status', 'paid')->latest(),
        ])->get();
    }

    public function getHasPendingProperty(): bool
    {
        return $this->deployments->whereIn('status', ['pending', 'provisioning'])->isNotEmpty();
    }

    public function cancel(int $deploymentId): void
    {
        $deployment = $this->currentUser()->deployments()->find($deploymentId);

        if (! $deployment || ! in_array($deployment->status, ['pending', 'failed'])) {
            return;
        }

        $deployment->userProduct?->markAsCancelled();
        $deployment->delete();
    }

    public function retry(int $deploymentId): void
    {
        $deployment = $this->currentUser()->deployments()->find($deploymentId);

        if (! $deployment || $deployment->status !== 'failed') {
            return;
        }

        $deployment->update(['status' => 'pending', 'failure_reason' => null]);

        FulfillProductDeployment::dispatch($deployment);
    }

    public function render()
    {
        return view('livewire.deployment-status', [
            'deployments' => $this->deployments,
            'hasPending' => $this->hasPending,
        ]);
    }
}
