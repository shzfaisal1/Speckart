<?php

namespace App\Models\sale;

use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    protected $table = 'tbl_order_item_tracking';

    protected $fillable = [
        'order_no',
        'sale_product_id',
        'store_id',
        'product_code',
        'product_type',
        'description',
        'tracking_status',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true; // set false if table has no timestamps
}