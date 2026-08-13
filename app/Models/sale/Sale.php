<?php

namespace App\Models\sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale  extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_sales';
    
    protected $fillable = ['sale_id','sale_date', 'order_no', 'contact_no', 'cust_name', 'email_id', 'cust_address', 'state_id', 'city_id','pincode','total_item_price'
    , 'total_discount', 'fitting_fee', 'coupon_amount', 'loyalty_point_amount', 'total_payable', 'pay_amount', 'pending_amount', 'pay_deatils', 'pay_method','extrnal_warranty'
    ,'added_by','store_id','created_at','updated_at','total_basic_amount','total_gst_amount','sales_type','cust_id','gst_no','from_store','sale_person','membership_id'
    ,'cart_discount','cart_discount_per','cart_discount_by','cart_discount_resion','bogo_discount','coupon_id','loyalty_point_apply','earnedPoints','earncoupon','delivery_date','tax_rule'
    ,'sales_status','ready_reminder_sms','roundoff','return_pay_amount','credit_amount','return_amount','customer_account','advance_amount','inter_sale'];

    public function products()
    {
        return $this->hasMany(SaleProduct::class);
    }
}