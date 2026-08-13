<?php

namespace App\Models\product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_product_code';

    protected $fillable =
    [
        'id','product_id', 'product_code','product_type', 'product_name', 'Company', 'Quality', 'Color', 'Type', 'Gender','Material','Temple_Detail','Bridge_Size', 
        'Description','Coating','Design','Index','Number','CT','Validity','Variant','Shape', 'Size','Modality','WC','Dk_t','Packing_Type','Track_Inventory',
        'Allow_Negative_Inventory','Purchase_Base_Price','Purchase_Price','Retail_Price','BB_Price','product_image','status','is_b2c','added_by','store_id','created_at',
        'updated_at','updated_by','main_image','productdetails','SPH','CYL','AXIS','ADD','in_house','category_id','subcategory_id',
        'vendor', 'tags', 'seo_title', 'seo_description', 'promotion_tag', 'special_collection', 'parent_product_code',
        'age', 'occasion', 'face_shape', 'lens_width', 'temple_length', 'frame_width', 
        'stock_quantity', 'stock_status', 'polarized', 'uv_protection', 'sunglass_colour', 'barcode',
        'discount_price', 'tax_hsn_code', 'base_carve', 'Diameter', 'supported_product_types',
        'selected_lens_packages'
    ];

    protected $casts = [
        'age' => 'array',
        'occasion' => 'array',
        'face_shape' => 'array',
        'Gender' => 'array',
        'supported_product_types' => 'array',
        'selected_lens_packages' => 'array',
    ];
}