<?php

namespace App\Models\sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleProduct  extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_sales_product';
    
    protected $fillable = ['id','sale_id','order_no', 'product_deatils', 'product_discount', 'product_code', 'product_type', 'barcode_use','qty','hsn_code','gst','gst_amount'
    ,'margin_amt','margin','base_price','retail_price','sale_price','store_id','product_id','product_company','product_quality','product_material','product_color','product_design'
    ,'product_coating','product_index','product_number','product_ct','product_typesss','product_validity','product_shape','product_size','product_variant','right_left','doc_name'
    ,'wearing_type','discount_amt','package_id','coating_apply','frame_dbl','frame_ed','frame_fh','frame_asize','frame_bsize','frametypeglass','GL_EYE_RS_D','GL_EYE_RC_D'
    ,'GL_EYE_RA_D','GL_EYE_RP_D','GL_EYE_RV_D','GL_EYE_RS_N','GL_EYE_RC_N','GL_EYE_RA_N','GL_EYE_RP_N','GL_EYE_RV_N','GL_EYE_RADD','GL_EYE_totalPD'
    ,'GL_EYE_LS_D','GL_EYE_LC_D','GL_EYE_LA_D','GL_EYE_LP_D','GL_EYE_LV_D','GL_EYE_LS_N','GL_EYE_LC_N','GL_EYE_LA_N','GL_EYE_LP_N','GL_EYE_LV_N'
    ,'GL_EYE_LADD','right_purchase','right_glass','left_purchase','left_glass','count_eye_test','prescription_notes','purchase_price','wearing_types_inhouse','no_of_glass'
    ,'product_tracking','lensRightNoOfBoxes','lensRightTotalPieces','lensLeftNoOfBoxes','lensLeftTotalPieces','handover_status','handover_by','handover_date'
    ,'pay_return_method','pay_return_details','gatepass_status','return_status','inter_sale'];
    
    
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'sale_id');
    }

}