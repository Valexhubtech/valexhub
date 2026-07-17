<?php

namespace App\Filament\Resources\SupportTickets;

use App\Filament\Resources\SupportTickets\Pages\EditSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\ListSupportTickets;
use App\Filament\Resources\SupportTickets\Pages\ViewSupportTicket;
use App\Mail\SupportReplyMail;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Wave\SupportMessage;
use Wave\SupportTicket;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-chat-circle-dots-duotone';

    protected static ?string $navigationLabel = 'Support Tickets';

    protected static string|\UnitEnum|null $navigationGroup = 'Deployments & Services';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ticket Details')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options([
                            'open' => 'Open',
                            'in_progress' => 'In Progress',
                            'resolved' => 'Resolved',
                            'closed' => 'Closed',
                        ]),

                    Select::make('priority')
                        ->options([
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                            'urgent' => 'Urgent',
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Client')
                    ->description(fn ($record) => $record->user?->email ?? '')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('deployment.product.name')
                    ->label('Product')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'in_progress' => 'warning',
                        'resolved' => 'info',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('messages_count')
                    ->label('Messages')
                    ->counts('messages')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Opened')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ]),
                SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),
            ])
            ->recordActions([
                Action::make('reply')
                    ->label('Reply')
                    ->icon('phosphor-paper-plane-duotone')
                    ->color('success')
                    ->form([
                        Textarea::make('body')
                            ->label('Reply Message')
                            ->required()
                            ->rows(4),
                        Select::make('status')
                            ->label('Update Status To')
                            ->options([
                                'open' => 'Open',
                                'in_progress' => 'In Progress',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])
                            ->nullable(),
                    ])
                    ->action(function (SupportTicket $record, array $data): void {
                        $message = SupportMessage::create([
                            'ticket_id' => $record->id,
                            'user_id' => auth()->id(),
                            'body' => $data['body'],
                            'is_admin' => true,
                        ]);

                        if ($data['status']) {
                            $record->update(['status' => $data['status']]);
                        }

                        Mail::to($record->user->email)->queue(new SupportReplyMail($record, $message));
                    })
                    ->successNotificationTitle('Reply sent and client notified.')
                    ->visible(fn (SupportTicket $record): bool => $record->isOpen()),

                EditAction::make()->label('Edit Status'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupportTickets::route('/'),
            'view' => ViewSupportTicket::route('/{record}'),
            'edit' => EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
