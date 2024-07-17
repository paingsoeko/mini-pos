<?php

namespace App\Filament\Clusters\UserManagements\Resources\UserResource\Pages;

use App\Filament\Clusters\UserManagements\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
