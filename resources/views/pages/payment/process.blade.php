<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Processing - {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Complete Your Payment</h2>

        @if (session('error'))
            <div class="mb-4 bg-red-100 text-red-700 text-center py-2 px-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h3>

            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Package:</span>
                    <span class="font-medium">{{ $packageSelection->packageCustomization->package->name }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Quantity:</span>
                    <span class="font-medium">{{ $packageSelection->quantity }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Unit Price:</span>
                    <span class="font-medium">₦{{ number_format($packageSelection->unit_price, 2) }}</span>
                </div>

                @if ($packageSelection->delivery_cost > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery:</span>
                        <span class="font-medium">₦{{ number_format($packageSelection->delivery_cost, 2) }}</span>
                    </div>
                @endif

                <hr class="my-3">

                <div class="flex justify-between text-lg font-bold">
                    <span>Total Amount:</span>
                    <span class="text-pink-600">₦{{ number_format($totalAmount, 2) }}</span>
                </div>
            </div>
        </div>

        <form method="POST"
            action="{{ route('payment.complete', ['token' => $token, 'order_id' => $packageSelection->id]) }}"
            class="space-y-4">
            @csrf

            <div>
                <label for="card_number" class="block font-semibold text-gray-700 mb-1">Card Number</label>
                <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9012 3456" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-pink-500 focus:border-pink-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="expiry_date" class="block font-semibold text-gray-700 mb-1">Expiry Date</label>
                    <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YY" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-pink-500 focus:border-pink-500">
                </div>
                <div>
                    <label for="cvv" class="block font-semibold text-gray-700 mb-1">CVV</label>
                    <input type="text" name="cvv" id="cvv" placeholder="123" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-pink-500 focus:border-pink-500">
                </div>
            </div>

            <div>
                <label for="card_name" class="block font-semibold text-gray-700 mb-1">Cardholder Name</label>
                <input type="text" name="card_name" id="card_name" placeholder="John Doe" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-pink-500 focus:border-pink-500">
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                    <strong>Note:</strong> This is a demo payment system. In production, this would integrate with a
                    real payment gateway like Paystack or Flutterwave.
                </p>
            </div>

            <button type="submit"
                class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-3 px-4 rounded-md transition">
                Pay ₦{{ number_format($totalAmount, 2) }}
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('rsvp.show', $token) }}" class="text-pink-600 hover:text-pink-700 text-sm">
                ← Back to RSVP
            </a>
        </div>
    </div>
</body>

</html>
