<?php

namespace App\Filament\Resources\PackageColorResource\Pages;

use App\Filament\Resources\PackageColorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackageColors extends ListRecords
{
  protected static string $resource = PackageColorResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }
}
