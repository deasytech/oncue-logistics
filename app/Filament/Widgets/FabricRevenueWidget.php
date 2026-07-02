<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class FabricRevenueWidget extends Widget
{
  protected static string $view = 'filament.widgets.fabric-revenue-widget';

  protected static ?int $sort = 5;

  protected int | string | array $columnSpan = [
    'default' => 'full',
    'lg' => 1,
  ];

  public function getPaymentsByEvent(): Collection
  {
    return Event::with(['customer'])
      ->withSum(['guestFabricSelections' => function ($q) {
        $q->where('payment_status', 'paid');
      }], 'total_amount')
      ->withCount(['guestFabricSelections as paid_fabric_count' => function ($q) {
        $q->where('payment_status', 'paid');
      }])
      ->whereHas('guestFabricSelections', function ($q) {
        $q->where('payment_status', 'paid');
      })
      ->orderByDesc('guest_fabric_selections_sum_total_amount')
      ->limit(15)
      ->get();
  }

  public function getViewData(): array
  {
    return [
      'paymentsByEvent' => $this->getPaymentsByEvent(),
    ];
  }
}
