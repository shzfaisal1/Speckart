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
        'name', 'slug', 'product_code', 'company', 'quality', 'lens_index', 'coating', 'material', 'design',
        'min_sph', 'max_sph', 'min_cyl', 'max_cyl', 'min_axis', 'max_axis',
        'short_description', 'purchase_price', 'current_price', 'original_price', 'warranty_months',
        'is_free_lens', 'package_type', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'current_price'  => 'decimal:2',
        'original_price' => 'decimal:2',
        'min_sph'        => 'decimal:2',
        'max_sph'        => 'decimal:2',
        'min_cyl'        => 'decimal:2',
        'max_cyl'        => 'decimal:2',
        'min_axis'       => 'integer',
        'max_axis'       => 'integer',
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

    // ── Mutators ──

    public function setPurchasePriceAttribute($value)
    {
        $this->attributes['purchase_price'] = ($value !== null && $value !== '') ? (float)$value : 0;
    }

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

    /**
     * Check if package has power restrictions.
     */
    public function getHasPowerLimitsAttribute(): bool
    {
        return !is_null($this->min_sph) || !is_null($this->max_sph)
            || !is_null($this->min_cyl) || !is_null($this->max_cyl)
            || !is_null($this->min_axis) || !is_null($this->max_axis);
    }

    /**
     * User-friendly power range string.
     * E.g. "SPH: -6.00 to +4.00 | CYL: -2.00 to 0.00 | Axis: 10° to 170°"
     */
    public function getPowerRangeTextAttribute(): ?string
    {
        if (!$this->has_power_limits) {
            return null;
        }

        $parts = [];
        if (!is_null($this->min_sph) && !is_null($this->max_sph)) {
            $min = ($this->min_sph > 0 ? '+' : '') . number_format($this->min_sph, 2);
            $max = ($this->max_sph > 0 ? '+' : '') . number_format($this->max_sph, 2);
            $parts[] = "SPH: {$min} to {$max}";
        } elseif (!is_null($this->min_sph)) {
            $min = ($this->min_sph > 0 ? '+' : '') . number_format($this->min_sph, 2);
            $parts[] = "SPH: Min {$min}";
        } elseif (!is_null($this->max_sph)) {
            $max = ($this->max_sph > 0 ? '+' : '') . number_format($this->max_sph, 2);
            $parts[] = "SPH: Max {$max}";
        }

        if (!is_null($this->min_cyl) && !is_null($this->max_cyl)) {
            $min = ($this->min_cyl > 0 ? '+' : '') . number_format($this->min_cyl, 2);
            $max = ($this->max_cyl > 0 ? '+' : '') . number_format($this->max_cyl, 2);
            $parts[] = "CYL: {$min} to {$max}";
        } elseif (!is_null($this->min_cyl)) {
            $min = ($this->min_cyl > 0 ? '+' : '') . number_format($this->min_cyl, 2);
            $parts[] = "CYL: Min {$min}";
        } elseif (!is_null($this->max_cyl)) {
            $max = ($this->max_cyl > 0 ? '+' : '') . number_format($this->max_cyl, 2);
            $parts[] = "CYL: Max {$max}";
        }

        if (!is_null($this->min_axis) && !is_null($this->max_axis)) {
            $parts[] = "Axis: {$this->min_axis}° to {$this->max_axis}°";
        } elseif (!is_null($this->min_axis)) {
            $parts[] = "Axis: Min {$this->min_axis}°";
        } elseif (!is_null($this->max_axis)) {
            $parts[] = "Axis: Max {$this->max_axis}°";
        }

        return implode(' | ', $parts);
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
