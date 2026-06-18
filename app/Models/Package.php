<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'cover_image', 'base_price', 'sku', 'metadata', 'is_active'];

    protected $casts = [
        'metadata' => 'array',
        'base_price' => 'decimal:2',
    ];

    public function materials()
    {
        return $this->hasMany(PackageMaterial::class);
    }

    public function fonts()
    {
        return $this->hasMany(PackageFont::class);
    }

    public function colors()
    {
        return $this->hasMany(PackageColor::class);
    }

    public function customizations()
    {
        return $this->hasMany(PackageCustomization::class);
    }
}
