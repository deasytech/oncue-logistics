<?php

namespace App\Filament\Resources\PackageFontResource\Pages;

use App\Filament\Resources\PackageFontResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackageFonts extends ListRecords
{
  protected static string $resource = PackageFontResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }
}
