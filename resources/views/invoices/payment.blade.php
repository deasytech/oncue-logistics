<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Invoice #{{ $invoice->invoice_number }}</title>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-[#8b0055] text-white p-6 text-center">
            <h1 class="text-2xl font-bold">Invoice Payment</h1>
            <p class="mt-2 opacity-90">Secure payment powered by Paystack</p>
        </div>

        <div class="p-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4">
                    <p class="font-semibold">{{ session('warning') }}</p>
                    @if ($invoice->paystack_reference)
                        <div class="mt-3">
                            <a href="{{ route('invoice.payment.verify', ['token' => $invoice->payment_token]) }}"
                                class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded transition duration-200">
                                Check Payment Status
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            @if (session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                    {{ session('info') }}
                </div>
            @endif

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Invoice Details</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Invoice Number:</span>
                        <span class="font-medium text-gray-800">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Invoice Date:</span>
                        <span class="font-medium text-gray-800">{{ $invoice->created_at->format('F j, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Customer:</span>
                        <span class="font-medium text-gray-800">{{ $invoice->customer_name }}</span>
                    </div>
                    @if ($invoice->due_date)
                        <div>
                            <span class="text-gray-600">Due Date:</span>
                            <span class="font-medium text-gray-800">{{ $invoice->due_date->format('F j, Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-3">Invoice Items</h3>
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 rounded-tl-lg">Description</th>
                            <th class="px-4 py-2 text-center">Qty</th>
                            <th class="px-4 py-2 text-right">Unit Price</th>
                            <th class="px-4 py-2 text-right rounded-tr-lg">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3">{{ $item->description }}</td>
                                <td class="px-4 py-3 text-center">{{ number_format($item->quantity, 2) }}</td>
                                <td class="px-4 py-3 text-right">₦{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right">₦{{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right font-medium">Subtotal</td>
                            <td class="px-4 py-2 text-right">₦{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if ($invoice->tax_amount > 0)
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-medium">Tax</td>
                                <td class="px-4 py-2 text-right">₦{{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                        @endif
                        @if ($invoice->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-medium">Discount</td>
                                <td class="px-4 py-2 text-right text-green-600">
                                    -₦{{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="bg-[#8b0055] text-white">
                            <td colspan="3" class="px-4 py-3 text-right font-bold rounded-bl-lg">Total Amount</td>
                            <td class="px-4 py-3 text-right font-bold rounded-br-lg">
                                ₦{{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($invoice->notes)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-yellow-800 mb-2">Notes:</h4>
                    <p class="text-yellow-700 text-sm">{{ $invoice->notes }}</p>
                </div>
            @endif

            <div class="bg-teal-50 border border-teal-200 rounded-lg p-6 text-center">
                <p class="text-teal-800 text-lg font-semibold mb-2">Amount Due</p>
                <p class="text-3xl font-bold text-[#8b0055] mb-4">₦{{ number_format($invoice->balance_due, 2) }}</p>

                <form action="{{ route('invoice.payment.process', ['token' => $invoice->payment_token]) }}"
                    method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-[#8b0055] hover:bg-[#6d0044] text-white font-bold py-3 px-8 rounded-lg transition duration-200 w-full sm:w-auto">
                        Pay Now with Paystack
                    </button>
                </form>

                <p class="text-gray-500 text-xs mt-3">Secure payment processing by Paystack</p>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 text-center text-sm text-gray-500">
                <p>Need help? Contact us at:</p>
                <p class="mt-1">📞 +234 708 909 1600 | 📧 info@oncuelogistics.com</p>
            </div>
        </div>
    </div>
</body>

</html>
