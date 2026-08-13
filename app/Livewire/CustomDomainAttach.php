<?php

namespace App\Livewire;

use App\Models\EmailDomain;
use App\Services\Domain\BringYourOwnDomainService;
use Livewire\Component;
use Wave\Deployment;

class CustomDomainAttach extends Component
{
    public Deployment $deployment;

    public string $domain = '';

    public string $step = 'form'; // form | records | done

    public ?EmailDomain $emailDomain = null;

    public string $error = '';

    public function mount(Deployment $deployment): void
    {
        $this->deployment  = $deployment;
        $this->emailDomain = EmailDomain::where('instance_id', $deployment->id)
            ->where('is_shared', false)
            ->latest()
            ->first();

        if ($this->emailDomain) {
            $this->step = match ($this->emailDomain->status) {
                'active'           => 'done',
                'manual', 'verifying' => 'records',
                default            => 'form',
            };
        }
    }

    public function attach(): void
    {
        $this->validate([
            'domain' => ['required', 'regex:/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/'],
        ]);

        $this->error = '';

        try {
            $service           = app(BringYourOwnDomainService::class);
            $this->emailDomain = $service->attach((string) $this->deployment->id, strtolower(trim($this->domain)));
            $this->step        = 'records';
        } catch (\Throwable $e) {
            $this->error = 'Failed to attach domain: ' . $e->getMessage();
        }
    }

    public function recheck(): void
    {
        if (! $this->emailDomain) {
            return;
        }

        $service           = app(BringYourOwnDomainService::class);
        $this->emailDomain = $service->recheck($this->emailDomain);

        if ($this->emailDomain->status === 'active') {
            $this->step = 'done';
        }
    }

    public function render()
    {
        return view('livewire.custom-domain-attach');
    }
}
