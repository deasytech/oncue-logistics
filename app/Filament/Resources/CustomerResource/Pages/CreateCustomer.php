<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Mail\NewCustomerMail;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected static bool $canCreateAnother = false;

    protected function afterCreate(): void
    {
        $customer = $this->record;
        $token = Str::uuid()->toString();
        $customer->setup_token = $token;
        $customer->setup_token_expires_at = Carbon::now()->addDays(5);
        $customer->save();

        if ($customer->email) {
            $setupUrl = route('customer.setup', ['token' => $token]);
            Mail::to($customer->email)->queue(new NewCustomerMail($customer, $setupUrl));
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Customer created')
            ->body('The customer has been created successfully.');
    }
}
