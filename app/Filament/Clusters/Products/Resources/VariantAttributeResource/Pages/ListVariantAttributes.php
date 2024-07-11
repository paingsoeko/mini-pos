<?php

namespace App\Filament\Clusters\Products\Resources\VariantAttributeResource\Pages;

use App\Filament\Clusters\Products\Resources\VariantAttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVariantAttributes extends ListRecords
{
    protected static string $resource = VariantAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
