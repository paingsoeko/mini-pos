<?php

namespace App\Filament\Clusters\UserManagements\Resources\RoleResource\Pages;

use App\Filament\Clusters\UserManagements\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
