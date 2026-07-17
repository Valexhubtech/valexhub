<?php

namespace App\Filament\Resources\Deployments;

use App\Filament\Resources\Deployments\Pages\AdminDeployDeployment;
use App\Filament\Resources\Deployments\Pages\EditDeployment;
use App\Filament\Resources\Deployments\Pages\ListDeployments;
use App\Filament\Resources\Deployments\Pages\ManageDeployment;
use App\Jobs\PushSoftwareUpdate;
use App\Mail\InvoiceMail;
use App\Services\Coolify\RealCoolifyDeploymentService;
use App\Services\InvoiceService;
use App\Services\ProductDeploymentService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Wave\Deployment;
use Wave\Invoice;
use Wave\Product;

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
                            ->formatStateUsing(fn ($record) => ($record->user->name ?? '—').' ('.$record->user->email.')'),

                        TextInput::make('business_name')
                            ->label('Business Name')
                            ->disabled(),

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
                                'pending' => 'Pending',
                                'provisioning' => 'Provisioning',
                                'active' => 'Active',
                                'failed' => 'Failed',
                                'suspended' => 'Suspended',
                                'terminated' => 'Terminated',
                            ]),

                        Select::make('domain_option')
                            ->options([
                                'self_managed' => 'Self-managed',
                                'requested' => 'Requested from team',
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

                TextColumn::make('business_name')
                    ->label('Business')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('client_name')
                    ->label('Client Contact')
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
                        'active' => 'success',
                        'provisioning' => 'warning',
                        'failed' => 'danger',
                        'suspended' => 'warning',
                        'terminated' => 'danger',
                        default => 'gray',
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
                SelectFilter::make('product_id')
                    ->label('Product / Software')
                    ->options(fn (): array => Product::orderBy('name')->pluck('name', 'id')->all()),

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'provisioning' => 'Provisioning',
                        'active' => 'Active',
                        'failed' => 'Failed',
                        'suspended' => 'Suspended',
                        'terminated' => 'Terminated',
                    ]),

                SelectFilter::make('deploy_for')
                    ->label('Deployed For')
                    ->options([
                        'self' => 'Self',
                        'client' => 'Client',
                    ]),
            ])
            ->recordActions([
                Action::make('manage')
                    ->label('Manage')
                    ->icon('phosphor-sliders-duotone')
                    ->color('gray')
                    ->url(fn (Deployment $record): string => static::getUrl('manage', ['record' => $record->id])),

                ActionGroup::make([
                    // ── Coolify container controls ────────────────────────────

                    Action::make('coolify_restart')
                        ->label('Restart Container')
                        ->icon('phosphor-arrows-clockwise-duotone')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Restart Container')
                        ->modalDescription('Sends a restart signal to the running container. UUID and domain stay the same.')
                        ->modalSubmitActionLabel('Restart')
                        ->action(function (Deployment $record, RealCoolifyDeploymentService $coolify): void {
                            $ok = $coolify->restartApp($record);
                            if (! $ok) {
                                Notification::make()
                                    ->title('Restart failed')
                                    ->body('Could not reach the Coolify API. Check the logs.')
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->successNotificationTitle('Restart signal sent.')
                        ->visible(fn (Deployment $record): bool => $record->coolify_app_id !== null),

                    Action::make('coolify_fix_redeploy')
                        ->label('Fix & Redeploy')
                        ->icon('phosphor-wrench-duotone')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Fix & Redeploy')
                        ->modalDescription('Re-injects all env vars into the existing Coolify app and triggers a fresh deploy. UUID and domain are preserved.')
                        ->modalSubmitActionLabel('Fix & Redeploy')
                        ->action(function (Deployment $record, RealCoolifyDeploymentService $coolify): void {
                            $ok = $coolify->redeployWithEnvFix($record);
                            if ($ok) {
                                $record->update([
                                    'status' => 'provisioning',
                                    'failure_reason' => null,
                                ]);
                            } else {
                                Notification::make()
                                    ->title('Fix & Redeploy failed')
                                    ->body('Could not reach Coolify. Check the server logs.')
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->successNotificationTitle('Re-injected env vars and triggered fresh deploy.')
                        ->visible(fn (Deployment $record): bool => $record->coolify_app_id !== null),

                    Action::make('coolify_wipe_recreate')
                        ->label('Wipe & Recreate')
                        ->icon('phosphor-trash-duotone')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Wipe & Recreate')
                        ->modalDescription('Deletes the Coolify app (volumes included) and provisions a brand-new container. UUID will change. Use only when the app is unrecoverable.')
                        ->modalSubmitActionLabel('Yes, Wipe & Recreate')
                        ->action(function (Deployment $record, RealCoolifyDeploymentService $coolify, ProductDeploymentService $deploymentService): void {
                            $coolify->deleteApp($record);
                            $record->update([
                                'status' => 'pending',
                                'coolify_app_id' => null,
                                'deployment_url' => null,
                                'failure_reason' => null,
                            ]);
                            $deploymentService->fulfill($record->fresh());
                        })
                        ->successNotificationTitle('Old container wiped. New container is provisioning.')
                        ->visible(fn (Deployment $record): bool => $record->coolify_app_id !== null),

                    // ── Lifecycle ─────────────────────────────────────────────

                    Action::make('suspend')
                        ->label('Suspend')
                        ->icon('phosphor-pause-circle-duotone')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Suspend Deployment')
                        ->modalDescription('The client will lose access to the app immediately.')
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
                        ->modalDescription('Marks the deployment and user product as active again.')
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
                        ->modalDescription('Permanently terminates the deployment. The client will lose access and this cannot be undone without a manual restore.')
                        ->modalSubmitActionLabel('Yes, Terminate Permanently')
                        ->action(function (Deployment $record): void {
                            $record->update(['status' => 'terminated']);
                            $record->userProduct?->update(['status' => 'cancelled']);
                        })
                        ->successNotificationTitle('Deployment terminated.')
                        ->visible(fn (Deployment $record): bool => ! in_array($record->status, ['terminated', 'pending'])),

                    // ── Invoicing ─────────────────────────────────────────────

                    Action::make('send_invoice')
                        ->label('Send Invoice')
                        ->icon('phosphor-paper-plane-duotone')
                        ->color('info')
                        ->modalHeading('Create & Send Invoice')
                        ->modalDescription('Review the pre-filled sections, adjust amounts, then send.')
                        ->schema(fn (Deployment $record): array => static::buildInvoiceFormSchema($record))
                        ->action(function (Deployment $record, array $data): void {
                            static::dispatchInvoice($record, $data);
                        })
                        ->successNotificationTitle('Invoice created and sent to client.')
                        ->visible(fn (Deployment $record): bool => $record->user_id !== null),

                    Action::make('view_invoices')
                        ->label('Invoices')
                        ->icon('phosphor-receipt-duotone')
                        ->color('gray')
                        ->url(fn (Deployment $record): string => '/admin/invoices'),

                    EditAction::make(),
                ])
                    ->label('Actions')
                    ->icon('phosphor-caret-down')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                BulkAction::make('push_software_update')
                    ->label('Push Software Update')
                    ->icon('phosphor-arrow-circle-up-duotone')
                    ->color('warning')
                    ->modalHeading('Push Software Update')
                    ->modalSubmitActionLabel('Push Update')
                    ->schema([
                        Select::make('product_ids')
                            ->label('Which software does this update apply to?')
                            ->helperText('Only deployments for the selected software will be redeployed. Any other software in your selection will be skipped automatically.')
                            ->options(fn (): array => Product::orderBy('name')->pluck('name', 'id')->all())
                            ->multiple()
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $productIds = $data['product_ids'] ?? [];
                        $queued = 0;
                        $skipped = 0;

                        foreach ($records as $deployment) {
                            if (
                                $deployment->coolify_app_id
                                && $deployment->status === 'active'
                                && in_array($deployment->product_id, $productIds)
                            ) {
                                PushSoftwareUpdate::dispatch($deployment);
                                $queued++;
                            } else {
                                $skipped++;
                            }
                        }

                        Notification::make()
                            ->title($queued > 0 ? "Update queued for {$queued} deployment(s)." : 'No eligible deployments matched the selected software.')
                            ->body($skipped > 0 ? "{$skipped} skipped — different software, not active, or no Coolify app." : null)
                            ->color($queued > 0 ? 'success' : 'warning')
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeployments::route('/'),
            'deploy' => AdminDeployDeployment::route('/deploy'),
            'edit' => EditDeployment::route('/{record}/edit'),
            'manage' => ManageDeployment::route('/{record}/manage'),
        ];
    }

    public static function buildInvoiceFormSchema(Deployment $record): array
    {
        $up = $record->userProduct?->load(['pricing', 'orderAddons.addon']);

        $projectItems = [];
        if ($up) {
            if ((float) ($up->setup_amount ?? 0) > 0) {
                $projectItems[] = [
                    'label' => $up->deployment_type === 'onprem' ? 'License & Setup Fee' : 'Setup Fee',
                    'amount' => (float) $up->setup_amount,
                    'type' => 'onetime',
                ];
            }
            if ($up->pricing) {
                $projectItems[] = [
                    'label' => $up->pricing->label.' (recurring)',
                    'amount' => (float) $up->pricing->amount,
                    'type' => 'recurring',
                ];
            }
        }

        $moduleItems = [];
        if ($up) {
            foreach ($up->orderAddons as $oa) {
                if ($oa->addon?->module_key) {
                    $moduleItems[] = [
                        'label' => $oa->addon->name ?? ucwords(str_replace('_', ' ', $oa->addon->module_key)),
                        'amount' => (float) $oa->amount_paid,
                        'type' => $oa->price_type ?? 'onetime',
                    ];
                }
            }
        }

        $itemSchema = fn (string $labelPlaceholder) => [
            TextInput::make('label')->label('Description')->placeholder($labelPlaceholder)->required()->columnSpan(2),
            TextInput::make('amount')->label('Amount (₦)')->numeric()->required()->minValue(0)->default(0),
            Select::make('type')->label('Type')->options(['onetime' => 'One-time', 'recurring' => 'Recurring'])->default('onetime')->required(),
        ];

        return [
            Section::make('Client Project')
                ->description('Pre-filled from the linked purchase record. Edit amounts as needed.')
                ->schema([
                    Repeater::make('project_items')
                        ->label('')
                        ->schema($itemSchema('e.g. Setup Fee'))
                        ->columns(4)
                        ->default($projectItems)
                        ->addActionLabel('Add project item'),
                ]),

            Section::make('Modules')
                ->description('Enabled modules — remove any you do not want to bill for.')
                ->schema([
                    Repeater::make('module_items')
                        ->label('')
                        ->schema($itemSchema('Module name'))
                        ->columns(4)
                        ->default($moduleItems),
                ]),

            Section::make('Custom Work')
                ->description('Ad-hoc charges for work not covered above.')
                ->schema([
                    Repeater::make('custom_items')
                        ->label('')
                        ->schema($itemSchema('e.g. Custom integration'))
                        ->columns(4)
                        ->default([]),
                ]),

            DatePicker::make('due_date')->label('Due Date (optional)')->nullable(),
        ];
    }

    public static function dispatchInvoice(Deployment $record, array $data): void
    {
        $lineItems = array_values(array_filter(array_merge(
            $data['project_items'] ?? [],
            $data['module_items'] ?? [],
            $data['custom_items'] ?? [],
        )));

        $amount = collect($lineItems)->sum('amount');

        $invoice = Invoice::create([
            'user_id' => $record->user_id,
            'user_product_id' => $record->user_product_id,
            'deployment_id' => $record->id,
            'amount' => $amount,
            'currency' => 'NGN',
            'status' => 'sent',
            'line_items' => $lineItems,
            'due_date' => $data['due_date'] ?? null,
        ]);

        app(InvoiceService::class)->generatePdf($invoice);
        Mail::to($record->user->email)->queue(new InvoiceMail($invoice));
    }

    private static function summaryRow(string $label, mixed $amount): string
    {
        return '<tr>
            <td class="text-gray-500 dark:text-gray-400 pr-8 py-0.5">'.$label.'</td>
            <td class="font-medium text-gray-900 dark:text-white py-0.5">₦'.number_format((float) $amount, 2).'</td>
        </tr>';
    }
}
