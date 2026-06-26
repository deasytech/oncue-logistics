<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Summary - {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Payment Summary</h2>

        @if (session('error'))
            <div class="mb-4 bg-red-100 text-red-700 text-center py-2 px-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Details</h3>

            <div class="space-y-3">
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
                    <span class="font-medium">₦{{ number_format($fabricSelection->total_fabric_cost, 2) }}</span>
                </div>

                @if ($fabricSelection->delivery_zone_id && $deliveryZone)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Shipping Zone:</span>
                        <span
                            class="font-medium">{{ $deliveryZone['name'] ?? ($deliveryZone['zone_name'] ?? 'Selected Zone') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Shipping Cost:</span>
                        <span class="font-medium">₦{{ number_format($fabricSelection->delivery_cost, 2) }}</span>
                    </div>
                @elseif ($fabricSelection->delivery_cost > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Shipping Cost:</span>
                        <span class="font-medium">₦{{ number_format($fabricSelection->delivery_cost, 2) }}</span>
                    </div>
                @endif

                <hr class="my-4">

                <div class="flex justify-between items-center text-lg font-bold">
                    <span>Total Amount:</span>
                    <span class="text-pink-600">₦{{ number_format($totalAmount, 2) }}</span>
                </div>
            </div>
        </div>

        <form id="payment-summary-form" method="POST"
            action="{{ route('payment.summary.submit', ['token' => $token, 'order_id' => $fabricSelection->id]) }}">
            @csrf
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span
                        class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" required
                    value="{{ $fabricSelection->guest->email ?? '' }}"
                    class="w-full border border-gray-300 rounded-md px-4 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent"
                    placeholder="Enter your email address">
            </div>

            <div class="bg-blue-50 p-6 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Delivery Information</h3>

                <div class="space-y-2 text-sm">
                    <div><strong>Recipient:</strong>
                        {{ $fabricSelection->guest->full_name ?? trim(($fabricSelection->guest->title ?? '') . ' ' . ($fabricSelection->guest->last_name ?? '')) }}
                    </div>
                    <div><strong>Address:</strong> {{ $fabricSelection->guest->address }}</div>
                    <div><strong>Location:</strong> {{ $fabricSelection->guest->city->name ?? 'N/A' }},
                        {{ $fabricSelection->guest->state->name ?? 'N/A' }}</div>
                    <div><strong>Email:</strong> {{ $fabricSelection->guest->email }}</div>
                    <div><strong>Phone:</strong> {{ $fabricSelection->guest->phone }}</div>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Method</h3>

                @if ($fabricSelection->payment_method === 'online')
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600 text-xs">✓</span>
                        </div>
                        <span class="font-medium">Online Payment (Paystack)</span>
                    </div>
                    <p class="text-sm text-gray-600">You will be redirected to Paystack to complete your payment
                        securely.
                    </p>
                @else
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 text-xs">💳</span>
                        </div>
                        <span class="font-medium">Offline Payment</span>
                    </div>
                    <p class="text-sm text-gray-600">You will receive payment instructions and our team will contact
                        you.
                    </p>
                @endif
            </div>

        </form>

        <div class="flex space-x-4 mt-6">
            <a href="{{ route('rsvp.show', $token) }}?edit=1"
                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-md text-center transition">
                ← Back to RSVP
            </a>

            <button type="submit" form="payment-summary-form"
                class="flex-1 {{ $fabricSelection->payment_method === 'online' ? 'bg-pink-600 hover:bg-pink-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white font-semibold py-3 px-4 rounded-md transition">
                @if ($fabricSelection->payment_method === 'online')
                    Proceed to Paystack
                @else
                    View Payment Instructions
                @endif
            </button>
        </div>

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                By proceeding, you agree to our terms and conditions. Your payment information is processed securely.
            </p>
        </div>
    </div>
</body>

</html>
