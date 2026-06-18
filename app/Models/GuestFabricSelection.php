<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestFabricSelection extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'event_id',
        'fabric_selections',
        'total_fabric_cost',
        'delivery_zone_id',
        'delivery_cost',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paid_at',
        'external_order_id',
    ];

    protected $casts = [
        'fabric_selections' => 'array',
        'total_fabric_cost' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function deliveryZone()
    {
        // This is a virtual relationship since zones come from external API
        // We'll use the service to get zone details when needed
        return null;
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function calculateTotal()
    {
        return $this->total_fabric_cost + $this->delivery_cost;
    }

    public function getFabricSelections()
    {
        return $this->fabric_selections ?? [];
    }

    public function getSelectedFabricIds()
    {
        return collect($this->getFabricSelections())->pluck('fabric_id')->toArray();
    }
}
