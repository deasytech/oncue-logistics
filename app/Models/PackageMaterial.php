<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackageMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['package_id', 'name', 'price_modifier', 'options', 'image'];

    protected $casts = [
        'price_modifier' => 'decimal:2',
        'options' => 'array',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
