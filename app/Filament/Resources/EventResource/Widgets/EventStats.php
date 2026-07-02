<?php

namespace App\Filament\Resources\EventResource\Widgets;

use App\Models\Customer;
use App\Models\Event;
use App\Models\Guest;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventStats extends BaseWidget
{
    protected static ?int $sort = 0;

    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $totalEvents = Event::count();
        $eventsThisMonth = Event::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $lastMonthDate = Carbon::now()->subMonth();
        $eventsLastMonth = Event::whereMonth('created_at', $lastMonthDate->month)
            ->whereYear('created_at', $lastMonthDate->year)->count();
        $upcomingEvents = Event::where('event_date', '>=', Carbon::today())->count();

        $totalCustomers = Customer::count();
        $newCustomersThisMonth = Customer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $newCustomersLastMonth = Customer::whereMonth('created_at', $lastMonthDate->month)
            ->whereYear('created_at', $lastMonthDate->year)->count();
        $customerGrowth = $newCustomersLastMonth > 0
            ? round((($newCustomersThisMonth - $newCustomersLastMonth) / $newCustomersLastMonth) * 100, 1)
            : ($newCustomersThisMonth > 0 ? 100 : 0);

        $totalGuests = Guest::count();
        $guestsThisMonth = Guest::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        // Sparkline: last 7 months
        $eventChart = [];
        $customerChart = [];
        $guestChart = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $eventChart[] = Event::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)->count();
            $customerChart[] = Customer::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)->count();
            $guestChart[] = Guest::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)->count();
        }

        $eventTrend = $eventsThisMonth >= $eventsLastMonth;
        $customerTrend = $customerGrowth >= 0;

        return [
            Stat::make('Total Events', number_format($totalEvents))
                ->description($eventsThisMonth . ' new this month')
                ->descriptionIcon($eventTrend ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($eventTrend ? 'success' : 'warning')
                ->icon('heroicon-o-calendar-days')
                ->chart($eventChart)
                ->url(url('/admin/events')),

            Stat::make('Upcoming Events', number_format($upcomingEvents))
                ->description('Scheduled ahead')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info')
                ->icon('heroicon-o-calendar')
                ->url(url('/admin/events')),

            Stat::make('Total Customers', number_format($totalCustomers))
                ->description(($customerTrend ? '+' : '') . $customerGrowth . '% vs last month')
                ->descriptionIcon($customerTrend ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($customerTrend ? 'success' : 'danger')
                ->icon('heroicon-o-user-group')
                ->chart($customerChart)
                ->url(url('/admin/customers')),

            Stat::make('Total Guests', number_format($totalGuests))
                ->description($guestsThisMonth . ' added this month')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary')
                ->icon('heroicon-o-list-bullet')
                ->chart($guestChart)
                ->url(url('/admin/guests')),
        ];
    }
}
