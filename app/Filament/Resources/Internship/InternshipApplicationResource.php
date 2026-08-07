<?php

namespace App\Filament\Resources\Internship;

use App\Filament\Resources\Internship\Pages\ListInternshipApplications;
use App\Http\Controllers\InternshipController;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\SelectAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Wave\InternshipApplication;

class InternshipApplicationResource extends Resource
{
    protected static ?string $model = InternshipApplication::class;

    protected static BackedEnum|string|null $navigationIcon = 'phosphor-clipboard-text-duotone';

    protected static ?string $navigationLabel = 'Applications';

    protected static string|\UnitEnum|null $navigationGroup = 'Talent & HR';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(fn ($record) => $record->first_name.' '.$record->last_name)
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('role')->badge()->sortable(),
                TextColumn::make('institution')->sortable()->toggleable(),
                TextColumn::make('graduation_year')->label('Grad Year')->sortable(),
                TextColumn::make('session.name')->label('Session')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'reviewing' => 'info',
                        'accepted'  => 'success',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')->label('Applied')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'reviewing' => 'Reviewing',
                        'accepted'  => 'Accepted',
                        'rejected'  => 'Rejected',
                    ]),
                SelectFilter::make('internship_session_id')
                    ->label('Session')
                    ->relationship('session', 'name'),
                SelectFilter::make('role')
                    ->options(fn () => InternshipApplication::distinct()->pluck('role', 'role')),
            ])
            ->actions([
                Action::make('update_status')
                    ->label('Update Status')
                    ->icon('phosphor-pencil')
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'pending'   => 'Pending',
                                'reviewing' => 'Reviewing',
                                'accepted'  => 'Accepted',
                                'rejected'  => 'Rejected',
                            ])
                            ->required(),
                    ])
                    ->action(fn (InternshipApplication $record, array $data) => $record->update(['status' => $data['status']])),

                Action::make('download_cv')
                    ->label('Download CV')
                    ->icon('phosphor-file-arrow-down-duotone')
                    ->url(fn (InternshipApplication $record) => route('internship.cv.download', $record))
                    ->openUrlInNewTab(),

                Action::make('view_details')
                    ->label('Details')
                    ->icon('phosphor-eye-duotone')
                    ->modalContent(fn (InternshipApplication $record) => view('filament.internship-application-detail', compact('record')))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInternshipApplications::route('/'),
        ];
    }
}
