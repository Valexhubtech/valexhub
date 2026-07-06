<?php

namespace App\Filament\Resources\DomainPurchases;

use App\Filament\Resources\DomainPurchases\Pages\ListDomainPurchases;
use App\Filament\Resources\DomainPurchases\Pages\ViewDomainPurchase;
use App\Jobs\RegisterDomainJob;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Wave\DomainPurchase;

class DomainPurchaseResource extends Resource
{
    protected static ?string $model = DomainPurchase::class;

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-globe-hemisphere-west-duotone';

    protected static ?string $navigationLabel = 'Domain Purchases';

    protected static string|\UnitEnum|null $navigationGroup = 'Deployments & Services';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Domain Details')->schema([
                \Filament\Forms\Components\TextInput::make('domain')->disabled(),
                \Filament\Forms\Components\TextInput::make('tld')->label('TLD')->disabled(),
                \Filament\Forms\Components\TextInput::make('hostinger_item_id')->label('Hostinger Item ID')->disabled(),
            ])->columns(3),

            Section::make('Payment')->schema([
                \Filament\Forms\Components\TextInput::make('paystack_reference')->label('Paystack Reference')->disabled(),
                \Filament\Forms\Components\TextInput::make('total_kobo')
                    ->label('Total (₦)')
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2))
                    ->disabled(),
                \Filament\Forms\Components\Select::make('payment_status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'])
                    ->disabled(),
            ])->columns(3),

            Section::make('Registration & DNS')->schema([
                \Filament\Forms\Components\Select::make('registration_status')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'registered' => 'Registered',
                        'failed'     => 'Failed',
                    ])
                    ->required(),
                \Filament\Forms\Components\Select::make('dns_status')
                    ->options([
                        'pending'    => 'Pending',
                        'configured' => 'Configured',
                        'failed'     => 'Failed',
                    ])
                    ->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain')
                    ->label('Domain')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('deployment.product.name')
                    ->label('Product')
                    ->searchable(),

                TextColumn::make('total_kobo')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => '₦' . number_format($state / 100, 2))
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'    => 'success',
                        'pending' => 'warning',
                        default   => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('registration_status')
                    ->label('Registration')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'registered' => 'success',
                        'processing' => 'warning',
                        'pending'    => 'gray',
                        default      => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('dns_status')
                    ->label('DNS')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'configured' => 'success',
                        'pending'    => 'gray',
                        default      => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Purchased')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed']),
                SelectFilter::make('registration_status')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'registered' => 'Registered',
                        'failed'     => 'Failed',
                    ]),
                SelectFilter::make('dns_status')
                    ->options([
                        'pending'    => 'Pending',
                        'configured' => 'Configured',
                        'failed'     => 'Failed',
                    ]),
            ])
            ->recordActions([
                Action::make('retry_registration')
                    ->label('Retry Registration')
                    ->icon('phosphor-arrow-clockwise-duotone')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (DomainPurchase $record): void {
                        $record->update(['registration_status' => 'pending', 'dns_status' => 'pending']);
                        RegisterDomainJob::dispatch($record->id);
                    })
                    ->visible(fn (DomainPurchase $r): bool =>
                        $r->payment_status === 'paid' &&
                        in_array($r->registration_status, ['failed', 'pending'])
                    ),

                Action::make('mark_registered')
                    ->label('Mark Registered')
                    ->icon('phosphor-check-circle-duotone')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (DomainPurchase $r) => $r->update(['registration_status' => 'registered']))
                    ->visible(fn (DomainPurchase $r): bool => $r->registration_status !== 'registered'),

                Action::make('mark_dns_configured')
                    ->label('Mark DNS Live')
                    ->icon('phosphor-globe-duotone')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (DomainPurchase $r) => $r->update(['dns_status' => 'configured']))
                    ->visible(fn (DomainPurchase $r): bool => $r->dns_status !== 'configured'),

                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDomainPurchases::route('/'),
            'view'  => ViewDomainPurchase::route('/{record}'),
        ];
    }
}
