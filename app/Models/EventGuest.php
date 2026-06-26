<?php

namespace App\Models;

use App\Models\GuestFabricSelection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EventGuest extends Model
{
    protected $table = 'event_guest';

    protected $fillable = [
        'event_id',
        'guest_id',
        'attendance_status',
        'plus_one',
        'rsvp_token',
        'reminder_attempts',
        'rsvp_sent_at',
        'rsvp_expires_at',
        'rsvp_responded_at',
        'last_reminder_sent_at',
    ];

    protected $casts = [
        'rsvp_sent_at'         => 'datetime',
        'rsvp_expires_at'      => 'datetime',
        'rsvp_responded_at'    => 'datetime',
        'last_reminder_sent_at' => 'datetime',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function fabricOrder(): HasOne
    {
        return $this->hasOne(GuestFabricSelection::class, 'guest_id', 'guest_id');
    }
}
