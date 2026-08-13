<?php

namespace App\Models\purchases;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challan  extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_challan';
    
    protected $fillable = ['id','supplier_name', 'challan_no', 'challan_date', 'tax_type', 'total_qty', 'total_base_amount', 'total_gst_amount'
    , 'total_p_amount',  'tax_rule', 'added_by', 'recevied_store_id', 'billing_store_id', 'created_at', 'updated_at', 'updated_by'];

    public function products()
    {
        return $this->hasMany(ChallanProduct::class);
    }
}