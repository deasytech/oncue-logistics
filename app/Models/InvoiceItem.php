<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'invoice_id',
    'description',
    'quantity',
    'unit',
    'unit_price',
    'amount',
    'sort_order',
  ];

  protected $casts = [
    'quantity' => 'decimal:2',
    'unit_price' => 'decimal:2',
    'amount' => 'decimal:2',
  ];

  public function invoice(): BelongsTo
  {
    return $this->belongsTo(Invoice::class);
  }

  protected static function boot(): void
  {
    parent::boot();

    static::creating(function ($item) {
      $item->amount = $item->quantity * $item->unit_price;
    });

    static::updating(function ($item) {
      $item->amount = $item->quantity * $item->unit_price;
    });
  }
}
