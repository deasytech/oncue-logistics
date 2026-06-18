<?php

namespace App\Filament\Resources\PaymentReceiptResource\Pages;

use App\Filament\Resources\PaymentReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentReceipts extends ListRecords
{
  protected static string $resource = PaymentReceiptResource::class;

  protected function getHeaderActions(): array
  {
    return [
      // No create action as receipts are uploaded by customers
    ];
  }
}
