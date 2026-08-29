<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GiftVoucher extends Model
{
    use HasFactory;

    protected $table = 'tbl_gift_vouchers';

    protected $fillable = [
        'name',
        'code',
        'voucher_value',
        'min_cart_amount',
        'start_date',
        'end_date',
        'validity_days',
        'membership_scope',
        'membership_card_id',
        'allow_bogo_stacking',
        'allow_coupon_stacking',
        'apply_on',
        'category_ids',
        'brand_ids',
        'product_ids',
        'description',
        'usage_limit_per_user',
        'total_used',
        'status',
        'added_by',
        'user_id',
        'contact_no',
        'source_order_no',
        'redeemed_order_no',
        'voucher_type',
        'is_single_use',
    ];

    protected $casts = [
        'voucher_value'          => 'decimal:2',
        'min_cart_amount'        => 'decimal:2',
        'start_date'             => 'date',
        'end_date'               => 'date',
        'validity_days'          => 'integer',
        'allow_bogo_stacking'    => 'boolean',
        'allow_coupon_stacking'  => 'boolean',
        'category_ids'           => 'array',
        'brand_ids'              => 'array',
        'product_ids'            => 'array',
        'usage_limit_per_user'   => 'integer',
        'total_used'             => 'integer',
        'is_single_use'          => 'boolean',
    ];

    /* ── Relationships ── */

    public function membershipCard()
    {
        return $this->belongsTo(\App\Models\MembershipCard::class, 'membership_card_id', 'id');
    }

    /* ── Scopes ── */

    public function scopeActive($query)
    {
        $now = now()->toDateString();
        return $query->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            });
    }

    public function scopeForCustomer($query, $userId = null, $phone = null)
    {
        return $query->where(function ($q) use ($userId, $phone) {
            $matched = false;
            if (!empty($userId)) {
                $q->orWhere('user_id', $userId);
                $matched = true;
            }
            if (!empty($phone)) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                $last10 = strlen($cleanPhone) >= 10 ? substr($cleanPhone, -10) : $cleanPhone;
                $q->orWhere('contact_no', $phone)
                  ->orWhere('contact_no', $cleanPhone)
                  ->orWhere('contact_no', 'LIKE', '%' . $last10);
                $matched = true;
            }
            if (!$matched) {
                // If neither user nor phone provided, match general vouchers with no customer binding
                $q->whereNull('user_id')->whereNull('contact_no');
            }
        });
    }

    /**
     * Check if a product item matches this voucher's eligibility scope.
     */
    public function isProductEligible($item)
    {
        if ($this->apply_on === 'all_products') {
            return true;
        }

        if ($this->apply_on === 'specific_category') {
            $catId = $item['category_id'] ?? null;
            return !empty($catId) && is_array($this->category_ids) && in_array($catId, $this->category_ids);
        }

        if ($this->apply_on === 'specific_brand') {
            $brandId = $item['brand_id'] ?? null;
            $company = $item['Company'] ?? $item['brand'] ?? '';
            if (!empty($brandId) && is_array($this->brand_ids) && in_array($brandId, $this->brand_ids)) {
                return true;
            }
            if (!empty($company) && is_array($this->brand_ids)) {
                return DB::table('tbl_brand')->where('brand_name', $company)->whereIn('brand_id', $this->brand_ids)->exists();
            }
            return false;
        }

        if ($this->apply_on === 'specific_products') {
            $prodId = $item['db_product_id'] ?? $item['frame_id'] ?? null;
            return !empty($prodId) && is_array($this->product_ids) && in_array($prodId, $this->product_ids);
        }

        return true;
    }
}
