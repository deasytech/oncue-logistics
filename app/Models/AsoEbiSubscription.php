<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsoEbiSubscription extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'amount'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
