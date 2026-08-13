<?php

namespace App\Models\purchases;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase  extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_purchase';
    
    protected $fillable = ['purchase_id','supplier_name', 'p_bill_no', 'purchase_date', 'tax_type', 'total_qty', 'total_unit_amount', 'total_base_amount', 'total_gst_amount'
    , 'total_p_amount', 'round_off', 'net_purchase_amount', 'tax_rule', 'added_by', 'store_id', 'created_at', 'updated_at', 'updated_by'];

    public function products()
    {
        return $this->hasMany(PurchaseProduct::class);
    }
}