<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city_id',
        'state_id',
        // 'rsvp_status',
        'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class)
            ->withPivot([
                'attendance_status',
                'plus_one',
                'rsvp_token',
                'reminder_attempts',
                'rsvp_sent_at',
                'rsvp_expires_at',
                'rsvp_responded_at',
                'last_reminder_sent_at',
            ])
            ->withTimestamps();
    }

    public function packageSelections()
    {
        return $this->hasMany(GuestPackageSelection::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->title} {$this->first_name} {$this->last_name}");
    }

    public function getCustomerNameAttribute(): ?string
    {
        return $this->customer ? "{$this->customer->first_name} {$this->customer->last_name}" : null;
    }

    /**
     * Shortcut: get RSVP details for a specific event
     */
    public function rsvpFor(Event $event): ?array
    {
        $pivot = $this->events()->where('event_id', $event->id)->first()?->pivot;

        if (!$pivot) return null;

        return [
            'attendance_status' => $pivot->attendance_status,
            'rsvp_token' => $pivot->rsvp_token,
            'plus_one' => $pivot->plus_one,
            'rsvp_sent_at' => $pivot->rsvp_sent_at,
            'rsvp_responded_at' => $pivot->rsvp_responded_at,
        ];
    }
}
