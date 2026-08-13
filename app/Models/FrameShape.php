<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrameShape extends Model
{
    protected $table = 'tbl_shape';
    protected $primaryKey = 'shape_id';

    protected $fillable = [
        'shape_name', 'product_type', 'status', 'added_by', 'store_id'
    ];

    public function lensPackages()
    {
        return $this->belongsToMany(
            LensPackage::class,
            'lens_package_frame_shapes',
            'shape_id',
            'lens_package_id'
        );
    }
}
