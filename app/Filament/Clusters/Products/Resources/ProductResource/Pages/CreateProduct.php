<?php

namespace App\Filament\Clusters\Products\Resources\ProductResource\Pages;

use App\Filament\Clusters\Products\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tags = $data['tags'] ?? [];
        $this->tags = $tags; // Store tags temporarily
        unset($data['tags']);

        return $data;
    }

    public function createAnother(): void
    {

    }
}
