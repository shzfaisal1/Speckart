<?php

namespace App\Models\sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    use HasFactory;

    protected $table = 'tbl_sale_payment';

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        // ── Original POS fields ──────────────────────────────────────────
        'payment_id', 'sale_id', 'order_no', 'total_price', 'pay_amount',
        'bal_amount', 'pay_details', 'pay_method', 'pay_date',
        'store_id', 'added_by', 'pay_type', 'created_at', 'updated_at',

        // ── B2C Gateway fields (replaces b2c_order_payments) ───────────────
        'payment_gateway', 'transaction_id', 'gateway_order_id',
        'currency', 'bank', 'card_network', 'gateway_status',
        'refund_id', 'refund_amount', 'refunded_at', 'refund_reason',
        'webhook_payload', 'failure_reason', 'paid_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'refunded_at'   => 'datetime',
        'paid_at'       => 'datetime',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'sale_id');
    }
}