<?php

namespace App\Filament\Resources\PayoutRequests;

use App\Filament\Resources\PayoutRequests\Pages\ListPayoutRequests;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Wave\AffiliateCommission;
use Wave\PayoutRequest;

class PayoutRequestResource extends Resource
{
    protected static ?string $model = PayoutRequest::class;

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-money-wavy-duotone';

    protected static ?string $navigationLabel = 'Payout Requests';

    protected static string|\UnitEnum|null $navigationGroup = 'Users & Affiliates';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('affiliate.name')
                    ->label('Affiliate')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PayoutRequest $r) => $r->affiliate?->email ?? ''),

                TextColumn::make('amount')
                    ->label('Amount Requested')
                    ->formatStateUsing(fn ($state) => '₦'.number_format($state, 2))
                    ->sortable(),

                TextColumn::make('bank_name')
                    ->label('Bank')
                    ->searchable(),

                TextColumn::make('account_name')
                    ->label('Account Name'),

                TextColumn::make('account_number')
                    ->label('Account Number')
                    ->copyable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->placeholder('—')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('processed_at')
                    ->label('Processed')
                    ->dateTime('d M Y')
                    ->placeholder('Pending')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Mark Paid')
                    ->icon('phosphor-check-circle-duotone')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Payout')
                    ->modalDescription('This marks the payout as paid and clears all accrued commissions for this affiliate.')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Payment notes (optional)')
                            ->placeholder('e.g. Transfer ref: TRF-12345')
                            ->nullable(),
                    ])
                    ->action(function (PayoutRequest $record, array $data): void {
                        $record->update([
                            'status' => 'paid',
                            'notes' => $data['notes'] ?? null,
                            'processed_at' => now(),
                        ]);

                        // Mark all accrued commissions for this affiliate as claimed
                        AffiliateCommission::where('affiliate_id', $record->affiliate_id)
                            ->where('status', 'accrued')
                            ->update(['status' => 'claimed']);
                    })
                    ->successNotificationTitle('Payout approved and commissions marked claimed.')
                    ->visible(fn (PayoutRequest $r): bool => $r->status === 'pending'),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('phosphor-x-circle-duotone')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Payout Request')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Reason for rejection')
                            ->required(),
                    ])
                    ->action(function (PayoutRequest $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'notes' => $data['notes'],
                            'processed_at' => now(),
                        ]);
                    })
                    ->successNotificationTitle('Payout request rejected.')
                    ->visible(fn (PayoutRequest $r): bool => $r->status === 'pending'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayoutRequests::route('/'),
        ];
    }
}
