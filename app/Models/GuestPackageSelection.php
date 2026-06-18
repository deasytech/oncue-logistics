<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestPackageSelection extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'event_id',
        'package_customization_id',
        'quantity',
        'unit_price',
        'total_price',
        'delivery_service_id',
        'delivery_cost',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paid_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
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

    public function packageCustomization()
    {
        return $this->belongsTo(PackageCustomization::class);
    }

    public function deliveryService()
    {
        return $this->belongsTo(DeliveryService::class);
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function calculateTotal()
    {
        return $this->total_price + $this->delivery_cost;
    }
}
