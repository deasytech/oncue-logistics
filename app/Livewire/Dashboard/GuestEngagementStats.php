<?php

namespace App\Livewire\Dashboard;

use App\Models\GuestFabricSelection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class GuestEngagementStats extends Component
{
  public int $totalRsvpSent = 0;
  public int $totalAccepted = 0;
  public int $totalDeclined = 0;
  public int $totalFabricPaid = 0;
  public int $totalGuestsInvited = 0;

  public function mount(): void
  {
    $this->loadStats();
  }

  public function loadStats(): void
  {
    $customerId = Auth::user()?->customer?->id;

    if (!$customerId) {
      return;
    }

    $eventGuestQuery = DB::table('event_guest')
      ->join('events', 'event_guest.event_id', '=', 'events.id')
      ->where('events.customer_id', $customerId);

    $this->totalGuestsInvited = (clone $eventGuestQuery)->count();

    $this->totalRsvpSent = (clone $eventGuestQuery)
      ->whereNotNull('event_guest.rsvp_sent_at')
      ->count();

    $this->totalAccepted = (clone $eventGuestQuery)
      ->where('event_guest.attendance_status', 'confirmed')
      ->count();

    $this->totalDeclined = (clone $eventGuestQuery)
      ->where('event_guest.attendance_status', 'declined')
      ->count();

    $this->totalFabricPaid = GuestFabricSelection::whereHas('event', function ($query) use ($customerId) {
      $query->where('customer_id', $customerId);
    })
      ->where('payment_status', 'paid')
      ->distinct('guest_id')
      ->count('guest_id');
  }

  public function getFabricPaidPercentageProperty(): int
  {
    if ($this->totalGuestsInvited === 0) {
      return 0;
    }

    return (int) round(($this->totalFabricPaid / $this->totalGuestsInvited) * 100);
  }

  public function render()
  {
    return view('livewire.dashboard.guest-engagement-stats');
  }
}
