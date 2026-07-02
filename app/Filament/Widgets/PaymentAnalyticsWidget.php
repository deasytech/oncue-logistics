<?php

namespace App\Filament\Widgets;

use App\Models\GuestFabricSelection;
use App\Models\PackagePayment;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class PaymentAnalyticsWidget extends Widget
{
  protected static string $view = 'filament.widgets.payment-analytics-widget';

  protected static ?int $sort = 6;

  protected int | string | array $columnSpan = [
    'default' => 'full',
    'lg' => 1,
  ];

  public function getPaymentStats(): array
  {
    $totalCustomerPayments = PackagePayment::where('status', 'success')->sum('amount');
    $totalGuestFabricPayments = GuestFabricSelection::where('payment_status', 'paid')->sum('total_amount');
    $totalRevenue = $totalCustomerPayments + $totalGuestFabricPayments;

    $customerPaymentsCount = PackagePayment::where('status', 'success')->count();
    $guestFabricPaymentsCount = GuestFabricSelection::where('payment_status', 'paid')->count();

    $revenueThisMonth = PackagePayment::where('status', 'success')
      ->whereMonth('created_at', Carbon::now()->month)
      ->whereYear('created_at', Carbon::now()->year)
      ->sum('amount')
      + GuestFabricSelection::where('payment_status', 'paid')
      ->whereMonth('created_at', Carbon::now()->month)
      ->whereYear('created_at', Carbon::now()->year)
      ->sum('total_amount');

    return [
      'total_revenue' => $totalRevenue,
      'total_customer_payments' => $totalCustomerPayments,
      'total_guest_fabric_payments' => $totalGuestFabricPayments,
      'customer_payments_count' => $customerPaymentsCount,
      'guest_fabric_payments_count' => $guestFabricPaymentsCount,
      'revenue_this_month' => $revenueThisMonth,
      'total_transactions' => $customerPaymentsCount + $guestFabricPaymentsCount,
    ];
  }

  public function getRecentPayments(int $limit = 10): Collection
  {
    $customerPayments = PackagePayment::with(['customer.events'])
      ->where('status', 'success')
      ->latest()
      ->limit($limit)
      ->get()
      ->map(fn($p) => (object) [
        'payment_type' => 'customer',
        'name'         => $p->customer
          ? trim($p->customer->title . ' ' . $p->customer->first_name . ' ' . $p->customer->last_name)
          : '—',
        'event'        => $p->customer?->events->first()?->name ?? '—',
        'amount'       => $p->amount,
        'created_at'   => $p->created_at,
      ]);

    $fabricPayments = GuestFabricSelection::with(['guest', 'event'])
      ->where('payment_status', 'paid')
      ->latest()
      ->limit($limit)
      ->get()
      ->map(fn($p) => (object) [
        'payment_type' => 'guest_fabric',
        'name'         => $p->guest
          ? trim($p->guest->first_name . ' ' . $p->guest->last_name)
          : '—',
        'event'        => $p->event?->name ?? '—',
        'amount'       => $p->total_amount,
        'created_at'   => $p->created_at,
      ]);

    return $customerPayments
      ->concat($fabricPayments)
      ->sortByDesc('created_at')
      ->take($limit)
      ->values();
  }

  public function getViewData(): array
  {
    return [
      'paymentStats'   => $this->getPaymentStats(),
      'recentPayments' => $this->getRecentPayments(),
    ];
  }
}
