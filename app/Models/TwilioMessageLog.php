<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwilioMessageLog extends Model
{
    protected $fillable = [
        'channel',
        'to',
        'to_country',
        'context',
        'content_sid',
        'message_sid',
        'status',
        'error_code',
        'error_message',
        'payload',
        'retried_at',
        'retry_of_id',
        'guest_id',
        'event_id',
    ];

    protected $casts = [
        'payload' => 'array',
        'retried_at' => 'datetime',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function retryOf()
    {
        return $this->belongsTo(self::class, 'retry_of_id');
    }

    public function retries()
    {
        return $this->hasMany(self::class, 'retry_of_id');
    }

    public function isRetryable(): bool
    {
        return $this->status === 'failed' && !empty($this->payload) && in_array($this->channel, ['sms', 'whatsapp', 'whatsapp_template'], true);
    }
}
