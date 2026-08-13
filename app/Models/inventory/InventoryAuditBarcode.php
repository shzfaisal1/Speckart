<?php

namespace App\Models\inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryAuditBarcode extends Model
{
    protected $table = 'tbl_inventory_audit_barcode';

    protected $fillable = [
        'id',
        'audit_id',
        'barcode',
        'status'
    ];

    public $timestamps = true; // set false if table has no timestamps
}