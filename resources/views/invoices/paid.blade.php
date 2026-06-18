<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Invoice #{{ $invoice->invoice_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-white rounded-xl shadow-lg overflow-hidden text-center">
        <div class="bg-green-600 text-white p-8">
            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <h1 class="text-3xl font-bold">Payment Successful!</h1>
            <p class="mt-2 text-green-100">Thank you for your payment</p>
        </div>

        <div class="p-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6">
                <p class="text-gray-600 mb-2">Invoice Number</p>
                <p class="text-2xl font-bold text-gray-800">{{ $invoice->invoice_number }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="text-left">
                        <span class="text-gray-600">Customer:</span>
                        <p class="font-medium text-gray-800">{{ $invoice->customer_name }}</p>
                    </div>
                    <div class="text-left">
                        <span class="text-gray-600">Amount Paid:</span>
                        <p class="font-medium text-green-600">₦{{ number_format($invoice->amount_paid, 2) }}</p>
                    </div>
                    <div class="text-left">
                        <span class="text-gray-600">Payment Date:</span>
                        <p class="font-medium text-gray-800">
                            {{ $invoice->paid_at ? $invoice->paid_at->format('F j, Y g:i A') : now()->format('F j, Y g:i A') }}
                        </p>
                    </div>
                    @if ($invoice->paystack_transaction_id)
                        <div class="text-left">
                            <span class="text-gray-600">Transaction ID:</span>
                            <p class="font-medium text-gray-800">{{ $invoice->paystack_transaction_id }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <p class="text-gray-600">
                    A confirmation email has been sent to <strong>{{ $invoice->customer_email }}</strong>
                </p>

                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-4">Need a receipt or have questions?</p>
                    <a href="mailto:info@oncuelogistics.com"
                        class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-lg transition duration-200">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-4 text-sm text-gray-500">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
