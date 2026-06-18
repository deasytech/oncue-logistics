<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackageCustomization extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'customer_id',
        'material_id',
        'font_id',
        'color_id',
        'message',
        'location',
        'quantity',
        'unit_price',
        'total_price',
        'preview_image_path',
        'meta',
        'status',
        'delivery_service_id'
    ];

    protected $casts = [
        'meta' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function material()
    {
        return $this->belongsTo(PackageMaterial::class);
    }

    public function font()
    {
        return $this->belongsTo(PackageFont::class);
    }

    public function color()
    {
        return $this->belongsTo(PackageColor::class);
    }

    public function deliveryService()
    {
        return $this->belongsTo(DeliveryService::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'package_customization_event');
    }
}
