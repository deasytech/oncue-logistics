<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = [
        'name',
        'iso_code',
        'country_code',
        'latitude',
        'longitude',
        'is_active'
    ];

    // Relationships
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
