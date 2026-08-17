<?php

namespace App\Models\inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryRecords extends Model
{
    protected $table = 'tbl_inventory_record';

    protected $fillable = [
        'id',
        'product_type',
        'product_code',
        'product_id',
        'product_details',
        'qty',
        'perbox',
        'added_date',
        'inward_status',
        'outward_status',
        'store_id',
        'created_at',
        'updated_at',
        'added_by'
    ];

    public $timestamps = true; // set false if table has no timestamps
}