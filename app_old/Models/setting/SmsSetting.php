<?php

namespace App\Models\setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    use HasFactory;
    
   protected $table = 'tbl_sms';
    protected $fillable = [
        'welcome_sms', 'important_otp_status', 'secure_otp_option', 
        'manually_mobile_no', 'secure_download_option', 'manually_mobile_no_report',
        'deleteOrder','deleteStock','deleteChallan','deleteExpense','deleteVouchers',
        'deleteProductCode','deleteCustomer','deletePrescription','userFranchise',
        'loyaltyProgram','discountCoupons','customerAccountOption','allowNegativeInventoryInProducts',
    ];
    public $timestamps = false; // if table doesn’t have created_at/updated_at
}