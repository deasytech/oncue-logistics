<?php

namespace App\Livewire\Delivery;

use App\Models\DeliveryService;
use App\Models\Delivery;
use App\Models\Event;
use App\Models\Guest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class DeliveryServices extends Component
{
  public $deliveryServices = [];
  public $selectedServiceId = null;
  public $showPaymentModal = false;
  public $showPostPaymentModal = false;
  public $showEndSessionModal = false;
  public $paymentMethod = 'online'; // Only online payment available
  public $deliveryCost = 0;
  public $selectedEventId = null;
  public $guestCount = 0;
  public $totalCost = 0;

  protected $rules = [
    'selectedServiceId' => 'required|exists:delivery_services,id',
    'paymentMethod' => 'required|in:online',
    'selectedEventId' => 'required|exists:events,id',
  ];

  protected $messages = [
    'selectedServiceId.required' => 'Please select a delivery service.',
    'selectedEventId.required' => 'Please select an event.',
  ];

  public function mount()
  {
    // Load all active delivery services
    $this->deliveryServices = DeliveryService::active()->get();

    // Check if post-payment modal should be shown
    if (session('show_post_payment_modal')) {
      $this->showPostPaymentModal = true;
    }
  }

  public function updatedSelectedServiceId($value)
  {
    if ($value) {
      $service = DeliveryService::find($value);
      $this->deliveryCost = $service ? $service->cost : 0;
      $this->calculateTotalCost();
    } else {
      $this->deliveryCost = 0;
      $this->totalCost = 0;
    }
  }

  public function updatedSelectedEventId($value)
  {
    if ($value) {
      // Use the many-to-many relationship to count guests for the event
      $this->guestCount = Guest::whereHas('events', function ($query) use ($value) {
        $query->where('events.id', $value);
      })->count();
      $this->calculateTotalCost();
    } else {
      $this->guestCount = 0;
      $this->totalCost = 0;
    }
  }

  private function calculateTotalCost()
  {
    $this->totalCost = $this->deliveryCost * $this->guestCount;
  }

  public function selectService()
  {
    $this->validate([
      'selectedServiceId' => 'required|exists:delivery_services,id',
    ]);

    $service = DeliveryService::find($this->selectedServiceId);

    if (!$service) {
      session()->flash('error', 'Invalid delivery service selected.');
      return;
    }

    // Check if service name contains "package" or "packaging" (case-insensitive)
    $serviceName = strtolower($service->name);
    if (str_contains($serviceName, 'package') || str_contains($serviceName, 'packaging')) {
      // Redirect to packages page
      return redirect()->route('packages.list')
        ->with('message', 'Package service selected. Please proceed with package selection.');
    } else {
      // Show payment modal for non-package services
      $this->showPaymentModal = true;
    }
  }

  public function processPayment()
  {
    try {
      // Validate that an event is selected
      $this->validate([
        'selectedEventId' => 'required|exists:events,id',
        'paymentMethod' => 'required|in:online',
      ]);

      $service = DeliveryService::find($this->selectedServiceId);
      $event = Event::find($this->selectedEventId);

      if (!$service) {
        session()->flash('error', 'Invalid delivery service selected.');
        return;
      }

      if (!$event) {
        session()->flash('error', 'Invalid event selected.');
        return;
      }

      // Verify the event belongs to the current customer
      $customer = Auth::user()->customer;
      if (!$customer || $event->customer_id !== $customer->id) {
        session()->flash('error', 'You do not have permission to select this event.');
        return;
      }

      // Calculate total cost based on guest count using the many-to-many relationship
      $guestCount = Guest::whereHas('events', function ($query) use ($event) {
        $query->where('events.id', $event->id);
      })->count();
      $totalCost = $service->cost * $guestCount;

      Log::info('DeliveryServices: Starting payment processing', [
        'selectedServiceId' => $this->selectedServiceId,
        'selectedEventId' => $this->selectedEventId,
        'guestCount' => $guestCount,
        'totalCost' => $totalCost,
        'paymentMethod' => $this->paymentMethod,
        'user_id' => Auth::id()
      ]);

      // Create delivery record with total cost
      $delivery = Delivery::create([
        'event_id' => $event->id,
        'delivery_service_id' => $this->selectedServiceId,
        'delivery_required' => true,
        'status' => 'pending',
        'cost' => $totalCost, // Use total cost instead of service cost
        'payment_status' => 'pending',
        'payment_method' => $this->paymentMethod,
      ]);

      Log::info('DeliveryServices: Delivery record created', [
        'delivery_id' => $delivery->id,
        'total_cost' => $totalCost,
        'guest_count' => $guestCount
      ]);

      // Close modal first, then redirect
      $this->showPaymentModal = false;

      // Create package payment record for tracking with total cost
      $reference = 'DEL_' . uniqid();
      $packagePayment = \App\Models\PackagePayment::create([
        'customer_id' => Auth::user()->customer->id,
        'reference' => $reference,
        'amount' => $totalCost * 100, // Paystack expects amount in kobo
        'status' => 'pending',
        'items' => [
          'type' => 'delivery_service',
          'delivery_id' => $delivery->id,
          'service_name' => $service->name,
          'guest_count' => $guestCount,
          'service_cost' => $service->cost,
          'event_name' => $event->name,
        ],
      ]);

      // Update delivery with payment reference
      $delivery->update([
        'payment_reference' => $reference,
      ]);

      Log::info('DeliveryServices: Package payment record created', [
        'reference' => $reference,
        'amount' => $totalCost * 100,
        'delivery_id' => $delivery->id,
        'guest_count' => $guestCount
      ]);

      try {
        $response = Http::withToken(config('services.paystack.secret'))
          ->timeout(30)
          ->connectTimeout(15)
          ->post('https://api.paystack.co/transaction/initialize', [
            'email' => Auth::user()->email,
            'amount' => $packagePayment->amount,
            'reference' => $packagePayment->reference,
            'callback_url' => route('payment.callback'),
          ]);

        $responseData = $response->json();

        if ($response->successful() && ($responseData['status'] ?? false)) {
          $authorizationUrl = $responseData['data']['authorization_url'];

          Log::info('DeliveryServices: Paystack initialized successfully', [
            'delivery_id' => $delivery->id,
            'reference' => $packagePayment->reference,
            'authorization_url' => $authorizationUrl,
            'total_amount' => $totalCost,
          ]);

          return redirect()->away($authorizationUrl);
        }

        Log::error('DeliveryServices: Failed to initialize Paystack payment', [
          'delivery_id' => $delivery->id,
          'reference' => $packagePayment->reference,
          'response' => $responseData,
        ]);

        session()->flash('error', 'Failed to initialize payment. Please try again.');
        return;
      } catch (\Exception $e) {
        Log::error('DeliveryServices: Paystack initialization exception', [
          'delivery_id' => $delivery->id,
          'reference' => $packagePayment->reference,
          'error' => $e->getMessage(),
        ]);

        session()->flash('error', 'Payment initialization failed. Please check your network connection and try again.');
        return;
      }
    } catch (\Exception $e) {
      Log::error('DeliveryServices: Payment processing failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);
      session()->flash('error', 'Payment processing failed: ' . $e->getMessage());
      return;
    }
  }

  private function initiatePaystackPayment($delivery)
  {
    try {
      $reference = 'DEL_' . uniqid();

      // Update delivery with payment reference
      $delivery->update([
        'payment_reference' => $reference,
      ]);

      // Create package payment record for tracking
      $packagePayment = \App\Models\PackagePayment::create([
        'customer_id' => Auth::user()->customer->id,
        'reference' => $reference,
        'amount' => $delivery->cost * 100, // Paystack expects amount in kobo
        'status' => 'pending',
        'items' => [
          'type' => 'delivery_service',
          'delivery_id' => $delivery->id,
          'service_name' => $delivery->deliveryService->name,
        ],
      ]);

      Log::info('DeliveryServices: Package payment record created', [
        'reference' => $reference,
        'amount' => $delivery->cost * 100,
        'delivery_id' => $delivery->id
      ]);

      // Build Paystack URL
      $paystackUrl = 'https://api.paystack.co/transaction/initialize?' . http_build_query([
        'email' => Auth::user()->email,
        'amount' => $delivery->cost * 100, // Amount in kobo
        'reference' => $reference,
        'callback_url' => route('payment.callback'),
      ]);

      Log::info('DeliveryServices: Redirecting to Paystack', ['url' => $paystackUrl]);

      // Redirect to Paystack
      return redirect()->away($paystackUrl);
    } catch (\Exception $e) {
      Log::error('DeliveryServices: Paystack payment initiation failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);
      session()->flash('error', 'Payment initiation failed: ' . $e->getMessage());
      return;
    }
  }

  public function cancelPayment()
  {
    $this->showPaymentModal = false;
  }

  public function selectPackaging()
  {
    $this->showPostPaymentModal = false;
    return redirect()->route('packages.list')
      ->with('message', 'Proceeding to package selection.');
  }

  public function endSession()
  {
    $this->showPostPaymentModal = false;
    $this->showEndSessionModal = true;
  }

  public function closeEndSessionModal()
  {
    $this->showEndSessionModal = false;
    return redirect()->route('dashboard');
  }

  public function render()
  {
    return view('livewire.delivery.delivery-services', [
      'deliveryServices' => $this->deliveryServices,
    ]);
  }
}
