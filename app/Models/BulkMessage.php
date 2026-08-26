<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkMessage extends Model
{
    protected $fillable = [
        'customer_id',
        'event_id',
        'created_by',
        'title',
        'body',
        'channels',
        'total_recipients',
    ];

    protected $casts = [
        'channels' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deliveries()
    {
        return $this->hasMany(BulkMessageDelivery::class);
    }
}
