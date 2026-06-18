<?php

namespace App\Livewire\Dashboard;

use App\Models\Delivery;
use App\Models\Event;
use App\Models\Guest;
use App\Models\GuestFabricSelection;
use App\Models\PackagePayment;
use App\Models\PaymentReceipt;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RecentActivity extends Component
{
  public Collection $activities;

  public function mount(): void
  {
    $this->loadActivities();
  }

  public function loadActivities(): void
  {
    $customerId = Auth::user()?->customer?->id;

    if (!$customerId) {
      $this->activities = collect();
      return;
    }

    $activities = collect()
      ->merge($this->getEventActivities($customerId))
      ->merge($this->getGuestActivities($customerId))
      ->merge($this->getPackagePaymentActivities($customerId))
      ->merge($this->getFabricPaymentActivities($customerId))
      ->merge($this->getReceiptActivities($customerId))
      ->merge($this->getDeliveryActivities($customerId))
      ->sortByDesc('timestamp')
      ->take(10)
      ->values();

    $this->activities = $activities;
  }

  private function getEventActivities(int $customerId): Collection
  {
    return Event::where('customer_id', $customerId)
      ->latest('updated_at')
      ->limit(10)
      ->get()
      ->flatMap(function (Event $event) {
        $activities = [[
          'action' => 'Created event',
          'item' => $event->name,
          'time' => $event->created_at->diffForHumans(),
          'timestamp' => $event->created_at,
          'icon' => 'calendar',
          'color' => 'blue',
        ]];

        if ($event->updated_at && $event->updated_at->ne($event->created_at)) {
          $activities[] = [
            'action' => 'Updated event',
            'item' => $event->name,
            'time' => $event->updated_at->diffForHumans(),
            'timestamp' => $event->updated_at,
            'icon' => 'pencil-square',
            'color' => 'indigo',
          ];
        }

        return $activities;
      });
  }

  private function getGuestActivities(int $customerId): Collection
  {
    return Guest::where('customer_id', $customerId)
      ->latest('updated_at')
      ->limit(10)
      ->get()
      ->flatMap(function (Guest $guest) {
        $activities = [[
          'action' => 'Added guest',
          'item' => $guest->full_name,
          'time' => $guest->created_at->diffForHumans(),
          'timestamp' => $guest->created_at,
          'icon' => 'user-plus',
          'color' => 'green',
        ]];

        if ($guest->updated_at && $guest->updated_at->ne($guest->created_at)) {
          $activities[] = [
            'action' => 'Updated guest',
            'item' => $guest->full_name,
            'time' => $guest->updated_at->diffForHumans(),
            'timestamp' => $guest->updated_at,
            'icon' => 'user',
            'color' => 'emerald',
          ];
        }

        return $activities;
      });
  }

  private function getPackagePaymentActivities(int $customerId): Collection
  {
    return PackagePayment::where('customer_id', $customerId)
      ->where('status', 'completed')
      ->latest('updated_at')
      ->limit(10)
      ->get()
      ->map(function (PackagePayment $payment) {
        return [
          'action' => 'Received package payment',
          'item' => '₦' . number_format($payment->amount / 100, 2),
          'time' => $payment->updated_at->diffForHumans(),
          'timestamp' => $payment->updated_at,
          'icon' => 'banknotes',
          'color' => 'yellow',
        ];
      });
  }

  private function getFabricPaymentActivities(int $customerId): Collection
  {
    return GuestFabricSelection::with(['guest', 'event'])
      ->whereHas('event', function ($query) use ($customerId) {
        $query->where('customer_id', $customerId);
      })
      ->where('payment_status', 'paid')
      ->latest('paid_at')
      ->limit(10)
      ->get()
      ->map(function (GuestFabricSelection $selection) {
        $guestName = $selection->guest?->full_name ?: 'Guest';
        $eventName = $selection->event?->name ?: 'event';

        return [
          'action' => 'Received fabric payment',
          'item' => $guestName . ' for ' . $eventName . ' (₦' . number_format($selection->total_amount, 2) . ')',
          'time' => ($selection->paid_at ?? $selection->updated_at)->diffForHumans(),
          'timestamp' => $selection->paid_at ?? $selection->updated_at,
          'icon' => 'currency-dollar',
          'color' => 'amber',
        ];
      });
  }

  private function getReceiptActivities(int $customerId): Collection
  {
    return PaymentReceipt::where('customer_id', $customerId)
      ->latest('updated_at')
      ->limit(10)
      ->get()
      ->flatMap(function (PaymentReceipt $receipt) {
        $activities = [[
          'action' => 'Uploaded payment receipt',
          'item' => $receipt->original_name,
          'time' => $receipt->created_at->diffForHumans(),
          'timestamp' => $receipt->created_at,
          'icon' => 'document-arrow-up',
          'color' => 'purple',
        ]];

        if ($receipt->status === 'approved') {
          $activities[] = [
            'action' => 'Payment receipt approved',
            'item' => $receipt->original_name,
            'time' => $receipt->updated_at->diffForHumans(),
            'timestamp' => $receipt->updated_at,
            'icon' => 'check-circle',
            'color' => 'green',
          ];
        } elseif ($receipt->status === 'rejected') {
          $activities[] = [
            'action' => 'Payment receipt rejected',
            'item' => $receipt->original_name,
            'time' => $receipt->updated_at->diffForHumans(),
            'timestamp' => $receipt->updated_at,
            'icon' => 'x-circle',
            'color' => 'red',
          ];
        }

        return $activities;
      });
  }

  private function getDeliveryActivities(int $customerId): Collection
  {
    return Delivery::with(['event', 'deliveryService'])
      ->whereHas('event', function ($query) use ($customerId) {
        $query->where('customer_id', $customerId);
      })
      ->latest('updated_at')
      ->limit(10)
      ->get()
      ->flatMap(function (Delivery $delivery) {
        $eventName = $delivery->event?->name ?: 'event';
        $serviceName = $delivery->deliveryService?->name ?: 'delivery service';
        $activities = [];

        if ($delivery->created_at) {
          $activities[] = [
            'action' => 'Configured delivery',
            'item' => $serviceName . ' for ' . $eventName,
            'time' => $delivery->created_at->diffForHumans(),
            'timestamp' => $delivery->created_at,
            'icon' => 'truck',
            'color' => 'sky',
          ];
        }

        if ($delivery->payment_status === 'paid' && $delivery->paid_at) {
          $activities[] = [
            'action' => 'Paid for delivery',
            'item' => $eventName . ' (₦' . number_format((float) $delivery->cost, 2) . ')',
            'time' => $delivery->paid_at->diffForHumans(),
            'timestamp' => $delivery->paid_at,
            'icon' => 'truck',
            'color' => 'cyan',
          ];
        }

        return $activities;
      });
  }

  public function render()
  {
    return view('livewire.dashboard.recent-activity');
  }
}
