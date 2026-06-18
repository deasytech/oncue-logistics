<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/20 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-600 dark:text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Order Placed Successfully!</h1>
            <p class="text-gray-600 dark:text-gray-300">Your order has been received and is being processed.</p>
        </div>

        <!-- Order Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Order Details</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order Reference</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">
                            {{ $payment->reference }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order Date</h3>
                        <p class="text-gray-900 dark:text-white">{{ $payment->created_at->format('F j, Y g:i A') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Amount</h3>
                        <p class="text-2xl font-bold text-green-600">₦{{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</h3>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            Offline Payment
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Instructions -->
        <div
            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-400 mb-3">Payment Instructions
                    </h3>
                    <p class="text-yellow-700 dark:text-yellow-300 mb-4"><strong>Please make payment within 24 hours to
                            confirm your order.</strong></p>

                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Bank Transfer Details</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Bank Name:</span>
                                <span class="font-medium text-gray-900 dark:text-white">First Bank of Nigeria</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Account Name:</span>
                                <span class="font-medium text-gray-900 dark:text-white">Oncue Events & Logistics</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Account Number:</span>
                                <span class="font-medium font-mono text-gray-900 dark:text-white">2034567890</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                                <span class="font-bold text-green-600">₦{{ number_format($payment->amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-800 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-pink-800 dark:text-pink-400 mb-2">⚠️ Important</h4>
                        <p class="text-pink-700 dark:text-pink-300 text-sm">
                            Please use your order reference <strong class="font-mono">{{ $payment->reference }}</strong>
                            as the payment description/remark.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <p class="text-yellow-700 dark:text-yellow-300">After making payment, please upload proof of
                            payment:</p>
                        <a href="{{ route('payment.receipts') }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            Upload Payment Receipt
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-green-800 dark:text-green-400 mb-3">🎯 What Happens Next?</h3>
            <ol class="space-y-2 text-green-700 dark:text-green-300">
                <li class="flex items-start">
                    <span
                        class="flex-shrink-0 w-6 h-6 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">1</span>
                    <span>Make payment using the details above</span>
                </li>
                <li class="flex items-start">
                    <span
                        class="flex-shrink-0 w-6 h-6 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">2</span>
                    <span>Upload proof of payment</span>
                </li>
                <li class="flex items-start">
                    <span
                        class="flex-shrink-0 w-6 h-6 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">3</span>
                    <span>We'll confirm your payment within 2 hours</span>
                </li>
                <li class="flex items-start">
                    <span
                        class="flex-shrink-0 w-6 h-6 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">4</span>
                    <span>Your order will be processed and prepared</span>
                </li>
                <li class="flex items-start">
                    <span
                        class="flex-shrink-0 w-6 h-6 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">5</span>
                    <span>You'll receive tracking information once ready</span>
                </li>
            </ol>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('packages.list') }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Continue Shopping
            </a>
            <button onclick="window.print()"
                class="inline-flex items-center justify-center px-6 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors font-medium cursor-pointer">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Order Details
            </button>
        </div>

        <!-- Contact Information -->
        <div class="mt-8 bg-gray-50 dark:bg-gray-800 rounded-lg p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">📞 Need Help?</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">If you have any questions about your order or payment,
                please contact us:</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">+234 803 456 7890</span>
                </div>
                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">info@oncuelogistics.com</span>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Monday - Saturday, 9:00 AM - 6:00 PM</p>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Print-specific styles */
        @media print {
            body {
                background-color: white !important;
                color: black !important;
                font-size: 12px !important;
                line-height: 1.4 !important;
                margin: 0 !important;
                padding: 10px !important;
            }

            .min-h-screen {
                min-height: auto !important;
                background-color: white !important;
            }

            .max-w-4xl {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Hide non-print elements */
            .no-print,
            button,
            .flex.justify-center.gap-4,
            .mt-8.bg-gray-50,
            #darkModeToggle {
                display: none !important;
            }

            /* Show print-only elements */
            .print-only {
                display: block !important;
            }

            /* Reset backgrounds and borders for print */
            .bg-white,
            .dark\:bg-gray-800,
            .bg-yellow-50,
            .dark\:bg-yellow-900\/20,
            .bg-green-50,
            .dark\:bg-green-900\/20,
            .bg-pink-50,
            .dark\:bg-pink-900\/20,
            .bg-gray-50,
            .dark\:bg-gray-800 {
                background-color: white !important;
                border: 1px solid #ddd !important;
            }

            /* Simplify shadows and effects */
            .shadow-sm,
            .rounded-lg {
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            /* Ensure text is black for print */
            .text-gray-900,
            .dark\:text-white,
            .text-gray-700,
            .dark\:text-gray-300,
            .text-gray-600,
            .dark\:text-gray-400,
            .text-green-600,
            .text-yellow-800,
            .text-pink-800 {
                color: black !important;
            }

            /* Simplify borders */
            .border-gray-200,
            .dark\:border-gray-700,
            .border-yellow-200,
            .dark\:border-yellow-800,
            .border-green-200,
            .dark\:border-green-800,
            .border-pink-200,
            .dark\:border-pink-800 {
                border-color: #ddd !important;
            }

            /* Page break control */
            .order-details,
            .bank-details,
            .contact-info {
                page-break-inside: avoid !important;
            }

            h1,
            h2,
            h3,
            h4 {
                page-break-after: avoid !important;
            }

            /* Make tables more compact */
            table,
            .items-table {
                font-size: 11px !important;
            }

            th,
            td {
                padding: 4px !important;
            }

            /* Header styling for print */
            .text-center.mb-8 {
                border-bottom: 2px solid #000 !important;
                padding-bottom: 10px !important;
                margin-bottom: 15px !important;
            }

            /* Ensure proper spacing */
            .mb-6,
            .mb-8 {
                margin-bottom: 10px !important;
            }

            .p-6 {
                padding: 8px !important;
            }

            .px-6 {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .py-4 {
                padding-top: 6px !important;
                padding-bottom: 6px !important;
            }
        }

        /* Screen-only elements */
        .screen-only {
            display: block;
        }

        @media print {
            .screen-only {
                display: none;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Enhanced print functionality
        function printOrder() {
            // Add print-specific styling
            const style = document.createElement('style');
            style.textContent = `
        @media print {
            @page { margin: 0.5in; }
            body { background: white !important; }
            .min-h-screen { background: white !important; }
        }
    `;
            document.head.appendChild(style);

            // Trigger print
            window.print();

            // Clean up
            setTimeout(() => {
                document.head.removeChild(style);
            }, 100);
        }

        // Make print button more robust
        document.addEventListener('DOMContentLoaded', function() {
            const printButton = document.querySelector('button[onclick="window.print()"]');
            if (printButton) {
                printButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    printOrder();
                });
                printButton.setAttribute('onclick', 'printOrder()');
            }
        });
    </script>
@endpush
