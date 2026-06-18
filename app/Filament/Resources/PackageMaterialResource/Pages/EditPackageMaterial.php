<?php

namespace App\Filament\Resources\PackageMaterialResource\Pages;

use App\Filament\Resources\PackageMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackageMaterial extends EditRecord
{
  protected static string $resource = PackageMaterialResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\ViewAction::make(),
      Actions\DeleteAction::make(),
    ];
  }
}
