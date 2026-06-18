<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'reference',
        'amount',
        'status',
        'items',
        'payment_method',
        'customer_info',
    ];

    protected $casts = [
        'items' => 'array',
        'customer_info' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
