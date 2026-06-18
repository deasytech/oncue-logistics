<?php

namespace App\Filament\Resources\PackageMaterialResource\Pages;

use App\Filament\Resources\PackageMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPackageMaterial extends ViewRecord
{
  protected static string $resource = PackageMaterialResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\EditAction::make(),
      Actions\DeleteAction::make(),
    ];
  }
}
