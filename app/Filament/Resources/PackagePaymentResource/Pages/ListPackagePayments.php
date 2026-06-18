<?php

namespace App\Filament\Resources\PackagePaymentResource\Pages;

use App\Filament\Resources\PackagePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackagePayments extends ListRecords
{
  protected static string $resource = PackagePaymentResource::class;

  protected function getHeaderActions(): array
  {
    return [];
  }
}
