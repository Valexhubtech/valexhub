<?php

namespace App\Filament\Resources\Deployments;

use App\Filament\Resources\Deployments\Pages\EditDeployment;
use App\Filament\Resources\Deployments\Pages\ListDeployments;
use App\Mail\InvoiceMail;
use App\Services\InvoiceService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Wave\Deployment;
use Wave\Invoice;

class DeploymentResource extends Resource
{
    protected static ?string $model = Deployment::class;

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-rocket-launch-duotone';

    protected static ?string $navigationLabel = 'Deployments';

    protected static string|\UnitEnum|null $navigationGroup = 'Deployments & Services';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client & Order')
                    ->columns(2)
                    ->schema([
                        TextInput::make('ordered_by')
                            ->label('Ordered By')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($record) =>
                                ($record->user->name ?? '—').' ('.$record->user->email.')'),

                        TextInput::make('deploy_for_display')
                            ->label('Deploying For')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($record) => $record->deploy_for === 'client'
                                ? 'Client — '.$record->client_name.' ('.$record->client_email.')'
                                : 'Self (same account)'),

                        TextInput::make('product_name')
                            ->label('Product')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($record) => $record->product->name ?? '—'),

                        TextInput::make('userProduct.deployment_type')
                            ->label('Deployment Type')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => $state === 'onprem' ? 'On-Premises' : 'Cloud'),
                    ]),

                Section::make('Order Summary')
                    ->schema([
                        Html::make(function ($record) {
                            $up = $record->userProduct;
                            if (! $up) {
                                return new HtmlString('<p class="text-sm text-gray-400">No purchase record linked.</p>');
                            }

                            $up->load(['pricing', 'orderAddons.addon']);

                            $rows = '';

                            if ((float) $up->setup_amount > 0) {
                                $label = $up->deployment_type === 'onprem' ? 'License & Setup' : 'Setup Fee';
                                $rows .= self::summaryRow($label, $up->setup_amount);
                            }

                            if ($up->pricing) {
                                $rows .= self::summaryRow($up->pricing->label.' (recurring)', $up->pricing->amount);
                            }

                            foreach ($up->orderAddons as $oa) {
                                $name = $oa->addon?->name ?? 'Addon';
                                if ($oa->price_type === 'recurring') {
                                    $name .= ' ('.$oa->addon?->billing_cycle.')';
                                }
                                $rows .= self::summaryRow($name, $oa->amount_paid);
                            }

                            $total = number_format((float) $up->amount_paid, 2);
                            $renewal = $up->next_renewal_date
                                ? '<tr><td class="text-gray-500 pr-4 pt-2 text-xs">Next renewal</td><td class="font-medium pt-2 text-xs">'.$up->next_renewal_date->format('d M Y').'</td></tr>'
                                : '<tr><td class="text-gray-500 pr-4 pt-2 text-xs">License</td><td class="font-medium pt-2 text-xs">Perpetual</td></tr>';

                            return new HtmlString('
                                <table class="w-full text-sm">
                                    <tbody>'.$rows.'</tbody>
                                    <tfoot>
                                        <tr class="border-t border-gray-200 dark:border-gray-700">
                                            <td class="text-gray-900 dark:text-white font-semibold pt-2">Total Paid</td>
                                            <td class="font-bold pt-2 text-gray-900 dark:text-white">₦'.$total.'</td>
                                        </tr>
                                        '.$renewal.'
                                    </tfoot>
                                </table>
                            ');
                        }),
                    ]),

                Section::make('Deployment')
                    ->columns(2)
                    ->schema([
                        TextInput::make('deployment_url')
                            ->label('App URL')
                            ->url(),

                        Select::make('status')
                            ->options([
                                'pending'      => 'Pending',
                                'provisioning' => 'Provisioning',
                                'active'       => 'Active',
                                'failed'       => 'Failed',
                                'suspended'    => 'Suspended',
                                'terminated'   => 'Terminated',
                            ]),

                        Select::make('domain_option')
                            ->options([
                                'self_managed' => 'Self-managed',
                                'requested'    => 'Requested from team',
                            ])
                            ->nullable(),

                        Textarea::make('failure_reason')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Ordered By')
                    ->description(fn ($record) => $record->user->email ?? '')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->description(fn ($record) => $record->userProduct?->deployment_type === 'onprem' ? 'On-Premises' : 'Cloud')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('deploy_for')
                    ->label('For')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'client' ? 'warning' : 'gray')
                    ->formatStateUsing(fn ($state): string => $state === 'client' ? 'Client' : 'Self'),

                TextColumn::make('client_name')
                    ->label('Client')
                    ->description(fn ($record) => $record->client_email ?? '')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('userProduct.amount_paid')
                    ->label('Paid')
                    ->formatStateUsing(fn ($state) => $state ? '₦'.number_format($state, 2) : '—')
                    ->sortable(),

                TextColumn::make('userProduct.next_renewal_date')
                    ->label('Renews')
                    ->date('d M Y')
                    ->placeholder('Perpetual')
                    ->color(fn ($record) => $record->userProduct?->next_renewal_date?->isPast() ? 'danger' : null)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'       => 'success',
                        'provisioning' => 'warning',
                        'failed'       => 'danger',
                        'suspended'    => 'warning',
                        'terminated'   => 'danger',
                        default        => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('deployed_at')
                    ->label('Live Since')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Ordered')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'      => 'Pending',
                        'provisioning' => 'Provisioning',
                        'active'       => 'Active',
                        'failed'       => 'Failed',
                        'suspended'    => 'Suspended',
                        'terminated'   => 'Terminated',
                    ]),

                SelectFilter::make('deploy_for')
                    ->label('Deployed For')
                    ->options([
                        'self'   => 'Self',
                        'client' => 'Client',
                    ]),
            ])
            ->recordActions([
                Action::make('suspend')
                    ->label('Suspend')
                    ->icon('phosphor-pause-circle-duotone')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Suspend Deployment')
                    ->modalDescription('This will suspend the deployment and the client\'s service. They will no longer be able to access the app.')
                    ->modalSubmitActionLabel('Yes, Suspend')
                    ->action(function (Deployment $record): void {
                        $record->update(['status' => 'suspended']);
                        $record->userProduct?->update(['status' => 'suspended']);
                    })
                    ->successNotificationTitle('Deployment suspended.')
                    ->visible(fn (Deployment $record): bool => $record->status === 'active'),

                Action::make('reactivate')
                    ->label('Reactivate')
                    ->icon('phosphor-play-circle-duotone')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Reactivate Deployment')
                    ->modalDescription('This will mark the deployment and user product as active again.')
                    ->modalSubmitActionLabel('Yes, Reactivate')
                    ->action(function (Deployment $record): void {
                        $record->update(['status' => 'active']);
                        $record->userProduct?->update(['status' => 'active']);
                    })
                    ->successNotificationTitle('Deployment reactivated.')
                    ->visible(fn (Deployment $record): bool => $record->status === 'suspended'),

                Action::make('terminate')
                    ->label('Terminate')
                    ->icon('phosphor-prohibit-duotone')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Terminate Deployment')
                    ->modalDescription('This permanently terminates the deployment. The client will lose access. This cannot be undone without a manual restore.')
                    ->modalSubmitActionLabel('Yes, Terminate Permanently')
                    ->action(function (Deployment $record): void {
                        $record->update(['status' => 'terminated']);
                        $record->userProduct?->update(['status' => 'cancelled']);
                    })
                    ->successNotificationTitle('Deployment terminated.')
                    ->visible(fn (Deployment $record): bool => ! in_array($record->status, ['terminated', 'pending'])),

                Action::make('send_invoice')
                    ->label('Send Invoice')
                    ->icon('phosphor-paper-plane-duotone')
                    ->color('info')
                    ->modalHeading('Create & Send Invoice')
                    ->modalDescription('Review the line items pre-filled from this deployment, adjust if needed, then send.')
                    ->schema(function (Deployment $record, InvoiceService $invoiceService): array {
                        $lineItems = $record->userProduct
                            ? $invoiceService->buildLineItems(
                                $record->userProduct->load(['pricing', 'orderAddons.addon'])
                            )
                            : [];
                        return [
                            \Filament\Schemas\Components\Section::make('Line Items')
                                ->schema([
                                    \Filament\Forms\Components\Repeater::make('line_items')
                                        ->label('')
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
                                                ->options(['onetime' => 'One-time', 'recurring' => 'Recurring'])
                                                ->default('onetime')
                                                ->required(),
                                        ])
                                        ->columns(4)
                                        ->default($lineItems)
                                        ->minItems(1)
                                        ->required(),
                                ]),
                            DatePicker::make('due_date')
                                ->label('Due Date (optional)')
                                ->nullable(),
                        ];
                    })
                    ->action(function (Deployment $record, array $data, InvoiceService $invoiceService): void {
                        $lineItems = $data['line_items'];
                        $amount    = collect($lineItems)->sum('amount');

                        $invoice = Invoice::create([
                            'user_id'         => $record->user_id,
                            'user_product_id' => $record->user_product_id,
                            'deployment_id'   => $record->id,
                            'amount'          => $amount,
                            'currency'        => 'NGN',
                            'status'          => 'sent',
                            'line_items'      => $lineItems,
                            'due_date'        => $data['due_date'] ?? null,
                        ]);

                        $invoiceService->generatePdf($invoice);
                        Mail::to($record->user->email)->queue(new InvoiceMail($invoice));
                    })
                    ->successNotificationTitle('Invoice created and sent to client.')
                    ->visible(fn (Deployment $record): bool => $record->user_id !== null),

                Action::make('view_invoices')
                    ->label('Invoices')
                    ->icon('phosphor-receipt-duotone')
                    ->color('gray')
                    ->url(fn (Deployment $record): string => '/admin/invoices')
                    ->openUrlInNewTab(false),

                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeployments::route('/'),
            'edit'  => EditDeployment::route('/{record}/edit'),
        ];
    }

    private static function summaryRow(string $label, mixed $amount): string
    {
        return '<tr>
            <td class="text-gray-500 dark:text-gray-400 pr-8 py-0.5">'.$label.'</td>
            <td class="font-medium text-gray-900 dark:text-white py-0.5">₦'.number_format((float) $amount, 2).'</td>
        </tr>';
    }
}
