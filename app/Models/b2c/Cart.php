<?php

namespace App\Models\b2c;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'session_id',
        'offer_id',
        'coupon_code',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function offer()
    {
        return $this->belongsTo(\App\Models\Offer::class, 'offer_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Get subtotal of all items in cart before discounts.
     */
    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return ($item->sale_price + $item->lens_package_price) * $item->qty;
        });
    }

    /**
     * Get grand total after discounts.
     */
    public function getGrandTotalAttribute(): float
    {
        return max(0, $this->subtotal - $this->discount_amount);
    }
}
