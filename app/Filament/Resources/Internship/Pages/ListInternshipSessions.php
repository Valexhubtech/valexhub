<?php

namespace App\Filament\Resources\Internship\Pages;

use App\Filament\Resources\Internship\InternshipSessionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInternshipSessions extends ListRecords
{
    protected static string $resource = InternshipSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
