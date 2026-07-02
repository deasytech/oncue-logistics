<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Event;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerStatsWidget extends BaseWidget
{
  protected static ?int $sort = 2;

  protected static ?string $pollingInterval = '60s';

  protected function getStats(): array
  {
    $totalCustomers = Customer::count();
    $activeCustomers = Customer::where('is_active', true)->count();
    $activePercentage = $totalCustomers > 0
      ? round(($activeCustomers / $totalCustomers) * 100, 1)
      : 0;

    $newThisMonth = Customer::whereMonth('created_at', now()->month)
      ->whereYear('created_at', now()->year)->count();
    $lastMonthDate = Carbon::now()->subMonth();
    $newLastMonth = Customer::whereMonth('created_at', $lastMonthDate->month)
      ->whereYear('created_at', $lastMonthDate->year)->count();
    $growthRate = $newLastMonth > 0
      ? round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100, 1)
      : ($newThisMonth > 0 ? 100 : 0);

    $totalEvents = Event::count();
    $avgEvents = $totalCustomers > 0
      ? round($totalEvents / $totalCustomers, 1)
      : 0;

    // Sparklines: last 7 months
    $totalChart = [];
    $activeChart = [];
    $newChart = [];

    for ($i = 6; $i >= 0; $i--) {
      $date = Carbon::now()->subMonths($i);
      $totalChart[] = Customer::whereMonth('created_at', $date->month)
        ->whereYear('created_at', $date->year)->count();
      $activeChart[] = Customer::where('is_active', true)
        ->whereMonth('created_at', $date->month)
        ->whereYear('created_at', $date->year)->count();
      $newChart[] = Customer::whereMonth('created_at', $date->month)
        ->whereYear('created_at', $date->year)->count();
    }

    $growthPositive = $growthRate >= 0;

    return [
      Stat::make('Total Customers', number_format($totalCustomers))
        ->description($activePercentage . '% are active')
        ->descriptionIcon('heroicon-m-user-group')
        ->color('info')
        ->icon('heroicon-o-user-group')
        ->chart($totalChart)
        ->url(url('/admin/customers')),

      Stat::make('Active Customers', number_format($activeCustomers))
        ->description($activePercentage . '% of total')
        ->descriptionIcon('heroicon-m-check-circle')
        ->color('success')
        ->icon('heroicon-o-check-circle')
        ->chart($activeChart)
        ->url(url('/admin/customers')),

      Stat::make('New This Month', number_format($newThisMonth))
        ->description(($growthPositive ? '+' : '') . $growthRate . '% vs last month')
        ->descriptionIcon($growthPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
        ->color($growthPositive ? 'success' : 'danger')
        ->icon('heroicon-o-user-plus')
        ->chart($newChart)
        ->url(url('/admin/customers')),

      Stat::make('Avg Events / Customer', $avgEvents)
        ->description(number_format($totalEvents) . ' total events')
        ->descriptionIcon('heroicon-m-calendar-days')
        ->color('warning')
        ->icon('heroicon-o-chart-bar')
        ->url(url('/admin/events')),
    ];
  }
}
