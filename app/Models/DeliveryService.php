<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryService extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'description',
    'cost',
    'is_active',
    'applicable_to',
  ];

  protected $casts = [
    'cost' => 'decimal:2',
    'is_active' => 'boolean',
  ];

  public function deliveries()
  {
    return $this->hasMany(Delivery::class);
  }

  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  public function scopeApplicableTo($query, $eventType)
  {
    return $query->where(function ($q) use ($eventType) {
      $q->where('applicable_to', 'both')
        ->orWhere('applicable_to', $eventType);
    });
  }
}
