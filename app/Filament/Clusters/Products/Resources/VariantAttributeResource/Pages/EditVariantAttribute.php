<?php

namespace App\Filament\Clusters\Products\Resources\VariantAttributeResource\Pages;

use App\Filament\Clusters\Products\Resources\VariantAttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVariantAttribute extends EditRecord
{
    protected static string $resource = VariantAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
