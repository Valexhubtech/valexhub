<?php

namespace App\Filament\Resources\DemoRequests\Pages;

use App\Filament\Resources\DemoRequests\DemoRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDemoRequests extends ListRecords
{
    protected static string $resource = DemoRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
