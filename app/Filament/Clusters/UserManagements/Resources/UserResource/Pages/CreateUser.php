<?php

namespace App\Filament\Clusters\UserManagements\Resources\UserResource\Pages;

use App\Filament\Clusters\UserManagements\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
