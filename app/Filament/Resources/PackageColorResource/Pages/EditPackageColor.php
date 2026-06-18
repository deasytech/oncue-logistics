<?php

namespace App\Filament\Resources\PackageColorResource\Pages;

use App\Filament\Resources\PackageColorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackageColor extends EditRecord
{
  protected static string $resource = PackageColorResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\ViewAction::make(),
      Actions\DeleteAction::make(),
    ];
  }
}
