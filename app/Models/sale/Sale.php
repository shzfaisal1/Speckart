<?php

namespace App\Models\sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'tbl_sales';

    protected $primaryKey = 'sale_id';

    public $incrementing = true;

    protected $fillable = [
        // ── Original POS fields ──────────────────────────────────────────
        'sale_id', 'sale_date', 'order_no', 'contact_no', 'cust_name', 'email_id',
        'cust_address', 'state_id', 'city_id', 'pincode',
        'total_item_price', 'total_discount', 'fitting_fee', 'coupon_amount',
        'loyalty_point_amount', 'total_payable', 'pay_amount', 'pending_amount',
        'pay_deatils', 'pay_method', 'extrnal_warranty',
        'added_by', 'store_id', 'created_at', 'updated_at',
        'total_basic_amount', 'total_gst_amount', 'sales_type', 'cust_id',
        'gst_no', 'from_store', 'sale_person', 'membership_id',
        'cart_discount', 'cart_discount_per', 'cart_discount_by', 'cart_discount_resion',
        'coupon_id', 'loyalty_point_apply', 'earnedPoints', 'earncoupon',
        'delivery_date', 'tax_rule', 'sales_status', 'ready_reminder_sms',
        'roundoff', 'return_pay_amount', 'credit_amount', 'return_amount',
        'customer_account', 'advance_amount', 'inter_sale',

        // ── B2C User & Order Identity ────────────────────────────────────
        'user_id',

        // ── B2C Status ───────────────────────────────────────────────────
        'order_status', 'payment_status',

        // ── Prescription / RX Verification ───────────────────────────────
        'rx_verification_status', 'is_rx_required',
        'verified_by', 'verified_at', 'optometrist_notes',

        // ── Delivery & Shipping ──────────────────────────────────────────
        'delivery_method', 'expected_delivery_date',
        'courier_partner', 'tracking_number', 'tracking_url',
        'shipping_address_snapshot',

        // ── Lab / Fulfillment ────────────────────────────────────────────
        'assigned_lab_id', 'lab_status', 'lab_job_number', 'lab_notes',
        'lab_assigned_at', 'lab_completed_at',

        // ── Source & Attribution ─────────────────────────────────────────
        'device_type', 'utm_source',

        // ── B2C Pricing ──────────────────────────────────────────────────
        'frame_total', 'lens_total', 'bogo_discount', 'shipping_fee',

        // ── Notes ────────────────────────────────────────────────────────
        'customer_note', 'admin_note', 'updated_by',

        // ── Return / Exchange (replaces b2c_order_returns) ───────────────
        'return_type', 'return_reason', 'return_exchange_type',
        'return_stage', 'return_admin_notes', 'warranty_claim',
    ];

    protected $casts = [
        'is_rx_required'  => 'boolean',
        'warranty_claim'  => 'boolean',
        'verified_at'     => 'datetime',
        'lab_assigned_at' => 'datetime',
        'lab_completed_at'=> 'datetime',
        'frame_total'     => 'decimal:2',
        'lens_total'      => 'decimal:2',
        'bogo_discount'   => 'decimal:2',
        'shipping_fee'    => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    public function products()
    {
        return $this->hasMany(SaleProduct::class, 'sale_id', 'sale_id');
    }

    public function items()
    {
        return $this->hasMany(SaleProduct::class, 'sale_id', 'sale_id');
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class, 'sale_id', 'sale_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────

    /** B2C online orders only */
    public function scopeB2c($query)
    {
        return $query->where('sales_type', 0);
    }

    /** POS in-store orders only */
    public function scopePos($query)
    {
        return $query->where('sales_type', 1);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePendingRx($query)
    {
        return $query->where('rx_verification_status', 'pending_review');
    }

    public function scopeInLab($query)
    {
        return $query->whereIn('lab_status', ['assigned', 'cutting', 'fitting']);
    }

    // ── Accessors ────────────────────────────────────────────────────────

    public function getIdAttribute()
    {
        return $this->sale_id;
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->cust_name ?? ($this->guest_name ?? 'Valued Customer');
    }

    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->contact_no;
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->email_id;
    }

    public function getOrderNumberAttribute(): string
    {
        return $this->order_no ?? ('ORD-' . $this->sale_id);
    }

    public function getGrandTotalAttribute()
    {
        return (float) ($this->total_payable ?: 0);
    }

    public function getDiscountAmountAttribute()
    {
        return (float) ($this->total_discount ?: 0);
    }

    public function getTaxAmountAttribute()
    {
        return (float) ($this->total_gst_amount ?: 0);
    }

    public function getLoyaltyPointsEarnedAttribute()
    {
        return (int) ($this->earnedPoints ?: 0);
    }

    public function getSubtotalAttribute()
    {
        return (float) ($this->total_item_price ?: ($this->total_basic_amount ?: $this->total_payable));
    }

    public function getCouponCodeAttribute(): ?string
    {
        return $this->earncoupon;
    }
}