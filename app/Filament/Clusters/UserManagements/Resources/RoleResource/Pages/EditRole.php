<?php

namespace App\Filament\Clusters\UserManagements\Resources\RoleResource\Pages;

use App\Filament\Clusters\UserManagements\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Get the currently authenticated user
        $user = Auth::user();

        Cache::forget('user_permissions_' . $user->id);

        // Extract permissions from data
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        // Set permissions to be used after save
        $this->permissions = $permissions;

        return $data;
    }

    protected function afterSave(): void
    {
        if (!empty($this->permissions)) {
            $this->record->permissions()->sync($this->permissions);
        }
    }

}
