<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\GuestFabricSelection;
use App\Models\PackagePayment;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PaymentAnalyticsWidget extends Widget
{
  protected static string $view = 'filament.widgets.payment-analytics-widget';

  protected int | string | array $columnSpan = 'full';

  protected static ?int $sort = 2;

  public function getPaymentStats(): array
  {
    $totalCustomerPayments = PackagePayment::where('status', 'success')->sum('amount');
    $totalGuestFabricPayments = GuestFabricSelection::where('payment_status', 'paid')->sum('total_amount');
    $totalRevenue = $totalCustomerPayments + $totalGuestFabricPayments;

    $customerPaymentsCount = PackagePayment::where('status', 'success')->count();
    $guestFabricPaymentsCount = GuestFabricSelection::where('payment_status', 'paid')->count();

    return [
      'total_revenue' => $totalRevenue,
      'total_customer_payments' => $totalCustomerPayments,
      'total_guest_fabric_payments' => $totalGuestFabricPayments,
      'customer_payments_count' => $customerPaymentsCount,
      'guest_fabric_payments_count' => $guestFabricPaymentsCount,
    ];
  }

  public function getPaymentsByEvent($eventId = null): Collection
  {
    $query = Event::with(['customer'])
      ->withSum(['guestFabricSelections' => function ($query) {
        $query->where('payment_status', 'paid');
      }], 'total_amount');

    if ($eventId) {
      $query->where('id', $eventId);
    }

    return $query->get();
  }

  public function getRecentPayments($limit = 10): Collection
  {
    $customerPayments = PackagePayment::with(['customer'])
      ->where('status', 'success')
      ->select([
        'package_payments.id',
        'package_payments.customer_id',
        'package_payments.reference',
        'package_payments.amount',
        'package_payments.status',
        'package_payments.created_at',
        'package_payments.updated_at',
        DB::raw("'customer' as payment_type"),
        DB::raw("NULL as event_id"),
        DB::raw("NULL as guest_id"),
        DB::raw("NULL as total_amount"),
        DB::raw("NULL as payment_status")
      ])
      ->latest();

    $guestFabricPayments = GuestFabricSelection::with(['guest', 'event'])
      ->where('payment_status', 'paid')
      ->select([
        'guest_fabric_selections.id',
        DB::raw("NULL as customer_id"),
        DB::raw("NULL as reference"),
        'guest_fabric_selections.total_amount as amount',
        DB::raw("'paid' as status"),
        'guest_fabric_selections.created_at',
        'guest_fabric_selections.updated_at',
        DB::raw("'guest_fabric' as payment_type"),
        'guest_fabric_selections.event_id',
        'guest_fabric_selections.guest_id',
        'guest_fabric_selections.total_amount',
        'guest_fabric_selections.payment_status'
      ])
      ->latest();

    return $customerPayments->union($guestFabricPayments)
      ->orderBy('created_at', 'desc')
      ->limit($limit)
      ->get();
  }

  public function getEventsForFilter(): Collection
  {
    return Event::select('id', 'name')
      ->whereHas('guestFabricSelections', function ($query) {
        $query->where('payment_status', 'paid');
      })
      ->orderBy('name')
      ->get();
  }

  public function getViewData(): array
  {
    return [
      'paymentStats' => $this->getPaymentStats(),
      'eventsForFilter' => $this->getEventsForFilter(),
      'recentPayments' => $this->getRecentPayments(),
    ];
  }
}
