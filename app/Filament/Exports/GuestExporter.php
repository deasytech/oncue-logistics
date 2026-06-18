<?php

namespace App\Filament\Exports;

use App\Models\Guest;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class GuestExporter extends Exporter
{
    protected static ?string $model = Guest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('customer.name')
                ->label('Customer Name'),
            ExportColumn::make('title'),
            ExportColumn::make('first_name'),
            ExportColumn::make('last_name'),
            ExportColumn::make('full_name')
                ->label('Full Name'),
            ExportColumn::make('email'),
            ExportColumn::make('phone'),
            ExportColumn::make('address'),
            ExportColumn::make('city.name')
                ->label('City'),
            ExportColumn::make('state.name')
                ->label('State'),
            ExportColumn::make('notes'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your guest export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
