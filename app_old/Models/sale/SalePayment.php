<?php

namespace App\Models\sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePayment  extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_sale_payment';
    
    protected $fillable = ['payment_id','sale_id', 'order_no','total_price', 'pay_amount', 'bal_amount', 'pay_details', 'pay_method', 'pay_date', 'store_id','added_by','pay_type'
    , 'created_at', 'updated_at'];

    
}