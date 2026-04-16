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
use Filament\Resources\Resource;
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

    protected static ?int $navigationSort = 7;

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
                    ->prefix('$')
                    ->label('Low Price'),

                TextInput::make('high_price')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('$')
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

                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('low_price')
                    ->label('Price Range')
                    ->formatStateUsing(function ($record) {
                        if ($record->low_price && $record->high_price) {
                            if ($record->low_price == $record->high_price) {
                                return '$' . number_format($record->low_price, 2);
                            }
                            return '$' . number_format($record->low_price, 2) . ' - $' . number_format($record->high_price, 2);
                        } elseif ($record->low_price) {
                            return 'From $' . number_format($record->low_price, 2);
                        } elseif ($record->high_price) {
                            return 'Up to $' . number_format($record->high_price, 2);
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
            //
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