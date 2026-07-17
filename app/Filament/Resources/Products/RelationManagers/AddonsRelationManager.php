<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AddonsRelationManager extends RelationManager
{
    protected static string $relationship = 'addons';

    protected static ?string $title = 'Add-ons';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->rows(2)
                ->nullable(),

            TextInput::make('price')
                ->numeric()
                ->prefix('₦')
                ->required(),

            Select::make('price_type')
                ->options([
                    'onetime' => 'One-time',
                    'recurring' => 'Recurring',
                ])
                ->required()
                ->live()
                ->default('onetime'),

            Select::make('billing_cycle')
                ->options([
                    'monthly' => 'Monthly',
                    'quarterly' => 'Quarterly',
                    'yearly' => 'Yearly',
                ])
                ->nullable()
                ->helperText('Only for recurring add-ons.'),

            TextInput::make('paystack_plan_code')
                ->label('Paystack Plan Code')
                ->placeholder('PLN_xxxxxxxxxx')
                ->helperText('Required for recurring add-ons.')
                ->nullable(),

            Select::make('deployment_type')
                ->options([
                    'cloud' => 'Cloud only',
                    'onprem' => 'On-Premises only',
                    'both' => 'Both',
                ])
                ->required()
                ->default('both'),

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
                TextColumn::make('name')->searchable(),

                TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => '₦'.number_format($state, 2)),

                TextColumn::make('price_type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'recurring' ? 'warning' : 'gray'),

                TextColumn::make('billing_cycle')
                    ->placeholder('—'),

                TextColumn::make('deployment_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cloud' => 'info',
                        'onprem' => 'warning',
                        default => 'gray',
                    }),

                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('sort_order');
    }
}
