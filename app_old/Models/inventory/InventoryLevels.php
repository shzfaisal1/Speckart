<?php

namespace App\Models\inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryLevels extends Model
{
    protected $table = 'tbl_inventory_levels';

    protected $fillable = [
        'id',
        'product_type',
        'product_id',
        'product_code',
        'available_quantity',
        'tota_lens_qty',
        'product_details',
        'perbox',
        'store_id',
        'created_at',
        'updated_at',
        'status'
    ];

    public $timestamps = true; // set false if table has no timestamps
}