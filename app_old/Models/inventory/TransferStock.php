<?php

namespace App\Models\inventory;

use Illuminate\Database\Eloquent\Model;

class TransferStock extends Model
{
    protected $table = 'tbl_transfer_stock';

    protected $fillable = [
        'transfer_id',
        'product_id',
        'product_type',
        'product_code',
        'barcode_no',
        'product_details',
        'perbox',
        'purchase_price',
        'retail_price',
        'refrence_no',
        'from_store',
        'to_store',
        'transfer_stock',
        'transfer_comment',
        'transfer_by',
        'created_at',
        'updated_at',
        'store_id'
    ];

    public $timestamps = true; // set false if table has no timestamps
}