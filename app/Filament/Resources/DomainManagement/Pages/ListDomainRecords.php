<?php

namespace App\Filament\Resources\DomainManagement\Pages;

use App\Filament\Resources\DomainManagement\DomainManagerResource;
use App\Models\Domain;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListDomainRecords extends ListRecords
{
    protected static string $resource = DomainManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add_domains')
                ->label('Add Domains')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalHeading('Add Domains to DNS Manager')
                ->modalDescription('Paste your domain names below, one per line. Any domains already in the list will be skipped.')
                ->form([
                    Textarea::make('domains')
                        ->label('Domain names (one per line)')
                        ->placeholder("valexhub.com\nexample.com\nclient.ng")
                        ->rows(8)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $lines = preg_split('/\r?\n/', trim($data['domains']));
                    $added = 0;

                    foreach ($lines as $line) {
                        $domain = strtolower(trim($line));

                        if (! $domain || ! str_contains($domain, '.')) {
                            continue;
                        }

                        $record = Domain::firstOrCreate(
                            ['domain' => $domain],
                            ['owner' => 'us', 'registrar' => 'go54', 'dns_host' => 'unknown', 'managed' => false],
                        );

                        if ($record->wasRecentlyCreated) {
                            $added++;
                        }
                    }

                    Notification::make()
                        ->title("{$added} domain(s) added.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
