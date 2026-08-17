<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eyetest extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_eye_test';

    protected $fillable =
    [
        'test_id', 'visit_purpose', 'token_no', 'waiting_time', 'contact_no', 'cust_name', 'age_group', 'gender', 'status','added_by','store_id','created_at','updated_at'
        ,'visit_rason','yob','screen_time','Occupation','cust_carry','eye_test_before','re_sph','re_cyl','re_axis','le_sph','le_cyl','le_axis'
        ,'remark_arpower','right_eye','left_eys','both_eyes','optometrist','test_status','re_distance','le_distance','re_pinhole','le_pinhole','re_near'
        ,'le_near','last_eye_test_date','torch_light','reason_torch','cover_uncover','reason_cover_uncover','convergence','reason_convergence'
        ,'re_green_red','le_green_red','re_refined','le_refined','re_balanced','le_balanced','additional_power','re_ap','le_ap','p_verify_remark'
        ,'re_sph_new','re_cyl_new','re_axis_new','pd_re_new','le_sph_new','le_cyl_new','le_axis_new','pd_le_new','re_sph_bif'
        ,'re_cyl_bif','re_axis_bif','pd_re_bif','le_sph_bif','le_cyl_bif','le_axis_bif','pd_le_bif','re_distance_new','le_distance_new'
        ,'re_near_new','le_near_new','frame_size','followup_date'
        
    ];
}