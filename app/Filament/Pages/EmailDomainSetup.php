<?php

namespace App\Filament\Pages;

use App\Services\Domain\PlatformDomainProvisioner;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class EmailDomainSetup extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Email & Domains';
    protected static ?string $navigationLabel = 'Platform Email Setup';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.email-domain-setup';

    public ?string $provisionStatus = null;
    public array $provisionedRecords = [];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('provision')
                ->label('Provision Platform Domain')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Provision ' . config('services.plume.mail_domain', 'notif.*'))
                ->modalDescription('This registers the platform domain in Plume and pushes DNS records to deSEC. Safe to re-run — idempotent.')
                ->action(function () {
                    try {
                        $result = app(PlatformDomainProvisioner::class)->provision();
                        $this->provisionStatus    = $result['status'];
                        $this->provisionedRecords = $result['records'];

                        Notification::make()
                            ->title('Done: ' . $result['status'])
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Failed: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('reverify')
                ->label('Re-check Verification')
                ->color('gray')
                ->action(function () {
                    $result = app(PlatformDomainProvisioner::class)->reverify();
                    $this->provisionStatus = $result['status'];

                    Notification::make()
                        ->title($result['status'] === 'verified' ? '✓ Verified' : 'Not yet verified — DNS may still be propagating')
                        ->color($result['status'] === 'verified' ? 'success' : 'warning')
                        ->send();
                }),
        ];
    }
}
