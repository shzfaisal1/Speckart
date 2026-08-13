<?php

namespace App\Models\purchases;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barcode  extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_barcode';
    
    protected $fillable = ['id','purchase_id','purchase_product_id', 'purchase_date', 'p_bill_no', 'product_code', 'product_type', 'product_id','barcode_no','lens_box',
    'purchase_price', 'retail_price', 'store_id', 'barcode_status', 'created_at', 'updated_at','perbox','product_details','refrence_no','transfer_store_id','discount',
    'updated_at_discount','discount_updated_by','inv_date','inv_ref_no','challan_date','challan_no','import_date','import_ref','inward_status','outward_status','transfer_store',
    'recevied_ref_no','recevied_date','transfer_inward_status','transfer_outward_status','t_status','adj_date','adj_comment','loss_damage','is_pair','batch_no','mfg_date',
    'expiry_date'];


}