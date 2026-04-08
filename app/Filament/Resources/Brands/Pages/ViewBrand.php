<?php

namespace App\Filament\Resources\Brands\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\Brands\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBrand extends ViewRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
