<?php

namespace App\Filament\Resources\GuestOrderResource\Pages;

use App\Filament\Resources\GuestOrderResource;
use App\Filament\Resources\GuestOrderResource\Widgets\GuestOrderStats;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListGuestOrders extends ListRecords
{
    protected static string $resource = GuestOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GuestOrderStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All RSVPs'),

            'confirmed' => Tab::make('Confirmed')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) =>
                $query->where('event_guest.attendance_status', 'confirmed')),

            'declined' => Tab::make('Declined')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) =>
                $query->where('event_guest.attendance_status', 'declined')),

            'pending' => Tab::make('Awaiting Response')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) =>
                $query->where('event_guest.attendance_status', 'invited')),

            'fabric_ordered' => Tab::make('Fabric Ordered')
                ->icon('heroicon-o-shopping-bag')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereNotNull('gfs.id')
                        ->where('gfs.payment_status', 'paid')),

            'fabric_paid' => Tab::make('Paid Orders')
                ->icon('heroicon-o-banknotes')
                ->modifyQueryUsing(fn(Builder $query) =>
                $query->where('gfs.payment_status', 'paid')),

            'fabric_pending_payment' => Tab::make('Pending Payment')
                ->icon('heroicon-o-exclamation-circle')
                ->modifyQueryUsing(fn(Builder $query) =>
                $query->where('gfs.payment_status', 'pending')),
        ];
    }
}
