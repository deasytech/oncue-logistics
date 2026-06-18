<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'state_id',
        'iso_code',
        'country_code',
        'latitude',
        'longitude',
        'is_active'
    ];

    // Relationships
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
