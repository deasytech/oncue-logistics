<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class UpcomingEventsWidget extends Widget
{
  protected static string $view = 'filament.widgets.upcoming-events-widget';

  protected int | string | array $columnSpan = 'full';

  public function getUpcomingEvents(): Collection
  {
    return Event::with(['customer', 'category', 'subCategory', 'guests'])
      ->where('event_date', '>=', Carbon::today())
      ->orderBy('event_date', 'asc')
      ->limit(10)
      ->get();
  }

  public function getEventsByMonth(): array
  {
    $events = $this->getUpcomingEvents();
    $grouped = [];

    foreach ($events as $event) {
      $month = Carbon::parse($event->event_date)->format('F Y');
      if (!isset($grouped[$month])) {
        $grouped[$month] = [];
      }
      $grouped[$month][] = $event;
    }

    return $grouped;
  }

  public function getViewData(): array
  {
    return [
      'eventsByMonth' => $this->getEventsByMonth(),
      'totalUpcoming' => Event::where('event_date', '>=', Carbon::today())->count(),
    ];
  }
}
