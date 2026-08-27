<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\product\Product;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'offers';

    protected $fillable = [
        'name',
        'offer_type',
        'discount_type',
        'discount_value',
        'coupon_code',
        'description',
        'start_date',
        'end_date',
        'min_cart_amount',
        'max_discount',
        'user_type',
        'usage_limit',
        'usage_limit_per_user',
        'apply_on',
        'category_ids',
        'brand_ids',
        'product_ids',
        'status',
        'show_as_banner',
        'banner_position',
        'banner_image',
        'added_by',
        'store_id',
        // BOGO fields
        'bogo_buy_qty',
        'bogo_get_qty',
        'bogo_free_discount',
        'bogo_extra_enabled',
        'bogo_extra_discount',
        'bogo_third_apply_on',
        'bogo_third_brand_ids',
        'bogo_third_category_ids',
        'bogo_third_product_ids',
        // Voucher fields
        'voucher_value',
        'voucher_validity_days',
        // Membership Bundle & 3-State Banner fields
        'linked_product_id',
        'membership_mrp',
        'membership_sale_price',
        'entitlement_type',
        'entitlement_scope',
        'cashback_percent',
        'cashback_delay_days',
        'stack_with_coupons',
        'trigger_product_id',
        'banner_message_1',
        'banner_message_2',
        'banner_message_3',
        'free_item_scope',
        'get_item_discount_percent',
    ];

    protected $casts = [
        'start_date'                => 'date',
        'end_date'                  => 'date',
        'discount_value'            => 'decimal:2',
        'min_cart_amount'           => 'decimal:2',
        'max_discount'              => 'decimal:2',
        'category_ids'              => 'array',
        'brand_ids'                 => 'array',
        'product_ids'               => 'array',
        'show_as_banner'            => 'boolean',
        'bogo_extra_enabled'        => 'boolean',
        'bogo_extra_discount'       => 'decimal:2',
        'bogo_free_discount'        => 'decimal:2',
        'bogo_third_brand_ids'      => 'array',
        'bogo_third_category_ids'   => 'array',
        'bogo_third_product_ids'    => 'array',
        'voucher_value'             => 'decimal:2',
        'membership_mrp'            => 'decimal:2',
        'membership_sale_price'     => 'decimal:2',
        'cashback_percent'          => 'decimal:2',
        'get_item_discount_percent' => 'decimal:2',
        'stack_with_coupons'        => 'boolean',
    ];

    /* ── Scopes ── */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /* ── Accessors ── */

    public function getDiscountLabelAttribute()
    {
        if ($this->offer_type === 'membership_bundle') {
            return 'Membership BOGO';
        }
        if ($this->discount_type === 'percentage') {
            return rtrim(rtrim(number_format($this->discount_value, 2), '0'), '.') . '% OFF';
        }
        return '₹' . number_format($this->discount_value, 0) . ' OFF';
    }

    public function getOfferTypeLabelAttribute()
    {
        $labels = [
            'percentage_discount' => 'Percentage Discount',
            'flat_discount'       => 'Flat Discount',
            'buy1get1'            => 'Buy 1 Get 1',
            'gift_voucher'        => 'Gift Voucher',
            'membership_bundle'   => 'Membership / Bundle Add-on',
            'cashback'            => 'Cashback',
        ];
        return $labels[$this->offer_type] ?? $this->offer_type;
    }

    /**
     * Fetch products associated with this offer using Query Builder.
     */
    public function getEligibleProducts()
    {
        $query = DB::table('tbl_product_code')->where('status', 1);

        switch ($this->apply_on) {
            case 'specific_category':
                $categoryIds = is_array($this->category_ids) ? $this->category_ids : json_decode($this->category_ids, true);
                if (!empty($categoryIds)) {
                    $query->whereIn('category_id', $categoryIds);
                } else {
                    $query->whereRaw('1 = 0'); // Empty categories, return no products
                }
                break;

            case 'specific_brand':
                $brandIds = is_array($this->brand_ids) ? $this->brand_ids : json_decode($this->brand_ids, true);
                if (!empty($brandIds)) {
                    // Map brand IDs to brand names since products match brands using 'Company' field
                    $brandNames = DB::table('tbl_brand')
                        ->whereIn('brand_id', $brandIds)
                        ->pluck('brand_name')
                        ->toArray();
                    
                    if (!empty($brandNames)) {
                        $query->whereIn('Company', $brandNames);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                } else {
                    $query->whereRaw('1 = 0');
                }
                break;

            case 'specific_products':
                $productIds = is_array($this->product_ids) ? $this->product_ids : json_decode($this->product_ids, true);
                if (!empty($productIds)) {
                    $query->whereIn('id', $productIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
                break;

            case 'all_products':
            default:
                // No limits, return all active products
                break;
        }

        return $query->orderBy('product_name')->get();
    }
}
