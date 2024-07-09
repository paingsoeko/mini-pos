<?php

namespace App\Filament\Clusters\Products\Resources\UnitCategoryResource\Pages;

use App\Filament\Clusters\Products\Resources\UnitCategoryResource;
use App\Models\UnitCategory;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUnitCategory extends CreateRecord
{
    protected static string $resource = UnitCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        $recordId = $this->record->id;
        return $this->getResource()::getUrl('edit', ['record' => $recordId]);
    }
}
