<?php

namespace App\Filament\Resources\DomainPurchases\Pages;

use App\Filament\Resources\DomainPurchases\DomainPurchaseResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Wave\DomainPurchase;

class ListDomainPurchases extends ListRecords
{
    protected static string $resource = DomainPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('link_existing')
                ->label('Link Existing Domain')
                ->icon('heroicon-o-link')
                ->color('primary')
                ->modalHeading('Link an Existing Domain to a Client')
                ->modalDescription('Use this for domains already registered (via GO54 or elsewhere) that need to appear in the purchases list.')
                ->form([
                    TextInput::make('domain')
                        ->label('Domain name')
                        ->placeholder('example.com')
                        ->required(),

                    Select::make('user_id')
                        ->label('Client')
                        ->options(User::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('payment_status')
                        ->label('Payment status')
                        ->options(['paid' => 'Paid', 'pending' => 'Pending'])
                        ->default('paid')
                        ->required(),

                    Select::make('registration_status')
                        ->label('Registration status')
                        ->options(['registered' => 'Registered', 'pending' => 'Pending'])
                        ->default('registered')
                        ->required(),

                    Select::make('dns_status')
                        ->label('DNS status')
                        ->options(['configured' => 'Configured', 'pending' => 'Pending'])
                        ->default('pending')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $tld = implode('.', array_slice(explode('.', $data['domain']), 1));

                    DomainPurchase::create([
                        'user_id'               => $data['user_id'],
                        'domain'                => strtolower(trim($data['domain'])),
                        'tld'                   => $tld,
                        'total_kobo'            => 0,
                        'payment_status'        => $data['payment_status'],
                        'registration_status'   => $data['registration_status'],
                        'dns_status'            => $data['dns_status'],
                        'paid_at'               => $data['payment_status'] === 'paid' ? now() : null,
                    ]);

                    Notification::make()
                        ->title('Domain linked successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
