<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkMessageDelivery extends Model
{
    protected $fillable = [
        'bulk_message_id',
        'guest_id',
        'channel',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function bulkMessage()
    {
        return $this->belongsTo(BulkMessage::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
