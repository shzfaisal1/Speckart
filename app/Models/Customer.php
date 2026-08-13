<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_customer';

    protected $fillable =
    [
        'customer_id', 'cust_unique_id', 'cust_name', 'contact_no', 'email_id', 'cust_category', 'gender', 'cust_address', 'state_id','city_id'
        ,'pincode','dob','doa','Loyalty_Points','Loyalty_Points_Redeem','Loyalty_Points_Bal','added_by','store_id','created_at','updated_at','updated_by'
        ,'is_Deleted','cust_type','cust_note','company_name','gst_no','credit_amount'
    ];
}