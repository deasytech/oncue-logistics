<?php

namespace App\Livewire\Guests;

use App\Models\Customer;
use App\Models\Event;
use App\Models\Guest;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use League\Csv\Reader;

class GuestImport extends Component
{
  use WithFileUploads;

  public $file;
  public $customer_id;
  public $event_id;
  public $columnMap = [];
  public $csvColumns = [];
  public $importPreview = [];
  public $showPreview = false;
  public $importing = false;
  public $importResults = [];
  public $showNotificationConfirm = false;
  public $notificationDecisionMade = false;

  protected $rules = [
    'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
    'event_id' => 'nullable|exists:events,id',
  ];

  public function mount()
  {
    // Automatically set customer_id to the logged-in user's customer
    if (Auth::check() && Auth::user()->customer) {
      $this->customer_id = Auth::user()->customer->id;
    }
  }

  public function updatedFile()
  {
    $this->validateOnly('file');

    if ($this->file) {
      try {
        $csvStream = fopen($this->file->getRealPath(), 'r');
        $csvReader = Reader::createFromStream($csvStream);
        $csvReader->setHeaderOffset(0);

        $this->csvColumns = $csvReader->getHeader();

        // Auto-map columns based on common names
        $this->columnMap = $this->autoMapColumns($this->csvColumns);

        // Show preview of first 5 rows
        $records = iterator_to_array($csvReader->getRecords());
        $this->importPreview = array_slice($records, 0, 5);
        $this->showPreview = true;

        fclose($csvStream);
      } catch (\Exception $e) {
        $this->addError('file', 'Unable to read CSV file: ' . $e->getMessage());
        $this->resetFile();
      }
    }
  }

  private function autoMapColumns($csvColumns)
  {
    $mapping = [];
    $lowercaseCsvColumns = array_map('strtolower', $csvColumns);

    $expectedColumns = [
      'event' => ['event', 'event_name', 'event name', 'occasion'],
      'title' => ['title', 'salutation', 'prefix'],
      'first_name' => ['first_name', 'firstname', 'first name', 'fname'],
      'last_name' => ['last_name', 'lastname', 'last name', 'lname', 'surname'],
      'email' => ['email', 'e-mail', 'email_address', 'email address'],
      'phone' => ['phone', 'mobile', 'cell', 'telephone', 'phone_number', 'phone number'],
      'address' => ['address', 'street_address', 'street address', 'home_address', 'home address', 'location'],
      'state' => ['state', 'state_name', 'state name', 'province', 'region'],
      'city' => ['city', 'city_name', 'city name', 'town', 'municipality'],
    ];

    foreach ($expectedColumns as $field => $possibleNames) {
      foreach ($possibleNames as $possibleName) {
        $key = array_search($possibleName, $lowercaseCsvColumns);
        if ($key !== false) {
          $mapping[$field] = $csvColumns[$key];
          break;
        }
      }
    }

    return $mapping;
  }

  public function resetFile()
  {
    $this->file = null;
    $this->csvColumns = [];
    $this->columnMap = [];
    $this->importPreview = [];
    $this->showPreview = false;
    $this->showNotificationConfirm = false;
    $this->notificationDecisionMade = false;
  }

  public function handleImportAction()
  {
    $this->validate();

    Log::info('Guest import pre-check triggered', [
      'event_id' => $this->event_id,
      'has_file' => (bool) $this->file,
      'column_map' => array_keys($this->columnMap ?? []),
      'notification_decision_made' => $this->notificationDecisionMade,
    ]);

    if (!$this->file || empty($this->columnMap)) {
      $this->addError('file', 'Please upload a file and map the columns.');
      return;
    }

    if ($this->shouldPromptForNotifications() && !$this->notificationDecisionMade) {
      Log::info('Guest import showing notification confirmation prompt', [
        'event_id' => $this->event_id,
      ]);
      $this->showNotificationConfirm = true;
      return;
    }

    Log::info('Guest import proceeding without pre-confirmation prompt', [
      'event_id' => $this->event_id,
      'notification_decision_made' => $this->notificationDecisionMade,
    ]);

    return $this->processImport(false);
  }

  public function importGuests()
  {
    return $this->handleImportAction();
  }

  public function confirmImportWithNotifications()
  {
    $this->validate();
    $this->showNotificationConfirm = false;
    $this->notificationDecisionMade = true;

    Log::info('Guest import confirmed with notifications', [
      'event_id' => $this->event_id,
    ]);

    return $this->processImport(true);
  }

  public function confirmImportWithoutNotifications()
  {
    $this->validate();
    $this->showNotificationConfirm = false;
    $this->notificationDecisionMade = true;

    Log::info('Guest import confirmed without notifications', [
      'event_id' => $this->event_id,
    ]);

    return $this->processImport(false);
  }

