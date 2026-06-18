<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackageFont extends Model
{
    use HasFactory;

    protected $fillable = ['package_id', 'name', 'google_font_family', 'price_modifier'];

    protected $casts = [
        'price_modifier' => 'decimal:2',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
