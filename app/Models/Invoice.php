<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'invoice_number',
    'customer_id',
    'customer_name',
    'customer_email',
    'customer_phone',
    'customer_address',
    'title',
    'description',
    'subtotal',
    'tax_amount',
    'discount_amount',
    'total_amount',
    'amount_paid',
    'status',
    'payment_status',
    'paystack_reference',
    'paystack_transaction_id',
    'due_date',
    'sent_at',
    'paid_at',
    'notes',
    'footer_notes',
    'payment_token',
    'token_expires_at',
  ];

  protected $casts = [
    'subtotal' => 'decimal:2',
    'tax_amount' => 'decimal:2',
    'discount_amount' => 'decimal:2',
    'total_amount' => 'decimal:2',
    'amount_paid' => 'decimal:2',
    'due_date' => 'datetime',
    'sent_at' => 'datetime',
    'paid_at' => 'datetime',
    'token_expires_at' => 'datetime',
  ];

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class);
  }

  public function items(): HasMany
  {
    return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
  }

  public function scopePending($query)
  {
    return $query->where('status', 'sent')->where('payment_status', 'pending');
  }

  public function scopeOverdue($query)
  {
    return $query->where('status', 'sent')
      ->where('payment_status', 'pending')
      ->where('due_date', '<', now());
  }

  public function scopePaid($query)
  {
    return $query->where('payment_status', 'completed');
  }

  public function getBalanceDueAttribute(): float
  {
    return max(0, $this->total_amount - $this->amount_paid);
  }

  public function getIsPaidAttribute(): bool
  {
    return $this->payment_status === 'completed' && $this->balance_due <= 0;
  }

  public function getIsOverdueAttribute(): bool
  {
    if ($this->status !== 'sent' || $this->payment_status === 'completed') {
      return false;
    }

    return $this->due_date && $this->due_date->isPast();
  }

  public function markAsSent(): void
  {
    $this->update([
      'status' => 'sent',
      'sent_at' => now(),
    ]);
  }

  public function markAsPaid(string $transactionId = null): void
  {
    $this->update([
      'payment_status' => 'completed',
      'amount_paid' => $this->total_amount,
      'paid_at' => now(),
      'paystack_transaction_id' => $transactionId ?? $this->paystack_transaction_id,
    ]);
  }

  public function calculateTotals(): void
  {
    $subtotal = $this->items->sum('amount');
    $total = $subtotal + $this->tax_amount - $this->discount_amount;

    $this->update([
      'subtotal' => $subtotal,
      'total_amount' => max(0, $total),
    ]);
  }

  public function generatePaymentToken(): string
  {
    $token = bin2hex(random_bytes(32));

    $this->update([
      'payment_token' => $token,
      'token_expires_at' => now()->addDays(7),
    ]);

    return $token;
  }

  public function isTokenValid(?string $token): bool
  {
    if (!$token || $this->payment_token !== $token) {
      return false;
    }

    return $this->token_expires_at && $this->token_expires_at->isFuture();
  }

  protected static function boot(): void
  {
    parent::boot();

    static::creating(function ($invoice) {
      if (empty($invoice->invoice_number)) {
        $invoice->invoice_number = 'INV-' . strtoupper(uniqid());
      }
    });
  }
}
