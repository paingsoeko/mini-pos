<?php

namespace App\Filament\Clusters\Products\Resources\TagResource\Pages;

use App\Filament\Clusters\Products\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;
}
