<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRecord extends Model
{
  protected $table = 'payment_records';

  protected $primaryKey = 'payment_key';

  public $incrementing = false;

  protected $keyType = 'string';

  public $timestamps = false;

  protected $casts = [
    'amount' => 'decimal:2',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];
}
