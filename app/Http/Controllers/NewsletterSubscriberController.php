<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterSubscriberController extends Controller
{
  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'email' => ['required', 'email:rfc,dns', 'max:255'],
    ]);

    $subscriber = NewsletterSubscriber::where('email', $validated['email'])->first();

    if ($subscriber) {
      if (! $subscriber->is_active) {
        $subscriber->update([
          'is_active' => true,
          'subscribed_at' => now(),
        ]);

        return back()->with('newsletter_success', 'Your newsletter subscription has been reactivated successfully.');
      }

      return back()->with('newsletter_success', 'This email is already subscribed to our newsletter.');
    }

    NewsletterSubscriber::create([
      'email' => $validated['email'],
      'is_active' => true,
      'subscribed_at' => now(),
    ]);

    return back()->with('newsletter_success', 'Thank you for subscribing to our newsletter!');
  }
}
