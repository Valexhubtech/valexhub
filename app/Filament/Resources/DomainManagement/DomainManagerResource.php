<?php

namespace App\Filament\Resources\DomainManagement;

use App\Models\Domain;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DomainManagerResource extends Resource
{
    protected static ?string $model = Domain::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-globe-alt'; }
    public static function getNavigationGroup(): ?string { return 'Deployments & Services'; }
    public static function getNavigationLabel(): string { return 'DNS Manager'; }
    public static function getNavigationSort(): int { return 7; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('domain')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('owner')->badge(),
                Tables\Columns\TextColumn::make('registrar')->badge(),
                Tables\Columns\TextColumn::make('dns_host')->badge(),
                Tables\Columns\IconColumn::make('managed')->boolean(),
            ])
            ->recordActions([
                Action::make('manage')
                    ->label('Manage DNS')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (Domain $record) => static::getUrl('records', ['record' => $record->id])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'   => Pages\ListDomainRecords::route('/'),
            'records' => Pages\ManageDomainRecords::route('/{record}/records'),
        ];
    }
}
