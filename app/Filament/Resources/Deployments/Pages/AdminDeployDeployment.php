<?php

namespace App\Filament\Resources\Deployments\Pages;

use App\Filament\Resources\Deployments\DeploymentResource;
use App\Models\User;
use App\Services\ProductDeploymentService;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Wave\Deployment;
use Wave\OrderAddon;
use Wave\Product;
use Wave\ProductAddon;
use Wave\UserProduct;

class AdminDeployDeployment extends Page
{
    protected static string $resource = DeploymentResource::class;

    protected string $view = 'filament.resources.deployments.admin-deploy';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'deploy_for' => 'self',
            'project_type' => 'existing_product',
            'custom_deploy_type' => 'docker_image',
            'custom_git_branch' => 'main',
            'deployment_type' => 'cloud',
            'price_type' => 'onetime',
            'amount_paid' => 0,
        ]);
    }

    public function getTitle(): string
    {
        return 'Deploy for Client';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                // ── Section 1: Client ─────────────────────────────────────────────
                Section::make('Client Details')
                    ->description('Who is this deployment for?')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Client Account')
                            ->options(
                                fn () => User::orderBy('name')
                                    ->pluck('name', 'id')
                                    ->map(fn ($name, $id) => $name.' — '.User::find($id)?->email)
                            )
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('business_name')
                            ->label('Business / Project Name')
                            ->required()
                            ->maxLength(255),

                        Select::make('deploy_for')
                            ->label('Deploying For')
                            ->options([
                                'self' => 'Self (client is the account owner)',
                                'client' => 'Client (account owner is managing for someone else)',
                            ])
                            ->required()
                            ->live(),

                        TextInput::make('client_name')
                            ->label('End-client Name')
                            ->visible(fn (Get $get) => $get('deploy_for') === 'client')
                            ->maxLength(255),

                        TextInput::make('client_email')
                            ->label('End-client Email')
                            ->email()
                            ->visible(fn (Get $get) => $get('deploy_for') === 'client')
                            ->maxLength(255),
                    ]),

                // ── Section 2: Project ────────────────────────────────────────────
                Section::make('Project Configuration')
                    ->description('Pick an existing product or configure a custom deployment.')
                    ->columns(2)
                    ->schema([
                        Select::make('project_type')
                            ->label('Deployment Type')
                            ->options([
                                'existing_product' => 'Existing Product (from catalog)',
                                'custom_work' => 'Custom Work (Docker image / Git repo)',
                            ])
                            ->required()
                            ->live()
                            ->columnSpanFull(),

                        // ── Existing product ──────────────────────────────────────
                        Select::make('product_id')
                            ->label('Product')
                            ->options(fn () => Product::where('type', 'prebuilt')
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->visible(fn (Get $get) => $get('project_type') === 'existing_product'),

                        Select::make('deployment_type')
                            ->label('Hosting Type')
                            ->options(['cloud' => 'Cloud (Coolify)', 'onprem' => 'On-Premises'])
                            ->default('cloud')
                            ->required(),

                        CheckboxList::make('enabled_modules')
                            ->label('Modules to Enable')
                            ->options([
                                'auth' => 'Auth & Users',
                                'org' => 'Organisation',
                                'dashboard' => 'Dashboard',
                                'contacts' => 'Contacts / CRM',
                                'catalog' => 'Product Catalog',
                                'inventory' => 'Inventory',
                                'pos' => 'Point of Sale',
                                'invoicing' => 'Invoicing',
                                'staff' => 'Staff Management',
                                'booking' => 'Booking & Scheduling',
                                'notifications' => 'Notifications',
                                'audit_log' => 'Audit Log',
                                'platform' => 'Platform / Settings',
                                'digital_products' => 'Digital Products',
                                'video_courses' => 'Video Courses',
                            ])
                            ->columns(3)
                            ->visible(fn (Get $get) => $get('project_type') === 'existing_product')
                            ->columnSpanFull(),

                        // ── Custom work ───────────────────────────────────────────
                        TextInput::make('custom_name')
                            ->label('Project Name')
                            ->placeholder('e.g. Client Inventory System')
                            ->required()
                            ->visible(fn (Get $get) => $get('project_type') === 'custom_work'),

                        Select::make('custom_deploy_type')
                            ->label('Source Type')
                            ->options([
                                'docker_image' => 'Docker Image',
                                'git_repo' => 'Git Repository',
                            ])
                            ->live()
                            ->visible(fn (Get $get) => $get('project_type') === 'custom_work'),

                        TextInput::make('custom_docker_image')
                            ->label('Docker Image')
                            ->placeholder('e.g. ghcr.io/valexhub-dev-s/core:latest')
                            ->visible(fn (Get $get) => $get('project_type') === 'custom_work' &&
                                $get('custom_deploy_type') === 'docker_image'),

                        TextInput::make('custom_git_repo')
                            ->label('Git Repository URL')
                            ->url()
                            ->placeholder('https://github.com/org/repo')
                            ->visible(fn (Get $get) => $get('project_type') === 'custom_work' &&
                                $get('custom_deploy_type') === 'git_repo'),

                        TextInput::make('custom_git_branch')
                            ->label('Branch')
                            ->default('main')
                            ->visible(fn (Get $get) => $get('project_type') === 'custom_work' &&
                                $get('custom_deploy_type') === 'git_repo'),

                        Repeater::make('extra_env_vars')
                            ->label('Environment Variables')
                            ->helperText('These will be injected into the Coolify container at deploy time and encrypted in the database.')
                            ->schema([
                                TextInput::make('key')
                                    ->label('Key')
                                    ->placeholder('e.g. STRIPE_SECRET_KEY')
                                    ->required()
                                    ->extraInputAttributes(['class' => 'font-mono text-sm']),
                                TextInput::make('value')
                                    ->label('Value')
                                    ->placeholder('e.g. sk_live_...')
                                    ->required()
                                    ->password()
                                    ->revealable()
                                    ->extraInputAttributes(['class' => 'font-mono text-sm']),
                            ])
                            ->columns(2)
                            ->default([])
                            ->addActionLabel('Add variable')
                            ->visible(fn (Get $get) => $get('project_type') === 'custom_work')
                            ->columnSpanFull(),

                        Toggle::make('env_inject_alongside')
                            ->label('Also inject standard variables (DB, Redis, storage, email)')
                            ->helperText('On: custom vars are added on top of the standard set. Off: only custom vars are injected.')
                            ->default(true)
                            ->visible(fn (Get $get) => $get('project_type') === 'custom_work')
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Internal Notes (admin only)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                // ── Section 3: Billing ────────────────────────────────────────────
                Section::make('Billing (optional)')
                    ->description('Leave blank if payment is not yet confirmed or handled separately.')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextInput::make('amount_paid')
                            ->label('Amount Paid')
                            ->numeric()
                            ->prefix('₦')
                            ->default(0)
                            ->minValue(0),

                        Select::make('price_type')
                            ->label('Billing Type')
                            ->options([
                                'onetime' => 'One-time / Perpetual',
                                'recurring' => 'Recurring',
                            ])
                            ->live()
                            ->default('onetime'),

                        DatePicker::make('renewal_date')
                            ->label('Next Renewal Date')
                            ->visible(fn (Get $get) => $get('price_type') === 'recurring'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deploy')
                ->label('Deploy Now')
                ->icon('phosphor-rocket-launch')
                ->color('success')
                ->action(fn () => $this->deploy()),

            Action::make('cancel')
                ->label('Cancel')
                ->icon('phosphor-x')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    public function deploy(): void
    {
        $data = $this->form->getState();

        // ── 1. Resolve or create product ─────────────────────────────────────
        $isCustom = $data['project_type'] === 'custom_work';

        if (! $isCustom) {
            $product = Product::findOrFail($data['product_id']);
            $deployType = $data['deployment_type'] ?? 'cloud';
            $modules = $data['enabled_modules'] ?? [];
            $extraVars = [];
            $injectMode = 'alongside';
        } else {
            $product = Product::create([
                'name' => $data['custom_name'],
                'slug' => Str::slug($data['custom_name']).'-'.Str::random(6),
                'type' => 'prebuilt',
                'coolify_deploy_type' => $data['custom_deploy_type'],
                'coolify_docker_image' => $data['custom_docker_image'] ?? null,
                'coolify_git_repo' => $data['custom_git_repo'] ?? null,
                'coolify_git_branch' => $data['custom_git_branch'] ?? 'main',
                'is_active' => false,
            ]);
            $deployType = $data['deployment_type'] ?? 'cloud';
            $modules = [];
            $extraVars = $data['extra_env_vars'] ?? [];
            $injectMode = ($data['env_inject_alongside'] ?? true) ? 'alongside' : 'standalone';
        }

        // ── 2. Create UserProduct ─────────────────────────────────────────────
        $userProduct = UserProduct::create([
            'user_id' => $data['user_id'],
            'product_id' => $product->id,
            'amount_paid' => $data['amount_paid'] ?? 0,
            'setup_amount' => $data['amount_paid'] ?? 0,
            'purchase_date' => now(),
            'next_renewal_date' => ($data['price_type'] === 'recurring' && ! empty($data['renewal_date']))
                ? $data['renewal_date']
                : null,
            'status' => 'active',
            'deployment_type' => $deployType,
        ]);

        // ── 3. Link module addons (existing product only) ─────────────────────
        foreach ($modules as $moduleKey) {
            $addon = ProductAddon::where('module_key', $moduleKey)->first();
            if ($addon) {
                OrderAddon::create([
                    'user_product_id' => $userProduct->id,
                    'product_addon_id' => $addon->id,
                    'amount_paid' => 0,
                    'price_type' => 'onetime',
                ]);
            }
        }

        // ── 4. Create Deployment record ───────────────────────────────────────
        $deployment = Deployment::create([
            'user_id' => $data['user_id'],
            'product_id' => $product->id,
            'user_product_id' => $userProduct->id,
            'deploy_for' => $data['deploy_for'],
            'business_name' => $data['business_name'],
            'client_name' => $data['client_name'] ?? null,
            'client_email' => $data['client_email'] ?? null,
            'status' => 'pending',
            'extra_env_vars' => count($extraVars) > 0 ? $extraVars : null,
            'env_inject_mode' => $injectMode,
        ]);

        // ── 5. Provision ──────────────────────────────────────────────────────
        app(ProductDeploymentService::class)->fulfill($deployment);

        Notification::make()
            ->title('Deployment queued — redirecting to management page.')
            ->success()
            ->send();

        $this->redirect(DeploymentResource::getUrl('manage', ['record' => $deployment->id]));
    }
}
