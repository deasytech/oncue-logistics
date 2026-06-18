<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackageColor extends Model
{
    use HasFactory;

    protected $fillable = ['package_id', 'name', 'hex', 'price_modifier'];

    protected $casts = [
        'price_modifier' => 'decimal:2',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
