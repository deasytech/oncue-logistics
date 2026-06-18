<?php

namespace App\Filament\Resources\PackageFontResource\Pages;

use App\Filament\Resources\PackageFontResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackageFont extends EditRecord
{
  protected static string $resource = PackageFontResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\ViewAction::make(),
      Actions\DeleteAction::make(),
    ];
  }
}
