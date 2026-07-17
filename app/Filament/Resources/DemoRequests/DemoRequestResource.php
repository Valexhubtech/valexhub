<?php

namespace App\Filament\Resources\DemoRequests;

use App\Filament\Resources\DemoRequests\Pages\CreateDemoRequest;
use App\Filament\Resources\DemoRequests\Pages\EditDemoRequest;
use App\Filament\Resources\DemoRequests\Pages\ListDemoRequests;
use App\Mail\DemoRequestNotification;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Wave\DemoRequest;
use Wave\Product;

class DemoRequestResource extends Resource
{
    protected static ?string $model = DemoRequest::class;

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-chats-circle-duotone';

    protected static ?string $navigationLabel = 'Demo Requests';

    protected static ?string $pluralModelLabel = 'Demo Requests';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance & Billing';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(20),

                TextInput::make('referral_code')
                    ->maxLength(100),

                Select::make('product_id')
                    ->label('Product')
                    ->options(Product::pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('request_type')
                    ->options([
                        'quote' => 'Quote',
                        'demo' => 'Demo',
                    ])
                    ->required()
                    ->default('quote'),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'contacted' => 'Contacted',
                        'quoted' => 'Quoted',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('request_type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'quote',
                        'success' => 'demo',
                    ]),

                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'contacted',
                        'info' => 'quoted',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('referral_code')
                    ->label('Referral')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Requested At'),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Product')
                    ->options(Product::pluck('name', 'id')),

                SelectFilter::make('request_type')
                    ->label('Type')
                    ->options([
                        'quote' => 'Quote',
                        'demo' => 'Demo',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'contacted' => 'Contacted',
                        'quoted' => 'Quoted',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('resend_notification')
                    ->label('Resend Reminder')
                    ->icon('phosphor-paper-plane-duotone')
                    ->color('warning')
                    ->action(function (DemoRequest $record) {
                        Mail::to(config('mail.from.address'))->send(new DemoRequestNotification($record));
                    })
                    ->successNotificationTitle('Reminder Sent!')
                    ->requiresConfirmation()
                    ->modalHeading('Resend Demo Request Reminder')
                    ->modalDescription('This will send another notification email about this demo request.')
                    ->modalSubmitActionLabel('Send Reminder'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDemoRequests::route('/'),
            'create' => CreateDemoRequest::route('/create'),
            'edit' => EditDemoRequest::route('/{record}/edit'),
        ];
    }
}
