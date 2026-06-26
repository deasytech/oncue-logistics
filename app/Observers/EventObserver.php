<?php

namespace App\Observers;

use App\Mail\EventActivatedMail;
use App\Models\Event;
use Illuminate\Support\Facades\Mail;

class EventObserver
{
    public function updated(Event $event): void
    {
        if ($event->wasChanged('is_active') && $event->is_active && $event->customer?->email) {
            Mail::to($event->customer->email)->send(new EventActivatedMail($event));
        }
    }
}
