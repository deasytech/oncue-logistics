<?php

namespace App\Livewire\ContactPage;

use App\Mail\ContactEnquiryMail;
use App\Models\ContactEnquiry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.main')]
class Contact extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $subject = '';
    public $message = '';
    public $successMessage = '';
    public $errorMessage = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ];

    public function submit()
    {
        $this->validate();

        try {
            $enquiry = ContactEnquiry::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'subject' => $this->subject,
                'message' => $this->message,
            ]);

            // Send email notification to admin
            Mail::to('info@oncuelogistics.com')->send(new ContactEnquiryMail($enquiry));
            Log::info('Contact form email sent successfully', [
                'enquiry_id' => $enquiry->id,
                'from_name' => $this->name,
                'from_email' => $this->email,
                'subject' => $this->subject,
            ]);

            // Clear form fields
            $this->reset(['name', 'email', 'phone', 'subject', 'message']);

            // Set success message
            $this->successMessage = 'Thank you for contacting us! We will get back to you soon.';
            $this->errorMessage = '';

            // Clear success message after 5 seconds
            $this->dispatch('clear-success-message');
        } catch (\Exception $e) {
            Log::error('Contact form email sending failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'form_data' => [
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'subject' => $this->subject,
                ],
            ]);
            $this->errorMessage = 'An error occurred while submitting your message. Please try again.';
            $this->successMessage = '';
        }
    }

    public function render()
    {
        return view('livewire.contact-page.contact');
    }
}
