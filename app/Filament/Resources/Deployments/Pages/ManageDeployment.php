<?php

namespace App\Filament\Resources\Deployments\Pages;

use App\Filament\Resources\Deployments\DeploymentResource;
use App\Mail\DeploymentCredentialsMail;
use App\Services\Coolify\RealCoolifyDeploymentService;
use App\Services\ProductDeploymentService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;
use Wave\OrderAddon;
use Wave\ProductAddon;

class ManageDeployment extends ViewRecord
{
    protected static string $resource = DeploymentResource::class;

    protected string $view = 'filament.resources.deployments.manage-deployment';

    public array $enabledModules = [];

    public string $customDomainInput = '';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load(['user', 'product', 'userProduct', 'coolifyServer', 'userProduct.orderAddons.addon']);

        // Load enabled modules from OrderAddon records
        if ($this->record->userProduct) {
            $this->enabledModules = OrderAddon::where('user_product_id', $this->record->user_product_id)
                ->with('addon:id,module_key')
                ->get()
                ->pluck('addon.module_key')
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        }
    }

    public function getTitle(): string
    {
        return 'Manage Deployment #'.$this->record->getKey();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_invoice')
                ->label('Create Invoice')
                ->icon('phosphor-paper-plane-duotone')
                ->color('info')
                ->modalHeading('Create & Send Invoice')
                ->modalDescription('Review the pre-filled sections, adjust amounts, then send.')
                ->schema(fn (): array => DeploymentResource::buildInvoiceFormSchema($this->record))
                ->action(function (array $data): void {
                    DeploymentResource::dispatchInvoice($this->record, $data);
                    Notification::make()->title('Invoice created and sent to client.')->success()->send();
                })
                ->visible(fn (): bool => $this->record->user_id !== null),

            Action::make('refresh')
                ->label('Refresh')
                ->icon('phosphor-arrows-clockwise')
                ->color('gray')
                ->action(function (): void {
                    $this->record->refresh();
                    $this->record->load(['user', 'product', 'userProduct', 'coolifyServer']);
                    Notification::make()->title('Refreshed.')->success()->send();
                }),

            Action::make('mark_active')
                ->label('Mark Active')
                ->icon('phosphor-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Manually Mark as Active')
                ->modalDescription('Use this if the container is confirmed running in Coolify but the setup-complete webhook never fired. This marks the deployment active and sends credentials to the client.')
                ->modalSubmitActionLabel('Mark Active & Send Credentials')
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'active',
                        'deployed_at' => now(),
                    ]);
                    $this->record->userProduct?->markAsActive();
                    if ($this->record->user?->email) {
                        Mail::to($this->record->user->email)
                            ->queue(new DeploymentCredentialsMail($this->record));
                    }
                    $this->record->refresh();
                    Notification::make()->title('Marked active. Credentials email queued.')->success()->send();
                })
                ->visible(fn (): bool => $this->record->status === 'provisioning'),

            Action::make('back')
                ->label('Back to list')
                ->icon('phosphor-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    // ── Coolify Actions ───────────────────────────────────────────────────────

    public function restart(): void
    {
        $ok = app(RealCoolifyDeploymentService::class)->restartApp($this->record);

        if ($ok) {
            Notification::make()->title('Restart signal sent.')->success()->send();
        } else {
            Notification::make()->title('Restart failed')->body('Could not reach Coolify. Check the logs.')->danger()->send();
        }
    }

    public function fixAndRedeploy(): void
    {
        $ok = app(RealCoolifyDeploymentService::class)->redeployWithEnvFix($this->record);

        if ($ok) {
            $this->record->update([
                'status' => 'provisioning',
                'failure_reason' => null,
            ]);
            $this->record->refresh();
            Notification::make()->title('Env vars re-injected and redeploy triggered.')->success()->send();
        } else {
            Notification::make()->title('Fix & Redeploy failed')->body('Check the Laravel logs.')->danger()->send();
        }
    }

    public function setCustomDomain(): void
    {
        $domain = trim($this->customDomainInput);

        if (! $domain) {
            Notification::make()->title('Please enter a domain.')->warning()->send();
            return;
        }

        $ok = app(RealCoolifyDeploymentService::class)->setDomain($this->record, $domain);

        if ($ok) {
            $this->record->update(['custom_domain' => $domain]);
            $this->record->refresh();
            $this->customDomainInput = '';
            Notification::make()->title('Domain updated. Container is restarting.')->success()->send();
        } else {
            Notification::make()->title('Failed to update domain in Coolify. Check the logs.')->danger()->send();
        }
    }

    public function retryDeployment(): void
    {
        $this->record->update([
            'status' => 'pending',
            'failure_reason' => null,
        ]);

        app(ProductDeploymentService::class)->fulfill($this->record->fresh());
        $this->record->refresh();

        Notification::make()->title('Retrying deployment…')->success()->send();
    }

    public function wipeAndRecreate(): void
    {
        app(RealCoolifyDeploymentService::class)->deleteApp($this->record);

        $this->record->update([
            'status' => 'pending',
            'coolify_app_id' => null,
            'deployment_url' => null,
            'failure_reason' => null,
        ]);

        app(ProductDeploymentService::class)->fulfill($this->record->fresh());
        $this->record->refresh();

        Notification::make()->title('Container wiped. New container is provisioning.')->success()->send();
    }

    public function suspend(): void
    {
        $this->record->update(['status' => 'suspended']);
        $this->record->userProduct?->update(['status' => 'suspended']);
        $this->record->refresh();
        Notification::make()->title('Deployment suspended.')->success()->send();
    }

    public function reactivate(): void
    {
        $this->record->update(['status' => 'active']);
        $this->record->userProduct?->update(['status' => 'active']);
        $this->record->refresh();
        Notification::make()->title('Deployment reactivated.')->success()->send();
    }

    public function terminate(): void
    {
        $this->record->update(['status' => 'terminated']);
        $this->record->userProduct?->update(['status' => 'cancelled']);
        $this->record->refresh();
        Notification::make()->title('Deployment terminated.')->success()->send();
    }

    // ── Module management ─────────────────────────────────────────────────────

    public function saveModules(): void
    {
        if (! $this->record->user_product_id) {
            Notification::make()->title('No purchase record linked — cannot update modules.')->danger()->send();

            return;
        }

        // Sync OrderAddon records to match the selected modules
        $existing = OrderAddon::where('user_product_id', $this->record->user_product_id)
            ->with('addon:id,module_key')
            ->get();

        $existingKeys = $existing->pluck('addon.module_key')->filter()->toArray();
        $toAdd = array_diff($this->enabledModules, $existingKeys);
        $toRemove = array_diff($existingKeys, $this->enabledModules);

        // Remove de-selected modules
        foreach ($toRemove as $key) {
            $addon = ProductAddon::where('module_key', $key)->first();
            if ($addon) {
                OrderAddon::where('user_product_id', $this->record->user_product_id)
                    ->where('product_addon_id', $addon->id)
                    ->delete();
            }
        }

        // Add newly selected modules
        foreach ($toAdd as $key) {
            $addon = ProductAddon::where('module_key', $key)->first();
            if ($addon) {
                OrderAddon::create([
                    'user_product_id' => $this->record->user_product_id,
                    'product_addon_id' => $addon->id,
                    'amount_paid' => 0,
                    'price_type' => 'onetime',
                ]);
            }
        }

        // Re-inject ENABLED_MODULES and trigger redeploy so the container picks up changes
        $ok = app(RealCoolifyDeploymentService::class)->redeployWithEnvFix($this->record);

        if ($ok) {
            $this->record->update(['status' => 'provisioning', 'failure_reason' => null]);
            $this->record->refresh();
            Notification::make()->title('Modules saved and redeploy triggered.')->success()->send();
        } else {
            Notification::make()
                ->title('Modules saved in DB but redeploy failed.')
                ->body('Trigger Fix & Redeploy manually to apply changes.')
                ->warning()
                ->send();
        }
    }
}
