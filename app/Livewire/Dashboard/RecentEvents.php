<?php

namespace App\Livewire\Dashboard;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RecentEvents extends Component
{
  public $recentEvents;

  public function mount()
  {
    $this->loadRecentEvents();
  }

  public function loadRecentEvents()
  {
    $user = Auth::user();

    // Get user's customer ID
    $customerId = $user->customer->id ?? null;

    if (!$customerId) {
      $this->recentEvents = collect(); // Empty collection if no customer profile
      return;
    }

    $this->recentEvents = Event::with(['category', 'guests'])
      ->where('customer_id', $customerId)
      ->where('event_date', '>=', now())
      ->orderBy('event_date', 'asc')
      ->limit(4)
      ->get()
      ->map(function ($event) {
        return [
          'id' => $event->id,
          'name' => $event->name,
          'date' => $event->event_date->format('Y-m-d'),
          'guests' => $event->guests->count(),
          'status' => $this->getEventStatus($event),
          'location' => $event->location
        ];
      });
  }

  private function getEventStatus($event)
  {
    if ($event->is_active && $event->event_date->isFuture()) {
      return 'confirmed';
    } elseif (!$event->is_active) {
      return 'pending';
    } else {
      return 'planning';
    }
  }

  public function render()
  {
    return view('livewire.dashboard.recent-events');
  }
}
