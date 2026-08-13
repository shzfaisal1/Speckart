<?php

namespace App\Models\purchases;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseProduct  extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_purchase_deatils';
    
    protected $fillable = ['purchase_id','bill_no','product_type', 'product_code', 'product_details', 'quality_detail', 'company_detail', 'product_price', 'product_base_price'
    , 'hsn_code', 'gst_amt', 'gst', 'product_purchase_price', 'qty', 'total_purchase_price', 'product_retail_price', 'color_details', 'material_detail','product_name'
    , 'size_details', 'Type_details', 'gender_details', 'shape_details', 'coating_detail', 'design_details', 'index_detail'
    , 'Number_detail', 'ct_detail', 'validity_detail', 'sph_detail', 'cyl_details', 'axis_detail', 'addiional_detail', 'bc_detail', 'diameter_detail', 'powertype_details'
    , 'box_detail', 'perbox_detail', 'batchno_details','mfg_detail','expiry_detail','variant_detail',
    'description_details', 'store_id','created_at','updated_at','product_id','is_pair'];


}