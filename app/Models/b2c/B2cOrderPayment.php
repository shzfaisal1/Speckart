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
        'payment_gateway',
        'transaction_id',
        'gateway_order_id',
        'amount',
        'currency',
        'payment_method',
        'bank',
        'card_network',
        'status',
        'refund_id',
        'refund_amount',
        'refunded_at',
        'refund_reason',
        'webhook_payload',
        'failure_reason',
        'paid_at',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'refund_amount'   => 'decimal:2',
        'refunded_at'     => 'datetime',
        'paid_at'         => 'datetime',
        'webhook_payload' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(B2cOrder::class, 'order_id');
    }
}
