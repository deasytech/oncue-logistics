<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Preview - {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Order Preview</h2>
        <p class="text-center text-gray-600 mb-6">Please review your order details before proceeding to payment</p>

        @if (session('error'))
            <div class="mb-4 bg-red-100 text-red-700 text-center py-2 px-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 bg-green-100 text-green-700 text-center py-2 px-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Details</h3>

            <div class="space-y-3">
                <div>
                    <span class="text-gray-600 font-medium">Event:</span>
                    <span class="text-gray-800 ml-2">{{ $event->name }}</span>
                </div>

                <div>
                    <span class="text-gray-600 font-medium">Selected Fabrics:</span>
                    <div class="mt-2 space-y-1">
                        @forelse ($eventFabrics as $fabric)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">{{ $fabric['name'] ?? 'Fabric' }}</span>
                                <span class="font-medium">₦{{ number_format($fabric['price'] ?? 0, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No fabrics selected.</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Fabric Cost:</span>
                    <span class="font-medium">₦{{ number_format($pendingData['total_fabric_cost'], 2) }}</span>
                </div>

                @if ($pendingData['delivery_zone_id'] && $deliveryZone)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Shipping Zone:</span>
                        <span
                            class="font-medium">{{ $deliveryZone['name'] ?? ($deliveryZone['zone_name'] ?? 'Selected Zone') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Shipping Cost:</span>
                        <span class="font-medium">₦{{ number_format($pendingData['delivery_cost'], 2) }}</span>
                    </div>
                @elseif ($pendingData['delivery_cost'] > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Shipping Cost:</span>
                        <span class="font-medium">₦{{ number_format($pendingData['delivery_cost'], 2) }}</span>
                    </div>
                @endif

                <hr class="my-4">

                <div class="flex justify-between items-center text-lg font-bold">
                    <span>Total Amount:</span>
                    <span class="text-pink-600">₦{{ number_format($totalAmount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Delivery Information</h3>

            <div class="space-y-2 text-sm">
                <div><strong>Recipient:</strong>
                    {{ $guest->full_name ?? trim(($guest->title ?? '') . ' ' . ($guest->last_name ?? '')) }}
                </div>
                <div><strong>Address:</strong> {{ $guest->address }}</div>
                <div><strong>Location:</strong> {{ $guest->city->name ?? 'N/A' }},
                    {{ $guest->state->name ?? 'N/A' }}</div>
                <div><strong>Email:</strong> {{ $guest->email }}</div>
                <div><strong>Phone:</strong> {{ $guest->phone }}</div>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Method</h3>

            <div class="flex items-center space-x-2 mb-2">
                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-green-600 text-xs">✓</span>
                </div>
                <span class="font-medium">Online Payment (Paystack)</span>
            </div>
            <p class="text-sm text-gray-600">You will be redirected to Paystack to complete your payment securely.</p>
        </div>

        <div class="flex space-x-4">
            <a href="{{ route('rsvp.show', $token) }}"
                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-md text-center transition">
                ← Back to RSVP
            </a>

            <form method="POST" action="{{ route('payment.confirm', $token) }}" class="flex-1">
                @csrf
                <button type="submit"
                    class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-3 px-4 rounded-md transition">
                    Proceed to Payment
                </button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                By proceeding, you agree to our terms and conditions. Your order will be created and you'll be taken to
                the payment page.
            </p>
        </div>
    </div>
</body>

</html>
