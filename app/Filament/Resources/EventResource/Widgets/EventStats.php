<?php

namespace App\Filament\Resources\EventResource\Widgets;

use App\Models\Customer;
use App\Models\Event;
use App\Models\Guest;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make("Total Events", Event::query()->count())
                ->color('success')
                ->icon('heroicon-o-calendar-days'),
            Stat::make("Total Customers", Customer::query()->count())
                ->color('info')
                ->icon('heroicon-o-user-group'),
            Stat::make("Total Users", User::query()->count())
                ->color('gray')
                ->icon('heroicon-o-users'),
            Stat::make("Total Guest List", Guest::query()->count())
                ->color('primary')
                ->icon('heroicon-o-list-bullet'),
        ];
    }
}
