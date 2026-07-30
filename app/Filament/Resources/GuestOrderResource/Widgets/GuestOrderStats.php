<?php

namespace App\Filament\Resources\GuestOrderResource\Widgets;

use App\Models\EventGuest;
use App\Models\GuestFabricSelection;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GuestOrderStats extends BaseWidget
{
    protected function getColumns(): int
    {
        return 6;
    }

    protected function getStats(): array
    {
        $total = EventGuest::count();
        $confirmed = EventGuest::where('attendance_status', 'confirmed')->count();
        $declined = EventGuest::where('attendance_status', 'declined')->count();
        $pending = EventGuest::where('attendance_status', 'invited')->count();

        $fabricOrdered = GuestFabricSelection::where('payment_status', 'paid')->count();
        $fabricPaid = $fabricOrdered;

        return [
            Stat::make('Total Guest Orders', number_format($total))
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make('Confirmed', number_format($confirmed))
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Declined', number_format($declined))
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Awaiting Response', number_format($pending))
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Fabric Orders', number_format($fabricOrdered))
                ->icon('heroicon-o-shopping-bag')
                ->color('info'),

            Stat::make('Fabric Paid', number_format($fabricPaid))
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }
}