  public function cancelNotificationPrompt()
  {
    $this->showNotificationConfirm = false;
    $this->notificationDecisionMade = false;
  }

  private function shouldPromptForNotifications(): bool
  {
    return !empty($this->event_id) || !empty($this->columnMap['event']);
  }

  private function processImport(bool $sendNotifications = true)
  {
    Log::info('Guest import process started', [
      'event_id' => $this->event_id,
      'send_notifications' => $sendNotifications,
    ]);

    $this->importing = true;
    $importResults = [
      'total' => 0,
      'successful' => 0,
      'failed' => 0,
      'errors' => [],
      'emails_sent' => 0,
      'email_errors' => [],
      'sms_sent' => 0,
      'sms_errors' => [],
    ];

    try {
      $csvStream = fopen($this->file->getRealPath(), 'r');
      $csvReader = Reader::createFromStream($csvStream);
      $csvReader->setHeaderOffset(0);

      $records = $csvReader->getRecords();

      foreach ($records as $index => $record) {
        $importResults['total']++;

        try {
          $rowEventId = $this->resolveEventIdForRecord($record);

          // Validate required fields
          if (empty($record[$this->columnMap['first_name']]) || empty($record[$this->columnMap['last_name']])) {
            throw new \Exception('First name and last name are required');
          }

          // Check for duplicate guest by email and customer
          if (!empty($record[$this->columnMap['email']])) {
            $existingGuest = Guest::where('email', $record[$this->columnMap['email']])
              ->where('customer_id', $this->customer_id)
              ->first();

            if ($existingGuest) {
              throw new \Exception('Guest with this email already exists for this customer');
            }
          }

          // Handle state and city lookup
          $stateId = null;
          $cityId = null;

          if (isset($this->columnMap['state']) && !empty($record[$this->columnMap['state']])) {
            $stateName = trim($record[$this->columnMap['state']]);
            $state = \App\Models\State::where('name', 'LIKE', "%{$stateName}%")
              ->first();
            $stateId = $state ? $state->id : null;

            // If state found and city provided, look up city
            if ($stateId && isset($this->columnMap['city']) && !empty($record[$this->columnMap['city']])) {
              $cityName = trim($record[$this->columnMap['city']]);
              $city = \App\Models\City::where('state_id', $stateId)
                ->where('name', 'LIKE', "%{$cityName}%")
                ->first();
              $cityId = $city ? $city->id : null;
            }
          }

          // Create guest
          $guest = Guest::create([
            'customer_id' => $this->customer_id,
            'title' => $record[$this->columnMap['title']] ?? null,
            'first_name' => $record[$this->columnMap['first_name']],
            'last_name' => $record[$this->columnMap['last_name']],
            'email' => $record[$this->columnMap['email']] ?? null,
            'phone' => $record[$this->columnMap['phone']] ?? null,
            'address' => isset($this->columnMap['address']) ? ($record[$this->columnMap['address']] ?? null) : null,
            'state_id' => $stateId,
            'city_id' => $cityId,
          ]);

          // Attach to event if specified
          if ($rowEventId) {
            $rsvpToken = Str::random(32);
            $guest->events()->attach($rowEventId, [
              'rsvp_token' => $rsvpToken,
              'rsvp_sent_at' => now(),
              'rsvp_expires_at' => now()->addDays(7),
              'attendance_status' => 'invited',
            ]);

            // Send RSVP invite via email and SMS if contact info exists
            if ($sendNotifications && ($guest->email || $guest->phone)) {
              $event = $guest->events->first();
              $rsvpToken = $event?->pivot?->rsvp_token;
              $rsvpLink = $rsvpToken ? route('rsvp.show', $rsvpToken) : null;
              $eventName = $event?->name ?? 'our event';
              $eventDate = $event?->event_date?->format('F j, Y') ?? 'Date: TBA';
              $smsMessage = $rsvpLink
                ? "Hi, {$guest->title} {$guest->first_name}, you're invited to {$eventName} on {$eventDate}. Please RSVP: {$rsvpLink}"
                : null;

              if ($guest->email) {
                try {
                  // Send email synchronously for immediate feedback
                  Mail::to($guest->email)->send(new \App\Mail\GuestRsvpInviteMail($guest));
                  $importResults['emails_sent']++;
                  Log::info('RSVP email sent successfully to guest: ' . $guest->email);
                } catch (\Exception $e) {
                  // Log email failure but don't fail the import
                  $importResults['email_errors'][] = [
                    'guest' => $guest->full_name,
                    'email' => $guest->email,
                    'error' => $e->getMessage()
                  ];
                  Log::error('Failed to send RSVP email to guest: ' . $guest->email . ' - ' . $e->getMessage());
                }
              }

              if ($guest->phone && $smsMessage) {
                try {
                  $to = app(TwilioService::class)->formatE164($guest->phone);
                  if ($to) {
                    app(TwilioService::class)->sendSms($to, $smsMessage);
                    $importResults['sms_sent']++;
                    Log::info('RSVP SMS sent successfully to guest: ' . $to);
                  } else {
                    $importResults['sms_errors'][] = [
                      'guest' => $guest->full_name,
                      'phone' => $guest->phone,
                      'error' => 'Unable to format phone number to E.164',
                    ];
                    Log::warning('Skipped RSVP SMS: unable to format phone for guest: ' . $guest->phone);
                  }
                } catch (\Exception $e) {
                  $importResults['sms_errors'][] = [
                    'guest' => $guest->full_name,
                    'phone' => $guest->phone,
                    'error' => $e->getMessage()
                  ];
                  Log::error('Failed to send RSVP SMS to guest: ' . $guest->phone . ' - ' . $e->getMessage());
                }
              }
            }
          }

          $importResults['successful']++;
        } catch (\Exception $e) {
          $importResults['failed']++;
          $importResults['errors'][] = [
            'row' => $index + 2, // +2 because header is row 1 and we start from 0
            'error' => $e->getMessage()
          ];
        }
      }

      fclose($csvStream);

      $this->importResults = $importResults;
      $this->showPreview = false;
      $this->notificationDecisionMade = false;
      $this->showNotificationConfirm = false;

      $msgParts = [];
      if ($importResults['emails_sent'] > 0) {
        $msgParts[] = "{$importResults['emails_sent']} RSVP emails sent";
      }
      if (!empty($importResults['email_errors'])) {
        $msgParts[] = count($importResults['email_errors']) . " email(s) failed";
      }
      if ($importResults['sms_sent'] > 0) {
        $msgParts[] = "{$importResults['sms_sent']} SMS sent";
      }
      if (!empty($importResults['sms_errors'])) {
        $msgParts[] = count($importResults['sms_errors']) . " SMS failed";
      }

      if (($this->event_id || !empty($this->columnMap['event'])) && !$sendNotifications) {
        $msgParts[] = 'No email or SMS notification was sent';
      }

      $emailMessage = empty($msgParts) ? '' : ' ' . implode('. ', $msgParts) . '.';

      session()->flash('message', "Import completed! {$importResults['successful']} guests imported successfully, {$importResults['failed']} failed." . $emailMessage);
    } catch (\Exception $e) {
      $this->addError('file', 'Import failed: ' . $e->getMessage());
    } finally {
      $this->importing = false;
    }
  }

