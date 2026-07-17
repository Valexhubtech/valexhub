<?php

namespace App\Filament\Resources\Deployments\Pages;

use App\Filament\Resources\Deployments\DeploymentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListDeployments extends ListRecords
{
    protected static string $resource = DeploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deploy_for_client')
                ->label('Deploy for Client')
                ->icon('phosphor-rocket-launch-duotone')
                ->color('success')
                ->url(DeploymentResource::getUrl('deploy')),
        ];
    }
}
