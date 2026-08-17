<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerType extends Model
{
    protected $table = 'power_type_cat';

    protected $fillable = ['description', 'tag', 'images', 'is_active'];

    public function lensPackages()
    {
        return $this->belongsToMany(
            LensPackage::class,
            'lens_package_power_types',
            'power_type_cat_id',
            'lens_package_id'
        );
    }
}
