<?php

namespace App\Models\inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryAudit extends Model
{
    protected $table = 'tbl_inventory_audit';

    protected $fillable = [
        'id',
        'store_id',
        'product_type',
        'company',
        'total_upload',
        'match_barcode',
        'invalid_barcode',
        'missing_barcode',
        'added_by',
        'created_at',
        'updated_at',
        'audit_status'
    ];

    public $timestamps = true; // set false if table has no timestamps
}