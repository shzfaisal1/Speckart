<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_store';

    protected $fillable =
    [
        'store_id', 'store_name', 'contact_no', 'email_id', 'state_id', 'city_id', 'store_address', 'pincode', 'barcode_name','order_no_prefix'
        ,'Is_same_orderon','invoice_no_prefix','next_order_no','sales_tax_type','sales_text_per','Is_orderno_editable','Is_bill_editable'
        ,'tax_voucher_entry','min_advance_amt','gst_no','bb_mobile_no','Is_orderno_autofill'
        ,'bb_email','print_cust_challan','print_cust_invoice','status','created_at','updated_at','tax_rule','terms_cond'
    ];
}