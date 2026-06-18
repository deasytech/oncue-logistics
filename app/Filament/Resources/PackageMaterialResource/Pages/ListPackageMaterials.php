<?php

namespace App\Filament\Resources\PackageMaterialResource\Pages;

use App\Filament\Resources\PackageMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackageMaterials extends ListRecords
{
  protected static string $resource = PackageMaterialResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }
}
