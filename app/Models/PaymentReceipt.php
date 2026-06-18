<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
  use HasFactory;

  protected $fillable = [
    'customer_id',
    'file_name',
    'file_path',
    'original_name',
    'mime_type',
    'file_size',
    'description',
    'status',
    'admin_notes',
  ];

  protected $casts = [
    'file_size' => 'integer',
  ];

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function getFileSizeFormattedAttribute(): string
  {
    $bytes = $this->file_size;

    if ($bytes >= 1073741824) {
      return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
      return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
      return number_format($bytes / 1024, 2) . ' KB';
    } else {
      return $bytes . ' bytes';
    }
  }

  public function getStatusBadgeClassAttribute(): string
  {
    return match ($this->status) {
      'approved' => 'bg-success',
      'rejected' => 'bg-danger',
      default => 'bg-warning',
    };
  }

  public function getStatusTextAttribute(): string
  {
    return ucfirst($this->status);
  }
}
