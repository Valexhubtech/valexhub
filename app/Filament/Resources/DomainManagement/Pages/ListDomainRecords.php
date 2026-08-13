<?php

namespace App\Filament\Resources\DomainManagement\Pages;

use App\Filament\Resources\DomainManagement\DomainManagerResource;
use App\Models\Domain;
use App\Services\Go54\Go54RegistrarProvider;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListDomainRecords extends ListRecords
{
    protected static string $resource = DomainManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_go54')
                ->label('Sync from GO54')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Sync GO54 Domains')
                ->modalDescription('This will pull all domains on your GO54 account and add any missing ones to the DNS Manager.')
                ->action(function (): void {
                    try {
                        $go54 = app(Go54RegistrarProvider::class);
                        $response = $go54->listDomains();

                        $domains = data_get($response, 'domains', $response['data'] ?? []);

                        if (empty($domains)) {
                            Notification::make()
                                ->title('No domains returned from GO54.')
                                ->warning()
                                ->send();

                            return;
                        }

                        $synced = 0;

                        foreach ($domains as $item) {
                            $domainName = is_string($item) ? $item : ($item['domain'] ?? $item['name'] ?? null);

                            if (! $domainName) {
                                continue;
                            }

                            $created = Domain::firstOrCreate(
                                ['domain' => strtolower(trim($domainName))],
                                ['owner' => 'us', 'registrar' => 'go54', 'dns_host' => 'unknown', 'managed' => false],
                            );

                            if ($created->wasRecentlyCreated) {
                                $synced++;
                            }
                        }

                        Notification::make()
                            ->title("Sync complete — {$synced} new domain(s) added.")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Sync failed: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
