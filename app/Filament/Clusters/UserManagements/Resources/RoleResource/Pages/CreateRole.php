<?php

namespace App\Filament\Clusters\UserManagements\Resources\RoleResource\Pages;

use App\Filament\Clusters\UserManagements\Resources\RoleResource;
use App\Models\Feature;
use App\Models\Role;
use App\Models\RolePermission;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract permissions from data
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        // Set permissions to be used after create
        $this->permissions = $permissions;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Attach the permissions to the role
        $this->record->permissions()->attach($this->permissions);
    }

}
