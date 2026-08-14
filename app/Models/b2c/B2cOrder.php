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
        'frame_total',
        'lens_total',
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
        // Statuses
        'order_status',
        'rx_verification_status',
        'is_rx_required',
        'payment_status',
        'delivery_method',
        // Delivery
        'expected_delivery_date',
        'courier_partner',
        'tracking_number',
        'tracking_url',
        // Lab & Fulfillment
        'assigned_lab_id',
        'lab_status',
        'lab_job_number',
        'lab_notes',
        'lab_assigned_at',
        'lab_completed_at',
        // Prescription Verification
        'verified_by',
        'verified_at',
        'optometrist_notes',
        // Source
        'device_type',
        'utm_source',
        // Notes & Returns
        'customer_note',
        'admin_note',
        'return_reason',
        'exchange_type',
        'warranty_status',
    ];

    protected $casts = [
        'shipping_address_snapshot' => 'array',
        'subtotal'                  => 'decimal:2',
        'frame_total'               => 'decimal:2',
        'lens_total'                => 'decimal:2',
        'discount_amount'           => 'decimal:2',
        'tax_amount'                => 'decimal:2',
        'shipping_fee'              => 'decimal:2',
        'grand_total'               => 'decimal:2',
        'roundoff'                  => 'decimal:2',
        'coupon_discount'           => 'decimal:2',
        'loyalty_points_used'       => 'decimal:2',
        'loyalty_points_earned'     => 'decimal:2',
        'bogo_discount'             => 'decimal:2',
        'is_rx_required'            => 'boolean',
        'expected_delivery_date'    => 'date',
        'lab_assigned_at'           => 'datetime',
        'lab_completed_at'          => 'datetime',
        'verified_at'               => 'datetime',
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

    public function logs()
    {
        return $this->hasMany(B2cOrderLog::class, 'order_id')->latest('created_at');
    }

    public function notes()
    {
        return $this->hasMany(B2cOrderNote::class, 'order_id')->latest();
    }

    public function returns()
    {
        return $this->hasMany(B2cOrderReturn::class, 'order_id')->latest();
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function optometrist()
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
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

    public function scopePendingRx($query)
    {
        return $query->where('rx_verification_status', 'pending_review');
    }

    public function scopeInLab($query)
    {
        return $query->whereIn('lab_status', ['assigned', 'cutting', 'fitting']);
    }

    // ── Accessors & Helpers ────────────────────────────────────────────────

    public function getCustomerNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name ?? $this->guest_name ?? 'Valued Customer';
        }
        $snapshot = $this->shipping_address_snapshot;
        if (is_array($snapshot) && !empty($snapshot['full_name'])) {
            return $snapshot['full_name'];
        }
        return $this->guest_name ?? 'Guest Customer';
    }

    public function getCustomerPhoneAttribute(): ?string
    {
        if ($this->user && !empty($this->user->phone)) {
            return $this->user->phone;
        }
        $snapshot = $this->shipping_address_snapshot;
        if (is_array($snapshot) && !empty($snapshot['phone'])) {
            return $snapshot['phone'];
        }
        return $this->guest_phone;
    }

    public function getCustomerEmailAttribute(): ?string
    {
        if ($this->user && !empty($this->user->email)) {
            return $this->user->email;
        }
        $snapshot = $this->shipping_address_snapshot;
        if (is_array($snapshot) && !empty($snapshot['email'])) {
            return $snapshot['email'];
        }
        return $this->guest_email;
    }

    public function getFullAddressTextAttribute(): string
    {
        $snapshot = $this->shipping_address_snapshot;
        if (is_array($snapshot)) {
            $parts = array_filter([
                $snapshot['house_no'] ?? null,
                $snapshot['road_area'] ?? null,
                $snapshot['landmark'] ?? null,
                $snapshot['city'] ?? null,
                $snapshot['state'] ?? null,
                $snapshot['pincode'] ?? null,
            ]);
            if (!empty($parts)) {
                return implode(', ', $parts);
            }
            if (!empty($snapshot['full_address'])) {
                return $snapshot['full_address'];
            }
        }
        return 'No address available';
    }

    /**
     * Generate unique Order Number: B2C-YYYY-XXXXX
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        $seq  = str_pad($last + 1, 5, '0', STR_PAD_LEFT);
        return "B2C-{$year}-{$seq}";
    }
}
