<?php

namespace App\Models\b2c;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class B2cOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'b2c_orders';

    protected $fillable = [
        'order_number',
        // Customer
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        // Cart & Address
        'cart_id',
        'shipping_address_id',
        'shipping_address_snapshot',
        // Pricing
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_fee',
        'grand_total',
        'roundoff',
        // Offers
        'offer_id',
        'coupon_code',
        'coupon_discount',
        'loyalty_points_used',
        'loyalty_points_earned',
        'bogo_discount',
        // Status
        'order_status',
        'payment_status',
        // Delivery
        'expected_delivery_date',
        'courier_partner',
        'tracking_number',
        'tracking_url',
        // Source
        'device_type',
        'utm_source',
        // Notes
        'customer_note',
        'admin_note',
    ];

    protected $casts = [
        'shipping_address_snapshot' => 'array',
        'subtotal'                  => 'decimal:2',
        'discount_amount'           => 'decimal:2',
        'tax_amount'                => 'decimal:2',
        'shipping_fee'              => 'decimal:2',
        'grand_total'               => 'decimal:2',
        'roundoff'                  => 'decimal:2',
        'coupon_discount'           => 'decimal:2',
        'loyalty_points_used'       => 'decimal:2',
        'loyalty_points_earned'     => 'decimal:2',
        'bogo_discount'             => 'decimal:2',
        'expected_delivery_date'    => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function items()
    {
        return $this->hasMany(B2cOrderItem::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(B2cOrderPayment::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(\App\Models\UserAddress::class, 'shipping_address_id');
    }

    public function offer()
    {
        return $this->belongsTo(\App\Models\Offer::class, 'offer_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('order_status', 'pending');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Generate a unique B2C order number.
     * Format: B2C-YYYY-XXXXX (e.g. B2C-2026-00123)
     */
    public static function generateOrderNumber(): string
    {
        $year  = date('Y');
        $last  = static::whereYear('created_at', $year)->max('id') ?? 0;
        $seq   = str_pad($last + 1, 5, '0', STR_PAD_LEFT);
        return "B2C-{$year}-{$seq}";
    }
}
