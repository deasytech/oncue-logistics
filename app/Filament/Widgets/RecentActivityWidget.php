<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Event;
use App\Models\Guest;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class RecentActivityWidget extends Widget
{
  protected static string $view = 'filament.widgets.recent-activity-widget';

  protected int | string | array $columnSpan = 'full';

  public function getRecentCustomers(): Collection
  {
    return Customer::with('user')
      ->latest()
      ->limit(5)
      ->get();
  }

  public function getRecentEvents(): Collection
  {
    return Event::with(['customer', 'category', 'subCategory'])
      ->latest()
      ->limit(5)
      ->get();
  }

  public function getRecentGuests(): Collection
  {
    return Guest::with(['customer', 'events'])
      ->latest()
      ->limit(5)
      ->get();
  }

  public function getViewData(): array
  {
    return [
      'recentCustomers' => $this->getRecentCustomers(),
      'recentEvents' => $this->getRecentEvents(),
      'recentGuests' => $this->getRecentGuests(),
    ];
  }
}
