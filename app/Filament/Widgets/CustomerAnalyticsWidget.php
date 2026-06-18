<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Event;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CustomerAnalyticsWidget extends Widget
{
  protected static string $view = 'filament.widgets.customer-analytics-widget';

  protected int | string | array $columnSpan = 'full';

  public function getCustomerStats(): array
  {
    $totalCustomers = Customer::count();
    $activeCustomers = Customer::where('is_active', true)->count();
    $newThisMonth = Customer::whereMonth('created_at', Carbon::now()->month)
      ->whereYear('created_at', Carbon::now()->year)
      ->count();
    $newLastMonth = Customer::whereMonth('created_at', Carbon::now()->subMonth()->month)
      ->whereYear('created_at', Carbon::now()->subMonth()->year)
      ->count();

    $growthRate = $newLastMonth > 0 ? round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100, 1) : 0;

    return [
      'total' => $totalCustomers,
      'active' => $activeCustomers,
      'new_this_month' => $newThisMonth,
      'growth_rate' => $growthRate,
      'active_percentage' => $totalCustomers > 0 ? round(($activeCustomers / $totalCustomers) * 100, 1) : 0,
    ];
  }

  public function getTopCustomersByEvents(): Collection
  {
    return Customer::withCount('events')
      ->having('events_count', '>', 0)
      ->orderBy('events_count', 'desc')
      ->limit(10)
      ->get();
  }

  public function getCustomerGrowthByMonth(): array
  {
    $growthData = [];
    $months = 6;

    for ($i = $months - 1; $i >= 0; $i--) {
      $date = Carbon::now()->subMonths($i);
      $count = Customer::whereMonth('created_at', $date->month)
        ->whereYear('created_at', $date->year)
        ->count();

      $growthData[] = [
        'month' => $date->format('M Y'),
        'count' => $count,
      ];
    }

    return $growthData;
  }

  public function getRecentActiveCustomers(): Collection
  {
    return Customer::with(['user', 'events'])
      ->where('is_active', true)
      ->latest()
      ->limit(5)
      ->get();
  }

  public function getViewData(): array
  {
    return [
      'customerStats' => $this->getCustomerStats(),
      'topCustomers' => $this->getTopCustomersByEvents(),
      'growthData' => $this->getCustomerGrowthByMonth(),
      'recentActiveCustomers' => $this->getRecentActiveCustomers(),
    ];
  }
}
