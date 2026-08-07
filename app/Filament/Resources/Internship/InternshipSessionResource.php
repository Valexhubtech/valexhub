<?php

namespace App\Filament\Resources\Internship;

use App\Filament\Resources\Internship\Pages\ListInternshipSessions;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Wave\InternshipSession;

class InternshipSessionResource extends Resource
{
    protected static ?string $model = InternshipSession::class;

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-graduation-cap-duotone';

    protected static ?string $navigationLabel = 'Internship Sessions';

    protected static string|\UnitEnum|null $navigationGroup = 'Talent & HR';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Session Details')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('slug')->required()->maxLength(255),
                DatePicker::make('application_deadline')->nullable(),
                Toggle::make('is_active')->label('Accept Applications')->columnSpanFull(),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                TagsInput::make('roles')
                    ->label('Available Roles')
                    ->placeholder('Add a role…')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('applications_count')
                    ->label('Applications')
                    ->counts('applications')
                    ->sortable(),
                TextColumn::make('application_deadline')->date()->sortable(),
                IconColumn::make('is_active')->label('Open')->boolean(),
                TextColumn::make('created_at')->since()->sortable(),
            ])
            ->actions([EditAction::make()])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInternshipSessions::route('/'),
        ];
    }
}
