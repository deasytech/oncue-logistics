<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
  /**
   * Handle contact form submission
   */
  public function store(Request $request)
  {
    try {
      // Clear any previous output
      if (ob_get_level()) {
        ob_end_clean();
      }

      // Validate the form data
      $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
      ]);

      // Parse the full name into first and last name
      $nameParts = explode(' ', trim($validated['name']), 2);
      $firstName = $nameParts[0];
      $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

      // Check if customer already exists by phone number
      $existingCustomer = Customer::where('phone', $validated['phone'])->first();

      if ($existingCustomer) {
        // Update existing customer with new information
        $existingCustomer->update([
          'first_name' => $firstName,
          'last_name' => $lastName,
          'email' => $validated['email'],
          'title' => 'Mr/Mrs', // Default title
        ]);

        $customer = $existingCustomer;
      } else {
        // Create new customer
        $customer = Customer::create([
          'first_name' => $firstName,
          'last_name' => $lastName,
          'email' => $validated['email'],
          'phone' => $validated['phone'],
          'title' => 'Mr/Mrs', // Default title
          'is_active' => true,
        ]);
      }

      // Here you could also store the subject and message in a separate table
      // For now, we'll just store the customer information

      return response()
        ->redirectTo('/contact-us')
        ->with('success', 'Thank you for contacting us! We will get back to you soon.');
    } catch (\Exception $e) {
      \Log::error('Contact form submission error: ' . $e->getMessage());
      return redirect()->back()
        ->with('error', 'An error occurred while submitting your message. Please try again.')
        ->withInput();
    }
  }
}
