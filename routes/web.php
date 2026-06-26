<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NewsletterSubscriberController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RsvpController;
use App\Livewire\Cart\CartSummary;
use App\Livewire\Cart\Checkout;
use App\Livewire\Cart\OrderConfirmation;
use App\Livewire\Cart\OrderConfirmationLive;
use App\Livewire\ContactPage\Contact;
use App\Livewire\Events\EventCreate;
use App\Livewire\Events\EventEdit;
use App\Livewire\Events\EventList;
use App\Livewire\Frontend\CustomerSetup;
use App\Livewire\Guests\GuestCreate;
use App\Livewire\Guests\GuestEdit;
use App\Livewire\Guests\GuestImport;
use App\Livewire\Guests\GuestList;
use App\Livewire\Packages\PackageCustomizer;
use App\Livewire\Packages\PackageList;
use App\Livewire\PaymentReceipts;
use App\Livewire\Delivery\DeliveryServices;
use App\Livewire\Faq\Faq;
use App\Livewire\Pages\HomePage;
use App\Livewire\PrivacyPolicy\PrivacyPolicy;
use App\Livewire\RefundPolicy\RefundPolicy;
use App\Livewire\Terms\Terms;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', HomePage::class)->name('home');
Route::get('/who-we-are', function () {
    return view('pages.who-we-are.index');
})->name('who-we-are.index');
Route::get('/blog', function () {
    return view('pages.blog.index');
})->name('blog.index');
Route::get('/blog/why-event-logistics-is-the-unsung-hero-of-every-successful-celebration', function () {
    return view('pages.blog.why-event-logistics-is-the-unsung-hero-of-every-successful-celebration.index');
});
Route::get('/blog/the-benefits-of-an-automated-payment-system-in-event-management', function () {
    return view('pages.blog.the-benefits-of-an-automated-payment-system-in-event-management.index');
});
Route::get('/blog/redefining-event-management-through-customization', function () {
    return view('pages.blog.redefining-event-management-through-customization.index');
});
Route::get('/our-services', function () {
    return view('pages.our-services.index');
})->name('our-services.index');
Route::get('/our-services/package-customization', function () {
    return view('pages.our-services.package-customization.index');
});
Route::get('/our-services/social-and-corporate-logistics', function () {
    return view('pages.our-services.social-and-corporate-logistics.index');
});
Route::get('/faq', Faq::class)->name('faq.page');
Route::get('/terms-conditions', Terms::class)->name('terms.page');
Route::get('/refund-policy', RefundPolicy::class)->name('refund.page');
Route::get('/privacy-policy', PrivacyPolicy::class)->name('privacy.page');
Route::get('/contact', Contact::class)->name('contact.page');
Route::post('/newsletter-subscribe', [NewsletterSubscriberController::class, 'store'])->name('newsletter.subscribe');

Route::get('/rsvp/{token}', [RsvpController::class, 'show'])->name('rsvp.show');
Route::post('/rsvp/{token}', [RsvpController::class, 'submit'])->name('rsvp.submit');
Route::get('/rsvp/states/{state}/cities', [RsvpController::class, 'citiesByState'])->name('rsvp.cities');

// Payment Routes
Route::get('/payment/preview/{token}', [PaymentController::class, 'preview'])->name('payment.preview');
Route::post('/payment/confirm/{token}', [PaymentController::class, 'confirmAndProceed'])->name('payment.confirm');
Route::get('/payment/summary/{token}/{order_id}', [PaymentController::class, 'summary'])->name('payment.summary');
Route::post('/payment/summary/{token}/{order_id}', [PaymentController::class, 'submitSummary'])->name('payment.summary.submit');
Route::get('/payment/offline/{token}/{order_id}', [PaymentController::class, 'showOfflineInstructions'])->name('payment.offline');
Route::post('/payment/paystack/{token}/{order_id}', [PaymentController::class, 'initializePaystack'])->name('payment.paystack');
Route::get('/payment/paystack/callback/{token}/{order_id}', [PaymentController::class, 'handlePaystackCallback'])->name('payment.paystack.callback');
// Keep old routes for backup
Route::get('/payment/process/{token}/{order_id}', [PaymentController::class, 'process'])->name('payment.process');
Route::post('/payment/complete/{token}/{order_id}', [PaymentController::class, 'complete'])->name('payment.complete');
Route::get('/customer/setup/{token}', CustomerSetup::class)->name('customer.setup');

Route::get('generate', function () {
    Artisan::call('storage:link');
    echo 'storage generated';
});

Route::get('optimize', function () {
    Artisan::call('optimize:clear');
    echo 'site optimized';
});

// Temporary: test payment reminder email — remove before deploying to production
Route::get('test-payment-reminder/{order_id}', function ($order_id) {
    $fabricSelection = \App\Models\GuestFabricSelection::with(['guest', 'event'])->findOrFail($order_id);
    \Illuminate\Support\Facades\Mail::to($fabricSelection->guest->email)
        ->send(new \App\Mail\GuestPaymentReminderMail($fabricSelection));
    return 'Payment reminder email sent to ' . $fabricSelection->guest->email;
});

Route::get('migrate', function () {
    Artisan::call('migrate');
    echo 'database migrated';
});

Route::get('/payment/callback', [PaystackController::class, 'handle'])->name('payment.callback');
Route::get('/delivery/paystack/redirect', [PaystackController::class, 'deliveryRedirect'])->name('delivery.paystack.redirect');

// Invoice Payment Routes
Route::get('/invoice/pay/{token}', [InvoiceController::class, 'showPayment'])->name('invoice.payment');
Route::post('/invoice/pay/{token}', [InvoiceController::class, 'initializePayment'])->name('invoice.payment.process');
Route::get('/invoice/pay/callback/{token}', [InvoiceController::class, 'handleCallback'])->name('invoice.payment.callback');
Route::get('/invoice/pay/verify/{token}', [InvoiceController::class, 'retryVerification'])->name('invoice.payment.verify');
Route::get('/invoice/paid/{token}', [InvoiceController::class, 'showPaid'])->name('invoice.paid');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::view('dashboard', 'dashboard.dashboard')->name('dashboard');

    Route::get('guests', GuestList::class)->name('guests.list');

    Route::middleware(['events.active'])->group(function () {
        Route::get('guests/create', GuestCreate::class)->name('guests.create');
        Route::get('guests/import', GuestImport::class)->name('guests.import');
        Route::get('guests/import/template', [GuestImport::class, 'downloadTemplate'])->name('guests.import.template');
        Route::get('guests/{guest}/edit', GuestEdit::class)->name('guests.edit');
    });

    Route::get('events', EventList::class)->name('events.list');
    Route::get('events/create', EventCreate::class)->name('events.create');
    Route::get('events/{event}/edit', EventEdit::class)->name('events.edit');

    Route::get('/packages', PackageList::class)->name('packages.list');
    Route::get('/packages/{packageId}/customize', PackageCustomizer::class)->name('packages.customize');
    Route::get('/cart', CartSummary::class)->name('cart.summary');
    Route::get('/checkout', Checkout::class)->name('cart.checkout');
    Route::get('/order-confirmation/{reference}', OrderConfirmation::class)->name('order.confirmation');
    Route::get('/order-confirmation-online/{reference}', OrderConfirmationLive::class)->name('order.confirmation.online');
    Route::get('/payment-receipts', PaymentReceipts::class)->name('payment.receipts');
    Route::get('/delivery-services', DeliveryServices::class)->name('delivery.services');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});


require __DIR__ . '/auth.php';
