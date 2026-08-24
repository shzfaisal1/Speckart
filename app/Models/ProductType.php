<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $table = "product_type_masters";

    protected $fillable = [
        'name',
        'slug',
        'subtitle',
        'icon',
        'has_power',
        'default_powers',
        'default_lens_package_id', // FIX: Added for Zero Power auto-bundle
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'default_powers'          => 'array',
        'has_power'               => 'boolean',
        'is_active'               => 'boolean',
        'default_lens_package_id' => 'integer',
    ];

    /**
     * The default lens package linked to this product type.
     * Used for Zero Power / Screen Glass auto-bundle on BUY NOW.
     */
    public function defaultLensPackage()
    {
        return $this->belongsTo(\App\Models\LensPackage::class, 'default_lens_package_id');
    }
}
