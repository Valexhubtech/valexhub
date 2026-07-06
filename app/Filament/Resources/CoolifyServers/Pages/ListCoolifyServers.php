<?php

namespace App\Filament\Resources\CoolifyServers\Pages;

use App\Filament\Resources\CoolifyServers\CoolifyServerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCoolifyServers extends ListRecords
{
    protected static string $resource = CoolifyServerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
