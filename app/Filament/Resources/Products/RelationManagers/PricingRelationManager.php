<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PricingRelationManager extends RelationManager
{
    protected static string $relationship = 'pricingOptions';

    protected static ?string $title = 'Pricing Tiers';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('deployment_type')
                ->options([
                    'cloud'  => 'Cloud',
                    'onprem' => 'On-Premises',
                    'both'   => 'Both',
                ])
                ->required()
                ->default('both'),

            Select::make('price_type')
                ->options([
                    'setup'     => 'Setup Fee (one-time)',
                    'license'   => 'License Fee (on-prem, one-time)',
                    'monthly'   => 'Monthly Subscription',
                    'quarterly' => 'Quarterly Subscription',
                    'yearly'    => 'Yearly Subscription',
                    'onetime'   => 'One-time Purchase',
                ])
                ->required()
                ->live(),

            TextInput::make('amount')
                ->numeric()
                ->prefix('₦')
                ->required(),

            TextInput::make('paystack_plan_code')
                ->label('Paystack Plan Code')
                ->placeholder('PLN_xxxxxxxxxx')
                ->helperText('Required for monthly/quarterly/yearly tiers — create the plan in your Paystack dashboard first.')
                ->nullable(),

            Toggle::make('is_required')
                ->label('Required at checkout')
                ->helperText('Use for mandatory setup/license fees that cannot be skipped.')
                ->default(false),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),

            TextInput::make('sort_order')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('deployment_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cloud'  => 'info',
                        'onprem' => 'warning',
                        default  => 'gray',
                    }),

                TextColumn::make('price_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                TextColumn::make('amount')
                    ->formatStateUsing(fn ($state) => '₦'.number_format($state, 2))
                    ->sortable(),

                TextColumn::make('paystack_plan_code')
                    ->label('Plan Code')
                    ->placeholder('—'),

                IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('sort_order');
    }
}
