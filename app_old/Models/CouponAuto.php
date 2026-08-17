<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponAuto extends Model
{
    protected $table = 'tbl_coupon_auto';

    protected $fillable = [
        'from_range',
        'to_range',
        'coupon_value',
        'sales_value_amount',
        'auto_status',
        'coupon_value_type',
        'sales_value',
        'valid_dyas'
    ];

    public $timestamps = true; // set false if table has no timestamps
}