<?php

namespace App\Models\b2c;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2cOrderPayment extends Model
{
    use HasFactory;

    protected $table = 'b2c_order_payments';

    protected $fillable = [
        'order_id',
        // Gateway
        'payment_gateway',
        'transaction_id',
        'gateway_order_id',
        // Amount
        'amount',
        'currency',
        // Method
        'payment_method',
        'bank',
        'card_network',
        // Status
        'status',
        // Refund
        'refund_id',
        'refund_amount',
        'refunded_at',
        'refund_reason',
        // Raw Response
        'webhook_payload',
        'failure_reason',
        // Timestamp
        'paid_at',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'refund_amount'   => 'decimal:2',
        'webhook_payload' => 'array',
        'refunded_at'     => 'datetime',
        'paid_at'         => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(B2cOrder::class, 'order_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return in_array($this->status, ['refunded', 'partially_refunded']);
    }
}
