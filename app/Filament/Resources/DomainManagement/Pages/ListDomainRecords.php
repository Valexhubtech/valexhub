<?php

namespace App\Filament\Resources\DomainManagement\Pages;

use App\Filament\Resources\DomainManagement\DomainManagerResource;
use Filament\Resources\Pages\ListRecords;

class ListDomainRecords extends ListRecords
{
    protected static string $resource = DomainManagerResource::class;
}
