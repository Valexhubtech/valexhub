<?php

namespace App\Filament\Resources\CoolifyServers;

use App\Filament\Resources\CoolifyServers\Pages\CreateCoolifyServer;
use App\Filament\Resources\CoolifyServers\Pages\EditCoolifyServer;
use App\Filament\Resources\CoolifyServers\Pages\ListCoolifyServers;
use App\Services\Coolify\CoolifyServerSelector;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Wave\CoolifyServer;

class CoolifyServerResource extends Resource
{
    protected static ?string $model = CoolifyServer::class;

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-hard-drives-duotone';

    protected static ?string $navigationLabel = 'Coolify Servers';

    protected static string|\UnitEnum|null $navigationGroup = 'Deployments & Services';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Server Details')->schema([
                TextInput::make('name')
                    ->label('Server Name')
                    ->placeholder('e.g. Server 1 — DigitalOcean')
                    ->required()
                    ->maxLength(255),

                TextInput::make('api_url')
                    ->label('Coolify API URL')
                    ->placeholder('https://coolify.yourdomain.com')
                    ->url()
                    ->required(),

                TextInput::make('ip_address')
                    ->label('Server Public IP')
                    ->placeholder('e.g. 165.22.48.101')
                    ->helperText('Required for automatic DNS configuration when clients purchase a domain.'),

                TextInput::make('api_token')
                    ->label('API Token')
                    ->password()
                    ->revealable()
                    ->required()
                    ->helperText('Stored encrypted. Retrieve from Coolify → Settings → API.'),

                TextInput::make('coolify_server_uuid')
                    ->label('Server UUID')
                    ->placeholder('e.g. mweh2i2nb6lno55bpveef1t1')
                    ->helperText('Found in Coolify → Servers → (server name) → UUID.')
                    ->required(),

                TextInput::make('coolify_project_uuid')
                    ->label('Project UUID')
                    ->placeholder('e.g. jzgwwn01j55iyj29cvwlyz9z')
                    ->helperText('Found in Coolify → Projects → (project name) → UUID.')
                    ->required(),

                TextInput::make('coolify_environment_name')
                    ->label('Environment Name')
                    ->placeholder('production')
                    ->default('production')
                    ->required(),
            ])->columns(1),

            Section::make('Capacity & Cost')->schema([
                TextInput::make('max_deployments')
                    ->label('Max Deployments (slots)')
                    ->numeric()
                    ->default(50)
                    ->required()
                    ->minValue(1)
                    ->helperText('How many active apps this server can host before we move to the next one.'),

                TextInput::make('monthly_cost')
                    ->label('Monthly Cost (₦)')
                    ->numeric()
                    ->default(0)
                    ->prefix('₦')
                    ->helperText('Used for profit calculations in the financial dashboard.'),

                Select::make('status')
                    ->options([
                        'active'      => 'Active',
                        'maintenance' => 'Maintenance',
                        'offline'     => 'Offline',
                    ])
                    ->default('active')
                    ->required(),

                TextInput::make('sort_order')
                    ->label('Priority (lower = tried first)')
                    ->numeric()
                    ->default(0),
            ])->columns(2),

            Section::make('Notes')->schema([
                Textarea::make('notes')
                    ->label('Internal Notes')
                    ->placeholder('e.g. DigitalOcean droplet, 4 CPU / 8 GB RAM, created 2026-07')
                    ->nullable()
                    ->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('capacity_overview')
                    ->label(function (): string {
                        $summary = app(CoolifyServerSelector::class)->capacitySummary();
                        return $summary['active_servers'] . ' servers · '
                            . $summary['used_slots'] . '/' . $summary['total_slots'] . ' slots used';
                    })
                    ->icon('phosphor-chart-bar-duotone')
                    ->color(function (): string {
                        $summary = app(CoolifyServerSelector::class)->capacitySummary();
                        return $summary['all_full'] ? 'danger' : 'gray';
                    })
                    ->disabled(),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Server')
                    ->searchable()
                    ->description(fn (CoolifyServer $r) => $r->api_url)
                    ->sortable(),

                TextColumn::make('used_slots')
                    ->label('Deployments')
                    ->getStateUsing(fn (CoolifyServer $r) => $r->used_slots . ' / ' . $r->max_deployments)
                    ->badge()
                    ->color(fn (CoolifyServer $r): string => $r->isFull() ? 'danger' : ($r->used_slots / max(1, $r->max_deployments) > 0.8 ? 'warning' : 'success')),

                TextColumn::make('monthly_cost')
                    ->label('Monthly Cost')
                    ->formatStateUsing(fn ($state) => '₦' . number_format($state, 2))
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'      => 'success',
                        'maintenance' => 'warning',
                        default       => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Priority')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Added')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active'      => 'Active',
                        'maintenance' => 'Maintenance',
                        'offline'     => 'Offline',
                    ]),
            ])
            ->recordActions([
                Action::make('set_maintenance')
                    ->label('Maintenance')
                    ->icon('phosphor-wrench-duotone')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (CoolifyServer $r) => $r->update(['status' => 'maintenance']))
                    ->visible(fn (CoolifyServer $r): bool => $r->status === 'active'),

                Action::make('set_active')
                    ->label('Activate')
                    ->icon('phosphor-check-circle-duotone')
                    ->color('success')
                    ->action(fn (CoolifyServer $r) => $r->update(['status' => 'active']))
                    ->visible(fn (CoolifyServer $r): bool => $r->status !== 'active'),

                EditAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCoolifyServers::route('/'),
            'create' => CreateCoolifyServer::route('/create'),
            'edit'   => EditCoolifyServer::route('/{record}/edit'),
        ];
    }
}
