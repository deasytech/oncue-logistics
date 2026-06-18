<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Offline Payment Instructions - {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Offline Payment Instructions</h2>

        @if (session('success'))
            <div class="mb-4 bg-green-100 text-green-700 text-center py-2 px-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-blue-50 p-6 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Details</h3>

            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Order Type:</span>
                    <span class="font-medium">Fabric Selection</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Total Amount:</span>
                    <span
                        class="font-bold text-pink-600">₦{{ number_format($fabricSelection->calculateTotal(), 2) }}</span>
                </div>

                @if ($fabricSelection->fabric_selections && count($fabricSelection->fabric_selections) > 0)
                    <div class="mt-3">
                        <span class="text-gray-600">Selected Fabrics:</span>
                        <ul class="mt-1 space-y-1">
                            @foreach ($fabricSelection->fabric_selections as $fabric)
                                <li class="text-sm">• {{ $fabric['name'] }} - ₦{{ number_format($fabric['price'], 2) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Instructions</h3>

            <div class="space-y-3 text-sm text-gray-700">
                <p><strong>Bank Transfer:</strong></p>
                <p>Account Name: ON CUE LOGISTICS LIMITED</p>
                <p>Account Number: 2007399144</p>
                <p>Bank: FCMB</p>

                <hr class="my-3">

                <p><strong>Important:</strong></p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Use the name of the event as payment reference</li>
                    <li>Send payment proof to our support team</li>
                    <li>Your order will be processed within 24 hours</li>
                    <li>You will receive confirmation via email</li>
                </ul>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-800">
                <strong>Next Steps:</strong> Our team will contact you within 24 hours to confirm your payment and
                arrange delivery details.
            </p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('rsvp.show', $token) }}"
                class="block w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-3 px-4 rounded-md text-center transition">
                Back to RSVP
            </a>

            <button onclick="window.print()"
                class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-md transition">
                Print Instructions
            </button>
        </div>
    </div>
</body>

</html>
