<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LensPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lens_packages';

    protected $fillable = [
        'name', 'slug', 'short_description',
        'current_price', 'original_price', 'warranty_months',
        'is_free_lens', 'package_type', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'current_price'  => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_free_lens'   => 'boolean',
        'is_active'      => 'boolean',
    ];

    // ── Relationships ──

    public function tags()
    {
        return $this->belongsToMany(
            LensPackageTag::class,
            'lens_package_tag_map',
            'lens_package_id',
            'tag_id'
        );
    }

    public function benefits()
    {
        return $this->belongsToMany(
            LensBenefit::class,
            'lens_package_benefits',
            'lens_package_id',
            'benefit_id'
        )->withPivot('sort_order', 'is_highlighted')
         ->orderByPivot('sort_order');
    }

    public function media()
    {
        return $this->hasMany(LensPackageMedia::class);
    }

    public function badges()
    {
        return $this->hasMany(LensPackageBadge::class)->orderBy('sort_order');
    }

    public function coupons()
    {
        return $this->belongsToMany(
            Coupon::class,
            'lens_package_coupons',
            'lens_package_id',
            'coupon_id'
        );
    }

    public function powerTypes()
    {
        return $this->belongsToMany(
            PowerType::class,
            'lens_package_power_types',
            'lens_package_id',
            'power_type_cat_id'
        );
    }

    public function categories()
    {
        return $this->belongsToMany(
            ProductType::class,
            'lens_package_product_types',
            'lens_package_id',
            'product_type_id'
        );
    }

    public function frameTypes()
    {
        return $this->belongsToMany(
            FrameType::class,
            'lens_package_frame_types',
            'lens_package_id',
            'type_id'
        );
    }

    public function frameShapes()
    {
        return $this->belongsToMany(
            FrameShape::class,
            'lens_package_frame_shapes',
            'lens_package_id',
            'shape_id'
        );
    }

    public function brands()
    {
        return $this->belongsToMany(
            Brand::class,
            'lens_package_brands',
            'lens_package_id',
            'brand_id'
        );
    }

    // ── Accessors ──

    /**
     * Compute the discount percentage from original → current price.
     */
    public function getDiscountPercentAttribute(): ?int
    {
        if (!$this->original_price || $this->original_price <= 0) {
            return null;
        }

        return (int) round(
            (($this->original_price - $this->current_price) / $this->original_price) * 100
        );
    }

    // ── Scopes ──

    /**
     * Scope to only active lens packages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
