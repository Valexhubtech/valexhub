<?php

namespace App\Filament\Resources\Invoices;

use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Mail\InvoiceMail;
use App\Models\User;
use App\Services\InvoiceService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Wave\Deployment;
use Wave\Invoice;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-receipt-duotone';

    protected static ?string $navigationLabel = 'Invoices';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance & Billing';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('create_manual')
                    ->label('Create Invoice')
                    ->icon('phosphor-plus-duotone')
                    ->color('primary')
                    ->schema([
                        Select::make('user_id')
                            ->label('Client')
                            ->options(
                                fn () => User::orderBy('name')->get()
                                    ->mapWithKeys(fn ($u) => [$u->id => ($u->name ?: $u->email).' — '.$u->email])
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('deployment_id', null);
                                $set('user_product_id', null);
                                $set('line_items', []);
                            }),

                        Select::make('deployment_id')
                            ->label('Deployment (auto-fills line items)')
                            ->placeholder('Select a deployment to pre-fill line items…')
                            ->options(function (Get $get): array {
                                $userId = $get('user_id');
                                if (! $userId) {
                                    return [];
                                }

                                return Deployment::where('user_id', $userId)
                                    ->with('product')
                                    ->get()
                                    ->mapWithKeys(fn (Deployment $d) => [
                                        $d->id => ($d->product?->name ?? 'Unknown Product')
                                            .' — '.ucfirst($d->status)
                                            .($d->client_name ? ' (for '.$d->client_name.')' : ''),
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state, InvoiceService $invoiceService): void {
                                if (! $state) {
                                    return;
                                }
                                $deployment = Deployment::with([
                                    'userProduct.pricing',
                                    'userProduct.orderAddons.addon',
                                ])->find($state);

                                if (! $deployment?->userProduct) {
                                    return;
                                }

                                $set('user_product_id', $deployment->user_product_id);
                                $set('line_items', $invoiceService->buildLineItems($deployment->userProduct));
                            }),

                        Hidden::make('user_product_id'),

                        Repeater::make('line_items')
                            ->label('Line Items')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Description')
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('amount')
                                    ->label('Amount (₦)')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0),
                                Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'onetime' => 'One-time',
                                        'recurring' => 'Recurring',
                                    ])
                                    ->default('onetime')
                                    ->required(),
                            ])
                            ->columns(4)
                            ->minItems(1)
                            ->required(),

                        DatePicker::make('due_date')
                            ->label('Due Date (optional)')
                            ->nullable(),
                    ])
                    ->action(function (array $data, InvoiceService $invoiceService): void {
                        $lineItems = $data['line_items'];
                        $amount = collect($lineItems)->sum('amount');

                        $invoice = Invoice::create([
                            'user_id' => $data['user_id'],
                            'user_product_id' => $data['user_product_id'] ?? null,
                            'deployment_id' => $data['deployment_id'] ?? null,
                            'amount' => $amount,
                            'currency' => 'NGN',
                            'status' => 'draft',
                            'line_items' => $lineItems,
                            'due_date' => $data['due_date'] ?? null,
                        ]);

                        $invoiceService->generatePdf($invoice);
                    })
                    ->successNotificationTitle('Invoice created.'),
            ])
            ->columns([
                TextColumn::make('id')
                    ->label('Invoice #')
                    ->formatStateUsing(fn ($state) => 'INV-'.str_pad($state, 6, '0', STR_PAD_LEFT))
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Client')
                    ->description(fn ($record) => $record->user->email ?? '')
                    ->searchable(['users.name', 'users.email'])
                    ->sortable(),

                TextColumn::make('userProduct.product.name')
                    ->label('Product')
                    ->placeholder('—'),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => '₦'.number_format($state, 2))
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'sent' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Due')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() && $record->status !== 'paid' ? 'danger' : null)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('paid_at')
                    ->label('Paid On')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('paystack_reference')
                    ->label('Reference')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'paid' => 'Paid',
                    ]),

                Filter::make('client')
                    ->label('Search by Client')
                    ->schema([
                        TextInput::make('client_search')->label('Name or Email')->placeholder('Search client…'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['client_search'],
                            fn ($q, $search) => $q->whereHas(
                                'user',
                                fn ($u) => $u->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                            )
                        );
                    }),

                Filter::make('overdue')
                    ->label('Overdue Only')
                    ->query(
                        fn (Builder $q) => $q->where('status', '!=', 'paid')
                            ->whereNotNull('due_date')
                            ->where('due_date', '<', now())
                    ),
            ])
            ->recordActions([
                Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('phosphor-file-pdf-duotone')
                    ->color('gray')
                    ->action(function (Invoice $record, InvoiceService $invoiceService) {
                        if (! $record->pdf_path || ! Storage::exists($record->pdf_path)) {
                            $invoiceService->generatePdf($record);
                            $record->refresh();
                        }

                        return Storage::download($record->pdf_path, $record->invoiceNumber().'.pdf');
                    }),

                Action::make('resend')
                    ->label('Resend')
                    ->icon('phosphor-paper-plane-duotone')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Resend Invoice Email')
                    ->modalDescription('This will re-send the invoice with PDF to the client.')
                    ->modalSubmitActionLabel('Send')
                    ->action(function (Invoice $record, InvoiceService $invoiceService): void {
                        if (! $record->pdf_path || ! Storage::exists($record->pdf_path)) {
                            $invoiceService->generatePdf($record);
                            $record->refresh();
                        }
                        Mail::to($record->user->email)->queue(new InvoiceMail($record));
                        $record->update(['status' => 'sent']);
                    })
                    ->successNotificationTitle('Invoice email queued.'),

                Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('phosphor-check-circle-duotone')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Invoice $record) => $record->update(['status' => 'paid', 'paid_at' => now()]))
                    ->successNotificationTitle('Marked as paid.')
                    ->visible(fn (Invoice $record): bool => $record->status !== 'paid'),

                Action::make('split')
                    ->label('Split')
                    ->icon('phosphor-scissors-duotone')
                    ->color('info')
                    ->modalHeading('Split Invoice')
                    ->modalDescription('Select the line items to move into a new separate invoice. The original invoice will keep the remaining items.')
                    ->schema(fn (Invoice $record): array => [
                        CheckboxList::make('items_to_split')
                            ->label('Items to move to new invoice')
                            ->options(
                                collect($record->line_items ?? [])
                                    ->mapWithKeys(fn ($item, $i) => [
                                        $i => $item['label'].' — ₦'.number_format($item['amount'], 2),
                                    ])
                                    ->all()
                            )
                            ->required(),
                    ])
                    ->action(function (Invoice $record, array $data, InvoiceService $invoiceService): void {
                        $allItems = $record->line_items ?? [];
                        $splitIndexes = array_map('intval', $data['items_to_split']);

                        $splitItems = [];
                        $remainingItems = [];

                        foreach ($allItems as $i => $item) {
                            if (in_array($i, $splitIndexes)) {
                                $splitItems[] = $item;
                            } else {
                                $remainingItems[] = $item;
                            }
                        }

                        if (empty($splitItems) || empty($remainingItems)) {
                            return; // Can't split all or none
                        }

                        // Update original invoice
                        $record->update([
                            'amount' => collect($remainingItems)->sum('amount'),
                            'line_items' => array_values($remainingItems),
                            'pdf_path' => null,
                            'status' => 'draft',
                        ]);

                        // Create new split invoice
                        $newInvoice = Invoice::create([
                            'user_id' => $record->user_id,
                            'user_product_id' => $record->user_product_id,
                            'deployment_id' => $record->deployment_id,
                            'amount' => collect($splitItems)->sum('amount'),
                            'currency' => $record->currency,
                            'status' => 'draft',
                            'line_items' => array_values($splitItems),
                            'due_date' => $record->due_date,
                        ]);

                        $invoiceService->generatePdf($record);
                        $invoiceService->generatePdf($newInvoice);
                    })
                    ->successNotificationTitle('Invoice split. Two draft invoices created.')
                    ->visible(fn (Invoice $record): bool => count($record->line_items ?? []) > 1),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('merge')
                        ->label('Merge Selected')
                        ->icon('phosphor-git-merge-duotone')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Merge Invoices')
                        ->modalDescription('This will combine all selected invoices into one new invoice. The originals will be deleted.')
                        ->modalSubmitActionLabel('Merge')
                        ->action(function (Collection $records, InvoiceService $invoiceService): void {
                            if ($records->count() < 2) {
                                return;
                            }

                            $first = $records->first();
                            $allItems = [];

                            foreach ($records as $inv) {
                                foreach ($inv->line_items ?? [] as $item) {
                                    $allItems[] = $item;
                                }
                            }

                            $merged = Invoice::create([
                                'user_id' => $first->user_id,
                                'user_product_id' => $first->user_product_id,
                                'deployment_id' => $first->deployment_id,
                                'amount' => collect($allItems)->sum('amount'),
                                'currency' => $first->currency,
                                'status' => 'draft',
                                'line_items' => $allItems,
                                'due_date' => $records->max('due_date'),
                            ]);

                            $invoiceService->generatePdf($merged);

                            // Remove originals
                            $records->each->delete();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Invoices merged into one draft.'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
        ];
    }
}
