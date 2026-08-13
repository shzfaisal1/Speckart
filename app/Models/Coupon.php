<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        'code', 'description', 'discount_type', 'discount_value',
        'min_order_value', 'max_discount_amount',
        'valid_from', 'valid_until',
        'max_uses', 'current_uses', 'is_active',
    ];

    protected $casts = [
        'discount_value'     => 'decimal:2',
        'min_order_value'    => 'decimal:2',
        'max_discount_amount'=> 'decimal:2',
        'is_active'          => 'boolean',
        'valid_from'         => 'datetime',
        'valid_until'        => 'datetime',
    ];

    // ── Relationships ──

    public function lensPackages()
    {
        return $this->belongsToMany(
            LensPackage::class,
            'lens_package_coupons',
            'coupon_id',
            'lens_package_id'
        );
    }

    // ── Scopes ──

    /**
     * Coupons that are flagged active and within their validity window.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('valid_from', '<=', Carbon::now())
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', Carbon::now());
            });
    }

    /**
     * Active coupons that have not exceeded their usage limit.
     */
    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('max_uses')
                  ->orWhereColumn('current_uses', '<', 'max_uses');
            });
    }

    // ── Helper Methods ──

    /**
     * Determine whether this coupon is currently valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        if ($this->max_uses !== null && $this->current_uses >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount for a given order amount.
     */
    public function calculateDiscount(float $amount): float
    {
        $discount = 0.0;

        if ($this->discount_type === 'percentage') {
            $discount = ($amount * $this->discount_value) / 100;
        } else {
            // fixed
            $discount = (float) $this->discount_value;
        }

        // Cap at the configured maximum, if any.
        if ($this->max_discount_amount !== null && $discount > (float) $this->max_discount_amount) {
            $discount = (float) $this->max_discount_amount;
        }

        // Never discount more than the order amount itself.
        return min($discount, $amount);
    }
}