  public function downloadTemplate()
  {
    // Create sample CSV content with headers
    $headers = [
      'event',
      'title',
      'first_name',
      'last_name',
      'email',
      'phone',
      'address',
      'state',
      'city'
    ];

    // Create sample data rows
    $sampleData = [
      ['Sample Wedding', 'Mr', 'John', 'Doe', 'john.doe@email.com', '08012345678', '123 Main Street, Lagos', 'Lagos', 'Ikeja'],
      ['Sample Wedding', 'Mrs', 'Jane', 'Smith', 'jane.smith@email.com', '08087654321', '456 Oak Avenue, Abuja', 'Abuja', 'Garki'],
      ['Birthday Dinner', 'Ms', 'Michael', 'Johnson', 'michael.j@email.com', '07098765432', '789 Pine Road, Port Harcourt', 'Rivers', 'Port Harcourt'],
    ];

    // Build CSV content
    $csvContent = implode(',', $headers) . "\n";
    foreach ($sampleData as $row) {
      $csvContent .= implode(',', array_map(function ($field) {
        return '"' . str_replace('"', '""', $field) . '"';
      }, $row)) . "\n";
    }

    // Set headers for file download
    $filename = 'guest_import_template.csv';

    return response($csvContent, 200, [
      'Content-Type' => 'text/csv',
      'Content-Disposition' => 'attachment; filename="' . $filename . '"',
      'Cache-Control' => 'no-cache, no-store, must-revalidate',
      'Pragma' => 'no-cache',
      'Expires' => '0',
    ]);
  }

  public function render()
  {
    // Get events only for the logged-in customer's events
    $events = collect();
    if (Auth::check() && Auth::user()->customer) {
      $events = Auth::user()->customer->events()->pluck('name', 'id');
    }

    return view('livewire.guests.guest-import', [
      'events' => $events,
    ]);
  }

  private function resolveEventIdForRecord(array $record): ?int
  {
    if (!empty($this->event_id)) {
      return (int) $this->event_id;
    }

    if (empty($this->columnMap['event'])) {
      return null;
    }

    $eventName = trim((string) ($record[$this->columnMap['event']] ?? ''));

    if ($eventName === '') {
      return null;
    }

    return Event::query()
      ->where('customer_id', $this->customer_id)
      ->where('name', 'LIKE', "%{$eventName}%")
      ->value('id');
  }
}
