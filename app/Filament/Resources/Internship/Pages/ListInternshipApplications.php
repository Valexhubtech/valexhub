<?php

namespace App\Filament\Resources\Internship\Pages;

use App\Filament\Resources\Internship\InternshipApplicationResource;
use Filament\Resources\Pages\ListRecords;

class ListInternshipApplications extends ListRecords
{
    protected static string $resource = InternshipApplicationResource::class;
}
