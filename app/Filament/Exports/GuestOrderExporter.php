<?php

namespace App\Filament\Exports;

use App\Models\EventGuest;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class GuestOrderExporter extends Exporter
{
    protected static ?string $model = EventGuest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('guest_name')
                ->label('Guest Name')
                ->state(fn(EventGuest $record): string =>
                trim(collect([$record->guest?->title, $record->guest?->first_name, $record->guest?->last_name])->filter()->join(' '))),

            ExportColumn::make('guest.email')
                ->label('Email'),

            ExportColumn::make('guest.phone')
                ->label('Phone'),

            ExportColumn::make('guest.address')
                ->label('Address'),

            ExportColumn::make('event.name')
                ->label('Event'),

            ExportColumn::make('event.customer.full_name')
                ->label('Customer / Host'),

            ExportColumn::make('attendance_status')
                ->label('RSVP Status')
                ->formatStateUsing(fn(?string $state): string => match ($state) {
                    'confirmed' => 'Confirmed',
                    'declined'  => 'Declined',
                    default     => 'Pending',
                }),

            ExportColumn::make('rsvp_responded_at')
                ->label('Responded At')
                ->formatStateUsing(fn(?string $state): ?string => $state ? Carbon::parse($state)->format('d M Y') : null),

            ExportColumn::make('fabric_payment_status')
                ->label('Fabric Payment')
                ->state(fn(EventGuest $record): string => $record->fabric_payment_status ?? 'No Order')
                ->formatStateUsing(fn(string $state): string => match ($state) {
                    'No Order' => 'No Order',
                    default    => ucfirst($state),
                }),

            ExportColumn::make('fabric_total_amount')
                ->label('Amount (NGN)'),

            ExportColumn::make('fabric_paid_at')
                ->label('Paid At')
                ->state(fn(EventGuest $record): ?string =>
                $record->fabric_paid_at
                    ? Carbon::parse($record->fabric_paid_at)->format('d M Y')
                    : null),

            ExportColumn::make('rsvp_created_at')
                ->label('RSVP Date')
                ->state(fn(EventGuest $record): string =>
                Carbon::parse($record->rsvp_created_at ?? $record->created_at)->format('d M Y')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your guest order export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
