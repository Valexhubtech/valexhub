<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Wave\Category;
use Wave\Product;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-package-duotone';

    protected static string|\UnitEnum|null $navigationGroup = 'Products & Catalog';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $context, $state, callable $set) {
                        if ($context === 'create') {
                            $set('slug', Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('type')
                    ->label('Product Type')
                    ->options([
                        'custom' => 'Custom (Quote / Demo)',
                        'prebuilt' => 'Pre-built (Pay & Deploy)',
                    ])
                    ->required()
                    ->default('custom')
                    ->helperText('Pre-built products get a "Deploy" checkout button; Custom products get a quote/demo request form.'),

                TextInput::make('short_description')
                    ->maxLength(500),

                RichEditor::make('description')
                    ->fileAttachmentsDisk(config('filament.default_filesystem_disk'))
                    ->fileAttachmentsDirectory('attachments')
                    ->fileAttachmentsVisibility('public')
                    ->columnSpanFull(),

                TextInput::make('low_price')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('₦')
                    ->label('Low Price'),

                TextInput::make('high_price')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('₦')
                    ->label('High Price'),

                TagsInput::make('features')
                    ->label('Key Features')
                    ->columnSpanFull(),

                FileUpload::make('icon')
                    ->image()
                    ->disk(config('filament.default_filesystem_disk'))
                    ->directory('products/icons')
                    ->maxSize(2048),

                FileUpload::make('images')
                    ->image()
                    ->multiple()
                    ->disk(config('filament.default_filesystem_disk'))
                    ->directory('products/images')
                    ->maxSize(5120)
                    ->maxFiles(10)
                    ->columnSpanFull(),

                TextInput::make('website_url')
                    ->url()
                    ->label('Website URL')
                    ->maxLength(500),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('Sort Order'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                TextInput::make('seo_title')
                    ->maxLength(255)
                    ->label('SEO Title'),

                Textarea::make('seo_description')
                    ->maxLength(500)
                    ->rows(3)
                    ->label('SEO Description'),

                Textarea::make('seo_keywords')
                    ->maxLength(500)
                    ->rows(2)
                    ->label('SEO Keywords'),

                Section::make('Coolify Deployment Configuration')
                    ->description('Required only for Pre-built (Pay & Deploy) products.')
                    ->schema([
                        Select::make('coolify_deploy_type')
                            ->label('Deploy Type')
                            ->options([
                                'docker_image' => 'Docker Image',
                                'git_repo'     => 'Public Git Repository',
                            ])
                            ->nullable()
                            ->live()
                            ->helperText('Leave blank for Custom (quote/demo) products.'),

                        TextInput::make('coolify_docker_image')
                            ->label('Docker Image')
                            ->placeholder('e.g. bitnami/wordpress:latest')
                            ->hidden(fn (Get $get): bool => $get('coolify_deploy_type') !== 'docker_image')
                            ->nullable(),

                        TextInput::make('coolify_git_repo')
                            ->label('Git Repository URL')
                            ->placeholder('https://github.com/org/repo')
                            ->url()
                            ->hidden(fn (Get $get): bool => $get('coolify_deploy_type') !== 'git_repo')
                            ->nullable(),

                        TextInput::make('coolify_git_branch')
                            ->label('Git Branch')
                            ->placeholder('main')
                            ->default('main')
                            ->hidden(fn (Get $get): bool => $get('coolify_deploy_type') !== 'git_repo')
                            ->nullable(),

                        TextInput::make('login_path')
                            ->label('Login Path')
                            ->placeholder('/dashboard')
                            ->default('/dashboard')
                            ->helperText('Path appended to the app URL for the login page. e.g. "/dashboard" → https://app.example.com/dashboard. Used for the one-click login button.')
                            ->nullable(),

                        Repeater::make('coolify_env_template')
                            ->label('Environment Variable Template')
                            ->schema([
                                TextInput::make('key')
                                    ->label('Key')
                                    ->placeholder('e.g. DB_HOST')
                                    ->required(),
                                TextInput::make('value')
                                    ->label('Value')
                                    ->placeholder('e.g. localhost')
                                    ->nullable(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Variable')
                            ->helperText('These env vars are injected into every deployment. APP_ADMIN_EMAIL and APP_ADMIN_PASSWORD are always appended automatically.')
                            ->nullable()
                            ->defaultItems(0),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon')
                    ->size(40),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'prebuilt' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => $state === 'prebuilt' ? 'Pay & Deploy' : 'Custom')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('low_price')
                    ->label('Price Range')
                    ->formatStateUsing(function ($record) {
                        if ($record->low_price && $record->high_price) {
                            if ($record->low_price == $record->high_price) {
                                return '₦' . number_format($record->low_price, 2);
                            }
                            return '₦' . number_format($record->low_price, 2) . ' - ₦' . number_format($record->high_price, 2);
                        } elseif ($record->low_price) {
                            return 'From ₦' . number_format($record->low_price, 2);
                        } elseif ($record->high_price) {
                            return 'Up to ₦' . number_format($record->high_price, 2);
                        }
                        return 'Contact for pricing';
                    })
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id')),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\Products\RelationManagers\PricingRelationManager::class,
            \App\Filament\Resources\Products\RelationManagers\AddonsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}