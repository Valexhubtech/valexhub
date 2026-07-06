<?php

namespace App\Filament\Resources\AffiliateCommissions;

use App\Filament\Resources\AffiliateCommissions\Pages\EditAffiliateCommission;
use App\Filament\Resources\AffiliateCommissions\Pages\ListAffiliateCommissions;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Wave\AffiliateCommission;

class AffiliateCommissionResource extends Resource
{
    protected static ?string $model = AffiliateCommission::class;

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-hand-coins-duotone';

    protected static ?string $navigationLabel = 'Affiliate Commissions';

    protected static string|\UnitEnum|null $navigationGroup = 'Users & Affiliates';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->options([
                        'accrued' => 'Accrued (pending claim)',
                        'claimed' => 'Claimed',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('affiliate.name')
                    ->label('Affiliate')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('referredUser.name')
                    ->label('Referred Client')
                    ->searchable(),

                TextColumn::make('billing_month_number')
                    ->label('Month #')
                    ->sortable(),

                TextColumn::make('commission_rate')
                    ->label('Rate')
                    ->formatStateUsing(fn ($state) => number_format($state, 0).'%'),

                TextColumn::make('commission_amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => '₦'.number_format($state, 2))
                    ->sortable(),

                TextColumn::make('plan_monthly_price')
                    ->label('Plan Price')
                    ->formatStateUsing(fn ($state) => '₦'.number_format($state, 2))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'claimed' ? 'success' : 'warning')
                    ->sortable(),

                TextColumn::make('accrued_at')
                    ->label('Accrued')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'accrued' => 'Accrued',
                        'claimed' => 'Claimed',
                    ]),
            ])
            ->recordActions([EditAction::make()])
            ->defaultSort('accrued_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAffiliateCommissions::route('/'),
            'edit' => EditAffiliateCommission::route('/{record}/edit'),
        ];
    }
}
