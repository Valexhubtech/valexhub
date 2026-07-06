<?php

namespace App\Filament\Resources\DomainPurchases\Pages;

use App\Filament\Resources\DomainPurchases\DomainPurchaseResource;
use Filament\Resources\Pages\ListRecords;

class ListDomainPurchases extends ListRecords
{
    protected static string $resource = DomainPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
