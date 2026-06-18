<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Guest;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RsvpStatusWidget extends Widget
{
  protected static string $view = 'filament.widgets.rsvp-status-widget';

  protected int | string | array $columnSpan = 'full';

  public function getRsvpStats(): array
  {
    $totalGuests = Guest::count();
    $respondedGuests = DB::table('event_guest')
      ->whereNotNull('rsvp_responded_at')
      ->count();

    $pendingGuests = $totalGuests - $respondedGuests;
    $responseRate = $totalGuests > 0 ? round(($respondedGuests / $totalGuests) * 100, 1) : 0;

    return [
      'total_guests' => $totalGuests,
      'responded' => $respondedGuests,
      'pending' => $pendingGuests,
      'response_rate' => $responseRate,
    ];
  }

  public function getRecentRsvpResponses(): \Illuminate\Support\Collection
  {
    return DB::table('event_guest')
      ->join('guests', 'event_guest.guest_id', '=', 'guests.id')
      ->join('events', 'event_guest.event_id', '=', 'events.id')
      ->join('customers', 'events.customer_id', '=', 'customers.id')
      ->whereNotNull('event_guest.rsvp_responded_at')
      ->select(
        'guests.first_name',
        'guests.last_name',
        'guests.email',
        'events.name as event_name',
        'event_guest.attendance_status',
        'event_guest.rsvp_responded_at',
        'customers.first_name as customer_first_name',
        'customers.last_name as customer_last_name'
      )
      ->orderBy('event_guest.rsvp_responded_at', 'desc')
      ->limit(10)
      ->get();
  }

  public function getUpcomingEventsWithRsvp(): Collection
  {
    return Event::with(['guests', 'customer'])
      ->where('event_date', '>=', Carbon::today())
      ->whereHas('guests')
      ->withCount(['guests as total_guests', 'guests as responded_guests' => function ($query) {
        $query->whereNotNull('event_guest.rsvp_responded_at');
      }])
      ->orderBy('event_date', 'asc')
      ->limit(5)
      ->get();
  }

  public function getViewData(): array
  {
    return [
      'rsvpStats' => $this->getRsvpStats(),
      'recentResponses' => $this->getRecentRsvpResponses(),
      'upcomingEvents' => $this->getUpcomingEventsWithRsvp(),
    ];
  }
}
