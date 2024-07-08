<?php

namespace App\Filament\Clusters\Products\Resources\UnitCategoryResource\Pages;

use App\Filament\Clusters\Products\Resources\UnitCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUnitCategory extends ViewRecord
{
    protected static string $resource = UnitCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
