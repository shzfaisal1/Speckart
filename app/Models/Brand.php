<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'tbl_brand';
    protected $primaryKey = 'brand_id';

    protected $fillable = [
        'brand_name', 'product_type', 'image', 'by_one_get_one', 'loyalty_eligible', 'status', 'added_by', 'store_id'
    ];

    public function lensPackages()
    {
        return $this->belongsToMany(
            LensPackage::class,
            'lens_package_brands',
            'brand_id',
            'lens_package_id'
        );
    }
}
