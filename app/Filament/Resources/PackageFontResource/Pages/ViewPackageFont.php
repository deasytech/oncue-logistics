<?php

namespace App\Filament\Resources\PackageFontResource\Pages;

use App\Filament\Resources\PackageFontResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPackageFont extends ViewRecord
{
  protected static string $resource = PackageFontResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\EditAction::make(),
      Actions\DeleteAction::make(),
    ];
  }
}
