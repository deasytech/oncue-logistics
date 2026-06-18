<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FabricType extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'name',
    'description',
    'base_price',
    'is_active',
  ];

  protected $casts = [
    'base_price' => 'decimal:2',
    'is_active' => 'boolean',
    'deleted_at' => 'datetime',
  ];

  public function events()
  {
    return $this->belongsToMany(Event::class)
      ->withPivot('custom_price')
      ->withTimestamps();
  }

  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }
}
