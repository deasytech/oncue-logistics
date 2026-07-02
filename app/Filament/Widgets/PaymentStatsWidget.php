<?php

namespace App\Filament\Widgets;

use App\Models\GuestFabricSelection;
use App\Models\PackagePayment;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PaymentStatsWidget extends BaseWidget
{
  protected static ?int $sort = 4;

  protected static ?string $pollingInterval = '60s';

  protected function getStats(): array
  {
    $totalRevenue = PackagePayment::where('status', 'success')->sum('amount')
      + GuestFabricSelection::where('payment_status', 'paid')->sum('total_amount');

    $revenueThisMonth = PackagePayment::where('status', 'success')
      ->whereMonth('created_at', now()->month)
      ->whereYear('created_at', now()->year)
      ->sum('amount')
      + GuestFabricSelection::where('payment_status', 'paid')
      ->whereMonth('created_at', now()->month)
      ->whereYear('created_at', now()->year)
      ->sum('total_amount');

    $lastMonthDate = Carbon::now()->subMonth();
    $revenueLastMonth = PackagePayment::where('status', 'success')
      ->whereMonth('created_at', $lastMonthDate->month)
      ->whereYear('created_at', $lastMonthDate->year)
      ->sum('amount')
      + GuestFabricSelection::where('payment_status', 'paid')
      ->whereMonth('created_at', $lastMonthDate->month)
      ->whereYear('created_at', $lastMonthDate->year)
      ->sum('total_amount');

    $monthlyGrowth = $revenueLastMonth > 0
      ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
      : ($revenueThisMonth > 0 ? 100 : 0);

    $totalPackagePayments = PackagePayment::where('status', 'success')->sum('amount');
    $packageCount = PackagePayment::where('status', 'success')->count();

    $totalFabricPayments = GuestFabricSelection::where('payment_status', 'paid')->sum('total_amount');
    $fabricCount = GuestFabricSelection::where('payment_status', 'paid')->count();

    $totalTransactions = $packageCount + $fabricCount;
    $avgOrder = $totalTransactions > 0 ? round($totalRevenue / $totalTransactions, 2) : 0;

    // Sparklines: last 7 months revenue
    $revenueChart = [];
    $packageChart = [];
    $fabricChart = [];

    for ($i = 6; $i >= 0; $i--) {
      $date = Carbon::now()->subMonths($i);
      $revenueChart[] = (int) (PackagePayment::where('status', 'success')
        ->whereMonth('created_at', $date->month)
        ->whereYear('created_at', $date->year)
        ->sum('amount')
        + GuestFabricSelection::where('payment_status', 'paid')
        ->whereMonth('created_at', $date->month)
        ->whereYear('created_at', $date->year)
        ->sum('total_amount'));

      $packageChart[] = (int) PackagePayment::where('status', 'success')
        ->whereMonth('created_at', $date->month)
        ->whereYear('created_at', $date->year)
        ->sum('amount');

      $fabricChart[] = (int) GuestFabricSelection::where('payment_status', 'paid')
        ->whereMonth('created_at', $date->month)
        ->whereYear('created_at', $date->year)
        ->sum('total_amount');
    }

    $monthlyTrend = $monthlyGrowth >= 0;

    return [
      Stat::make('Total Revenue', '₦' . number_format($totalRevenue, 2))
        ->description(number_format($totalTransactions) . ' total transactions')
        ->descriptionIcon('heroicon-m-banknotes')
        ->color('success')
        ->icon('heroicon-o-banknotes')
        ->chart($revenueChart)
        ->url(url('/admin/package-payments')),

      Stat::make('Revenue This Month', '₦' . number_format($revenueThisMonth, 2))
        ->description(($monthlyTrend ? '+' : '') . $monthlyGrowth . '% vs last month')
        ->descriptionIcon($monthlyTrend ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
        ->color($monthlyTrend ? 'success' : 'danger')
        ->icon('heroicon-o-calendar')
        ->chart($revenueChart)
        ->url(url('/admin/package-payments')),

      Stat::make('Package Payments', '₦' . number_format($totalPackagePayments, 2))
        ->description(number_format($packageCount) . ' transactions')
        ->descriptionIcon('heroicon-m-gift')
        ->color('info')
        ->icon('heroicon-o-gift')
        ->chart($packageChart)
        ->url(url('/admin/package-payments')),

      Stat::make('Fabric Sales', '₦' . number_format($totalFabricPayments, 2))
        ->description(number_format($fabricCount) . ' sales')
        ->descriptionIcon('heroicon-m-swatch')
        ->color('warning')
        ->icon('heroicon-o-swatch')
        ->chart($fabricChart)
        ->url(url('/admin/guest-orders')),

      Stat::make('Avg Order Value', '₦' . number_format($avgOrder, 2))
        ->description('Per transaction')
        ->descriptionIcon('heroicon-m-calculator')
        ->color('primary')
        ->icon('heroicon-o-calculator')
        ->url(url('/admin/package-payments')),
    ];
  }
}
