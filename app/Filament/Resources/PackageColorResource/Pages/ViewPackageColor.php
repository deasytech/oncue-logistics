<?php

namespace App\Filament\Resources\PackageColorResource\Pages;

use App\Filament\Resources\PackageColorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPackageColor extends ViewRecord
{
  protected static string $resource = PackageColorResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\EditAction::make(),
      Actions\DeleteAction::make(),
    ];
  }
}
