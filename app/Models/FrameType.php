<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrameType extends Model
{
    protected $table = 'tbl_type';
    protected $primaryKey = 'type_id';

    protected $fillable = [
        'type_name', 'product_type', 'status', 'added_by', 'store_id'
    ];

    public function lensPackages()
    {
        return $this->belongsToMany(
            LensPackage::class,
            'lens_package_frame_types',
            'type_id',
            'lens_package_id'
        );
    }
}
