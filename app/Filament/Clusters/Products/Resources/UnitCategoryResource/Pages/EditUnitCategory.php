<?php

namespace App\Filament\Clusters\Products\Resources\UnitCategoryResource\Pages;

use App\Filament\Clusters\Products\Resources\UnitCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitCategory extends EditRecord
{
    protected static string $resource = UnitCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}
