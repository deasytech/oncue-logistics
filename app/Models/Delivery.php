<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'event_id',
        'delivery_service_id',
        'delivery_required',
        'address',
        'status',
        'delivered_at',
        'cost',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
    ];

    protected $casts = [
        'delivery_required' => 'boolean',
        'cost' => 'decimal:2',
        'paid_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function deliveryService()
    {
        return $this->belongsTo(DeliveryService::class);
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function hasPackages()
    {
        // Check if the customer who owns this delivery's event has any package customizations
        return $this->event->customer->packageCustomizations()->exists();
    }
}
