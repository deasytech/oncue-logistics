<?php

namespace App\Livewire\Dashboard;

use App\Models\Event;
use App\Models\Guest;
use App\Models\GuestFabricSelection;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardStats extends Component
{
  public $totalEvents;
  public $totalGuests;
  public $activePackages;
  public $totalRevenue;
  public $eventsGrowth;
  public $guestsGrowth;
  public $revenueGrowth;
  public $activities;

  public function mount()
  {
    $this->loadDashboardStats();
  }

  public function loadDashboardStats()
  {
    $user = Auth::user();

    // Get current month's data
    $currentMonth = now()->startOfMonth();
    $lastMonth = now()->subMonth()->startOfMonth();

    // Get user's customer ID
    $customerId = $user->customer->id ?? null;

    if (!$customerId) {
      // User has no customer profile yet
      $this->totalEvents = 0;
      $this->eventsGrowth = 0;
      $this->totalGuests = 0;
      $this->guestsGrowth = 0;
      $this->activePackages = Package::where('is_active', true)->count();
      $this->totalRevenue = 0;
      $this->revenueGrowth = 0;
      return;
    }

    $this->activities = [['action' => 'New guest added to', 'item' => 'Sarah & John Wedding', 'time' => '2 hours ago', 'icon' => 'user-plus', 'color' => 'blue'], ['action' => 'Package customized for', 'item' => 'Corporate Annual Dinner', 'time' => '4 hours ago', 'icon' => 'shopping-bag', 'color' => 'purple'], ['action' => 'Event status updated to', 'item' => 'Confirmed', 'time' => '6 hours ago', 'icon' => 'check-circle', 'color' => 'green'], ['action' => 'Payment received for', 'item' => 'Birthday Celebration', 'time' => '1 day ago', 'icon' => 'currency-dollar', 'color' => 'yellow']];

    // Total Events (created by the user's customer)
    $this->totalEvents = Event::where('customer_id', $customerId)->count();

    // Events growth percentage
    $currentMonthEvents = Event::where('customer_id', $customerId)
      ->where('created_at', '>=', $currentMonth)
      ->count();
    $lastMonthEvents = Event::where('customer_id', $customerId)
      ->where('created_at', '>=', $lastMonth)
      ->where('created_at', '<', $currentMonth)
      ->count();

    $this->eventsGrowth = $lastMonthEvents > 0
      ? round((($currentMonthEvents - $lastMonthEvents) / $lastMonthEvents) * 100)
      : 0;

    // Total Guests (for user's customer - guests belong to customers)
    $this->totalGuests = Guest::where('customer_id', $customerId)->count();

    // Guests growth percentage (based on guest creation date)
    $currentMonthGuests = Guest::where('customer_id', $customerId)
      ->where('created_at', '>=', $currentMonth)
      ->count();
    $lastMonthGuests = Guest::where('customer_id', $customerId)
      ->where('created_at', '>=', $lastMonth)
      ->where('created_at', '<', $currentMonth)
      ->count();

    $this->guestsGrowth = $lastMonthGuests > 0
      ? round((($currentMonthGuests - $lastMonthGuests) / $lastMonthGuests) * 100)
      : 0;

    // Active Packages (packages with active status)
    $this->activePackages = Package::where('is_active', true)->count();

    // Total revenue received from fabric payments for the logged-in customer's events
    $fabricPaymentsQuery = GuestFabricSelection::whereHas('event', function ($query) use ($customerId) {
      $query->where('customer_id', $customerId);
    })->where('payment_status', 'paid');

    $this->totalRevenue = $fabricPaymentsQuery->sum('total_amount');

    // Revenue growth percentage
    $currentMonthRevenue = GuestFabricSelection::whereHas('event', function ($query) use ($customerId) {
      $query->where('customer_id', $customerId);
    })
      ->where('payment_status', 'paid')
      ->where('created_at', '>=', $currentMonth)
      ->sum('total_amount');

    $lastMonthRevenue = GuestFabricSelection::whereHas('event', function ($query) use ($customerId) {
      $query->where('customer_id', $customerId);
    })
      ->where('payment_status', 'paid')
      ->where('created_at', '>=', $lastMonth)
      ->where('created_at', '<', $currentMonth)
      ->sum('total_amount');

    $this->revenueGrowth = $lastMonthRevenue > 0
      ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
      : 0;
  }

  public function render()
  {
    return view('livewire.dashboard.dashboard-stats');
  }
}
