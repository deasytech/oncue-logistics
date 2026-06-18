<?php

namespace App\Filament\Exports;

use App\Models\ContactEnquiry;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ContactEnquiryExporter extends Exporter
{
  protected static ?string $model = ContactEnquiry::class;

  public static function getColumns(): array
  {
    return [
      ExportColumn::make('id')
        ->label('Enquiry ID'),
      ExportColumn::make('name')
        ->label('Name'),
      ExportColumn::make('email')
        ->label('Email Address'),
      ExportColumn::make('phone')
        ->label('Phone Number'),
      ExportColumn::make('subject')
        ->label('Subject'),
      ExportColumn::make('status')
        ->label('Status'),
      ExportColumn::make('message')
        ->label('Message')
        ->formatStateUsing(fn($state) => strip_tags($state)),
      ExportColumn::make('created_at')
        ->label('Created At'),
      ExportColumn::make('updated_at')
        ->label('Updated At'),
    ];
  }

  public static function getCompletedNotificationBody(Export $export): string
  {
    $body = 'Your contact enquiry export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

    if ($failedRowsCount = $export->getFailedRowsCount()) {
      $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
    }

    return $body;
  }
}
