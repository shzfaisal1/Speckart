<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Store;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use Hash;
use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use PDF;
use Carbon\Carbon;
use App\Models\product\Product;
use App\Models\Customer;
use App\Models\sale\Sale;
use App\Models\sale\SaleProduct;
use App\Models\sale\SalePayment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\OnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\sale\OrderTracking;


class SalesController extends Controller
{
    public $view_route = 'sales';
    
    
    public function saleDashboard()
    {
        $setting['page_title'] = 'Sale Dashboard';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view('layouts/sales',$setting);
    }
    
    public function createNewOrder()
    {
        $setting['page_title'] = 'Create New Order';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/new-order',$setting);
    }
    
    public function glassnumberDropdown(Request $request)
    {
        $search = $request->get('name');
        $glass = DB::table('tbl_gl_number')->where('glass_number', 'LIKE', "%{$search}%")->get(['glass_number']);
    
        return response()->json($glass);
    }
    
    
    public function getcustomerDetails(Request $request)
    {
        $contact_no = $request->contact_no;
        $customer = DB::table('tbl_customer')
            ->leftJoin('tbl_membership_card', 'tbl_customer.membership_card_id', '=', 'tbl_membership_card.card_id')
            ->where('tbl_customer.contact_no', $contact_no)
            ->select(
                'tbl_customer.*',
                'tbl_membership_card.card_name as membership_card_name',
                'tbl_membership_card.enable_bogo'
            )
            ->first();
    
        if ($customer) 
        {
            // Check if membership is active (not expired) and customer is B2C
            $bogoEnabled = false;
            $custType = $customer->cust_type ?? 'B2C';

            if ($custType === 'B2C' && $customer->membership_card_id && $customer->membership_expiry) {
                $expiry = \Carbon\Carbon::parse($customer->membership_expiry);
                if ($expiry->isFuture() && $customer->enable_bogo) {
                    $bogoEnabled = true;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'cust_name'            => $customer->cust_name,
                    'email_id'             => $customer->email_id,
                    'cust_category'        => $customer->cust_category,
                    'cust_type'            => $custType,
                    'company_name'         => $customer->company_name,
                    'gst_no'               => $customer->gst_no,
                    'credit_amount'        => $customer->credit_amount ?? 0,
                    'gender'               => $customer->gender,
                    'cust_address'         => $customer->cust_address,
                    'pincode'              => $customer->pincode,
                    'cust_note'            => $customer->cust_note,
                    'state_id'             => $customer->state_id,
                    'city_id'              => $customer->city_id,
                    'dob'                  => $customer->dob,
                    'doa'                  => $customer->doa,
                    // Membership fields
                    'membership_card_id'   => $customer->membership_card_id,
                    'membership_card_name' => $customer->membership_card_name,
                    'membership_expiry'    => $customer->membership_expiry,
                    'bogo_enabled'         => $bogoEnabled,
                ]
            ]);
        } 
        else
        {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ]);
        }
    }

    
    
    public function getStoreDetails(Request $request)
    {
        $selectedType = $request->selectedType;
    
        $tbl_store = DB::table('tbl_store')->where('id', $selectedType)->first();
    
        if (!$tbl_store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
            ]);
        }
    
        $lastOrder = DB::table('tbl_sales')
            ->where('store_id', $tbl_store->id)
            ->orderByDesc('sale_id')
            ->first();
    
        if ($lastOrder && isset($lastOrder->order_no)) {
            $numericPart = (int) str_replace($tbl_store->order_no_prefix, '', $lastOrder->order_no);
            $nextOrderNo = $numericPart + 1;
        } else {
            $nextOrderNo = $tbl_store->next_order_no;
        }
    
        $orderid = $tbl_store->order_no_prefix . $nextOrderNo;
    
        $state = State::find($tbl_store->state_id);
        $city  = City::find($tbl_store->city_id);
    
        return response()->json([
            'success' => true,
            'data' => [
                'order_no' => $orderid,
                'sales_tax_type' => $tbl_store->sales_tax_type,
                'tax_rule' => $tbl_store->tax_rule,
                'sales_text_per' => $tbl_store->sales_text_per,
    
                // Store details
                'store_name' => $tbl_store->store_name,
                'gst_no' => $tbl_store->gst_no,
                'mobile_no' => $tbl_store->contact_no,
                'address' => $tbl_store->store_address,
                'pincode' => $tbl_store->pincode,
                'city' => $city->name,
                'state' => $state->name,
            ]
        ]);
    }



    public function getBarcodeTable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $search = $request->input('pcode');
        
        $results = DB::table('tbl_barcode')
            ->where('barcode_status', '1')
            ->where('t_status', '0')
            ->where('store_id', $store_id)
            ->where('outward_status', NULL)
            ->where('product_code', $search)
            ->whereNull('lens_box');
        if (!$results)
        {
             $results->where('transfer_store_id', $store_id)->where('transfer_outward_status', NULL);
        } 
        
        $results = $results
            ->orderBy('id', 'DESC')
            ->get();
    

        return response()->json($results);
    }
    
    
    public function checkLoyaltyPoint(Request $request)
    {
        $mobile = $request->input('contact_no');
    
        $customer = DB::table('tbl_customer')
            ->where('contact_no', $mobile)
            ->select('Loyalty_Points_Bal')
            ->first();
    
        if ($customer) 
        {
            return response()->json([
                'exists' => true,
                'Loyalty_Points_Bal' => $customer->Loyalty_Points_Bal ?? 0,
            ]);
        }
    
        return response()->json(['exists' => false]);
    }
    
    
    public function checksetloyaltypointvalue(Request $request)
    {
        $tbl_loyalty = DB::table('tbl_loyalty')->where('id', 2)->first();
        
        $points = (int) $request->input('points');       
        $available = (int) $request->input('available'); 
        
        $order_use_loyalty = $tbl_loyalty->order_use_loyalty; 

        $maxAllowedPoints = ($available * $order_use_loyalty) / 100;
        
        if($points > $maxAllowedPoints)
        {
            $response['maxAllowedPoints'] = $maxAllowedPoints;
            $response['status_code'] = '200';
            $response['one_point_redem'] = '';
        }
        else
        {
            $tbl_loyaltys = DB::table('tbl_loyalty')->where('id', 1)->first();
            
            $one_point_redem = $tbl_loyaltys->one_point_redem; 
            
            $response['status_code'] = '201';
            $response['maxAllowedPoints'] = '';
            $response['one_point_redem'] = $one_point_redem;
        }

        return response()->json($response);
    }
    
    
    public function redeemOtp(Request $request)
    {
        if(empty($request->contact))
        {
            $response['status_code'] = '201';
        }
        else
        {
            $contact = $request->contact;
            $otp = '1111';
            /*$otp = rand('1111', '9999');
            $enmsg = "Dear user, your mobile verification code for Quickdaak is $otp DO NOT disclose it to anyone.";
            $msg2 = urlencode($enmsg);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://dtasit.ai/backend/api/http/sms/send?recipient=91$contact&sender_id=QKDAAK&message=$msg2&api_token=55|YA2O3opEwRIBxs2mpmvp4kwzO78krbw52faxKXzY8f233b7a&dlt_template_id=1707174359447112815&type=plain&entity_id=1701174168288220773");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 0);
            $response1 = curl_exec($ch);
            curl_close($ch);*/
            session([
                'redeemotp' => $otp,
                'redeemotp_stored_at' => now(),
            ]);   
            $response['status_code'] = '200';
        }
        
        return response()->json($response);
    }
    
    
    public function checkredeemOtp(Request $request)
    {
        if(empty($request->rotp))
        {
            $response['status'] = 'error';
        }
        else
        {
            $storedAt = session('redeemotp_stored_at');
            if ($storedAt && now()->diffInSeconds($storedAt) < 60) 
            {
                 $redeemotp = session('redeemotp');
                 if($redeemotp == $request->rotp)
                 {
                    session()->forget(['redeemotp', 'redeemotp_stored_at']); 
                    $response['status'] = 'success';
                    $response['status_code'] = '200';
                 }
                 else
                 {
                    $response['status'] = 'error';
                    $response['status_code'] = '201';
                 }
                 
            }
            else
            {
                $response['status'] = 'error';
                $response['status_code'] = '202';
            }
               
        }
        
        return response()->json($response);
    }
    
    
    public function checkcoupon(Request $request)
    {
        if (empty($request->contact_no) || empty($request->DiscountCoupon)) {
            return response()->json([
                'status' => 'error',
                'status_code' => '400', 
            ]);
        }
    
        $coupon = DB::table('tbl_coupon')
            ->where('coupon_code', $request->DiscountCoupon)
            ->first();
    
        if (!$coupon) {
            return response()->json([
                'status' => 'error',
                'status_code' => '201', // coupon not found
            ]);
        }
    
        // If coupon already used
        if ($coupon->coupon_status == '1') {
            return response()->json([
                'status' => 'error',
                'status_code' => '202', // already used
            ]);
        }
        
        if ($coupon->coupon_usages == '1') 
        {
            $saleCount = DB::table('tbl_sales')->where('contact_no', $request->contact_no)->count();
            if($saleCount > 0)
            {
                return response()->json([
                    'status' => 'error',
                    'status_code' => '205', // Only For First Order
                ]);
            }
        }
        
        
        if ($coupon->coupon_generate_type == '0') 
        {
            $saleCount = DB::table('tbl_coupon')->where('contact_no', $request->contact_no)->where('coupon_code', $request->DiscountCoupon)->count();
            if($saleCount > 0)
            {
                return response()->json([
                    'status' => 'error',
                    'status_code' => '206', // Only For First Order
                ]);
            }
        }
        
        
        // Check for new customer
    
        // Check date validity
        $currentDate = date('Y-m-d');
        if ($currentDate < $coupon->valid_from || $currentDate > $coupon->valid_to) {
            return response()->json([
                'status' => 'error',
                'status_code' => '203', // expired or not active
            ]);
        }
    
        // Check minimum sale value
        if ($request->total_item_price < $coupon->min_sale_vale) {
            return response()->json([
                'status' => 'error',
                'status_code' => '204',
                'min_sale_vale' => $coupon->min_sale_vale,
            ]);
        }
    
        // Calculate discount
        $discount_amount = 0;
        if ($coupon->coupon_type === '0') {
            $discount_amount = ($request->total_item_price * $coupon->coupon_value) / 100;
        } else {
            $discount_amount = $coupon->coupon_value;
        }
    
        // Optional: Ensure discount doesn't exceed total price
        if ($discount_amount > $request->total_item_price) {
            $discount_amount = $request->total_item_price;
        }
    
        return response()->json([
            'status' => 'success',
            'status_code' => '200',
            'discount_amount' => round($discount_amount, 2),
            'coupon_id' => $coupon->coupon_id,
        ]);
    }
    
    
    
    public function cartOtp(Request $request)
    {
        if(empty($request->modalCartmobile))
        {
            $response['status_code'] = '201';
        }
        else
        {
            $contact = $request->modalCartmobile;
            $otp = '1111';
            /*$otp = rand('1111', '9999');
            $enmsg = "Dear user, your mobile verification code for Quickdaak is $otp DO NOT disclose it to anyone.";
            $msg2 = urlencode($enmsg);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://dtasit.ai/backend/api/http/sms/send?recipient=91$contact&sender_id=QKDAAK&message=$msg2&api_token=55|YA2O3opEwRIBxs2mpmvp4kwzO78krbw52faxKXzY8f233b7a&dlt_template_id=1707174359447112815&type=plain&entity_id=1701174168288220773");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 0);
            $response1 = curl_exec($ch);
            curl_close($ch);*/
            session([
                'cartotp' => $otp,
                'cartotp_stored_at' => now(),
            ]);   
            $response['status_code'] = '200';
        }
        
        return response()->json($response);
    }
    
    
    public function checkcartOtp(Request $request)
    {
        if(empty($request->cotp))
        {
            $response['status'] = 'error';
        }
        else
        {
            $storedAt = session('cartotp_stored_at');
            if ($storedAt && now()->diffInSeconds($storedAt) < 60) 
            {
                 $cartotp = session('cartotp');
                 if($cartotp == $request->cotp)
                 {
                    session()->forget(['cartotp', 'cartotp_stored_at']); 
                    $response['status'] = 'success';
                    $response['status_code'] = '200';
                 }
                 else
                 {
                    $response['status'] = 'error';
                    $response['status_code'] = '201';
                 }
                 
            }
            else
            {
                $response['status'] = 'error';
                $response['status_code'] = '202';
            }
               
        }
        
        return response()->json($response);
    }
    
    

    public function getPackages(Request $request)
    {
        $lensType = $request->lens_type;
    
        $packages = DB::table('tbl_product_code')
            ->where('product_name', $lensType)
            ->get();
    
        if ($packages->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No packages found']);
        }
    
        return response()->json([
            'success' => true,
            'packages' => $packages
        ]);
    }
    
    public function getPackagesCoating(Request $request)
    {
        $pkgId = $request->pkgId;
    
        $packages = DB::table('tbl_product_coating')
            ->where('product_id', $pkgId)
            ->get();
    
        if ($packages->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No Coating found']);
        }
    
        return response()->json([
            'success' => true,
            'packages' => $packages
        ]);
    }
    
    
    public function getprescription(Request $request)
    {
        $contact_no = $request->contact_no;

        $prescriptions = DB::table('tbl_eye_test')->where('contact_no', $contact_no)->where('status', 2)
            ->orderBy('test_id', 'desc')
            ->get(['test_id','cust_name', 're_sph_new', 're_cyl_new', 're_axis_new','pd_re_new','le_sph_new','le_cyl_new','le_axis_new','pd_le_new','optometrist', 'created_at']);

        return response()->json([
            'data' => $prescriptions->map(function ($p) {
                return [
                    'test_id' => $p->test_id,
                    're_sph_new' => $p->re_sph_new,
                    'cust_name' => $p->cust_name,
                    're_cyl_new' => $p->re_cyl_new,
                    're_axis_new' => $p->re_axis_new,
                    'pd_re_new' => $p->pd_re_new,
                    'le_sph_new' => $p->le_sph_new,
                    'le_cyl_new' => $p->le_cyl_new,
                    'le_axis_new' => $p->le_axis_new,
                    'pd_le_new' => $p->pd_le_new,
                    'optometrist' => $p->optometrist,
                    'date' => $p->created_at,
                ];
            })
        ]);
    }
    
    
    public function getlensbarcode(Request $request)
    {
        $product_details = $request->product_details;
        $product_code = $request->product_code;
        $store_id = $request->store_id;

        $barcode = DB::table('tbl_barcode')->where('product_details', $product_details)->where('product_code', $product_code)
        ->where('store_id', $store_id)
        ->where('barcode_status', '1')
        ->where('t_status', '0')
        ->where('outward_status', NULL)
        ->whereNull('lens_box')
            ->orderBy('id', 'ASC')
            ->get(['id','product_code', 'product_type', 'barcode_no', 'lens_box','perbox','purchase_price','retail_price','product_details','challan_no','purchase_product_id']);
        if (!$barcode)
        {
           
                $barcode = DB::table('tbl_barcode')->where('product_details', $product_details)->where('product_code', $product_code)
                ->where('transfer_store', $store_id)
                ->where('barcode_status', '1')
                ->where('transfer_outward_status', NULL)
                ->whereNull('lens_box')
                    ->orderBy('id', 'ASC')
                    ->get(['id','product_code', 'product_type', 'barcode_no', 'lens_box','perbox','purchase_price','retail_price','product_details','challan_no','purchase_product_id']);
        }     

        return response()->json([
            'data' => $barcode->map(function ($p) {
                
                    if($p->challan_no == NULL)
                    {
                        
                        $p_deatils= DB::table('tbl_purchase_deatils')->where('id', $p->purchase_product_id)->first();
                    }
                    else
                    {
                        $p_deatils= DB::table('tbl_challan_deatils')->where('challan_no', $p->challan_no)->where('product_type', 'Lens')->first();
                    }
                    
                    $description = '<strong style="color:red"> Box per peice :  '.$p->perbox.'</strong><br>
                     <strong> Batch Number :  '.$p_deatils->batchno_details.'</strong><br>
                     <strong> Mfg Date  :  '.$p_deatils->mfg_detail.'</strong><br>
                     <strong> Expiry  Date  :  '.$p_deatils->expiry_detail.'</strong>
                    ';
                    
                    $p_details = $p->product_details.'<BR>'.$description;
                return [
                    'barcode_id' => $p->id,
                    'product_code' => $p->product_code,
                    'product_type' => $p->product_type,
                    'barcode_no' => $p->barcode_no,
                    'lens_box' => $p->lens_box,
                    'perbox' => $p->perbox,
                    'purchase_price' => $p->purchase_price,
                    'retail_price' => $p->retail_price,
                    'product_details' => $p->product_details,
                    'p_details' => $p_details,
   
                ];
            })
        ]);
    }
    
    
    
    public function saleOtp(Request $request)
    {
        if(empty($request->contact_no))
        {
            $response['status_code'] = '201';
        }
        else
        {
            $contact = $request->contact_no;
            $otp = '1111';
            /*$otp = rand('1111', '9999');
            $enmsg = "Dear user, your mobile verification code for Quickdaak is $otp DO NOT disclose it to anyone.";
            $msg2 = urlencode($enmsg);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://dtasit.ai/backend/api/http/sms/send?recipient=91$contact&sender_id=QKDAAK&message=$msg2&api_token=55|YA2O3opEwRIBxs2mpmvp4kwzO78krbw52faxKXzY8f233b7a&dlt_template_id=1707174359447112815&type=plain&entity_id=1701174168288220773");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 0);
            $response1 = curl_exec($ch);
            curl_close($ch);*/
            session([
                'saleotp' => $otp,
                'saleotp_stored_at' => now(),
            ]);   
            $response['status_code'] = '200';
        }
        
        return response()->json($response);
    }
    
    
    public function checksaleOtp(Request $request)
    {
        if(empty($request->scotp))
        {
            $response['status'] = 'error';
        }
        else
        {
            $storedAt = session('saleotp_stored_at');
            if ($storedAt && now()->diffInSeconds($storedAt) < 60) 
            {
                 $saleotp = session('saleotp');
                 if($saleotp == $request->scotp)
                 {
                    session()->forget(['saleotp', 'saleotp_stored_at']); 
                    $response['status'] = 'success';
                    $response['status_code'] = '200';
                 }
                 else
                 {
                    $response['status'] = 'error';
                    $response['status_code'] = '201';
                 }
                 
            }
            else
            {
                $response['status'] = 'error';
                $response['status_code'] = '202';
            }
               
        }
        
        return response()->json($response);
    }
    

    public function storedSaleOrder(Request $request)
    {
        //dd($request);
        DB::beginTransaction();
        
        try {
            $user = auth()->user();
    
            /** -------------------------
             *   Handle Customer
             *  ------------------------- */
            $customer = DB::table('tbl_customer')->where('contact_no', $request->contact_no)->first();
    
            if (!$customer) {
                $customerId = $this->generateUniqueRandomId(6, 'tbl_customer', 'cust_unique_id');
                $customer = Customer::create([
                    'cust_unique_id' => $customerId,
                    'cust_type'      => $request->cust_type ?? 'B2C',
                    'cust_name'      => $request->cust_name,
                    'contact_no'     => $request->contact_no,
                    'email_id'       => $request->email_id,
                    'cust_category'  => $request->cust_category,
                    'gender'         => $request->gender,
                    'cust_address'   => $request->cust_address,
                    'state_id'       => $request->state_id,
                    'city_id'        => $request->city_id,
                    'pincode'        => $request->pincode,
                    'dob'            => $request->dob,
                    'doa'            => $request->doa,
                    'cust_note'      => $request->cust_note,
                    'added_by'       => $user->id,
                    'store_id'       => $request->store_id,
                ]);
            }
    
            /** -------------------------
             *  Create Sale
             *  ------------------------- */
            $sale = Sale::create([
                'sale_date'           => $request->sale_date,
                'order_no'            => $request->order_no,
                'sale_person'         => $request->sale_person,
                'tax_rule'            => $request->taxrule,
                'contact_no'          => $request->contact_no,
                'cust_id'             => $customer->cust_unique_id,
                'cust_name'           => $request->cust_name,
                'membership_id'       => $request->membership_id,
                'email_id'            => $request->email_id,
                'cust_address'        => $request->cust_address,
                'state_id'            => $request->state_id,
                'city_id'             => $request->city_id,
                'pincode'             => $request->pincode,
                'total_basic_amount'  => $request->total_basic_amount ?? 0,
                'total_gst_amount'    => $request->total_gst_amount ?? 0,
                'total_item_price'    => $request->total_item_price ?? 0,
                'total_discount'      => $request->total_discount ?? 0,
                'fitting_fee'         => $request->fitting_fee ?? 0,
                'coupon_amount'       => $request->coupon_amount ?? 0,
                'coupon_id'           => $request->coupon_id,
                'cart_discount'       => $request->cart_discount,
                'cart_discount_by'    => $request->cart_discount_by,
                'cart_discount_per'   => $request->cart_discount_per,
                'cart_discount_resion'=> $request->cart_discount_resion,
                'bogo_discount'       => $request->bogo_discount ?? 0,
                'loyalty_point_amount'=> $request->loyalty_point ?? 0,
                'loyalty_point_apply' => $request->loyalty_point_apply ?? 0,
                'roundoff'            => $request->roundoff ?? 0,
                'total_payable'       => $request->total_payable ?? 0,
                'pay_amount'          => $request->pay_amount ?? 0,
                'pending_amount'      => $request->pending_amount ?? 0,
                'extrnal_warranty'    => $request->extrnal_warranty,
                'pay_method'    => $request->pay_method,
                'pay_deatils'    => $request->pay_deatils,
                'added_by'            => $user->id,
                'store_id'            => $request->store_id,
                'sales_type'          => 0,
                'delivery_date'       => $request->delivery_date,
                'sales_status'    => $request->submit_type,
                'customer_account'       => $request->customer_account,
                'advance_amount'       => $request->advance_amount,
            ]);
            
            $custData = DB::table('tbl_customer')->where('contact_no', $request->contact_no)->first();
            
            if($request->customer_account > 0)
            {
                $credit_amount = $custData->credit_amount;
                
                DB::table('tbl_customer')
                ->where('contact_no', $request->contact_no)
                ->update([
                    'credit_amount' => $credit_amount - $request->customer_account ,
                    'updated_at' => now()
                ]);
                
                DB::table('tbl_wallet_history')->insert([
                    'customer_id'    => $custData->customer_id,
                    'contact_no'     => $custData->contact_no,
                    'debit'          => $request->customer_account,
                    'order_no'       => $request->order_no,
                    'store_id'       => $data['store_id'],
                    'added_by'       => $user->id,
                    'created_at'     => now(),
                    'updated_at'     => now()
                ]);
                
            }
    
            /** -------------------------
             *   Save Sale Products
             *  ------------------------- */
            $data = $request->all();
    
            foreach (($data['product_type'] ?? []) as $i => $type)
            {

            /* ---------------------------------------------------------
                HANDLE GLASS PRODUCT
            --------------------------------------------------------- */
                if ($type === 'Glass')
                {
                    if($data['package_id'][$i] == '')
                    {
            
                        $value  = $data['right_left'][$i] ?? '';
                        $items  = array_filter(array_map('trim', explode(',', $value)));
                        $count  = count($items);
                
                        
                
                        if (!empty($data['barcode'][$i])) {
                            $this->UpdateBarcodes(
                                $data['store_id'],
                                $data['barcode'][$i],
                                $product_details,
                                $type,
                                $data['product_code'][$i],
                                $data['order_no']
                            );
                    }
                        
                        
                        $checknoglass = SaleProduct::where('order_no', $data['order_no'])->where('product_type', 'Glass')
                        ->orderBy('id', 'desc')
                        ->first();
                        
                        if(empty($checknoglass))
                        {
                            $no_of_glass =1;
                        }
                        else
                        {
                            $no_of_glass = $checknoglass->no_of_glass+1;
                        }
                        
                        
                        
                
                        /* ---------------------------------------------------------
                            BUILD GLASS DESCRIPTION AND UPDATE INVENTORY
                        --------------------------------------------------------- */
                
                        foreach ($items as $item) 
                        {
                            // Create Sale Product
                            $saleProduct = SaleProduct::create([
                                'sale_id'           => $sale->id,
                                'order_no'          => $data['order_no'],
                                'barcode_use'       => $data['barcode'][$i] ?? null,
                                'product_type'      => $data['product_type'][$i] ?? null,
                                'product_code'      => $data['product_code'][$i] ?? null,
                                'product_id'        => $data['product_id'][$i] ?? null,
                                'product_company'   => $data['product_company'][$i] ?? null,
                                'product_quality'   => $data['product_quality'][$i] ?? null,
                                'product_material'  => $data['product_material'][$i] ?? null,
                                'product_color'     => $data['product_color'][$i] ?? null,
                                'product_design'    => $data['product_design'][$i] ?? null,
                                'product_coating'   => $data['product_coating'][$i] ?? null,
                                'product_index'     => $data['product_index'][$i] ?? null,
                                'product_deatils'   => $data['product_description'][$i] ?? null,
                                'package_id'        => $data['package_id'][$i] ?? null,
                                'coating_apply'     => $data['coating_apply'][$i] ?? null,
                    
                                // RIGHT EYE VALUES
                                'GL_EYE_RS_D'       => $data['GL_EYE_RS_D'][$i] ?? null,
                                'GL_EYE_RC_D'       => $data['GL_EYE_RC_D'][$i] ?? null,
                                'GL_EYE_RA_D'       => $data['GL_EYE_RA_D'][$i] ?? null,
                                'GL_EYE_RP_D'       => $data['GL_EYE_RP_D'][$i] ?? null,
                                'GL_EYE_RV_D'       => $data['GL_EYE_RV_D'][$i] ?? null,
                    
                                'GL_EYE_RS_N'       => $data['GL_EYE_RS_N'][$i] ?? null,
                                'GL_EYE_RC_N'       => $data['GL_EYE_RC_N'][$i] ?? null,
                                'GL_EYE_RA_N'       => $data['GL_EYE_RA_N'][$i] ?? null,
                                'GL_EYE_RP_N'       => $data['GL_EYE_RP_N'][$i] ?? null,
                                'GL_EYE_RV_N'       => $data['GL_EYE_RV_N'][$i] ?? null,
                    
                                'GL_EYE_RADD'       => $data['GL_EYE_RADD'][$i] ?? null,
                                'GL_EYE_totalPD'    => $data['GL_EYE_totalPD'][$i] ?? null,
                    
                                // LEFT EYE VALUES
                                'GL_EYE_LS_D'       => $data['GL_EYE_LS_D'][$i] ?? null,
                                'GL_EYE_LC_D'       => $data['GL_EYE_LC_D'][$i] ?? null,
                                'GL_EYE_LA_D'       => $data['GL_EYE_LA_D'][$i] ?? null,
                                'GL_EYE_LP_D'       => $data['GL_EYE_LP_D'][$i] ?? null,
                                'GL_EYE_LV_D'       => $data['GL_EYE_LV_D'][$i] ?? null,
                    
                                'GL_EYE_LS_N'       => $data['GL_EYE_LS_N'][$i] ?? null,
                                'GL_EYE_LC_N'       => $data['GL_EYE_LC_N'][$i] ?? null,
                                'GL_EYE_LA_N'       => $data['GL_EYE_LA_N'][$i] ?? null,
                                'GL_EYE_LP_N'       => $data['GL_EYE_LP_N'][$i] ?? null,
                                'GL_EYE_LV_N'       => $data['GL_EYE_LV_N'][$i] ?? null,
                    
                                'GL_EYE_LADD'       => $data['GL_EYE_LADD'][$i] ?? null,
                    
                                // FRAME DETAILS
                                'frame_asize'       => $data['frame_asize'][$i] ?? null,
                                'frame_bsize'       => $data['frame_bsize'][$i] ?? null,
                                'frametypeglass'    => $data['frametypeglass'][$i] ?? null,
                                'frame_dbl'         => $data['frame_dbl'][$i] ?? null,
                                'frame_fh'          => $data['frame_fh'][$i] ?? null,
                                'frame_ed'          => $data['frame_ed'][$i] ?? null,
                                
                    
                                'doc_name'          => $data['doc_name'][$i] ?? null,
                                'wearing_type'      => $data['wearing_type'][$i] ?? null,
                                'wearing_types_inhouse'      => $data['wearing_types_inhouse'][$i] ?? null,
                                'prescription_notes'=> $data['prescription_notes'][$i] ?? null,
                    
                                'qty'               => $count,
                                'hsn_code'          => $data['hsn_code'][$i] ?? null,
                                'gst'               => $data['gst'][$i] ?? 0,
                                'gst_amount'        => $data['gst_amount'][$i]/$count ?? 0,
                                'discount_amt'      => $data['discount_amt'][$i]/$count ?? 0,
                                'product_discount'  => $data['discount'][$i]/$count ?? 0,
                                'purchase_price'     => $data['purchase_price'][$i]/$count ?? 0,
                                'base_price'        => $data['base_price'][$i]/$count ?? 0,
                                'retail_price'      => $data['retail_price'][$i]/$count ?? 0,
                                'sale_price'        => $data['sale_price'][$i]/$count ?? 0,
                                'store_id'          => $data['store_id'],
                                'right_left'        => $value,
                                'no_of_glass'        => $no_of_glass,
                                'product_tracking'       => 'ORDER PLACED AND READY TO SHIP',
                            ]);
                    
                            // FIX: $product_details must be defined before using in UpdateBarcodes
                            $product_details = $data['product_description'][$i] ?? null;
                
                            $eyeFields = [
                                'Right' => [
                                    'SPH' => 'GL_EYE_RS_D',
                                    'CYL' => 'GL_EYE_RC_D',
                                    'ADD' => 'GL_EYE_RADD',
                                    'Axis' => 'GL_EYE_RA_D',
                                ],
                                'Left' => [
                                    'SPH' => 'GL_EYE_LS_D',
                                    'CYL' => 'GL_EYE_LC_D',
                                    'ADD' => 'GL_EYE_LADD',
                                    'Axis' => 'GL_EYE_LA_D',
                                ]
                            ];
                            
                            // Only proceed if $item is 'Right' or 'Left'
                            if (isset($eyeFields[$item])) {
                                $fields = $eyeFields[$item];
                            
                                // Build the product description dynamically
                                $product_description_parts = array_filter([
                                    $data['product_description'][$i] ?? '',
                                    isset($data[$fields['SPH']][$i]) ? 'SPH:' . $data[$fields['SPH']][$i] : '',
                                    isset($data[$fields['CYL']][$i]) ? 'CYL:' . $data[$fields['CYL']][$i] : '',
                                    isset($data[$fields['ADD']][$i]) ? 'ADD:' . $data[$fields['ADD']][$i] : '',
                                    isset($data[$fields['Axis']][$i]) ? 'Axis:' . $data[$fields['Axis']][$i] : '',
                                ], function($value) {
                                    return $value !== '' && $value !== null;
                                });
                            
                                $product_details = $this->buildGlassDescription($product_description_parts);
                            
                                // Update inventory
                                $this->UpdateGlassInventory(
                                    $data['store_id'],
                                    $product_details,
                                    $type,
                                    $data['product_code'][$i],
                                    $data['product_id'][$i],
                                    $data['product_qty'][$i],
                                    $data['sale_date'],
                                    $saleProduct->id,
                                    $item
                                );
                            }
                            
                            $tracking_status = 'ORDER PLACED AND READY TO SHIP';
                            
                            $OrderTracking = OrderTracking::create([
                                'order_no'       => $data['order_no'],
                                'sale_product_id'       => $saleProduct->id,
                                'product_type'   => $type,
                                'product_code'   => $data['product_code'][$i] ?? null,
                                'description'=> $product_details ?? null,
                                'tracking_status'   => $tracking_status,
                                'store_id'       => $data['store_id'],
                            ]);
                    }
                    }
                    else
                    {
                        $value  = $data['right_left'][$i] ?? '';
                        $items  = array_filter(array_map('trim', explode(',', $value)));
                        $count  = count($items);
                
                        
                
                            
                            
                        $checknoglass = SaleProduct::where('order_no', $data['order_no'])->where('product_type', 'Glass')
                        ->orderBy('id', 'desc')
                        ->first();
                        
                        if(empty($checknoglass))
                        {
                            $no_of_glass =1;
                        }
                        else
                        {
                            $no_of_glass = $checknoglass->no_of_glass+1;
                        }
                            
                            
                            
                    
                        /* ---------------------------------------------------------
                            BUILD GLASS DESCRIPTION AND UPDATE INVENTORY
                        --------------------------------------------------------- */
                
                        foreach ($items as $item) 
                        {
                            // Create Sale Product
                            $saleProduct = SaleProduct::create([
                                'sale_id'           => $sale->id,
                                'order_no'          => $data['order_no'],
                                'barcode_use'       => $data['barcode'][$i] ?? null,
                                'product_type'      => $data['product_type'][$i] ?? null,
                                'product_code'      => $data['product_code'][$i] ?? null,
                                'product_id'        => $data['product_id'][$i] ?? null,
                                'product_company'   => $data['product_company'][$i] ?? null,
                                'product_quality'   => $data['product_quality'][$i] ?? null,
                                'product_material'  => $data['product_material'][$i] ?? null,
                                'product_color'     => $data['product_color'][$i] ?? null,
                                'product_design'    => $data['product_design'][$i] ?? null,
                                'product_coating'   => $data['product_coating'][$i] ?? null,
                                'product_index'     => $data['product_index'][$i] ?? null,
                                'product_deatils'   => $data['product_description'][$i] ?? null,
                                'package_id'        => $data['package_id'][$i] ?? null,
                                'coating_apply'     => $data['coating_apply'][$i] ?? null,
                    
                                // RIGHT EYE VALUES
                                'GL_EYE_RS_D'       => $data['GL_EYE_RS_D'][$i] ?? null,
                                'GL_EYE_RC_D'       => $data['GL_EYE_RC_D'][$i] ?? null,
                                'GL_EYE_RA_D'       => $data['GL_EYE_RA_D'][$i] ?? null,
                                'GL_EYE_RP_D'       => $data['GL_EYE_RP_D'][$i] ?? null,
                                'GL_EYE_RV_D'       => $data['GL_EYE_RV_D'][$i] ?? null,
                    
                                'GL_EYE_RS_N'       => $data['GL_EYE_RS_N'][$i] ?? null,
                                'GL_EYE_RC_N'       => $data['GL_EYE_RC_N'][$i] ?? null,
                                'GL_EYE_RA_N'       => $data['GL_EYE_RA_N'][$i] ?? null,
                                'GL_EYE_RP_N'       => $data['GL_EYE_RP_N'][$i] ?? null,
                                'GL_EYE_RV_N'       => $data['GL_EYE_RV_N'][$i] ?? null,
                    
                                'GL_EYE_RADD'       => $data['GL_EYE_RADD'][$i] ?? null,
                                'GL_EYE_totalPD'    => $data['GL_EYE_totalPD'][$i] ?? null,
                    
                                // LEFT EYE VALUES
                                'GL_EYE_LS_D'       => $data['GL_EYE_LS_D'][$i] ?? null,
                                'GL_EYE_LC_D'       => $data['GL_EYE_LC_D'][$i] ?? null,
                                'GL_EYE_LA_D'       => $data['GL_EYE_LA_D'][$i] ?? null,
                                'GL_EYE_LP_D'       => $data['GL_EYE_LP_D'][$i] ?? null,
                                'GL_EYE_LV_D'       => $data['GL_EYE_LV_D'][$i] ?? null,
                    
                                'GL_EYE_LS_N'       => $data['GL_EYE_LS_N'][$i] ?? null,
                                'GL_EYE_LC_N'       => $data['GL_EYE_LC_N'][$i] ?? null,
                                'GL_EYE_LA_N'       => $data['GL_EYE_LA_N'][$i] ?? null,
                                'GL_EYE_LP_N'       => $data['GL_EYE_LP_N'][$i] ?? null,
                                'GL_EYE_LV_N'       => $data['GL_EYE_LV_N'][$i] ?? null,
                    
                                'GL_EYE_LADD'       => $data['GL_EYE_LADD'][$i] ?? null,
                    
                                // FRAME DETAILS
                                'frame_asize'       => $data['frame_asize'][$i] ?? null,
                                'frame_bsize'       => $data['frame_bsize'][$i] ?? null,
                                'frametypeglass'    => $data['frametypeglass'][$i] ?? null,
                                'frame_dbl'         => $data['frame_dbl'][$i] ?? null,
                                'frame_fh'          => $data['frame_fh'][$i] ?? null,
                                'frame_ed'          => $data['frame_ed'][$i] ?? null,
                                
                    
                                'doc_name'          => $data['doc_name'][$i] ?? null,
                                'wearing_type'      => $data['wearing_type'][$i] ?? null,
                                'wearing_types_inhouse'      => $data['wearing_types_inhouse'][$i] ?? null,
                                'prescription_notes'=> $data['prescription_notes'][$i] ?? null,
                    
                                'qty'               => $count,
                                'hsn_code'          => $data['hsn_code'][$i] ?? null,
                                'gst'               => $data['gst'][$i] ?? 0,
                                'gst_amount'        => $data['gst_amount'][$i]/$count ?? 0,
                                'discount_amt'      => $data['discount_amt'][$i]/$count ?? 0,
                                'product_discount'  => $data['discount'][$i]/$count ?? 0,
                                'purchase_price'     => $data['purchase_price'][$i]/$count ?? 0,
                                'base_price'        => $data['base_price'][$i]/$count ?? 0,
                                'retail_price'      => $data['retail_price'][$i]/$count ?? 0,
                                'sale_price'        => $data['sale_price'][$i]/$count ?? 0,
                                'store_id'          => $data['store_id'],
                                'right_left'        => $value,
                                'no_of_glass'        => $no_of_glass,
                                'product_tracking'       => 'ORDER PLACED AND READY TO SHIP',
                            ]);
                    
                            // FIX: $product_details must be defined before using in UpdateBarcodes
                            $product_details = $data['product_description'][$i] ?? null;
                
                            $eyeFields = [
                                'Right' => [
                                    'SPH' => 'GL_EYE_RS_D',
                                    'CYL' => 'GL_EYE_RC_D',
                                    'ADD' => 'GL_EYE_RADD',
                                    'Axis' => 'GL_EYE_RA_D',
                                ],
                                'Left' => [
                                    'SPH' => 'GL_EYE_LS_D',
                                    'CYL' => 'GL_EYE_LC_D',
                                    'ADD' => 'GL_EYE_LADD',
                                    'Axis' => 'GL_EYE_LA_D',
                                ]
                            ];
                            
                            // Only proceed if $item is 'Right' or 'Left'
                            if (isset($eyeFields[$item])) {
                                $fields = $eyeFields[$item];
                            
                                // Build the product description dynamically
                                $product_description_parts = array_filter([
                                    $data['product_description'][$i] ?? '',
                                    isset($data[$fields['SPH']][$i]) ? 'SPH:' . $data[$fields['SPH']][$i] : '',
                                    isset($data[$fields['CYL']][$i]) ? 'CYL:' . $data[$fields['CYL']][$i] : '',
                                    isset($data[$fields['ADD']][$i]) ? 'ADD:' . $data[$fields['ADD']][$i] : '',
                                    isset($data[$fields['Axis']][$i]) ? 'Axis:' . $data[$fields['Axis']][$i] : '',
                                ], function($value) {
                                    return $value !== '' && $value !== null;
                                });
                            
                                $product_details = $this->buildGlassDescription($product_description_parts);
                                
                                if($item == 'Right')
                                {
                                    DB::table('tbl_sales_product')
                                    ->where('id', $saleProduct->id)
                                    ->update([
                                        'right_purchase' => 1,
                                        'right_glass' => $product_details,
                                        'updated_at'         => now()
                                    ]);
                                }
                                elseif($item == 'Left')
                                {
                                    DB::table('tbl_sales_product')
                                    ->where('id', $saleProduct->id)
                                    ->update([
                                        'left_purchase' => 1,
                                        'left_glass' => $product_details,
                                        'updated_at'         => now()
                                    ]);
                                }
                            
                               
                            }
                            
                            $tracking_status = 'ORDER PLACED AND READY TO SHIP';
                            
                            $OrderTracking = OrderTracking::create([
                                'order_no'       => $data['order_no'],
                                'sale_product_id'       => $saleProduct->id,
                                'product_type'   => $type,
                                'product_code'   => $data['product_code'][$i] ?? null,
                                'description'=> $product_details ?? null,
                                'tracking_status'   => $tracking_status,
                                'store_id'       => $data['store_id'],
                            ]);
                    }
                        
                    }
                    
                }
            
                /* ---------------------------------------------------------
                    HANDLE FRAME OR GOGGLES PRODUCT
                --------------------------------------------------------- */
                elseif ($type === 'Frame' || $type === 'Goggles') {
            
                    $saleProduct = SaleProduct::create([
                        'sale_id'        => $sale->id,
                        'order_no'       => $data['order_no'],
                        'barcode_use'    => $data['barcode'][$i] ?? null,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'product_id'     => $data['product_id'][$i] ?? null,
                        'product_company'=> $data['product_company'][$i] ?? null,
                        'product_quality'=> $data['product_quality'][$i] ?? null,
                        'product_deatils'=> $data['product_description'][$i] ?? null,
                        'qty'            => $data['product_qty'][$i] ?? 1,
                        'hsn_code'       => $data['hsn_code'][$i] ?? null,
                        'gst'            => $data['gst'][$i] ?? 0,
                        'gst_amount'     => $data['gst_amount'][$i] ?? 0,
                        'discount_amt'   => $data['discount_amt'][$i] ?? 0,
                        'product_discount'=> $data['discount'][$i] ?? 0,
                        'purchase_price'     => $data['purchase_price'][$i] ?? 0,
                        'base_price'     => $data['base_price'][$i] ?? 0,
                        'retail_price'   => $data['retail_price'][$i] ?? 0,
                        'sale_price'     => $data['sale_price'][$i] ?? 0,
                        'store_id'       => $data['store_id'],
                        'product_tracking'       => 'ORDER PLACED AND READY TO SHIP',
                    ]);
            
                    if (!empty($data['barcode'][$i])) {
                        $this->UpdateBarcodes(
                            $data['store_id'],
                            $data['barcode'][$i],
                            $data['product_description'][$i],
                            $type,
                            $data['product_code'][$i],
                            $data['order_no']
                        );
                    }
            
                    $this->UpdateFrameGogglesSolutuionInventory(
                        $data['store_id'],
                        $data['product_description'][$i],
                        $type,
                        $data['product_code'][$i],
                        $data['product_id'][$i],
                        $data['product_qty'][$i],
                        $data['sale_date'],
                        $saleProduct->id
                    );
                    
                    $tracking_status = 'ORDER PLACED AND READY TO SHIP';
                    $OrderTracking = OrderTracking::create([
                        'order_no'       => $data['order_no'],
                        'sale_product_id'       => $saleProduct->id,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'description'=> $data['product_description'][$i] ?? null,
                        'tracking_status'   => $tracking_status,
                        'store_id'       => $data['store_id'],
                    ]);
                }
                
            /* ---------------------------------------------------------
                HANDLE LENS PRODUCT
            --------------------------------------------------------- */
                
                elseif ($type === 'Lens') 
                {
                    if (!empty($data['lens_bids'][$i])) {
                        $val  = $data['lens_bids'][$i];
                        $bids = explode(',', $val);
                    
                        $barcode_numbers = []; // collect here
                    
                        foreach ($bids as $bid) {
                            $buse = DB::table('tbl_barcode')
                                ->where('id', $bid)
                                ->first();
                    
                            if ($buse) {
                                $barcode_numbers[] = $buse->barcode_no;
                            }
                        }
                    
                        // OUTSIDE LOOP  desired format
                        $barcode_use_no = implode(',', $barcode_numbers);
                    
                    } else {
                        $barcode_use_no = null;
                    } 
                    
                    
                  

                    $saleProduct = SaleProduct::create([
                        'sale_id'        => $sale->id,
                        'order_no'       => $data['order_no'],
                        'barcode_use'    => $data['barcode'][$i] ?? null,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'product_id'     => $data['product_id'][$i] ?? null,
                        'product_company'=> $data['product_company'][$i] ?? null,
                        'product_quality'=> $data['product_quality'][$i] ?? null,
                        'product_deatils'=> $data['product_description'][$i] ?? null,
                        'product_material'=> $data['product_material'][$i] ?? null,
                        'product_color'=> $data['product_color'][$i] ?? null,
                        'product_number'=> $data['product_number'][$i] ?? null,
                        'product_ct'=> $data['product_ct'][$i] ?? null,
                        'product_typesss'=> $data['product_typesss'][$i] ?? null,
                        'product_validity'=> $data['product_validity'][$i] ?? null,
                        'count_eye_test'=> $data['count_eye_test'][$i] ?? null,
                        'prescription_notes'=> $data['prescription_notes'][$i] ?? null,
                        'lensRightNoOfBoxes'=> $data['lensRightNoOfBoxes'][$i] ?? null,
                        'lensRightTotalPieces'=> $data['lensRightTotalPieces'][$i] ?? null,
                        'lensLeftNoOfBoxes'=> $data['lensLeftNoOfBoxes'][$i] ?? null,
                        'lensLeftTotalPieces'=> $data['lensLeftTotalPieces'][$i] ?? null,
                        'barcode_use'       => $barcode_use_no,
                        'GL_EYE_RS_D'       => $data['GL_EYE_RS_D'][$i] ?? null,
                        'GL_EYE_RC_D'       => $data['GL_EYE_RC_D'][$i] ?? null,
                        'GL_EYE_RA_D'       => $data['GL_EYE_RA_D'][$i] ?? null,
                        'GL_EYE_RP_D'       => $data['GL_EYE_RP_D'][$i] ?? null,
                        'GL_EYE_RV_D'       => $data['GL_EYE_RV_D'][$i] ?? null,
                        'GL_EYE_LS_D'       => $data['GL_EYE_LS_D'][$i] ?? null,
                        'GL_EYE_LC_D'       => $data['GL_EYE_LC_D'][$i] ?? null,
                        'GL_EYE_LA_D'       => $data['GL_EYE_LA_D'][$i] ?? null,
                        'GL_EYE_LP_D'       => $data['GL_EYE_LP_D'][$i] ?? null,
                        'GL_EYE_LV_D'       => $data['GL_EYE_LV_D'][$i] ?? null,
                        'qty'            => $data['product_qty'][$i] ?? 1,
                        'hsn_code'       => $data['hsn_code'][$i] ?? null,
                        'gst'            => $data['gst'][$i] ?? 0,
                        'gst_amount'     => $data['gst_amount'][$i] ?? 0,
                        'discount_amt'   => $data['discount_amt'][$i] ?? 0,
                        'product_discount'=> $data['discount'][$i] ?? 0,
                        'purchase_price'     => $data['purchase_price'][$i] ?? 0,
                        'base_price'     => $data['base_price'][$i] ?? 0,
                        'retail_price'   => $data['retail_price'][$i] ?? 0,
                        'sale_price'     => $data['sale_price'][$i] ?? 0,
                        'store_id'       => $data['store_id'],
                        'product_tracking'       => 'ORDER PLACED AND READY TO SHIP',
                    ]);
                    
                    $product_details = $data['product_description'][$i] ?? null;
                    if(!empty($data['lens_bids'][$i]))
                    {
                        $val = $data['lens_bids'][$i]; 
                        $bids = explode(',', $val); 
                        foreach ($bids as $bid) 
                        {
                            $product = DB::table('tbl_barcode')
                            ->where('t_status', '0')
                            ->where('id', $bid)
                            ->first();
                        
                            if (empty($product)) 
                            {
                                $product = DB::table('tbl_barcode')
                                    ->where('id', $bid)
                                    ->where('transfer_store_id', $data['store_id'])
                                    ->first();
                                    
                                 DB::table('tbl_barcode')->where([['id', '=', $bid],
                                ])->update([
                                    'transfer_outward_status' => 0,
                                    'refrence_no'   => $data['order_no'],
                                    'updated_at'    => now()
                                ]); 
                                
                                 DB::table('tbl_barcode')->where([['lens_box', '=', $product->barcode_no],
                                ])->update([
                                    'transfer_outward_status' => 0,
                                    'refrence_no'   => $data['order_no'],
                                    'updated_at'    => now()
                                ]); 
                            }
                            else
                            {
                                 DB::table('tbl_barcode')->where([['id', '=', $bid],
                                ])->update([
                                    'transfer_outward_status' => 0,
                                    'refrence_no'   => $data['order_no'],
                                    'updated_at'    => now()
                                ]);
                                
                                 DB::table('tbl_barcode')->where([['lens_box', '=', $product->barcode_no],
                                ])->update([
                                    'transfer_outward_status' => 0,
                                    'refrence_no'   => $data['order_no'],
                                    'updated_at'    => now()
                                ]); 
                            }
                            
                            
                            $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                                        'barcode_no' => $product->barcode_no,
                                        'store_id' => $data['store_id'],
                                        'reference_type' => 'Sale',
                                        'action_perform' => 'Order',
                                        'added_by' => $user->id,
                                ]);
                               
                            }
                            
                            
                        if($data['lensRightNoOfBoxes'][$i] > 0)
                        {
                            
            
                            $this->UpdateLensInventory(
                                $data['store_id'],
                                $product_details,
                                $type,
                                $data['product_code'][$i],
                                $data['product_id'][$i],
                                $data['lensRightNoOfBoxes'][$i],
                                $data['lensRightTotalPieces'][$i],
                                $data['sale_date'],
                                $saleProduct->id
                            );
                        }
                        
                        if($data['lensLeftNoOfBoxes'][$i] > 0)
                        {
                            $this->UpdateLensInventory(
                                $data['store_id'],
                                $product_details,
                                $type,
                                $data['product_code'][$i],
                                $data['product_id'][$i],
                                $data['lensLeftNoOfBoxes'][$i],
                                $data['lensLeftTotalPieces'][$i],
                                $data['sale_date'],
                                $saleProduct->id
                            );
                        }    
                    
                    }
                    else
                    {
                        if($data['lensRightNoOfBoxes'][$i] > 0)
                        {
                            $product_details = $this->buildGlassDescription([
                                $data['product_description'][$i] ?? '',
                                'SPH:' . ($data['GL_EYE_RS_D'][$i] ?? ''),
                                'CYL:' . ($data['GL_EYE_RC_D'][$i] ?? ''),
                                'ADD:' . ($data['GL_EYE_RADD'][$i] ?? ''),
                                'Axis:' . ($data['GL_EYE_RA_D'][$i] ?? ''),
                            ]);
            
                            // BUG FIX: last argument must be $item, not full array
                            $this->UpdateLensInventory(
                                $data['store_id'],
                                $product_details,
                                $type,
                                $data['product_code'][$i],
                                $data['product_id'][$i],
                                $data['lensRightNoOfBoxes'][$i],
                                $data['lensRightTotalPieces'][$i],
                                $data['sale_date'],
                                $saleProduct->id
                            );
                        }
                        
                        if($data['lensLeftNoOfBoxes'][$i] > 0)
                        {
                            $product_details = $this->buildGlassDescription([
                                $data['product_description'][$i] ?? '',
                                'SPH:' . ($data['GL_EYE_LS_D'][$i] ?? ''),
                                'CYL:' . ($data['GL_EYE_LC_D'][$i] ?? ''),
                                'ADD:' . ($data['GL_EYE_LADD'][$i] ?? ''),
                                'Axis:' . ($data['GL_EYE_LA_D'][$i] ?? ''),
                            ]);
            
                            $this->UpdateLensInventory(
                                $data['store_id'],
                                $product_details,
                                $type,
                                $data['product_code'][$i],
                                $data['product_id'][$i],
                                $data['lensLeftNoOfBoxes'][$i],
                                $data['lensLeftTotalPieces'][$i],
                                $data['sale_date'],
                                $saleProduct->id
                            );
                        }
                    }
                    
                    
                    
                    
                    $tracking_status = 'ORDER PLACED AND READY TO SHIP';
                    $OrderTracking = OrderTracking::create([
                        'order_no'       => $data['order_no'],
                        'sale_product_id'       => $saleProduct->id,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'description'=> $data['product_description'][$i] ?? null,
                        'tracking_status'   => $tracking_status,
                        'store_id'       => $data['store_id'],
                    ]);
                    
                }
                
                /* ---------------------------------------------------------
                HANDLE SOLUTION PRODUCT
                --------------------------------------------------------- */
                
                elseif ($type === 'Solution') 
                {
                    $saleProduct = SaleProduct::create([
                        'sale_id'        => $sale->id,
                        'order_no'       => $data['order_no'],
                        'barcode_use'    => $data['barcode'][$i] ?? null,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'product_id'     => $data['product_id'][$i] ?? null,
                        'product_company'=> $data['product_company'][$i] ?? null,
                        'product_quality'=> $data['product_quality'][$i] ?? null,
                        'product_deatils'=> $data['product_description'][$i] ?? null,
                        'product_color'=> $data['product_color'][$i] ?? null,
                        'product_typesss'=> $data['product_typesss'][$i] ?? null,
                        'product_variant'=> $data['product_variant'][$i] ?? null,
                        'qty'            => $data['product_qty'][$i] ?? 1,
                        'hsn_code'       => $data['hsn_code'][$i] ?? null,
                        'gst'            => $data['gst'][$i] ?? 0,
                        'gst_amount'     => $data['gst_amount'][$i] ?? 0,
                        'discount_amt'   => $data['discount_amt'][$i] ?? 0,
                        'product_discount'=> $data['discount'][$i] ?? 0,
                        'purchase_price'     => $data['purchase_price'][$i] ?? 0,
                        'base_price'     => $data['base_price'][$i] ?? 0,
                        'retail_price'   => $data['retail_price'][$i] ?? 0,
                        'sale_price'     => $data['sale_price'][$i] ?? 0,
                        'store_id'       => $data['store_id'],
                        'product_tracking'       => 'ORDER PLACED AND READY TO SHIP',
                    ]);
            
                    if (!empty($data['barcode'][$i])) {
                        $this->UpdateBarcodes(
                            $data['store_id'],
                            $data['barcode'][$i],
                            $data['product_description'][$i],
                            $type,
                            $data['product_code'][$i],
                            $data['order_no']
                        );
                    }
            
                    $this->UpdateFrameGogglesSolutuionInventory(
                        $data['store_id'],
                        $data['product_description'][$i],
                        $type,
                        $data['product_code'][$i],
                        $data['product_id'][$i],
                        $data['product_qty'][$i],
                        $data['sale_date'],
                        $saleProduct->id
                    );
                    
                    $tracking_status = 'ORDER PLACED AND READY TO SHIP';
                    $OrderTracking = OrderTracking::create([
                        'order_no'       => $data['order_no'],
                        'sale_product_id'       => $saleProduct->id,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'description'=> $data['product_description'][$i] ?? null,
                        'tracking_status'   => $tracking_status,
                        'store_id'       => $data['store_id'],
                    ]);
                }
                
            /* ---------------------------------------------------------
                HANDLE OTHER PRODUCT
            --------------------------------------------------------- */
                
                elseif ($type === 'Other') 
                {
                    $saleProduct = SaleProduct::create([
                        'sale_id'        => $sale->id,
                        'order_no'       => $data['order_no'],
                        'barcode_use'    => $data['barcode'][$i] ?? null,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'product_id'     => $data['product_id'][$i] ?? null,
                        'product_company'=> $data['product_company'][$i] ?? null,
                        'product_quality'=> $data['product_quality'][$i] ?? null,
                        'product_deatils'=> $data['product_description'][$i] ?? null,
                        'product_color'=> $data['product_color'][$i] ?? null,
                        'product_typesss'=> $data['product_typesss'][$i] ?? null,
                        'product_shape'=> $data['product_variant'][$i] ?? null,
                        'product_shape'=> $data['product_shape'][$i] ?? null,
                        'product_size'            => $data['product_size'][$i] ?? 1,
                        'hsn_code'       => $data['hsn_code'][$i] ?? null,
                        'gst'            => $data['gst'][$i] ?? 0,
                        'gst_amount'     => $data['gst_amount'][$i] ?? 0,
                        'discount_amt'   => $data['discount_amt'][$i] ?? 0,
                        'product_discount'=> $data['discount'][$i] ?? 0,
                        'purchase_price'     => $data['purchase_price'][$i] ?? 0,
                        'base_price'     => $data['base_price'][$i] ?? 0,
                        'retail_price'   => $data['retail_price'][$i] ?? 0,
                        'sale_price'     => $data['sale_price'][$i] ?? 0,
                        'store_id'       => $data['store_id'],
                        'product_tracking'       => 'ORDER PLACED AND READY TO SHIP',
                    ]);
            
                    if (!empty($data['barcode'][$i])) {
                        $this->UpdateBarcodes(
                            $data['store_id'],
                            $data['barcode'][$i],
                            $data['product_description'][$i],
                            $type,
                            $data['product_code'][$i],
                            $data['order_no']
                        );
                    }
            
                    $this->UpdateOtherInventory(
                        $data['store_id'],
                        $data['product_description'][$i],
                        $type,
                        $data['product_code'][$i],
                        $data['product_id'][$i],
                        $data['product_qty'][$i],
                        $data['sale_date'],
                        $saleProduct->id
                    );
                    
                    $tracking_status = 'ORDER PLACED AND READY TO SHIP';
                    $OrderTracking = OrderTracking::create([
                        'order_no'       => $data['order_no'],
                        'sale_product_id'       => $saleProduct->id,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'description'=> $data['product_description'][$i] ?? null,
                        'tracking_status'   => $tracking_status,
                        'store_id'       => $data['store_id'],
                    ]);
                }
                
                
            /* ---------------------------------------------------------
                HANDLE REPAIR PRODUCT
            --------------------------------------------------------- */
                
                elseif ($type === 'Repair') 
                {
                    $saleProduct = SaleProduct::create([
                        'sale_id'        => $sale->id,
                        'product_type'       => 'Repair',
                        'product_code'       => 'SYS1407',
                        'order_no'       => $data['order_no'],
                        'product_deatils'=> $data['product_description'][$i] ?? null,
                        'discount_amt'   => $data['discount_amt'][$i] ?? 0,
                        'product_discount'=> $data['discount'][$i] ?? 0,
                        'base_price'     => $data['base_price'][$i] ?? 0,
                        'retail_price'   => $data['retail_price'][$i] ?? 0,
                        'sale_price'     => $data['sale_price'][$i] ?? 0,
                        'store_id'       => $data['store_id'],
                        'product_tracking'       => 'RECEIVED BY BRANCH',
                    ]);
                    
                    $tracking_status = 'RECEIVED BY BRANCH';
                    
                    $OrderTracking = OrderTracking::create([
                        'order_no'       => $data['order_no'],
                        'sale_product_id'       => $saleProduct->id,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'description'=> $data['product_description'][$i] ?? null,
                        'tracking_status'   => $tracking_status,
                        'store_id'       => $data['store_id'],
                    ]);
                }
                
             /* ---------------------------------------------------------
                ORDER TRACKING RECORD
            --------------------------------------------------------- */   
            
     
                
                
            }
    
            /** -------------------------
             *  Payment Entry
             *  ------------------------- */
            SalePayment::create([
                'sale_id'     => $sale->id,
                'order_no'    => $data['order_no'],
                'total_price'    => $data['total_payable']?? 0,
                'pay_amount'  => $data['pay_amount'] ?? 0,
                'bal_amount'  => $data['pending_amount'] ?? 0,
                'pay_details' => $data['pay_deatils'],
                'pay_method'  => $data['pay_method'],
                'pay_date'    => $data['sale_date'],
                'added_by'    => $user->id,
                'store_id'    => $data['store_id'],
                'pay_type'    => 0,
            ]);
    
            /** -------------------------
             *  Coupon & Loyalty Logic
             *  ------------------------- */
            if ($data['coupon_id'] > 0) {
                DB::table('tbl_coupon')
                    ->where('coupon_id', $data['coupon_id'])
                    ->update([
                        'coupon_status' => 1,
                        'contact_no' => $data['contact_no'],
                        'coupon_usages_date' => date('Y-m-d'),
                        'updated_at' => now()
                    ]);
            }
    
            if ($data['loyalty_point_apply'] > 0) {
                
                $bal_point = $custData->Loyalty_Points_Bal - $data['loyalty_point_apply'];
    
                DB::table('tbl_loyaltyrogram_histroy')->insert([
                    'customer_id'    => $custData->customer_id,
                    'opening_points' => $custData->Loyalty_Points_Bal,
                    'redeem'         => $data['loyalty_point_apply'],
                    'bal_point'      => $bal_point,
                    'description'    => 'Sales Invoice ' . $data['order_no'],
                    'add_remove'     => 1,
                    'store_id'       => $data['store_id'],
                    'added_by'       => $user->id,
                    'created_at'     => now(),
                    'updated_at'     => now()
                ]);
    
                DB::table('tbl_customer')->where('customer_id', $custData->customer_id)->update([
                    'Loyalty_Points_Redeem' => $custData->Loyalty_Points_Redeem + $data['loyalty_point_apply'],
                    'Loyalty_Points_Bal'    => $bal_point,
                    'updated_at'            => now()
                ]);
            }
            
            /** -------------------------
             *  Generate Loyalty point 
             *  ------------------------- */
             
            /*$tblloyalty = DB::table('tbl_loyalty')->where('id', 2)->first();

            if ((int)$tblloyalty->auto_status === 0) {
            
                if ((int)$tblloyalty->sales_value === 2) {
                    $salesamount = (float)$data['pay_amount'];
                } else {
                    $salesamount = (float)$data['total_item_price'];
                }
            
                // Earned points
                if ((int)$tblloyalty->auto_set_loyalty_point === 0) {
            
                    if ($tblloyalty->no_of_points > 0) {
                        $earnedPoints = floor(
                            ($salesamount / $tblloyalty->x_number_sale_value) * $tblloyalty->no_of_points
                        );
                    } else {
                        $earnedPoints = 0;
                    }
            
                } else {
                    $earnedPoints = floor(
                        ($salesamount * $tblloyalty->fixed_per) / 100
                    );
                }
                

                $tblcustomer = DB::table('tbl_customer')
                    ->where('contact_no', $data['contact_no'])
                    ->first();
                    
                $earnedPoints  = (int) $earnedPoints;
                DB::table('tbl_sales')
                    ->where('sale_id', $sale->id)
                    ->update([
                        'earnedPoints' => $earnedPoints,
                        'updated_at'   => now(),
                    ]);
            }*/

             
             
             /** -------------------------
             *  Generate Coupon
             *  ------------------------- */
             
             /*$tblcoupon = DB::table('tbl_coupon_auto')->where('id', 1)->first();
             if($tblcoupon->auto_status == '0')
             {
                 if($tblcoupon->sales_value == '0')
                 {
                     $salesamount = $data['total_item_price'];
                 }
                 elseif($tblcoupon->sales_value == '1')
                 {
                     $salesamount = $data['total_item_price'];
                 }
                 elseif($tblcoupon->sales_value == '2')
                 {
                     $salesamount = $data['pay_amount'];
                 }
                 
                $row = DB::table('tbl_coupon_auto')
                    ->where('from_range', '>=', $salesamount)
                    ->where('to_range', '<=', $salesamount)
                    ->first();

                if(!empty($row))
                {
                    $id = $row->id;
                
                    $tblcouponrange = DB::table('tbl_coupon_auto')->where('id', $id)->first();
                    
                    $coupon_code = $this->generateUniqueRandomCoupon(6, 'tbl_coupon', 'coupon_code');
                    
                    $couponId = DB::table('tbl_coupon')->insertGetId([
                        'coupon_type'          => $tblcoupon->coupon_value_type,
                        'coupon_code'          => $coupon_code,
                        'coupon_value'         => $tblcouponrange->coupon_value,
                        'min_sale_vale'        => $tblcouponrange->sales_value_amount,
                        'valid_from'           => Carbon::now()->toDateString(),
                        'valid_to'             => Carbon::now()->addDays($tblcouponrange->valid_dyas)->toDateString(),
                        'coupon_usages'        => 0,
                        'coupon_generate_type' => 0,
                        'coupon_status'        => '0',
                        'added_by'             => $user->id,
                        'store_id'             => $data['store_id'],
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ]);
                    
                    DB::table('tbl_sales')->where('sale_id', $sale->id)->update([
                        'earncoupon'      =>  $couponId,
                        'updated_at' => now()
                    ]);
                }

             }*/
            
    
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'message' => 'Sale order saved successfully.',
                'sale_id' => $sale->id,
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the sales save process.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    private function generateUniqueRandomCoupon($length, $table, $column)
    {
        do {
            $id = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
        } while (DB::table($table)->where($column, $id)->exists());

        return $id;
    }
    
    private function buildGlassDescription(array $fields)
    {
        $filtered = array_filter(array_map('trim', $fields), fn($v) => !empty($v));
        return implode(' - ', $filtered);
    }
    
    /** -------------------------
     *   Barcode Or Inventory Update
     *  ------------------------- */

    private function UpdateBarcodes($store_id, $barcodeno,$product_description,$product_type,$product_code,$order_no)
    {
        $user = auth()->user();
        
        $product = DB::table('tbl_barcode')
            ->where('t_status', '0')
            ->where('store_id', $store_id)
            ->where('barcode_no', $barcodeno)
            ->where('product_details', $product_description)
            ->where('product_type', $product_type)
            ->where('product_code', $product_code)
            ->first();
        
        if (empty($product)) 
        {
            $product = DB::table('tbl_barcode')
                ->where('barcode_no', $barcodeno)
                ->where('product_details', $product_description)
                ->where('product_type', $product_type)
                ->where('product_code', $product_code)
                ->where('transfer_store_id', $store_id)
                ->first();
                
             DB::table('tbl_barcode')->where([['id', '=', $product->id],
            ])->update([
                'transfer_outward_status' => 0,
                'refrence_no'   => $order_no,
                'updated_at'    => now()
            ]);    
        }
        else
        {
             DB::table('tbl_barcode')->where([['id', '=', $product->id],
            ])->update([
                'outward_status' => 0,
                'refrence_no'   => $order_no,
                'updated_at'    => now()
            ]);
        }
        
        
        $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                'barcode_no' => $barcodeno,
                'store_id' => $store_id,
                'reference_type' => 'Sale',
                'action_perform' => 'Order',
                'added_by' => $user->id,
        ]);
    }
    
    private function UpdateLensInventory($store_id, $product_description, $product_type, $product_code, $product_id, $noofbox, $perbox, $sale_date, $saleProductid) 
    {
        $user = auth()->user();
    
        $perboxunit = $perbox / $noofbox;
    
        $query = DB::table('tbl_inventory_levels')
            ->where('product_code', $product_code)
            ->where('product_details', $product_description)
            ->where('perbox', $perboxunit)
            ->where('store_id', $store_id);
    
        $inventory = $query->first();
    
        if (!empty($inventory)) 
        {
            $newBoxQty  = ($inventory->available_quantity ?? 0) - $noofbox;
            $newLensQty = ($inventory->tota_lens_qty ?? 0) - $perbox;
    
            DB::table('tbl_inventory_levels')
                ->where('id', $inventory->id)
                ->update([
                    'available_quantity' => $newBoxQty,
                    'tota_lens_qty'      => $newLensQty,
                    'updated_at'         => now()
                ]);
    
            // If stock becomes 0 or negative
            if ($newBoxQty <= 0 || $newLensQty <= 0) 
            {
                DB::table('tbl_sales_product')
                    ->where('id', $saleProductid)
                    ->update([
                        'pending_purchase_status' => 0, // Pending
                        'updated_at'              => now()
                    ]);
            }
            else
            {
                DB::table('tbl_sales_product')
                    ->where('id', $saleProductid)
                    ->update([
                        'pending_purchase_status' => 1, // Done
                        'updated_at'              => now()
                    ]);
            }
        } 
        else 
        {
            // Insert negative inventory if not found
            DB::table('tbl_inventory_levels')->insert([
                'product_code'       => $product_code,
                'product_id'         => $product_id,
                'product_type'       => $product_type,
                'product_details'    => $product_description,
                'store_id'           => $store_id,
                'available_quantity' => -$noofbox,
                'tota_lens_qty'      => -$perbox,
                'perbox'             => $perboxunit,
                'created_at'         => now(),
                'updated_at'         => now()
            ]);
    
            DB::table('tbl_sales_product')
                ->where('id', $saleProductid)
                ->update([
                    'pending_purchase_status' => 0, // Pending
                    'updated_at'              => now()
                ]);
        }
    
        // Inventory outward record
        DB::table('tbl_inventory_record')->insert([
            'product_code'    => $product_code,
            'product_id'      => $product_id,
            'product_type'    => $product_type,
            'product_details' => $product_description,
            'store_id'        => $store_id,
            'qty'             => $noofbox,
            'added_date'      => $sale_date,
            'outward_status'  => 0,
            'added_by'        => $user->id,
            'created_at'      => now(),
            'updated_at'      => now()
        ]);
    }
    
    
    private function UpdateGlassInventory($store_id, $product_description, $product_type, $product_code, $product_id, $qty, $sale_date, $saleProductid, $items) 
    {
        $user = auth()->user();
    
        $qty = 1;
    
        $query = DB::table('tbl_inventory_levels')
            ->where('product_code', $product_code)
            ->where('product_details', $product_description)
            ->where('store_id', $store_id);
    
        $inventory = $query->first();
    
        // Default purchase status
        $pending_purchase = 0;
    
        if (!empty($inventory)) 
        {
            $newQty = ($inventory->available_quantity ?? 0) - $qty;
    
            DB::table('tbl_inventory_levels')
                ->where('id', $inventory->id)
                ->update([
                    'available_quantity' => $newQty,
                    'updated_at'         => now()
                ]);
    
            // If stock becomes 0 or negative then pending purchase
            if ($newQty <= 0) 
            {
                $pending_purchase = 1;
    
                if ($items == 'Right') 
                {
                    DB::table('tbl_sales_product')
                        ->where('id', $saleProductid)
                        ->update([
                            'right_purchase'         => 1,
                            'right_glass'            => $product_description,
                            'pending_purchase_status'=> 0, // Pending
                            'updated_at'             => now()
                        ]);
                } 
                elseif ($items == 'Left') 
                {
                    DB::table('tbl_sales_product')
                        ->where('id', $saleProductid)
                        ->update([
                            'left_purchase'          => 1,
                            'left_glass'             => $product_description,
                            'pending_purchase_status'=> 0, // Pending
                            'updated_at'             => now()
                        ]);
                }
            }
            else
            {
                // Stock available
                DB::table('tbl_sales_product')
                    ->where('id', $saleProductid)
                    ->update([
                        'pending_purchase_status' => 1, // Done
                        'updated_at'              => now()
                    ]);
            }
        } 
        else 
        {
            // Inventory record not found, create negative stock
            DB::table('tbl_inventory_levels')->insert([
                'product_code'       => $product_code,
                'product_id'         => $product_id,
                'product_type'       => $product_type,
                'product_details'    => $product_description,
                'store_id'           => $store_id,
                'available_quantity' => -1,
                'created_at'         => now(),
                'updated_at'         => now()
            ]);
    
            if ($items == 'Right') 
            {
                DB::table('tbl_sales_product')
                    ->where('id', $saleProductid)
                    ->update([
                        'right_purchase'          => 1,
                        'right_glass'             => $product_description,
                        'pending_purchase_status' => 0, // Pending
                        'updated_at'              => now()
                    ]);
            } 
            elseif ($items == 'Left') 
            {
                DB::table('tbl_sales_product')
                    ->where('id', $saleProductid)
                    ->update([
                        'left_purchase'           => 1,
                        'left_glass'              => $product_description,
                        'pending_purchase_status' => 0, // Pending
                        'updated_at'              => now()
                    ]);
            }
        }
    
        // Inventory record entry
        DB::table('tbl_inventory_record')->insert([
            'product_code'    => $product_code,
            'product_id'      => $product_id,
            'product_type'    => $product_type,
            'product_details' => $product_description,
            'store_id'        => $store_id,
            'qty'             => $qty,
            'added_date'      => $sale_date,
            'outward_status'  => 0,
            'added_by'        => $user->id,
            'created_at'      => now(),
            'updated_at'      => now()
        ]);
    }
    
    private function UpdateFrameGogglesSolutuionInventory($store_id, $product_description, $product_type, $product_code, $product_id, $qty, $sale_date, $saleProductid) 
    {
        $user = auth()->user();
    
        $qty = (int) $qty;
    
        $query = DB::table('tbl_inventory_levels')
            ->where('product_code', $product_code)
            ->where('product_details', $product_description)
            ->where('store_id', $store_id);
    
        $inventory = $query->first();
    
        if (!empty($inventory)) 
        {
            $newQty = ($inventory->available_quantity ?? 0) - $qty;
    
            DB::table('tbl_inventory_levels')
                ->where('id', $inventory->id)
                ->update([
                    'available_quantity' => $newQty,
                    'updated_at'         => now()
                ]);
    
            // Pending Purchase Status
            if ($newQty <= 0) 
            {
                DB::table('tbl_sales_product')
                    ->where('id', $saleProductid)
                    ->update([
                        'pending_purchase_status' => 0, // Pending
                        'updated_at'              => now()
                    ]);
            }
            else
            {
                DB::table('tbl_sales_product')
                    ->where('id', $saleProductid)
                    ->update([
                        'pending_purchase_status' => 1, // Done
                        'updated_at'              => now()
                    ]);
            }
        }
        else
        {
            // Insert negative stock
            DB::table('tbl_inventory_levels')->insert([
                'product_code'       => $product_code,
                'product_id'         => $product_id,
                'product_type'       => $product_type,
                'product_details'    => $product_description,
                'store_id'           => $store_id,
                'available_quantity' => -$qty,
                'created_at'         => now(),
                'updated_at'         => now()
            ]);
    
            DB::table('tbl_sales_product')
                ->where('id', $saleProductid)
                ->update([
                    'pending_purchase_status' => 0, // Pending
                    'updated_at'              => now()
                ]);
        }
    
        // Inventory outward record
        DB::table('tbl_inventory_record')->insert([
            'product_code'    => $product_code,
            'product_id'      => $product_id,
            'product_type'    => $product_type,
            'product_details' => $product_description,
            'store_id'        => $store_id,
            'qty'             => $qty,
            'added_date'      => $sale_date,
            'outward_status'  => 0,
            'added_by'        => $user->id,
            'created_at'      => now(),
            'updated_at'      => now()
        ]);
    }
    
    
    
    private function UpdateOtherInventory($store_id, $product_description, $product_type, $product_code, $product_id, $qty, $sale_date, $saleProductid) 
    {
        $user = auth()->user();
    
        $qty = (int) $qty;
    
        $query = DB::table('tbl_inventory_levels')
            ->where('product_code', $product_code)
            ->where('product_details', $product_description)
            ->where('store_id', $store_id);
    
        $inventory = $query->first();
    
        if (!empty($inventory)) 
        {
            $newQty = ($inventory->available_quantity ?? 0) - $qty;
    
            DB::table('tbl_inventory_levels')
                ->where('id', $inventory->id)
                ->update([
                    'available_quantity' => $newQty,
                    'updated_at'         => now()
                ]);
    
            // Pending Purchase Status
            if ($newQty <= 0) 
            {
                DB::table('tbl_sales_product')
                    ->where('id', $saleProductid)
                    ->update([
                        'pending_purchase_status' => 0, // Pending
                        'updated_at'              => now()
                    ]);
            }
            else
            {
                DB::table('tbl_sales_product')
                    ->where('id', $saleProductid)
                    ->update([
                        'pending_purchase_status' => 1, // Done
                        'updated_at'              => now()
                    ]);
            }
        }
        else
        {
            // Insert negative stock
            DB::table('tbl_inventory_levels')->insert([
                'product_code'       => $product_code,
                'product_id'         => $product_id,
                'product_type'       => $product_type,
                'product_details'    => $product_description,
                'store_id'           => $store_id,
                'available_quantity' => -$qty,
                'created_at'         => now(),
                'updated_at'         => now()
            ]);
    
            DB::table('tbl_sales_product')
                ->where('id', $saleProductid)
                ->update([
                    'pending_purchase_status' => 0, // Pending
                    'updated_at'              => now()
                ]);
        }
    
        // Inventory outward record
        DB::table('tbl_inventory_record')->insert([
            'product_code'    => $product_code,
            'product_id'      => $product_id,
            'product_type'    => $product_type,
            'product_details' => $product_description,
            'store_id'        => $store_id,
            'qty'             => $qty,
            'added_date'      => $sale_date,
            'outward_status'  => 0,
            'added_by'        => $user->id,
            'created_at'      => now(),
            'updated_at'      => now()
        ]);
    }
    
    public function generateUniqueRandomId($length = 6, $table = 'tbl_customer', $column = 'cust_unique_id', $min = 100000, $max = 999999)
    {
        do {
            $id = 'C'.random_int($min, $max);
        } while (
            DB::table($table)->where($column, $id)->exists()
        );
    
        return $id;
    }
    
    
    public function salePendingHistory()
    {
        $setting['page_title'] = 'Pending Order';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/pending-order',$setting);
    }
    
    
    public function salesPendingDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $search1 = $request->input('search1');
        $sale_person = $request->input('sale_person');
        
        if($store_id == '0')
        {
            $totalData = DB::table('tbl_sales')->where('sales_status', 0)->where('is_deleted', 0);
        }
        else
        {
            $totalData = DB::table('tbl_sales')->where('store_id', $store_id)->where('sales_status', 0)->where('is_deleted', 0);
        }
        if ($sale_person != '')
        {
            $totalData->where('sale_person', [$sale_person]);
        }
        if ($date_from != '' && $date_to != '') {
            $totalData->whereBetween('sale_date', [$date_from,  $date_to . ' 23:59:59']);
        }

        if ($search1 != '') 
        {
            $totalData->where('order_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_id', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%');
        }
        $totalData = $totalData->count();
        
        if($store_id == '0')
        {
            $templates = DB::table('tbl_sales')->where('sales_status', 0)->where('is_deleted', 0);
        }
        else
        {
            $templates = DB::table('tbl_sales')->where('store_id', $store_id)->where('sales_status', 0)->where('is_deleted', 0);
        }
        if ($sale_person != '')
        {
            $templates->where('sale_person', [$sale_person]);
        }
        if ($date_from != '' && $date_to != '') 
        {
            $templates->whereBetween('sale_date', [$date_from,  $date_to . ' 23:59:59']);
        }

        if ($search1 != '') 
        {
            $templates->where('order_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_id', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%');
        }


        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('sale_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (!empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                $sale_person = User::find($template->sale_person);
                $tbl_store   = Store::find($template->store_id);
                $encryptedId = base64_encode($template->sale_id);
                
                if($template->sales_type == '1')
                {
                    $sales_type = '<div class="tooltip">
                                      <i class="fa fa-exchange" aria-hidden="true"></i>
                                      <span class="tooltiptext">Inter Store Sale</span>
                                    </div>';
                }
                else
                {
                    $sales_type = '';
                }
                
                $encryptedId = base64_encode($template->sale_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['order_details']     = '<strong>Order Date</strong> :'.date('d M, Y h:i A', strtotime($template->created_at)).'<BR> <strong>Delivery Date</strong> :'.date('d M, Y h:i A', strtotime($template->delivery_date));
                $nestedData['bill_details']      = '<strong>Order Date</strong> :'.date('d M, Y h:i A', strtotime($template->created_at)).'<BR><strong>Bill No : </strong> : '.$template->order_no.'<br>'.$sales_type;
                $nestedData['customer_details']  = '<strong>Customer Name</strong> :'.$template->cust_name.'<BR><strong>Mobile No :</strong>'.$template->contact_no.'<BR><strong>Cust ID : </strong> : '.$template->cust_id;
                $nestedData['invoice_details']   = '
                                                  <strong>Order Value : </strong>'.$template->total_item_price.'
                                                  <BR><strong>Total Discount : </strong>'.$template->total_discount.'
                                                  <BR><strong>Total Payable : </strong>'.$template->total_payable.'
                                                  <BR><strong>Advance Paid : </strong>'.$template->pay_amount.'
                                                  <BR><strong>Balance Paid  : </strong>'.$template->pending_amount.'';
                $nestedData['store_name']   = $tbl_store->store_name;
                $nestedData['sale_person']  = $sale_person->name;
                $nestedData['encryptedId']  = $encryptedId;
                $nestedData['sales_type']   = $template->sales_type;
                $nestedData['oid']  = $template->order_no;
                $nestedData['ready_reminder_sms']  = $template->ready_reminder_sms;
                $data[]  = $nestedData;
            }
        }
        
        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();
        
    }
    
    public function saleHistory()
    {
        $setting['page_title'] = 'Sales Records';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/sale-histroy',$setting);
    }
    
    
    public function salesDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $search1 = $request->input('search1');
        $sale_person = $request->input('sale_person');
        
        if($store_id == '0')
        {
            $totalData = DB::table('tbl_sales')->where('sales_status', 1)->where('is_deleted', 0);
        }
        else
        {
            $totalData = DB::table('tbl_sales')->where('store_id', $store_id)->where('sales_status', 1)->where('is_deleted', 0);
        }
        if ($sale_person != '')
        {
            $totalData->where('sale_person', [$sale_person]);
        }
        if ($date_from != '' && $date_to != '') {
            $totalData->whereBetween('sale_date', [$date_from,  $date_to . ' 23:59:59']);
        }

        if ($search1 != '') 
        {
            $totalData->where('order_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_id', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%');
        }
        $totalData = $totalData->count();
        
        if($store_id == '0')
        {
            $templates = DB::table('tbl_sales')->where('sales_status', 1)->where('is_deleted', 0);
        }
        else
        {
            $templates = DB::table('tbl_sales')->where('store_id', $store_id)->where('sales_status', 1)->where('is_deleted', 0);
        }
        if ($sale_person != '')
        {
            $templates->where('sale_person', [$sale_person]);
        }
        if ($date_from != '' && $date_to != '') 
        {
            $templates->whereBetween('sale_date', [$date_from,  $date_to . ' 23:59:59']);
        }

        if ($search1 != '') 
        {
            $templates->where('order_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_id', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%');
        }


        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('sale_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (!empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                $sale_person = User::find($template->sale_person);
                $tbl_store   = Store::find($template->store_id);
                $encryptedId = base64_encode($template->sale_id);
                
                if($template->inter_sale == '1')
                {
                    $sales_type = '<div class="tooltip">
                                      <i class="fa fa-exchange" aria-hidden="true"></i>
                                      <span class="tooltip-text">Inter Store Sale</span>
                                    </div>';
                                    
                    $custdetails = '<strong>Customer Name</strong> :'.$template->cust_name.'<BR><strong>Mobile No :</strong>'.$template->contact_no.'<BR><strong>Store ID : </strong> : '.$template->cust_id;                
                }
                else
                {
                    $sales_type = '';
                    $custdetails = '<strong>Customer Name</strong> :'.$template->cust_name.'<BR><strong>Mobile No :</strong>'.$template->contact_no.'<BR><strong>Cust ID : </strong> : '.$template->cust_id;
                }
                
                $encryptedId = base64_encode($template->sale_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['order_details']     = '<strong>Order Date</strong> :'.date('d M, Y h:i A', strtotime($template->created_at)).'<BR> <strong>Delivery Date</strong> :'.date('d M, Y h:i A', strtotime($template->delivery_date));
                $nestedData['bill_details']      = '<strong>Order Date</strong> :'.date('d M, Y h:i A', strtotime($template->created_at)).'<BR><strong>Bill No : </strong> : '.$template->order_no.'<br>'.$sales_type;
                $nestedData['customer_details']  = $custdetails;
                $nestedData['invoice_details']   = '
                                                  <strong>Order Value : </strong>'.$template->total_item_price.'
                                                  <BR><strong>Total Discount : </strong>'.$template->total_discount.'
                                                  <BR><strong>Total Payable : </strong>'.$template->total_payable.'
                                                  <BR><strong>Advance Paid : </strong>'.$template->pay_amount.'
                                                  <BR><strong>Balance Paid  : </strong>'.$template->pending_amount.'';
                $nestedData['store_name']   = $tbl_store->store_name;
                $nestedData['sale_person']  = $sale_person->name;
                $nestedData['encryptedId']  = $encryptedId;
                $nestedData['sales_type']   = $template->sales_type;
                $nestedData['oid']  = $template->order_no;
                $data[]  = $nestedData;
            }
        }
        
        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();
        
    }
    
    public function checkremindersms(Request $request)
    {
        $oid = $request->oid;
        $messages = [];
    
        
    
        /* ===================== SMS CHECK ===================== */
        $smsTemplate = DB::table('tbl_sms_template')
            ->where('title', 'Order Ready reminder SMS')
            ->first();
    
        if ($smsTemplate && $smsTemplate->send_status == 0) 
        {
            $tbl_sms_whatsapp_record = DB::table('tbl_sms_whatsapp_record')->where('order_no', $oid)->where('title', 'Order Ready reminder SMS')->first();
            
            if (!empty($tbl_sms_whatsapp_record))
            {
                if($tbl_sms_whatsapp_record->sms_status == 1)
                {
                     $messages[] = [
                        'type' => 'error',
                        'text' => 'Order Ready Reminder SMS already sent to customer.'
                    ];
                }
                else
                {
                    // SEND SMS CODE HERE
        
                    DB::table('tbl_sms_whatsapp_record')
                        ->where('order_no', $oid)
                        ->update(['sms_status' => 1,'sms_datetime' => now()]);
        
                    $messages[] = [
                        'type' => 'success',
                        'text' => 'Order Ready Reminder SMS sent successfully.'
                    ];
                    
                }
               
            } 
            else
            {
                DB::table('tbl_sms_whatsapp_record')->insert([
                    'order_no'    => $oid,
                    'sms_status' => 1,
                    'sms_datetime' => now(),
                ]);
                
            }
        } else {
            $messages[] = [
                'type' => 'error',
                'text' => 'Order Ready Reminder SMS template is disabled in SMS Template Settings.'
            ];
        }
    
        /* ===================== WHATSAPP CHECK ===================== */
        $waTemplate = DB::table('tbl_whatsapp_template')
            ->where('title', 'Order Ready reminder SMS')
            ->first();
    
        if ($waTemplate && $waTemplate->send_status == 0) 
        {
            $tbl_sms_whatsapp_record = DB::table('tbl_sms_whatsapp_record')->where('order_no', $oid)->where('title', 'Order Ready reminder SMS')->first();
            
            if (!empty($tbl_sms_whatsapp_record))
            {
                if($tbl_sms_whatsapp_record->whatsapp_status == 1)
                {
                     $messages[] = [
                        'type' => 'error',
                        'text' => 'Order Ready Reminder Whatsapp already sent to customer.'
                    ];
                }
                else
                {
                    // SEND WHATSAPP CODE HERE
        
                    DB::table('tbl_sms_whatsapp_record')
                        ->where('order_no', $oid)
                        ->update(['whatsapp_status' => 1,'whatsapp_datetime' => now()]);
        
                    $messages[] = [
                        'type' => 'success',
                        'text' => 'Order Ready Reminder Whatsapp sent successfully.'
                    ];
                    
                }
               
            } 
            else
            {
                DB::table('tbl_sms_whatsapp_record')->insert([
                    'order_no'    => $oid,
                    'whatsapp_status' => 1,
                    'whatsapp_datetime' => now(),
                ]);
                
                $messages[] = [
                        'type' => 'success',
                        'text' => 'Order Ready Reminder Whatsapp sent successfully.'
                    ];
                
            }
            
        } else {
            $messages[] = [
                'type' => 'error',
                'text' => 'Order Ready Reminder WhatsApp template is disabled in WhatsApp Template Settings.'
            ];
        }
    
        return response()->json([
            'status' => 'ok',
            'messages' => $messages
        ]);
    }
    
    
    public function getallwhatsapptamplete(Request $request)
    {
        $oid = $request->oid;
    
        $whatsapp = DB::table('tbl_whatsapp_template as wt')
            ->where('wt.send_status', 0)
            ->whereNotExists(function ($query) use ($oid) {
                $query->select(DB::raw(1))
                    ->from('tbl_sms_whatsapp_record as wr')
                    ->whereColumn('wr.title', 'wt.title')
                    ->where('wr.order_id', $oid)
                    ->where('wr.whatsapp_status', 1);
            })
            ->orderBy('wt.id', 'ASC')
            ->get();
    
        return response()->json([
            'data' => $whatsapp->map(function ($p) use ($oid) {
                return [
                    'orderid'    => $oid,
                    'title'      => $p->title,
                    'pay_method' => $p->send_method == 0 ? 'API' : 'WEB',
                ];
            })
        ]);
    }
    
    
    public function sendmessageonwhtasapp(Request $request)
    {
        $oid = $request->oid;
        $title = $request->title;
        
        $waTemplate = DB::table('tbl_whatsapp_template')
            ->where('title', $title)
            ->first();
    
        if ($waTemplate && $waTemplate->send_status == 0) 
        {
            $tbl_sms_whatsapp_record = DB::table('tbl_sms_whatsapp_record')->where('order_no', $oid)->where('title',$title)->first();
            
            if (!empty($tbl_sms_whatsapp_record))
            {
                if($tbl_sms_whatsapp_record->whatsapp_status == 1)
                {
                    $response['status_code'] = '200';
                    $response['msg'] = $title.' Whatsapp already sent to customer.';
        
                }
                else
                {
                    // SEND WHATSAPP CODE HERE
        
                    DB::table('tbl_sms_whatsapp_record')
                        ->where('order_no', $oid)
                        ->update(['whatsapp_status' => 1,'whatsapp_datetime' => now()]);
                    
                    $response['status_code'] = '201';
                    $response['msg'] = $title.' Order Ready Reminder Whatsapp sent successfully.';

                }
               
            } 
            else
            {
                DB::table('tbl_sms_whatsapp_record')->insert([
                    'order_no'    => $oid,
                    'whatsapp_status' => 1,
                    'whatsapp_datetime' => now(),
                ]);
                
                $response['status_code'] = '201';
                $response['msg'] = $title.' Order Ready Reminder Whatsapp sent successfully.';
                
            }
            
        } else {
            
            $response['status_code'] = '202';
            $response['msg'] = $title.' WhatsApp template is disabled in WhatsApp Template Settings.';
            
        }
    
        return response()->json($response);
    }

    public function saleConfirmpage($id)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Pending Sale Confirm';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        
        $sale = Sale::where('sale_id', $decryptedId)->first();
        $store= Store::where('id', $sale->store_id)->first();
        
        $setting['store'] = Store::find($sale->store_id);
        $setting['salePerson'] = User::find($sale->sale_person);
        $setting['customer'] = Customer::where('contact_no', $sale->contact_no)->first();
        $setting['state'] = State::find($store->state_id);
        $setting['city'] = City::find($store->city_id);
        $setting['sale'] = $sale;
        $setting['saleproduct'] = SaleProduct::where('sale_id', $decryptedId)
        ->orderBy('id', 'asc')
        ->get()
        ->unique(function ($item) {
            return $item->product_type . '|' .
                   $item->product_code . '|' .
                   $item->barcode_use . '|' .
                   $item->base_price . '|' .
                   $item->discount_amt . '|' .
                   $item->return_status . '|' .
                   $item->qty . '|' .
                   $item->no_of_glass . '|' .
                   $item->product_deatils;
        })
        ->values(); 
        
    
        return view($this->view_route.'/pending-sale-confirm',$setting);
    }
    
    public function orderconfirm(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|integer'
        ]);
    
        $sale_id = $request->sale_id;
    
        DB::beginTransaction();
    
        try {
    
            DB::table('tbl_sales')
                ->where('sale_id', $sale_id)
                ->update(['sales_status' => 1]);
    
            // Get sale products
            $saleProducts = SaleProduct::where('sale_id', $sale_id)->get();
    
            $tracking_status = 'ORDER CONFIRM';
    
            foreach ($saleProducts as $product) {
    
                OrderTracking::firstOrCreate(
                    [
                        'order_no'        => $product->order_no,
                        'product_code'    => $product->product_code ?? null,
                        'tracking_status' => $tracking_status,
                    ],
                    [
                        'product_type' => $product->product_type,
                        'description'  => $product->product_deatils ?? null,
                        'store_id'     => $product->store_id,
                    ]
                );
            }
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'msg' => 'Order confirmed successfully!'
            ], 200);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            // Log actual error
            \Log::error('Order Confirm Error: ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong during the confirm order process.'
            ], 500);
        }
    }
    
    public function saleInvoiceEdit($id)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Sale invoice Edit';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        
        $sale = Sale::where('sale_id', $decryptedId)->first();
        $store= Store::where('id', $sale->store_id)->first();
        
        $setting['store'] = Store::find($sale->store_id);
        $setting['salePerson'] = User::find($sale->sale_person);
        $setting['customer'] = Customer::where('contact_no', $sale->contact_no)->first();
        $setting['state'] = State::find($store->state_id);
        $setting['city'] = City::find($store->city_id);
        $setting['sale'] = $sale;
        $setting['saleproduct'] = SaleProduct::where('sale_id', $decryptedId)
        ->orderBy('id', 'asc')
        ->get()
        ->unique(function ($item) {
            return $item->product_type . '|' .
                   $item->product_code . '|' .
                   $item->barcode_use . '|' .
                   $item->base_price . '|' .
                   $item->discount_amt . '|' .
                   $item->return_status . '|' .
                   $item->qty . '|' .
                   $item->no_of_glass . '|' .
                   $item->product_deatils;
        })
        ->values(); 
    
        return view($this->view_route.'/sale-invoice-edit',$setting);
    }
    
    public function loadorderdetails(Request $request)
    {
        $sale = Sale::where('sale_id', $request->sale_id)->firstOrFail();
    
        $store = Store::find($sale->store_id);
    
        $data = [
            'sale'        => $sale,
            'store'       => $store,
            'salePerson'  => User::find($sale->sale_person),
            'customer'    => Customer::where('contact_no', $sale->contact_no)->first(),
            'state'       => State::find(optional($store)->state_id),
            'city'        => City::find(optional($store)->city_id),
            'saleproduct' => SaleProduct::where('sale_id', $sale->sale_id)
                            ->orderBy('id', 'asc')
                            ->get()
                            ->unique(function ($item) {
                                return $item->product_type . '|' .
                                       $item->product_code . '|' .
                                       $item->barcode_use . '|' .
                                       $item->base_price . '|' .
                                       $item->discount_amt . '|' .
                                       $item->return_status . '|' .
                                       $item->qty . '|' .
                                       $item->no_of_glass . '|' .
                                       $item->product_deatils;
                            })
                            ->values(),
    
            'salepayment' => DB::table('tbl_sale_payment')
                                ->where('order_no', $sale->order_no)
                                ->where('pay_type', '!=', 2)
                                ->orderBy('payment_id', 'ASC')
                                ->get(),
    
            'salereturnpayment' => DB::table('tbl_sale_payment')
                                ->where('order_no', $sale->order_no)
                                ->where('pay_type', 2)
                                ->orderBy('payment_id', 'ASC')
                                ->get(),
        ];
    
        $html = view('sales.order_details', $data)->render();
    
        return response()->json([
            'status' => 'success',
            'order_section' => $html
        ]);
    }
    
    
    public function saleseditpaymentdestroy($id)
    {
        $tbl_sale_payment = DB::table('tbl_sale_payment')->where('payment_id', $id)->first();
        
        $tbl_sale = DB::table('tbl_sales')->where('order_no', $tbl_sale_payment->order_no)->first();
        
        $payamount = $tbl_sale->pay_amount - $tbl_sale_payment->pay_amount;
        $pendingamount = $tbl_sale->pending_amount + $tbl_sale_payment->pay_amount;
        
        DB::table('tbl_sales')
        ->where('order_no', $tbl_sale_payment->order_no)
        ->update(['pay_amount' => $payamount,'pending_amount' => $pendingamount]);
        
        
        $Is_delted=DB::table('tbl_sale_payment')->where('payment_id',$id)->delete();

        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment was successfully deleted',
        ]);
    }
    
    
    public function saleseditpaymentreturndelete($id)
    {
        
        $Is_delted=DB::table('tbl_sale_payment')->where('payment_id',$id)->delete();

        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment was successfully deleted',
        ]);
    }
   
   
    public function salesCustomerUpdate(Request $request)
    {
        $user = auth()->user();
        
        //dd($request);
        
        DB::table('tbl_sales')
        ->where('order_no', $request->order_no)
        ->update(['cust_name' => $request->cust_name,'contact_no' => $request->contact_no,'email_id' => $request->email_id]);
    
        $validator = Validator::make($request->all(), [
            'cust_name' => 'required|string',
            'email_id'  => 'required|email',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        $data = [
            'cust_type'     => $request->cust_type,
            'cust_name'     => $request->cust_name,
            'contact_no'    => $request->contact_no,
            'email_id'      => $request->email_id,
            'cust_category' => $request->cust_category,
            'gender'        => $request->gender,
            'cust_address'  => $request->cust_address,
            'state_id'      => $request->state_id,
            'city_id'       => $request->city_id,
            'pincode'       => $request->pincode,
            'dob'           => $request->dob,
            'doa'           => $request->doa,
            'cust_note'     => $request->cust_note,
            'updated_by'    => $user->id,
            'updated_at'    => now(),
        ];
    
        // Remove null / empty values
        $data = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });
    
        Customer::where('contact_no', $request->contact_no)->update($data);
    
        return response()->json(['success' => 'Customer updated successfully.']);
    }
    
    
    public function salesorderUpdate(Request $request)
    {
        $user = auth()->user();
        
        
    
        $validator = Validator::make($request->all(), [
            'sale_person' => 'required',
            'delivery_date'  => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        $data = [
            'delivery_date'     => $request->delivery_date,
            'sale_person'     => $request->sale_person,
            'extrnal_warranty'    => $request->extrnal_warranty,
        ];
    
        // Remove null / empty values
        $data = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });
    
        Sale::where('order_no', $request->order_no)->update($data);
    
        return response()->json(['success' => 'Order Details updated successfully.']);
    }
    
    

    public function salesRemoveProduct(Request $request)
    {
        $user = auth()->user();
        $saleId = $request->order_no;
        $itemIds = json_decode($request->remove_items, true);
    
        DB::beginTransaction();
    
        try {
    
            $remainingItems = SaleProduct::where('order_no', $saleId)->count();
            if (count($itemIds) >= $remainingItems) {
                return response()->json([
                    'error' => ['items' => 'At least one item must remain']
                ]);
            }
            
            

            // Fetch items before delete
            $items = SaleProduct::whereIn('id', $itemIds)->get();
    
            foreach ($items as $item) 
            {
                if(!empty($item->barcode_use))
                {
                    DB::table('tbl_barcode')
                    ->where('refrence_no', $item->order_no)
                    ->where('barcode_no', $item->barcode_use)
                    ->update([
                        'refrence_no' => '',
                        'outward_status' => '',
                        'transfer_outward_status' => ''
                    ]);
                    
                    
                    $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                            'barcode_no' => $item->barcode_use,
                            'store_id' => $item->store_id,
                            'reference_type' => 'Sale',
                            'action_perform' => 'Remove',
                            'added_by' => $user->id,
                    ]);
                    
                }
                
                if ($item->product_type === 'Glass')
                {
                    // Both Glass
                    if ($item->qty == 2) {
                        $this->addInventoryStock($item, $item->right_glass, 1, $user);
                        $this->addInventoryStock($item, $item->left_glass, 1, $user);
                    }
                    // Single Glass
                    else {
                        if ($item->right_purchase == 1) {
                            $this->addInventoryStock($item, $item->right_glass, 1, $user);
                        } else {
                            $this->addInventoryStock($item, $item->left_glass, 1, $user);
                        }
                    }
                    
                    SaleProduct::where('no_of_glass', $item->no_of_glass)->where('order_no', $item->order_no)->where('product_type', $item->product_type)->delete();
                }
                elseif ($item->product_type === 'Lens')
                {
                    if(!empty($item->barcode_use))
                    {
                        $array = explode(',', $item->barcode_use);

                        foreach ($array as $value) 
                        {
                            $lensbarcode = DB::table('tbl_barcode')->where('order_no', $item->order_no)->where('product_type', 'Lens')->where('barcode_no', $value)->first;
                            
                            $query = DB::table('tbl_inventory_levels')
                                ->where('product_code', $lensbarcode->product_code)
                                ->where('product_details', $lensbarcode->product_details)
                                ->where('product_type', $lensbarcode->product_type)
                                ->where('perbox', $lensbarcode->perbox)
                                ->where('store_id', $item->store_id);
                        
                            $inventory = $query->first();
                            
                            if ($inventory) 
                            {
                                $query->update([
                                    'available_quantity' => $inventory->available_quantity + 1,
                                    'tota_lens_qty' => $inventory->tota_lens_qty + ($lensbarcode->perbox),
                                    'updated_at' => now()
                                ]);
                                
                                DB::table('tbl_inventory_record')->insert([
                                    'product_code'    => $lensbarcode->product_code,
                                    'product_id'      => $lensbarcode->product_id,
                                    'product_type'    => $lensbarcode->product_type,
                                    'product_details' => $lensbarcode->product_details,
                                    'store_id'        => $item->store_id,
                                    'qty'             => 1,
                                    'added_date'      => now()->format('Y-m-d'),
                                    'outward_status'  => null,
                                    'added_by'        => $user->id,
                                    'created_at'      => now(),
                                    'updated_at'      => now()
                                ]);
                            }
                            
                            DB::table('tbl_barcode')
                                ->where('id', $lensbarcode->id)
                                ->update([
                                    'refrence_no' => '',
                                    'outward_status' => '',
                                    'transfer_outward_status' => ''
                                ]);
                                
                                
                                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                                        'barcode_no' => $lensbarcode->barcode_no,
                                        'store_id' => $item->store_id,
                                        'reference_type' => 'Sale',
                                        'action_perform' => 'Remove',
                                        'added_by' => $user->id,
                                ]);
                                
                                DB::table('tbl_barcode')
                                ->where('lens_box', $lensbarcode->barcode_no)
                                ->update([
                                    'refrence_no' => '',
                                    'outward_status' => '',
                                    'transfer_outward_status' => ''
                                ]);
                            
                        }
                    }
                    else
                    {
                        if($item->lensRightNoOfBoxes > 0)
                        {
                        	$product_details = $this->buildGlassDescription([
                        		$item->product_description ?? '',
                        		'SPH:' . ($item->GL_EYE_RS_D ?? ''),
                        		'CYL:' . ($item->GL_EYE_RC_D ?? ''),
                        		'ADD:' . ($item->GL_EYE_RADD ?? ''),
                        		'Axis:' . ($item->GL_EYE_RA_D ?? ''),
                        	]);
                        
                        	// BUG FIX: last argument must be $item, not full array
                        	$perboxunit = $item->perbox / $item->lensRightNoOfBoxes;
                                
                                $query = DB::table('tbl_inventory_levels')
                                    ->where('product_code', $item->product_code)
                                    ->where('product_details', $product_details)
                        			->where('perbox', $perboxunit)
                                    ->where('store_id', $item->store_id);
                                $inventory = $query->first();
                                if(!empty($inventory)) 
                                {
                                    DB::table('tbl_inventory_levels')
                                    ->where('id', $inventory->id)
                                    ->update([
                                        'available_quantity' =>  $inventory->available_quantity - $item->lensRightNoOfBoxes,
                        				'tota_lens_qty' => $inventory->tota_lens_qty - ($perbox),
                                        'updated_at'         => now()
                                    ]);
                                       
                        
                                }
                        }
                        
                        if($item->lensLeftNoOfBoxes > 0)
                        {
                        	$product_details = $this->buildGlassDescription([
                        		$item->product_description ?? '',
                        		'SPH:' . ($item->GL_EYE_LS_D ?? ''),
                        		'CYL:' . ($item->GL_EYE_LC_D ?? ''),
                        		'ADD:' . ($item->GL_EYE_LADD ?? ''),
                        		'Axis:' . ($item->GL_EYE_LA_D ?? ''),
                        	]);
                        
                        	// BUG FIX: last argument must be $item, not full array
                        	$perboxunit = $item->perbox / $item->lensLeftNoOfBoxes;
                                
                                $query = DB::table('tbl_inventory_levels')
                                    ->where('product_code', $item->product_code)
                                    ->where('product_details', $product_details)
                        			->where('perbox', $perboxunit)
                                    ->where('store_id', $item->store_id);
                                $inventory = $query->first();
                                if(!empty($inventory)) 
                                {
                                    DB::table('tbl_inventory_levels')
                                    ->where('id', $inventory->id)
                                    ->update([
                                        'available_quantity' =>  $inventory->available_quantity - $item->lensLeftNoOfBoxes,
                        				'tota_lens_qty' => $inventory->tota_lens_qty - ($perbox),
                                        'updated_at'         => now()
                                    ]);
                                       
                        
                                }
                        }
                    }
                    

                    SaleProduct::whereIn('id', $itemIds)->delete();
                }
                elseif ($item->product_type === 'Repair') 
                {
                    // No inventory required
                    
                    SaleProduct::whereIn('id', $itemIds)->delete();
                }
                else {
                    // Other products
                    $this->addInventoryStock(
                        $item,
                        $item->product_deatils,
                        $item->qty,
                        $user
                    );
                    
                    SaleProduct::whereIn('id', $itemIds)->delete();
                }
                
            }
    
            // Delete sale items
            
            
            $salesv = Sale::where('order_no', $saleId)->first();
            
            DB::transaction(function () use ($saleId, $salesv, $request, $user) {

            if ($salesv) {
        
                /* ============================
                   LOYALTY POINTS
                ============================ */
                if ($salesv->earnedPoints > 0) {
        
                    $tblcustomer = DB::table('tbl_customer')
                        ->where('contact_no', $salesv->contact_no)
                        ->first();
        
                    if ($tblcustomer) {
                        $description = 'Sales Invoice ' . $saleId;
        
                        // Remove previous earned points
                        DB::table('tbl_loyaltyrogram_histroy')
                            ->where('description', $description)
                            ->delete();
        
                        $oldPoints = (int) $salesv->earnedPoints;
        
                        DB::table('tbl_customer')
                            ->where('customer_id', $tblcustomer->customer_id)
                            ->update([
                                'Loyalty_Points'     => DB::raw("GREATEST(Loyalty_Points - $oldPoints, 0)"),
                                'Loyalty_Points_Bal' => DB::raw("GREATEST(Loyalty_Points_Bal - $oldPoints, 0)"),
                                'updated_at'         => now(),
                            ]);
        
                        // Generate new points
                        $tblloyalty = DB::table('tbl_loyalty')->where('id', 2)->first();
        
                        if ($tblloyalty && (int)$tblloyalty->auto_status === 0) {
        
                            $salesamount = ((int)$tblloyalty->sales_value === 2)
                                ? (float) $request->total_payable
                                : (float) $request->total_item_price;
        
                            if ((int)$tblloyalty->auto_set_loyalty_point === 0) {
                                $earnedPoints = ($tblloyalty->no_of_points > 0 && $tblloyalty->x_number_sale_value > 0)
                                    ? floor(($salesamount / $tblloyalty->x_number_sale_value) * $tblloyalty->no_of_points)
                                    : 0;
                            } else {
                                $earnedPoints = floor(($salesamount * $tblloyalty->fixed_per) / 100);
                            }
        
                            $earnedPoints = (int) max($earnedPoints, 0);

        
                            DB::table('tbl_sales')
                                ->where('sale_id', $salesv->sale_id)
                                ->update([
                                    'earnedPoints' => $earnedPoints,
                                    'updated_at'   => now(),
                                ]);
                        }
                    }
                }
        
                /* ============================
                   COUPONS
                ============================ */
                if ($salesv->earncoupon !== null) {
        
                    // Remove old coupon
                    DB::table('tbl_coupon')
                        ->where('coupon_id', $salesv->earncoupon)
                        ->delete();
        
                    $tblcoupon = DB::table('tbl_coupon_auto')->where('id', 1)->first();
        
                    if ($tblcoupon && (int)$tblcoupon->auto_status === 0) {
        
                        $salesamount = ((int)$tblcoupon->sales_value === 2)
                            ? (float) $request->pay_amount
                            : (float) $request->total_item_price;
        
                        $range = DB::table('tbl_coupon_auto')
                            ->where('from_range', '<=', $salesamount)
                            ->where('to_range', '>=', $salesamount)
                            ->first();
        
                        if ($range) {
        
                            $couponCode = $this->generateUniqueRandomCoupon(6, 'tbl_coupon', 'coupon_code');
        
                            $couponId = DB::table('tbl_coupon')->insertGetId([
                                'coupon_type'          => $tblcoupon->coupon_value_type,
                                'coupon_code'          => $couponCode,
                                'coupon_value'         => $range->coupon_value,
                                'min_sale_vale'        => $range->sales_value_amount,
                                'valid_from'           => now()->toDateString(),
                                'valid_to'             => now()->addDays($range->valid_dyas)->toDateString(),
                                'coupon_usages'        => 0,
                                'coupon_generate_type' => 0,
                                'coupon_status'        => '0',
                                'added_by'             => $user->id,
                                'store_id'             => $salesv->store_id,
                                'created_at'           => now(),
                                'updated_at'           => now(),
                            ]);
        
                            DB::table('tbl_sales')
                                ->where('sale_id', $salesv->sale_id)
                                ->update([
                                    'earncoupon' => $couponId,
                                    'updated_at' => now(),
                                ]);
                        }
                    }
                }
            }
        });

     
            

            Sale::where('order_no', $saleId)
            ->update([
                'total_basic_amount'  => $request->total_basic_amount ?? 0,
                'total_gst_amount'    => $request->total_gst_amount ?? 0,
                'total_item_price'    => $request->total_item_price ?? 0,
                'total_discount'      => $request->total_discount ?? 0,
                'fitting_fee'         => $request->fitting_fee ?? 0,
                'coupon_amount'       => $request->coupon_amount ?? 0,
                'coupon_id'           => $request->coupon_id,
                'cart_discount'       => $request->cart_discount,
                'cart_discount_by'    => $request->cart_discount_by,
                'cart_discount_per'   => $request->cart_discount_per,
                'cart_discount_resion'=> $request->cart_discount_resion,
                'loyalty_point_amount'=> $request->loyalty_point ?? 0,
                'loyalty_point_apply' => $request->loyalty_point_apply ?? 0,
                'total_payable'       => $request->total_payable ?? 0,
                'pay_amount'          => $request->pay_amount ?? 0,
                'pending_amount'      => $request->total_payable - $request->pay_amount  ?? 0,
            ]);

    
            DB::commit();
    
            return response()->json([
                'success' => 'Item removed and inventory restored successfully'
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'error' => ['server' => $e->getMessage()]
            ], 500);
        }
    }
    
    
    function addInventoryStock($item, $productDetails, $qty, $user)
    {
        $query = DB::table('tbl_inventory_levels')
            ->where('product_code', $item->product_code)
            ->where('product_details', $productDetails)
            ->where('product_type', $item->product_type)
            ->where('store_id', $item->store_id);
    
        $inventory = $query->first();
    
        if ($inventory) 
        {
            $query->update([
                'available_quantity' => $inventory->available_quantity + $qty,
                'updated_at' => now()
            ]);
            
            DB::table('tbl_inventory_record')->insert([
                'product_code'    => $item->product_code,
                'product_id'      => $item->product_id,
                'product_type'    => $item->product_type,
                'product_details' => $productDetails,
                'store_id'        => $item->store_id,
                'qty'             => $qty,
                'added_date'      => now()->format('Y-m-d'),
                'outward_status'  => null,
                'added_by'        => $user->id,
                'created_at'      => now(),
                'updated_at'      => now()
            ]);
        }
    
        
    }
    
    
    public function roundoffValueUpdate(Request $request)
    {
        $user = auth()->user();
        
        
    
        $validator = Validator::make($request->all(), [
            'roundOffAmount' => 'required',
            'newtotalpayable'  => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $sale = Sale::where('order_no', $request->order_no)->first();
    
        $data = [
            'roundoff'          => $request->roundOffAmount,
            'total_payable'     => $request->newtotalpayable,
            'pending_amount'    => $request->newtotalpayable - $sale->pay_amount ,
        ];
    
        // Remove null / empty values
        $data = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });
    
        Sale::where('order_no', $request->order_no)->update($data);
    
        return response()->json(['success' => 'Round off value  updated successfully.']);
    }
    
    
    public function addNewPaymentOrder(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'payamount' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $sale = Sale::where('order_no', $request->order_no)->first();
    
        $data = [
            'pay_amount'     => $sale->pay_amount + $request->payamount,
            'pending_amount'    => $sale->pending_amount - $request->payamount ,
        ];
    

    
        Sale::where('order_no', $request->order_no)->update($data);
        
        SalePayment::create([
                'sale_id'     => $sale->sale_id,
                'order_no'    => $request->order_no,
                'total_price'    => $sale->pending_amount ?? 0,
                'pay_amount'  => $request->payamount ?? 0,
                'bal_amount'  => $sale->pending_amount - $request->payamount ?? 0,
                'pay_details' => $request->paydetails,
                'pay_method'  => $request->paymethod,
                'pay_date'    => date('Y-m-d'),
                'added_by'    => $user->id,
                'store_id'    => $sale->store_id,
                'pay_type'    => 1,
            ]);
    
        return response()->json(['success' => 'Payment add  successfully.']);
    }
    
    
    public function addNewItemOrder(Request $request)
    {
        $user = auth()->user();
    
       
    
        DB::beginTransaction();
    
        try {
            
            $tracking_status = null;
            $salesv = Sale::where('order_no', $request->order_no)->first();
            // -----------------------------
            // HANDLE PRODUCT (Glass)
            // -----------------------------
            if ($request->producttype === 'Glass')
            {
                if($request->lens_package == '')
                {
            
                        $value = $request->input('modal_rightleft', []); // always array
                        $items = array_map('trim', $value);
                        $count = count($items);
                        
    
                        /* ---------------------------------------------------------
                            BUILD GLASS DESCRIPTION AND UPDATE INVENTORY
                        --------------------------------------------------------- */
                        
                        $checknoglass = SaleProduct::where('order_no', $request->order_no)->where('product_type', 'Glass')
                        ->orderBy('id', 'desc')
                        ->first();
                        
                        if(empty($checknoglass))
                        {
                            $no_of_glass =1;
                        }
                        else
                        {
                            $no_of_glass = $checknoglass->no_of_glass+1;
                        }
                        foreach ($items as $item) 
                        {
                            // Create Sale Product
                            $saleProduct = SaleProduct::create([
                                'sale_id'           => $salesv->sale_id,
                                'order_no'          => $request->order_no,
                                'barcode_use'       => $request->barcode ?? null,
                                'product_type'      => $request->producttype ?? null,
                                'product_code'      => $request->modal_product_code ?? null,
                                'product_id'        => $request->modal_product_id ?? null,
                                'product_company'   => $request->modal_company ?? null,
                                'product_quality'   => $request->modal_quality ?? null,
                                'product_material'  => $request->modal_product_material ?? null,
                                'product_color'     => $request->modal_product_color ?? null,
                                'product_design'    => $request->modal_product_design ?? null,
                                'product_coating'   => $request->modal_product_coating ?? null,
                                'product_index'     => $request->modal_product_index ?? null,
                                'product_deatils'   => $request->modal_product_details ?? null,
                                'package_id'        => $request->package_id ?? null,
                                'coating_apply'     => $request->coating_apply ?? null,
                    
                                // RIGHT EYE VALUES
                                'GL_EYE_RS_D'       => $request->GL_EYE_RS_D ?? null,
                                'GL_EYE_RC_D'       => $request->GL_EYE_RC_D ?? null,
                                'GL_EYE_RA_D'       => $request->GL_EYE_RA_D ?? null,
                                'GL_EYE_RP_D'       => $request->GL_EYE_RP_D ?? null,
                                'GL_EYE_RV_D'       => $request->GL_EYE_RV_D ?? null,
                    
                                'GL_EYE_RS_N'       => $request->GL_EYE_RS_N ?? null,
                                'GL_EYE_RC_N'       => $request->GL_EYE_RC_N ?? null,
                                'GL_EYE_RA_N'       => $request->GL_EYE_RA_N ?? null,
                                'GL_EYE_RP_N'       => $request->GL_EYE_RP_N ?? null,
                                'GL_EYE_RV_N'       => $request->GL_EYE_RV_N ?? null,
                    
                                'GL_EYE_RADD'       => $request->GL_EYE_RADD ?? null,
                                'GL_EYE_totalPD'    => $request->GL_EYE_totalPD ?? null,
                    
                                // LEFT EYE VALUES
                                'GL_EYE_LS_D'       => $request->GL_EYE_LS_D ?? null,
                                'GL_EYE_LC_D'       => $request->GL_EYE_LC_D ?? null,
                                'GL_EYE_LA_D'       => $request->GL_EYE_LA_D ?? null,
                                'GL_EYE_LP_D'       => $request->GL_EYE_LP_D ?? null,
                                'GL_EYE_LV_D'       => $request->GL_EYE_LV_D ?? null,
                    
                                'GL_EYE_LS_N'       => $request->GL_EYE_LS_N ?? null,
                                'GL_EYE_LC_N'       => $request->GL_EYE_LC_N ?? null,
                                'GL_EYE_LA_N'       => $request->GL_EYE_LA_N ?? null,
                                'GL_EYE_LP_N'       => $request->GL_EYE_LP_N ?? null,
                                'GL_EYE_LV_N'       => $request->GL_EYE_LV_N ?? null,
                    
                                'GL_EYE_LADD'       => $request->GL_EYE_LADD ?? null,
                    
                                // FRAME DETAILS
                                'frame_asize'       => $request->modal_asize ?? null,
                                'frame_bsize'       => $request->modal_bsize ?? null,
                                'frametypeglass'    => $request->frametypeglass ?? null,
                                'frame_dbl'         => $request->modal_dbl ?? null,
                                'frame_fh'          => $request->modal_FH ?? null,
                                'frame_ed'          => $request->modal_ED ?? null,
                                
                                'count_eye_test'          => $request->count_eye_test ?? null,
                                'patient_name'          => $request->modal_patient_name ?? null,
                                'doc_name'          => $request->modal_doctor_name ?? null,
                                'wearing_type' => !empty($request->glassWearingType) ? implode(',', $request->glassWearingType) : null,
                                'wearing_types_inhouse' => !empty($request->lenstype) ? $request->lenstype : null,
                                'prescription_notes'=> $request->modal_prescription_notes ?? null,
                    
                                'qty'               => $count,
                                'hsn_code'          => $request->modal_hsncode ?? null,
                                'gst'               => $request->modal_gst ?? 0,
                                'gst_amount'        => $request->modal_gst_amount/$count ?? 0,
                                'discount_amt'      => $request->modal_discount_amount/$count ?? 0,
                                'product_discount'  => $request->modal_discount/$count ?? 0,
                                'purchase_price'     => $request->modal_purchase_price/$count ?? 0,
                                'base_price'        => $request->modal_base_price/$count ?? 0,
                                'retail_price'      => $request->modal_retail_price/$count ?? 0,
                                'sale_price'        => $request->modal_total_sale/$count ?? 0,
                                'store_id'          => $salesv->store_id,
                                'right_left'        => $item,
                                'no_of_glass'        => $no_of_glass,
                            ]);
                    
                            // FIX: $product_details must be defined before using in UpdateBarcodes
                            $product_details = $request->modal_product_details ?? null;
                
                            if ($item === 'Right')
                            {
                                $product_details = $this->buildGlassDescription([
                                    $request->modal_product_details ?? '',
                                    'SPH:' . ($request->GL_EYE_RS_D ?? ''),
                                    'CYL:' . ($request->GL_EYE_RC_D ?? ''),
                                    'ADD:' . ($request->GL_EYE_RADD ?? ''),
                                    'Axis:' . ($request->GL_EYE_RA_D ?? ''),
                                ]);
                
                                // BUG FIX: last argument must be $item, not full array
                                $this->UpdateGlassInventory(
                                    $salesv->store_id,
                                    $product_details,
                                    $request->producttype,
                                    $request->modal_product_code,
                                    $request->modal_product_id,
                                    $count,
                                    $salesv->sale_date,
                                    $saleProduct->id,
                                    'Right'
                                );
                                
                                
                
                            } elseif ($item === 'Left') {
                
                                $product_details = $this->buildGlassDescription([
                                    $request->modal_product_details ?? '',
                                    'SPH:' . ($request->GL_EYE_LS_D ?? ''),
                                    'CYL:' . ($request->GL_EYE_LC_D ?? ''),
                                    'ADD:' . ($request->GL_EYE_LADD ?? ''),
                                    'Axis:' . ($request->GL_EYE_LA_D ?? ''),
                                ]);
                
                                $this->UpdateGlassInventory(
                                    $salesv->store_id,
                                    $product_details,
                                    $request->producttype,
                                    $request->modal_product_code,
                                    $request->modal_product_id,
                                    $count,
                                    $salesv->sale_date,
                                    $saleProduct->id,
                                    'Left'
                                );
                            }
                        }
                        
                        if (!empty($request->barcode)) 
                        {
                            $this->UpdateBarcodes(
                                $salesv->store_id,
                                $request->barcode,
                                $request->modal_product_details,
                                $request->producttype,
                                $request->modal_product_code,
                                $salesv->order_no
                            );
                        }
                } 
                else
                {
                        $value = $request->input('modal_rightleft', []); // always array
                        $items = array_map('trim', $value);
                        $count = count($items);
                        
    
                        /* ---------------------------------------------------------
                            BUILD GLASS DESCRIPTION AND UPDATE INVENTORY
                        --------------------------------------------------------- */
                        
                        $checknoglass = SaleProduct::where('order_no', $request->order_no)->where('product_type', 'Glass')
                        ->orderBy('id', 'desc')
                        ->first();
                        
                        if(empty($checknoglass))
                        {
                            $no_of_glass =1;
                        }
                        else
                        {
                            $no_of_glass = $checknoglass->no_of_glass+1;
                        }
                        foreach ($items as $item) 
                        {
                            // Create Sale Product
                            $saleProduct = SaleProduct::create([
                                'sale_id'           => $salesv->sale_id,
                                'order_no'          => $request->order_no,
                                'barcode_use'       => $request->barcode ?? null,
                                'product_type'      => $request->producttype ?? null,
                                'product_code'      => $request->modal_product_code ?? null,
                                'product_id'        => $request->modal_product_id ?? null,
                                'product_company'   => $request->modal_company ?? null,
                                'product_quality'   => $request->modal_quality ?? null,
                                'product_material'  => $request->modal_product_material ?? null,
                                'product_color'     => $request->modal_product_color ?? null,
                                'product_design'    => $request->modal_product_design ?? null,
                                'product_coating'   => $request->modal_product_coating ?? null,
                                'product_index'     => $request->modal_product_index ?? null,
                                'product_deatils'   => $request->modal_product_details ?? null,
                                'package_id'        => $request->lens_package ?? null,
                                'coating_apply'     => $request->is_Coating ?? null,
                    
                                // RIGHT EYE VALUES
                                'GL_EYE_RS_D'       => $request->GL_EYE_RS_D ?? null,
                                'GL_EYE_RC_D'       => $request->GL_EYE_RC_D ?? null,
                                'GL_EYE_RA_D'       => $request->GL_EYE_RA_D ?? null,
                                'GL_EYE_RP_D'       => $request->GL_EYE_RP_D ?? null,
                                'GL_EYE_RV_D'       => $request->GL_EYE_RV_D ?? null,
                    
                                'GL_EYE_RS_N'       => $request->GL_EYE_RS_N ?? null,
                                'GL_EYE_RC_N'       => $request->GL_EYE_RC_N ?? null,
                                'GL_EYE_RA_N'       => $request->GL_EYE_RA_N ?? null,
                                'GL_EYE_RP_N'       => $request->GL_EYE_RP_N ?? null,
                                'GL_EYE_RV_N'       => $request->GL_EYE_RV_N ?? null,
                    
                                'GL_EYE_RADD'       => $request->GL_EYE_RADD ?? null,
                                'GL_EYE_totalPD'    => $request->GL_EYE_totalPD ?? null,
                    
                                // LEFT EYE VALUES
                                'GL_EYE_LS_D'       => $request->GL_EYE_LS_D ?? null,
                                'GL_EYE_LC_D'       => $request->GL_EYE_LC_D ?? null,
                                'GL_EYE_LA_D'       => $request->GL_EYE_LA_D ?? null,
                                'GL_EYE_LP_D'       => $request->GL_EYE_LP_D ?? null,
                                'GL_EYE_LV_D'       => $request->GL_EYE_LV_D ?? null,
                    
                                'GL_EYE_LS_N'       => $request->GL_EYE_LS_N ?? null,
                                'GL_EYE_LC_N'       => $request->GL_EYE_LC_N ?? null,
                                'GL_EYE_LA_N'       => $request->GL_EYE_LA_N ?? null,
                                'GL_EYE_LP_N'       => $request->GL_EYE_LP_N ?? null,
                                'GL_EYE_LV_N'       => $request->GL_EYE_LV_N ?? null,
                    
                                'GL_EYE_LADD'       => $request->GL_EYE_LADD ?? null,
                    
                                // FRAME DETAILS
                                'frame_asize'       => $request->modal_asize ?? null,
                                'frame_bsize'       => $request->modal_bsize ?? null,
                                'frametypeglass'    => $request->frametypeglass ?? null,
                                'frame_dbl'         => $request->modal_dbl ?? null,
                                'frame_fh'          => $request->modal_FH ?? null,
                                'frame_ed'          => $request->modal_ED ?? null,
                                
                                'count_eye_test'          => $request->count_eye_test ?? null,
                                'patient_name'          => $request->modal_patient_name ?? null,
                                'doc_name'          => $request->modal_doctor_name ?? null,
                                'wearing_type' => !empty($request->glassWearingType) ? implode(',', $request->glassWearingType) : null,
                                'wearing_types_inhouse' => !empty($request->lenstype) ? $request->lenstype : null,
                                'prescription_notes'=> $request->modal_prescription_notes ?? null,
                    
                                'qty'               => $count,
                                'hsn_code'          => $request->modal_hsncode ?? null,
                                'gst'               => $request->modal_gst ?? 0,
                                'gst_amount'        => $request->modal_gst_amount/$count ?? 0,
                                'discount_amt'      => $request->modal_discount_amount/$count ?? 0,
                                'product_discount'  => $request->modal_discount/$count ?? 0,
                                'purchase_price'     => $request->modal_purchase_price/$count ?? 0,
                                'base_price'        => $request->modal_base_price/$count ?? 0,
                                'retail_price'      => $request->modal_retail_price/$count ?? 0,
                                'sale_price'        => $request->modal_total_sale/$count ?? 0,
                                'store_id'          => $salesv->store_id,
                                'right_left'        => $item,
                                'no_of_glass'        => $no_of_glass,
                            ]);
                    
                            // FIX: $product_details must be defined before using in UpdateBarcodes
                            $product_details = $request->modal_product_details ?? null;
                
                            if ($item === 'Right')
                            {
                                $product_details = $this->buildGlassDescription([
                                    $request->modal_product_details ?? '',
                                    'SPH:' . ($request->GL_EYE_RS_D ?? ''),
                                    'CYL:' . ($request->GL_EYE_RC_D ?? ''),
                                    'ADD:' . ($request->GL_EYE_RADD ?? ''),
                                    'Axis:' . ($request->GL_EYE_RA_D ?? ''),
                                ]);
                                
                                DB::table('tbl_sales_product')
                                    ->where('id', $saleProduct->id)
                                    ->update([
                                        'right_purchase' => 1,
                                        'right_glass' => $product_details,
                                        'updated_at'         => now()
                                    ]);
                
                            } elseif ($item === 'Left') {
                
                                $product_details = $this->buildGlassDescription([
                                    $request->modal_product_details ?? '',
                                    'SPH:' . ($request->GL_EYE_LS_D ?? ''),
                                    'CYL:' . ($request->GL_EYE_LC_D ?? ''),
                                    'ADD:' . ($request->GL_EYE_LADD ?? ''),
                                    'Axis:' . ($request->GL_EYE_LA_D ?? ''),
                                ]);
                                
                                DB::table('tbl_sales_product')
                                    ->where('id', $saleProduct->id)
                                    ->update([
                                        'left_purchase' => 1,
                                        'left_glass' => $product_details,
                                        'updated_at'         => now()
                                    ]);
                
                                
                            }
                        }

                    
                }
                        
                $tracking_status = 'ORDER PLACED AND WAIT FOR CONFIRMATION';
            }
            // -----------------------------
            // HANDLE PRODUCT (Frame/Goggles)
            // -----------------------------    
            elseif (in_array($request->producttype, ['Frame', 'Goggles'])) {
                $saleProduct = SaleProduct::create([
                    'sale_id'        => $salesv->sale_id,
                    'order_no'       => $salesv->order_no,
                    'barcode_use'    => $request->barcode ?? null,
                    'product_type'   => $request->producttype,
                    'product_code'   => $request->modal_product_code ?? null,
                    'product_id'     => $request->modal_product_id ?? null,
                    'product_company'=> $request->modal_company ?? null,
                    'product_quality'=> $request->modal_quality ?? null,
                    'product_deatils'=> $request->modal_product_details ?? null,
                    'qty'            => 1,
                    'hsn_code'       => $request->modal_hsncode ?? null,
                    'gst'            => $request->modal_gst ?? 0,
                    'gst_amount'     => $request->modal_gst_amount ?? 0,
                    'discount_amt'   => $request->modal_discount_amount ?? 0,
                    'product_discount'=> $request->modal_discount ?? 0,
                    'purchase_price' => $request->modal_purchase_price ?? 0,
                    'base_price'     => $request->modal_base_price ?? 0,
                    'retail_price'   => $request->modal_retail_price ?? 0,
                    'sale_price'     => $request->modal_total_sale ?? 0,
                    'store_id'       => $salesv->store_id,
                ]);
    
                if (!empty($request->barcode)) {
                    $this->UpdateBarcodes(
                        $salesv->store_id,
                        $request->barcode,
                        $request->modal_product_details,
                        $request->producttype,
                        $request->modal_product_code,
                        $request->order_no
                    );
                }
    
                $this->UpdateFrameGogglesSolutuionInventory(
                    $salesv->store_id,
                    $request->modal_product_details,
                    $request->producttype,
                    $request->modal_product_code,
                    $request->modal_product_id,
                    1,
                    $salesv->sale_date,
                    $saleProduct->id
                );
    
                $tracking_status = 'ORDER PLACED AND WAIT FOR CONFIRMATION';
            }
            
            // -----------------------------
            // HANDLE PRODUCT (Lens)
            // -----------------------------
            
            elseif ($request->producttype === 'Lens') 
            {
                     

                    $saleProduct = SaleProduct::create([
                        'sale_id'        => $salesv->sale_id,
                        'order_no'       => $salesv->order_no,
                        'barcode_use'    => $request->barcode ?? null,
                        'product_type'   => $request->producttype,
                        'product_code'   => $request->modal_product_code ?? null,
                        'product_id'     => $request->modal_product_id ?? null,
                        'product_company'=> $request->modal_company ?? null,
                        'product_quality'=> $request->modal_quality ?? null,
                        'product_deatils'=> $request->modal_product_details ?? null,
                        'product_material'=> $request->product_material ?? null,
                        'product_color'=> $request->product_color ?? null,
                        'product_number'=> $request->product_number ?? null,
                        'product_ct'=> $request->product_ct ?? null,
                        'product_typesss'=> $request->product_typesss ?? null,
                        'product_validity'=> $request->product_validity ?? null,
                        'count_eye_test'=> $request->count_eye_test ?? null,
                        'prescription_notes'=> $request->prescription_notes ?? null,
                        'lensRightNoOfBoxes'=> $request->lensRightNoOfBoxes ?? null,
                        'lensRightTotalPieces'=> $request->lensRightTotalPieces ?? null,
                        'lensLeftNoOfBoxes'=> $request->lensLeftNoOfBoxes ?? null,
                        'lensLeftTotalPieces'=> $request->lensLeftTotalPieces ?? null,
                        'GL_EYE_RS_D'       => $request->GL_EYE_RS_D ?? null,
                        'GL_EYE_RC_D'       => $request->GL_EYE_RC_D ?? null,
                        'GL_EYE_RA_D'       => $request->GL_EYE_RA_D ?? null,
                        'GL_EYE_RP_D'       => $request->GL_EYE_RP_D ?? null,
                        'GL_EYE_RV_D'       => $request->GL_EYE_RV_D ?? null,
                        'GL_EYE_LS_D'       => $request->GL_EYE_LS_D ?? null,
                        'GL_EYE_LC_D'       => $request->GL_EYE_LC_D ?? null,
                        'GL_EYE_LA_D'       => $request->GL_EYE_LA_D ?? null,
                        'GL_EYE_LP_D'       => $request->GL_EYE_LP_D ?? null,
                        'GL_EYE_LV_D'       => $request->GL_EYE_LV_D ?? null,
                        'qty'               => $request->product_qty ?? 1,
                        'hsn_code'          => $request->modal_hsncode ?? null,
                        'gst'               => $request->modal_gst ?? 0,
                        'gst_amount'        => $request->modal_gst_amount ?? 0,
                        'discount_amt'      => $request->modal_discount_amount ?? 0,
                        'product_discount'  => $request->modal_discount ?? 0,
                        'purchase_price'    => $request->modal_purchase_price ?? 0,
                        'base_price'        => $request->modal_base_price ?? 0,
                        'retail_price'      => $request->modal_retail_price ?? 0,
                        'sale_price'        => $request->modal_total_sale ?? 0,
                        'store_id'          => $salesv->store_id,
                        'product_tracking'       => 'ORDER PLACED AND READY TO SHIP',
                    ]);
                    
                    $product_details = $request->modal_product_details ?? null;
                    if(!empty($request->lens_bids))
                    {
                        $val = $request->lens_bids; 
                        $bids = explode(',', $val); 
                        foreach ($bids as $bid) 
                        {
                            $product = DB::table('tbl_barcode')
                            ->where('t_status', '0')
                            ->where('id', $bid)
                            ->first();
                        
                            if (empty($product)) 
                            {
                                $product = DB::table('tbl_barcode')
                                    ->where('id', $bid)
                                    ->where('transfer_store_id', $salesv->store_id)
                                    ->first();
                                    
                                 DB::table('tbl_barcode')->where([['id', '=', $bid],
                                ])->update([
                                    'transfer_outward_status' => 0,
                                    'refrence_no'   => $salesv->order_no,
                                    'updated_at'    => now()
                                ]); 
                                
                                 DB::table('tbl_barcode')->where([['lens_box', '=', $product->barcode_no],
                                ])->update([
                                    'transfer_outward_status' => 0,
                                    'refrence_no'   => $salesv->order_no,
                                    'updated_at'    => now()
                                ]); 
                            }
                            else
                            {
                                 DB::table('tbl_barcode')->where([['id', '=', $bid],
                                ])->update([
                                    'transfer_outward_status' => 0,
                                    'refrence_no'   => $salesv->order_no,
                                    'updated_at'    => now()
                                ]);
                                
                                 DB::table('tbl_barcode')->where([['lens_box', '=', $product->barcode_no],
                                ])->update([
                                    'transfer_outward_status' => 0,
                                    'refrence_no'   => $salesv->order_no,
                                    'updated_at'    => now()
                                ]); 
                            }
                            
                            
                            $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                                        'barcode_no' => $product->barcode_no,
                                        'store_id' => $salesv->store_id,
                                        'reference_type' => 'Sale',
                                        'action_perform' => 'Order',
                                        'added_by' => $user->id,
                                ]);
                               
                            }
                            
                            
                        if($request->lensRightNoOfBoxes > 0)
                        {
                            
            
                            $this->UpdateLensInventory(
                                $salesv->store_id,
                                $product_details,
                                $request->producttype,
                                $request->modal_product_code ?? null,
                                $request->modal_product_id ?? null,
                                $request->lensRightNoOfBoxes,
                                $request->lensRightTotalPieces,
                                $salesv->sale_date,
                                $saleProduct->id
                            );
                        }
                        
                         if($request->lensLeftNoOfBoxes > 0)
                        {
                            $this->UpdateLensInventory(
                                $salesv->store_id,
                                $product_details,
                                $request->producttype,
                                $request->modal_product_code ?? null,
                                $request->modal_product_id ?? null,
                                $request->lensLeftNoOfBoxes,
                                $request->lensLeftTotalPieces,
                                $salesv->sale_date,
                                $saleProduct->id
                            );
                        }    
                    
                    }
                    else
                    {
                        if($request->lensRightNoOfBoxes > 0)
                        {
                            $product_details = $this->buildGlassDescription([
                                $request->modal_product_details ?? '',
                                'SPH:' . ($request->GL_EYE_RS_D ?? ''),
                                'CYL:' . ($request->GL_EYE_RC_D ?? ''),
                                'ADD:' . (($request->GL_EYE_RADD ?? '') ?? ''),
                                'Axis:' . ($request->GL_EYE_RA_D ?? ''),
                            ]);
            
                            // BUG FIX: last argument must be $item, not full array
                            $this->UpdateLensInventory(
                                $salesv->store_id,
                                $product_details,
                                $request->producttype,
                                $request->modal_product_code,
                                $request->modal_product_id,
                                $request->lensRightNoOfBoxes,
                                $request->lensRightTotalPieces,
                                $salesv->sale_date,
                                $saleProduct->id
                            );
                        }
                        
                        if($request->lensLeftNoOfBoxes > 0)
                        {
                            $product_details = $this->buildGlassDescription([
                                $request->modal_product_details ?? '',
                                'SPH:' . ($request->GL_EYE_LS_D ?? ''),
                                'CYL:' . ($request->GL_EYE_LC_D ?? ''),
                                'ADD:' . ($request->GL_EYE_LADD ?? ''),
                                'Axis:' . ($request->GL_EYE_LA_D ?? ''),
                            ]);
            
                            // BUG FIX: last argument must be $item, not full array
                            $this->UpdateLensInventory(
                                $salesv->store_id,
                                $product_details,
                                $request->producttype,
                                $request->modal_product_code,
                                $request->modal_product_id,
                                $request->lensLeftNoOfBoxes,
                                $request->lensLeftTotalPieces,
                                $salesv->sale_date,
                                $saleProduct->id
                            );
                        }
                    }
                    
                    
                    
                    
                    $tracking_status = 'ORDER PLACED AND READY TO SHIP';

                }
            
            
            
            // -----------------------------
            // HANDLE PRODUCT (Solution)
            // -----------------------------
            elseif ($request->producttype === 'Solution') 
            {
                    $saleProduct = SaleProduct::create([
                        'sale_id'        => $salesv->sale_id,
                        'order_no'       => $request->order_no,
                        'barcode_use'    => $request->barcode ?? null,
                        'product_type'   => $request->producttype,
                        'product_code'   => $request->modal_product_code ?? null,
                        'product_id'     => $request->modal_product_id ?? null,
                        'product_company'=> $request->modal_company ?? null,
                        'product_quality'=> $request->modal_quality ?? null,
                        'product_deatils'=> $request->modal_product_details ?? null,
                        'product_color'  => $request->modal_product_color ?? null,
                        'product_typesss'=> $request->modal_packing_type ?? null,
                        'product_variant'=> $request->modal_product_variant ?? null,
                        'qty'            =>  1,
                        'hsn_code'       => $request->modal_hsncode ?? null,
                        'gst'            => $request->modal_gst ?? 0,
                        'gst_amount'     => $request->modal_gst_amount ?? 0,
                        'discount_amt'   => $request->modal_discount_amount ?? 0,
                        'product_discount'=> $request->modal_discount ?? 0,
                        'purchase_price' => $request->modal_purchase_price ?? 0,
                        'base_price'     => $request->modal_base_price ?? 0,
                        'retail_price'   => $request->modal_retail_price ?? 0,
                        'sale_price'     => $request->modal_total_sale ?? 0,
                        'store_id'       => $salesv->store_id,
                    ]);
            
                    if (!empty($request->barcode)) {
                        $this->UpdateBarcodes(
                            $salesv->store_id,
                            $request->barcode,
                            $request->modal_product_details,
                            $request->producttype,
                            $request->modal_product_code,
                            $request->order_no
                        );
                    }
            
                    $this->UpdateFrameGogglesSolutuionInventory(
                        $salesv->store_id,
                        $request->modal_product_details,
                        $request->producttype,
                        $request->modal_product_code,
                        $request->modal_product_id,
                        1,
                        $salesv->sale_date,
                        $saleProduct->id
                    );
                    
                    $tracking_status = 'ORDER PLACED AND WAIT FOR CONFIRMATION';
                }
                
            /* ---------------------------------------------------------
                HANDLE REPAIR PRODUCT
            --------------------------------------------------------- */
                
            elseif ($request->producttype === 'Repair') 
            {
                $saleProduct = SaleProduct::create([
                    'sale_id'        => $salesv->sale_id,
                    'product_type'       => 'Repair',
                    'product_code'       => 'SYS1407',
                    'order_no'       => $request->order_no,
                    'product_deatils'=> $request->modal_product_details ?? null,
                    'hsn_code'       => $request->modal_hsncode ?? null,
                    'gst'            => $request->modal_gst ?? 0,
                    'gst_amount'     => $request->modal_gst_amount ?? 0,
                    'discount_amt'   => $request->modal_discount_amount ?? 0,
                    'product_discount'=> $request->modal_discount ?? 0,
                    'purchase_price' => $request->modal_purchase_price ?? 0,
                    'base_price'     => $request->modal_base_price ?? 0,
                    'retail_price'   => $request->modal_retail_price ?? 0,
                    'sale_price'     => $request->modal_total_sale ?? 0,
                    'store_id'       => $salesv->store_id,
                ]);
                
                $tracking_status = 'RECEIVED BY BRANCH';
            } 
            
            
            /* ---------------------------------------------------------
                HANDLE OTHER PRODUCT
            --------------------------------------------------------- */
                
            elseif ($request->producttype === 'Other') 
            {
                $saleProduct = SaleProduct::create([
                    'sale_id'        => $salesv->sale_id,
                    'order_no'       => $request->order_no,
                    'barcode_use'    => $request->barcode ?? null,
                    'product_type'   => $request->producttype,
                    'product_code'   => $request->modal_product_code ?? null,
                    'product_id'     => $request->modal_product_id ?? null,
                    'product_company'=> $request->modal_company ?? null,
                    'product_quality'=> $request->modal_quality ?? null,
                    'product_deatils'=> $request->modal_product_details ?? null,
                    'product_color'=> $request->modal_product_color ?? null,
                    'product_typesss'=> $request->modal_product_typesss ?? null,
                    'product_shape'=> $request->modal_product_variant ?? null,
                    'product_shape'=> $request->modal_product_shape ?? null,
                    'product_size'            => $request->modal_product_size ?? 1,
                    'hsn_code'       => $request->modal_hsncode ?? null,
                    'gst'            => $request->modal_gst ?? 0,
                    'gst_amount'     => $request->modal_gst_amount ?? 0,
                    'discount_amt'   => $request->modal_discount_amount ?? 0,
                    'product_discount'=> $request->modal_discount ?? 0,
                    'purchase_price' => $request->modal_purchase_price ?? 0,
                    'base_price'     => $request->modal_base_price ?? 0,
                    'retail_price'   => $request->modal_retail_price ?? 0,
                    'sale_price'     => $request->modal_total_sale ?? 0,
                    'store_id'       => $salesv->store_id,
                ]);
        
                if (!empty($request->barcode)) {
                    $this->UpdateBarcodes(
                        $salesv->store_id,
                        $request->barcode,
                        $request->modal_product_details,
                        $request->producttype,
                        $request->modal_product_code,
                        $request->order_no
                    );
                }
        
                $this->UpdateOtherInventory(
                    $salesv->store_id,
                    $request->modal_product_details,
                    $request->producttype,
                    $request->modal_product_code,
                    $request->modal_product_id,
                    1,
                    $salesv->sale_date,
                    $saleProduct->id
                );
                
                $tracking_status = 'ORDER PLACED AND WAIT FOR CONFIRMATION';
            }
    
            // -----------------------------
            // ORDER TRACKING
            // -----------------------------
            OrderTracking::create([
                'order_no'       => $request->order_no,
                'product_type'   => $request->producttype,
                'product_code'   => $request->modal_product_code ?? null,
                'description'    => $request->modal_product_details ?? null,
                'tracking_status'=> $tracking_status,
                'store_id'       => $request->store_id ?? $salesv->store_id,
            ]);
            
            
            // -----------------------------
            // UPDATE SALE TOTALS
            // -----------------------------
            DB::table('tbl_sales')
                ->where('sale_id', $salesv->sale_id)
                ->update([
                    'total_item_price'  => $salesv->total_item_price + $request->modal_retail_price,
                    'total_basic_amount'=> $salesv->total_basic_amount + $request->modal_base_price,
                    'total_gst_amount'  => $salesv->total_gst_amount + $request->modal_gst_amount,
                    'total_discount'    => $salesv->total_discount + $request->modal_discount_amount,
                    'total_payable'     => $salesv->total_payable + $request->modal_total_sale,
                    'pending_amount'    => $salesv->pending_amount + $request->modal_total_sale,
                    'updated_at'        => now(),
                ]);
    
            // -----------------------------
            // LOYALTY POINTS
            // -----------------------------
            if ($salesv->earnedPoints > 0) {
                $tblcustomer = DB::table('tbl_customer')
                    ->where('contact_no', $salesv->contact_no)
                    ->first();
    
                if ($tblcustomer) {
                    $description = 'Sales Invoice ' . $salesv->order_no;
    
                    // Remove old earned points
                    DB::table('tbl_loyaltyrogram_histroy')
                        ->where('description', $description)
                        ->delete();
    
                    $oldPoints = (int)$salesv->earnedPoints;
    
                    // Deduct old points
                    DB::table('tbl_customer')
                        ->where('customer_id', $tblcustomer->customer_id)
                        ->update([
                            'Loyalty_Points'     => DB::raw("GREATEST(Loyalty_Points - $oldPoints, 0)"),
                            'Loyalty_Points_Bal' => DB::raw("GREATEST(Loyalty_Points_Bal - $oldPoints, 0)"),
                            'updated_at'         => now(),
                        ]);
    
                    // Generate new points
                    $tblloyalty = DB::table('tbl_loyalty')->where('id', 2)->first();
                    if ($tblloyalty && (int)$tblloyalty->auto_status === 0) {
                        $salesamount = ((int)$tblloyalty->sales_value === 2)
                            ? (float)$salesv->total_payable + $request->modal_total_sale
                            : (float)$salesv->total_item_price + $request->modal_retail_price;
    
                        if ((int)$tblloyalty->auto_set_loyalty_point === 0) {
                            $earnedPoints = ($tblloyalty->no_of_points > 0 && $tblloyalty->x_number_sale_value > 0)
                                ? floor(($salesamount / $tblloyalty->x_number_sale_value) * $tblloyalty->no_of_points)
                                : 0;
                        } else {
                            $earnedPoints = floor(($salesamount * $tblloyalty->fixed_per) / 100);
                        }
    
                        $earnedPoints = (int) max($earnedPoints, 0);
                        
                        
    
                        DB::table('tbl_sales')
                            ->where('sale_id', $salesv->sale_id)
                            ->update([
                                'earnedPoints' => $earnedPoints,
                                'updated_at'   => now(),
                            ]);
                    }
                }
            }
    
            // -----------------------------
            // COUPONS
            // -----------------------------
            if ($salesv->earncoupon !== null) {
                DB::table('tbl_coupon')->where('coupon_id', $salesv->earncoupon)->delete();
    
                $tblcoupon = DB::table('tbl_coupon_auto')->where('id', 1)->first();
                if ($tblcoupon && (int)$tblcoupon->auto_status === 0) {
                    $salesamount = ((int)$tblcoupon->sales_value === 2)
                        ? (float)$salesv->total_payable + $request->modal_total_sale
                        : (float)$salesv->total_item_price + $request->modal_retail_price;
    
                    $range = DB::table('tbl_coupon_auto')
                        ->where('from_range', '<=', $salesamount)
                        ->where('to_range', '>=', $salesamount)
                        ->first();
                        
                        
                        dd($range);
    
                    if ($range) {
                        $couponCode = $this->generateUniqueRandomCoupon(6, 'tbl_coupon', 'coupon_code');
    
                        $couponId = DB::table('tbl_coupon')->insertGetId([
                            'coupon_type'          => $tblcoupon->coupon_value_type,
                            'coupon_code'          => $couponCode,
                            'coupon_value'         => $range->coupon_value,
                            'min_sale_vale'        => $range->sales_value_amount,
                            'valid_from'           => now()->toDateString(),
                            'valid_to'             => now()->addDays($range->valid_dyas)->toDateString(),
                            'coupon_usages'        => 0,
                            'coupon_generate_type' => 0,
                            'coupon_status'        => '0',
                            'added_by'             => $user->id,
                            'store_id'             => $salesv->store_id,
                            'created_at'           => now(),
                            'updated_at'           => now(),
                        ]);
    
                        DB::table('tbl_sales')
                            ->where('sale_id', $salesv->sale_id)
                            ->update(['earncoupon' => $couponId, 'updated_at' => now()]);
                    }
                }
            }
    
            
    
            DB::commit();
    
            return response()->json([
                'success' => 'Item added successfully'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Add New Item Order Error: '.$e->getMessage());
    
            return response()->json([
                'error' => ['server' => 'Something went wrong, please try again.']
            ], 500);
        }
    }

    public function getsalespayment(Request $request)
    {
        $oid = $request->oid;

        $salepayment = DB::table('tbl_sale_payment')->where('order_no', $oid)->where('pay_type','!=', 2)
            ->orderBy('payment_id', 'ASC')
            ->get();

        return response()->json([
            'data' => $salepayment->map(function ($p) 
            {
                $sale_person = User::find($p->added_by);
                return [
                    'payment_id' => $p->payment_id,
                    'pay_amount' => $p->pay_amount,
                    'pay_details' => $p->pay_details,
                    'pay_method' => $p->pay_method,
                    'created_by' => $sale_person->name,
                    'created_at' => date('d M, Y h:i A', strtotime($p->created_at)),
                    'pay_date' => date('d M, Y ', strtotime($p->pay_date)),
      
                ];
            })
        ]);
    }
    
    public function getreturnpayment(Request $request)
    {
        $oid = $request->oid;

        $salepayment = DB::table('tbl_sale_payment')->where('order_no', $oid)->where('pay_type', 2)
            ->orderBy('payment_id', 'ASC')
            ->get();

        return response()->json([
            'data' => $salepayment->map(function ($p) 
            {
                $sale_person = User::find($p->added_by);
                return [
                    'payment_id' => $p->payment_id,
                    'pay_amount' => $p->pay_amount,
                    'pay_details' => $p->pay_details,
                    'pay_method' => $p->pay_method,
                    'created_by' => $sale_person->name,
                    'created_at' => date('d M, Y h:i A', strtotime($p->created_at)),
                    'pay_date' => date('d M, Y ', strtotime($p->pay_date)),
      
                ];
            })
        ]);
    }
    
    
    public function getsalesproduct(Request $request)
    {
        $oid = $request->oid;

        $saleproduct = DB::table('tbl_sales_product')->where('order_no', $oid)
            ->orderBy('id', 'ASC')
            ->get();

        return response()->json([
            'data' => $saleproduct->map(function ($p) 
            {
                return [
                    'pid' => $p->id,
                    'product_type' => $p->product_type,
                    'product_code' => $p->product_code,
                    'product_deatils' => $p->product_deatils,
                    'purchase_price' => $p->purchase_price,
                    'qty' => $p->qty,

      
                ];
            })
        ]);
    }
    
    
    public function updatePurchasePrice(Request $request)
    {
        $request->validate([
            'pid' => 'required|array',
            'new_purchase_price' => 'required|array',
        ]);
    
        $pids = $request->pid;
        $prices = $request->new_purchase_price;
    
        foreach ($pids as $index => $pid) {
    
            // Skip empty inputs
            if (!isset($prices[$index]) || $prices[$index] === null || $prices[$index] === '') {
                continue;
            }
    
            SaleProduct::where('id', $pid)->update([
                'purchase_price' => $prices[$index],
            ]);
        }
    
        return redirect()->back()->with('success', 'Purchase prices updated successfully.');
    }
    
    
    
    
    
    public function saleInvoice($id,$idd)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Sale invoice';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        
        $sale = Sale::where('sale_id', $decryptedId)->first();
        $store= Store::where('id', $sale->store_id)->first();

        $setting['sale'] = $sale;
        $setting['salePerson'] = User::find($sale->sale_person);
        $setting['store'] = Store::find($sale->store_id);
        $setting['state'] = State::find($store->state_id);
        $setting['city'] = City::find($store->city_id);
        $setting['saleid'] = $id;
        $setting['printtype'] = $idd;
        $setting['saleproduct'] = SaleProduct::where('sale_id', $decryptedId)
        ->orderBy('id', 'asc')
        ->get()
        ->unique(function ($item) {
            return $item->product_type . '|' .
                   $item->product_code . '|' .
                   $item->barcode_use . '|' .
                   $item->base_price . '|' .
                   $item->discount_amt . '|' .
                   $item->qty . '|' .
                   $item->no_of_glass . '|' .
                   $item->product_deatils;
        })
        ->values(); 
    
        return view($this->view_route.'/sale-invoice',$setting);
    }
    
    
    public function salePdf($id,$idd)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Sale invoice';
        
        $sale = Sale::where('sale_id', $decryptedId)->first();
        $store= Store::where('id', $sale->store_id)->first();

        $setting['sale'] = $sale;
        $setting['salePerson'] = User::find($sale->sale_person);
        $setting['store'] = Store::find($sale->store_id);
        $setting['state'] = State::find($store->state_id);
        $setting['city'] = City::find($store->city_id);
        $setting['saleid'] = $id;
        $setting['printtype'] = $idd;
        $setting['saleproduct'] = SaleProduct::where('sale_id', $decryptedId)
        ->orderBy('id', 'asc')
        ->get()
        ->unique(function ($item) {
            return $item->product_type . '|' .
                   $item->product_code . '|' .
                   $item->barcode_use . '|' .
                   $item->base_price . '|' .
                   $item->discount_amt . '|' .
                   $item->return_status . '|' .
                   $item->qty . '|' .
                   $item->no_of_glass . '|' .
                   $item->product_deatils;
        })
        ->values(); 
        
        $pdf = Pdf::loadView($this->view_route . '/sale-pdf',$setting)
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);

        return $pdf->stream($decryptedId . '.pdf');
    }
    
    public function applyredeempoint(Request $request)
    {
        $order = DB::table('tbl_sales')
            ->where('order_no', $request->oid)
            ->first();
    
        if (!$order) {
            return response()->json(['status' => 'error']);
        }
    
        $customer = DB::table('tbl_customer')
            ->where('contact_no', $order->contact_no)
            ->first();
    
        $canRedeem = (
            $customer->Loyalty_Points_Bal > 0 &&
            $order->pending_amount > 0 && 
            $order->loyalty_point_apply == 0
        );
    
        return response()->json([
            'status'          => 'success',
            'points'          => $customer->Loyalty_Points_Bal,
            'pending_amount'  => $order->pending_amount,
            'contact_no'      => $order->contact_no,
            'order_no'      => $order->order_no,
            'can_redeem'      => $canRedeem
        ]);
    }
    
    
    public function updateredeempoint(Request $request)
    {
        $rotp = $request->rotp;
        $redeemPoints = $request->redeemPoints;
        $redeemPointsAmount = $request->redeemPointsAmount;
        $orderon = $request->orderon;
        $contact_no = $request->contact_no;
    
        if (empty($rotp)) {
            return response()->json(['status' => 'error', 'status_code' => '201']);
        }
    
        $storedAt = session('redeemotp_stored_at');
        $redeemotp = session('redeemotp');
    
        if (!$storedAt || now()->diffInSeconds($storedAt) >= 60) {
            return response()->json(['status' => 'error', 'status_code' => '202']);
        }
    
        if ($rotp != $redeemotp) {
            return response()->json(['status' => 'error', 'status_code' => '201']);
        }
    
        session()->forget(['redeemotp', 'redeemotp_stored_at']);
    
        $sale = DB::table('tbl_sales')->where('order_no', $orderon)->first();
        $custData = DB::table('tbl_customer')->where('contact_no', $contact_no)->first();
    
        if (!$sale || !$custData) {
            return response()->json(['status' => 'error', 'status_code' => '203', 'message' => 'Sale or customer not found']);
        }
    
        $bal_point = $custData->Loyalty_Points_Bal - $redeemPoints;
    
        DB::beginTransaction();
        try {
            DB::table('tbl_loyaltyrogram_histroy')->insert([
                'customer_id' => $custData->customer_id,
                'opening_points' => $custData->Loyalty_Points_Bal,
                'redeem' => $redeemPoints,
                'bal_point' => $bal_point,
                'description' => 'Sales Invoice ' . $orderon,
                'add_remove' => 1,
                'store_id' => $sale->store_id,
                'added_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
    
            DB::table('tbl_customer')->where('customer_id', $custData->customer_id)->update([
                'Loyalty_Points_Redeem' => $custData->Loyalty_Points_Redeem + $redeemPoints,
                'Loyalty_Points_Bal' => $bal_point,
                'updated_at' => now()
            ]);
    
            DB::table('tbl_sales')->where('order_no', $orderon)->update([
                'loyalty_point_apply' => $redeemPoints,
                'loyalty_point_amount' => $redeemPointsAmount,
                'total_payable' => $sale->total_payable - $redeemPointsAmount,
                'pending_amount' => $sale->pending_amount - $redeemPointsAmount,
                'updated_at' => now()
            ]);
    
            DB::commit();
            return response()->json(['status' => 'success', 'status_code' => '200']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'status_code' => '500', 'message' => $e->getMessage()]);
        }
    }
    
    
    public function checkcouponapplyornot(Request $request)
    {
        $order = DB::table('tbl_sales') // corrected table name
            ->where('order_no', $request->oid)
            ->first();
    
        if (!$order) {
            return response()->json(['status' => 'error']);
        }
        
        
        $canCoupon = (
            $order->pending_amount > 0
        );
    
        $data = '';
    
        if ($order->coupon_amount > 0) {
            $coupon = DB::table('tbl_coupon')
                ->where('coupon_id', $order->coupon_id)
                ->first();
    
            if ($coupon) {
                $data .= '
                    <div class="row">
                        <strong>Used Discount Coupon Details</strong>
                    </div>
                    <div class="row">
                        <strong>Discount Coupon :  '.$coupon->coupon_code.'</strong>
                    </div>
                    <div class="row">
                        <strong>Discount Coupon Amount :  Rs '.$order->coupon_amount.'</strong>
                    </div>
                ';
            }
        }
    
        return response()->json([
            'status'         => 'success',
            'pending_amount' => $order->pending_amount,
            'total_payable' => $order->total_payable,
            'contact_no'     => $order->contact_no,
            'order_no'       => $order->order_no,
            'canCoupon'      => $canCoupon,
            'oldcoupon'      => $data,
        ]);
    }
    
    
    
    public function couponupdateonorder(Request $request)
    {
        $DiscountCoupon = $request->DiscountCoupon;
        $coupon_id = $request->coupon_id;
        $orderon = $request->orderon;
        $contact_no = $request->contact_no;
    
        // Check if coupon code is empty
        if (empty($DiscountCoupon)) {
            return response()->json(['status' => 'error', 'status_code' => '201', 'message' => 'Coupon code is required']);
        }
    
        // Fetch sale and customer
        $sale = DB::table('tbl_sales')->where('order_no', $orderon)->first();
        $custData = DB::table('tbl_customer')->where('contact_no', $contact_no)->first();
    
        if (!$sale || !$custData) {
            return response()->json(['status' => 'error', 'status_code' => '202', 'message' => 'Sale or customer not found']);
        }
    
        DB::beginTransaction();
    
        try {
            // Reset previous coupon if exists
            if (!empty($sale->coupon_amount) && !empty($sale->coupon_id)) {
                $original_total_payable = $sale->total_payable + $sale->coupon_amount;
                $original_pending_amount = $sale->pending_amount + $sale->coupon_amount;
    
                DB::table('tbl_sales')->where('order_no', $orderon)->update([
                    'total_payable' => $original_total_payable,
                    'pending_amount' => $original_pending_amount,
                    'coupon_amount' => null,
                    'coupon_id' => null,
                    'updated_at' => now()
                ]);
    
                DB::table('tbl_coupon')->where('coupon_id', $sale->coupon_id)->update([
                    'coupon_status' => 0,
                    'coupon_usages_date' => null,
                    'sale_order_no' => null,
                    'updated_at' => now()
                ]);
            } 
            
            
            $original_total_payable = $sale->total_payable;
            $original_pending_amount = $sale->pending_amount;
            
    
            // Fetch new coupon
            $coupon = DB::table('tbl_coupon')->where('coupon_id', $coupon_id)->first();
    
            if (!$coupon) {
                return response()->json(['status' => 'error', 'status_code' => '203', 'message' => 'Coupon not found']);
            }
    
            // Calculate discount
            $discount_amount = ($coupon->coupon_type == 0) 
                ? ($original_total_payable * $coupon->coupon_value / 100)
                : $coupon->coupon_value;
    
            // Make sure discount does not exceed total
            $discount_amount = min($discount_amount, $original_total_payable);
    
            // Apply new coupon
            DB::table('tbl_sales')->where('order_no', $orderon)->update([
                'total_payable' => $original_total_payable - $discount_amount,
                'pending_amount' => $original_pending_amount - $discount_amount,
                'coupon_amount' => $discount_amount,
                'coupon_id' => $coupon_id,
                'updated_at' => now()
            ]);
    
            // Update coupon status
            DB::table('tbl_coupon')->where('coupon_id', $coupon_id)->update([
                'coupon_status' => 1,
                'coupon_usages_date' => date('Y-m-d'),
                'sale_order_no' => $orderon,
                'updated_at' => now()
            ]);
    
            DB::commit();
    
            return response()->json(['status' => 'success', 'status_code' => '200', 'message' => 'Coupon applied successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'status_code' => '500',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }



    public function checkcartapplyornot(Request $request)
    {
        $request->validate([
            'oid' => 'required'
        ]);
        

        $order = DB::table('tbl_sales')
            ->where('order_no', $request->oid)
            ->first();
    
        if (!$order) {
            return response()->json(['status' => 'error']);
        }
    
        $data = '';
    
        if ($order->cart_discount > 0 && $order->cart_discount_by) {
    
            $user = DB::table('users')
                ->where('id', $order->cart_discount_by)
                ->first();
    
            if ($user) {
                $data .= '
                    <div class="row mb-2">
                        <strong>Used Cart Discount Details</strong>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p>
                                Approval Name : '.e($user->name).' |
                                Mobile : '.e($user->phone).' |
                                Approval % : '.e($order->cart_discount_per).'%
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p>Approval Reason : '.e($order->cart_discount_resion).'</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p>
                                Cart Discount Amount : '.$order->cart_discount.'
                                ('.$order->cart_discount_per.'%)
                            </p>
                        </div>
                    </div>
                ';
            }
        }
    
        return response()->json([
            'status'         => 'success',
            'pending_amount' => $order->pending_amount,
            'total_payable'  => $order->total_payable,
            'contact_no'     => $order->contact_no,
            'order_no'       => $order->order_no,
            'oldcart'        => $data,
        ]);
    }


    public function updatecartdiscount(Request $request)
    {
        $cotp = $request->cotp;
        $selectedUser = $request->selectedUser;
        $discountAmount = $request->discountAmount;
        $discountPercent = $request->discountPercent;
        $reason = $request->reason;
        $orderon = $request->orderno;
    
        if (empty($cotp)) {
            return response()->json(['status' => 'error', 'status_code' => '201']);
        }
    
        $storedAt = session('cartotp_stored_at');
        $cartotp = session('cartotp');
    
        if (!$storedAt || now()->diffInSeconds($storedAt) >= 60) {
            return response()->json(['status' => 'error', 'status_code' => '202']);
        }
    
        if ($cotp != $cartotp) {
            return response()->json(['status' => 'error', 'status_code' => '201']);
        }
    
        session()->forget(['cartotp', 'cartotp_stored_at']);
    
        $sale = DB::table('tbl_sales')->where('order_no', $orderon)->first();
    
        if (!$sale) {
            return response()->json([
                'status' => 'error',
                'status_code' => '203',
                'message' => 'Sale not found'
            ]);
        }
    
        DB::beginTransaction();
        try {
    
            if (!empty($sale->cart_discount)) {
                DB::table('tbl_sales')->where('order_no', $orderon)->update([
                    'total_payable' => DB::raw('total_payable + cart_discount'),
                    'pending_amount' => DB::raw('pending_amount + cart_discount'),
                    'cart_discount' => null,
                    'cart_discount_per' => null,
                    'cart_discount_resion' => null,
                    'cart_discount_by' => null,
                ]);
            }
    
            $sale = DB::table('tbl_sales')->where('order_no', $orderon)->first();
    
            DB::table('tbl_sales')->where('order_no', $orderon)->update([
                'total_payable' => $sale->total_payable - $discountAmount,
                'pending_amount' => $sale->pending_amount - $discountAmount,
                'cart_discount' => $discountAmount,
                'cart_discount_per' => $discountPercent,
                'cart_discount_by' => $selectedUser,
                'cart_discount_resion' => $reason,
                'updated_at' => now()
            ]);
    
            DB::commit();
            return response()->json(['status' => 'success', 'status_code' => '200']);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'status_code' => '500',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    
    public function deleteOtp(Request $request)
    {
        if(empty($request->delete_contactno))
        {
            $response['status_code'] = '201';
        }
        else
        {
            $contact = $request->delete_contactno;
            $otp = '1111';
            /*$otp = rand('1111', '9999');
            $enmsg = "Dear user, your mobile verification code for Quickdaak is $otp DO NOT disclose it to anyone.";
            $msg2 = urlencode($enmsg);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://dtasit.ai/backend/api/http/sms/send?recipient=91$contact&sender_id=QKDAAK&message=$msg2&api_token=55|YA2O3opEwRIBxs2mpmvp4kwzO78krbw52faxKXzY8f233b7a&dlt_template_id=1707174359447112815&type=plain&entity_id=1701174168288220773");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 0);
            $response1 = curl_exec($ch);
            curl_close($ch);*/
            session([
                'deleteotp' => $otp,
                'deleteotp_stored_at' => now(),
            ]);   
            $response['status_code'] = '200';
        }
        
        return response()->json($response);
    }
    
    
    public function orderdelete(Request $request)
    {
        $user = auth()->user();
        
        $dotp = $request->dotp;
        $deletordercomment = $request->deletordercomment;
        $keepPaymentRecords = $request->keepPaymentRecords;
        $orderid = $request->orderid;
        
    
        if (empty($dotp)) {
            return response()->json(['status' => 'error', 'status_code' => '201']);
        }
    
        $storedAt = session('deleteotp_stored_at');
        $deleteotp = session('deleteotp');
    
        if (!$storedAt || now()->diffInSeconds($storedAt) >= 60) {
            return response()->json(['status' => 'error', 'status_code' => '202']);
        }
    
        if ($dotp != $deleteotp) {
            return response()->json(['status' => 'error', 'status_code' => '201']);
        }
    
        session()->forget(['deleteotp', 'deleteotp_stored_at']);
    
        $sale = DB::table('tbl_sales')->where('order_no', $orderid)->first();
    
        if (!$sale) {
            return response()->json([
                'status' => 'error',
                'status_code' => '203',
                'message' => 'Sale not found'
            ]);
        }
    
        DB::beginTransaction();
        try 
        {
            /// Restored Barcode Or Stock 
            $items = SaleProduct::where('order_no', $orderid)->get();
          
            foreach ($items as $item) 
            {
                if(!empty($item->barcode_use))
                {
                    DB::table('tbl_barcode')
                    ->where('refrence_no', $item->order_no)
                    ->where('barcode_no', $item->barcode_use)
                    ->update([
                        'refrence_no' => '',
                        'outward_status' => '',
                        'transfer_outward_status' => ''
                    ]);
                    
                    
                    $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                            'barcode_no' => $item->barcode_use,
                            'store_id' => $item->store_id,
                            'reference_type' => 'Sale',
                            'action_perform' => 'Delete',
                            'added_by' => $user->id,
                    ]);
                    
                }
                
                if ($item->product_type === 'Glass') {
                
                    // Both Glass
                    if ($item->qty == 2) {
                        $this->addInventoryStock($item, $item->right_glass, 1, $user);
                        $this->addInventoryStock($item, $item->left_glass, 1, $user);
                    }
                    // Single Glass
                    else {
                        if ($item->right_purchase == 1) {
                            $this->addInventoryStock($item, $item->right_glass, 1, $user);
                        } else {
                            $this->addInventoryStock($item, $item->left_glass, 1, $user);
                        }
                    }
                }
                elseif ($item->product_type === 'Lens') {
                    // FUTURE CODE
                }
                elseif ($item->product_type === 'Repair') {
                    // No inventory required
                }
                else {
                    // Other products
                    $this->addInventoryStock(
                        $item,
                        $item->product_deatils,
                        $item->qty,
                        $user
                    );
                }
                
            }
            
            // Remove EarnPoints Or EarnCoupon
            
            
            /* ============================
                   LOYALTY POINTS
                ============================ */
                if ($sale->earnedPoints > 0)
                {
        
                    $tblcustomer = DB::table('tbl_customer')
                        ->where('contact_no', $salesv->contact_no)
                        ->first();
        
                    if ($tblcustomer)
                    {
                        $description = 'Sales Invoice ' . $saleId;
        
                        // Remove previous earned points
                        DB::table('tbl_loyaltyrogram_histroy')
                            ->where('description', $description)
                            ->delete();
        
                        $oldPoints = (int) $sale->earnedPoints;
        
                        DB::table('tbl_customer')
                        ->where('customer_id', $tblcustomer->customer_id)
                        ->update([
                            'Loyalty_Points'     => DB::raw("GREATEST(Loyalty_Points - $oldPoints, 0)"),
                            'Loyalty_Points_Bal' => DB::raw("GREATEST(Loyalty_Points_Bal - $oldPoints, 0)"),
                            'updated_at'         => now(),
                        ]);
                    }
                }
                
                if ($sale->loyalty_point_apply > 0)
                {
                    $oldPoints = (int) $sale->loyalty_point_apply;
        
                    DB::table('tbl_customer')
                    ->where('customer_id', $tblcustomer->customer_id)
                    ->update([
                        'Loyalty_Points'     => DB::raw("GREATEST(Loyalty_Points + $oldPoints, 0)"),
                        'Loyalty_Points_Bal' => DB::raw("GREATEST(Loyalty_Points_Bal + $oldPoints, 0)"),
                        'updated_at'         => now(),
                    ]);
                }
                
                
        
                /* ============================
                   COUPONS
                ============================ */
                if ($sale->earncoupon !== null) 
                {
                    // Remove old coupon
                    DB::table('tbl_coupon')
                        ->where('coupon_id', $sale->earncoupon)
                        ->delete();
                }
                
                if ($sale->coupon_id > 0)
                {
                    DB::table('tbl_barcode')
                    ->where('coupon_id', $sale->coupon_id)
                    ->update([
                        'coupon_status' => 0,
                        'coupon_usages_date' => '',
                        'sale_order_no' => ''
                    ]);
                }
                
                
                if($keepPaymentRecords == 1)
                {
                    DB::table('tbl_sale_payment')
                        ->where('order_no',$orderid)
                        ->delete();
                }
                else
                {
                    DB::table('tbl_sale_payment')
                    ->where('order_no',$orderid)
                    ->update([
                        'is_deleted' => 1
                    ]);
                }
                
                
                DB::table('tbl_sales')
                    ->where('order_no',$orderid)
                    ->update([
                        'is_deleted' => 1,
                        'deletordercomment' => $deletordercomment
                    ]);
                    
                DB::table('tbl_sales_product')
                    ->where('order_no',$orderid)
                    ->update([
                        'is_deleted' => 1
                    ]);    
                
    
            
    
            DB::commit();
            return response()->json(['status' => 'success', 'status_code' => '200']);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'status_code' => '500',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    
    
    public function getorderprescription(Request $request)
    {
        $oid = $request->oid;
    
        $prescription = SaleProduct::where('order_no', $oid)
            ->where('product_type', 'Glass')
            ->orderBy('id', 'asc')
            ->get()
            ->unique(function ($item) {
                return $item->product_type . '|' .
                       $item->product_code . '|' .
                       $item->barcode_use . '|' .
                       $item->base_price . '|' .
                       $item->discount_amt . '|' .
                       $item->return_status . '|' .
                       $item->qty . '|' .
                       $item->no_of_glass . '|' .
                       $item->product_deatils;
            })
            ->values();
    
        return response()->json([
            'data' => $prescription
        ]);
    }
    
    
    public function prescriptionupdate(Request $request)
    {
        
    }
    

    
    
    
    public function interStoreSale()
    {
        $setting['page_title'] = 'Create Inter Store Sales';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/inter-store-order',$setting);
    }
    
    
    public function getStoreProductByBarcode(Request $request)
    {
        $store_id     = auth()->user()->store_id;
        $barcode      = $request->input('barcode');
        $from_store   = $request->input('from_store');
        $to_store     = $request->input('to_store');
        $tax_rule     = $request->input('tax_rule');

        
        if($request->input('sale_type') == 'inter')
        {
            $product = DB::table('tbl_barcode')
                ->where('barcode_status', '1')
                ->where('t_status', '0')
                ->where('store_id', $from_store)
                ->where('outward_status',NULL)
                ->where('barcode_no', $barcode)
                ->first();
            
            if (!$product) {
                $product = DB::table('tbl_barcode')
                    ->where('barcode_no', $barcode)
                    ->where('barcode_status', '1')
                    ->where('t_status', '1')
                    ->where('transfer_store_id', $from_store)
                    ->where('transfer_outward_status',NULL)
                    ->first();
            }
                

            if ($product)
            {
                $tbl_from_store = DB::table('tbl_store')->where('id', $from_store)->first();
                $tbl_to_store     = DB::table('tbl_store')->where('id', $to_store)->first();
                
                
                $basePrice = 0;
                $gstAmount = 0;
                $totalSale = 0;
    
                if($tbl_from_store->gst_no == $tbl_to_store->gst_no)
                {
                    $margin = 0;
                    $margin_amount = 0.00;
                }
                else
                {
                    $tbl_sales_setting = DB::table('tbl_sales_setting')->first();
                    if($product->product_type == 'Frame')
                    {
                        $margin = $tbl_sales_setting->frame_margin;
                        $margin_amount = ($product->purchase_price * $margin) / 100;
                    }
                    elseif($product->product_type == 'Glass')
                    {
                        $margin = $tbl_sales_setting->glass_margin;
                        $margin_amount = ($product->purchase_price * $margin) / 100;
                    }
                    elseif($product->product_type == 'Goggles')
                    {
                        $margin = $tbl_sales_setting->goggles_margin;
                        $margin_amount = ($product->purchase_price * $margin) / 100;
                    }
                    elseif($product->product_type == 'Lens')
                    {
                        $margin = $tbl_sales_setting->lens_margin;
                        $margin_amount = ($product->purchase_price * $margin) / 100;
                    }
                    elseif($product->product_type == 'Solution')
                    {
                        $margin = $tbl_sales_setting->solution_margin;
                        $margin_amount = ($product->purchase_price * $margin) / 100;
                    }
                    elseif($product->product_type == 'Other')
                    {
                        $margin = $tbl_sales_setting->other_margin;
                        $margin_amount = ($product->purchase_price * $margin) / 100;
                    }
                }
                
                $tbl_tax = DB::table('tbl_tax')
                ->where('product_type', $product->product_type)
                ->where('set_default', '1')
                ->first(); 
                 
                 
                 $price = $product->purchase_price+$margin_amount;
                
                
                if ($tax_rule === "Include") 
                {
                    $gstRate   = $tbl_tax->percentage ?? '';
                    $hsn_code   = $tbl_tax->hsn_code ?? '';
                
                    $basePrice = $price  / (1 + ($gstRate / 100));
                    $gstAmount = $price - $basePrice;
                    $totalSale = $basePrice-$gstAmount;  
                }
                elseif ($tax_rule === "Exclude") 
                {
                    $gstRate   = $tbl_tax->percentage ?? '';
                    $hsn_code   = $tbl_tax->hsn_code ?? '';
                
                    $basePrice = $price;
                    $gstAmount = ($basePrice * $gstRate) / 100;
                    $totalSale = $basePrice + $gstAmount;
                } 
                else 
                {
                    $gstRate   =  0;
                    $hsn_code   =  '';
                    $basePrice = $price;
                    $gstAmount = 0;
                    $totalSale = $price;
                }
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'product_type'    => $product->product_type,
                        'product_id'      => $product->product_id,
                        'product_details' => $product->product_details ?? '', 
                        'purchase_price'  => $product->purchase_price,
                        'retail_price'    => $product->retail_price,
                        'product_code'    => $product->product_code,
                        'margin'          => $margin,
                        'margin_amount'   => number_format($margin_amount, 2),
                        'gstRate'         => $gstRate,
                        'hsn_code'        => $hsn_code,
                        'gstAmount'       => number_format($gstAmount, 2),
                        'basePrice'       => number_format($basePrice, 2),
                        'totalSale'       => number_format($totalSale, 2),
                        'tax_rule'        => $tax_rule,
                    ]
                ]);
            }
            else 
            {
                return response()->json(['success' => false]);
            }
        }
        else
        {
            $user = auth()->user();
            
            
            $product = DB::table('tbl_barcode')
                ->where('barcode_status', '1')
                ->where('t_status', '0')
                ->where('store_id', $to_store)
                ->where('outward_status',NULL)
                ->where('barcode_no', $barcode)
                ->first();
            
            if (!$product) {
                $product = DB::table('tbl_barcode')
                    ->where('barcode_no', $barcode)
                    ->where('barcode_status', '1')
                    ->where('t_status', '1')
                    ->where('transfer_store_id', $to_store)
                    ->where('transfer_outward_status',NULL)
                    ->first();
            }
            

        
            if ($product)
            {
                $tbl_from_store = DB::table('tbl_store')->where('id', $from_store)->first();
                $tbl_to_store   = DB::table('tbl_store')->where('id', $to_store)->first();

                $basePrice = 0;
                $gstAmount = 0;
                $totalSale = 0;

                $tbl_tax = DB::table('tbl_tax')
                ->where('product_type', $product->product_type)
                ->where('set_default', '1')
                ->first(); 
                 
                 
                $price = $product->retail_price;
                $barcodedis = $product->discount;
                
                
                
                if ($tax_rule === "Include") 
                {
                    $gstRate   = $tbl_tax->percentage ?? '';
                    $hsn_code   = $tbl_tax->hsn_code ?? '';
                
                    $basePrice = $price  / (1 + ($gstRate / 100));
                    $gstAmount = $price - $basePrice;
                    $totalSale = $basePrice+$gstAmount;  
                }
                elseif ($tax_rule === "Exclude") 
                {
                    $gstRate   = $tbl_tax->percentage ?? '';
                    $hsn_code   = $tbl_tax->hsn_code ?? '';
                
                    $basePrice = $price;
                    $gstAmount = ($basePrice * $gstRate) / 100;
                    $totalSale = $basePrice + $gstAmount;
                } 
                else 
                {
                    $gstRate   =  0;
                    $hsn_code   =  '';
                    $basePrice = $price;
                    $gstAmount = 0;
                    $totalSale = $price;
                }
                
                $tbl_product_code = DB::table('tbl_product_code')->where('product_id', $product->product_id)->first();
                
                if(!empty($barcodedis))
                {
                    $discount = $barcodedis;
                    $discountamount = ($totalSale * $barcodedis) / 100;
                }
                else
                {
                    if(!empty($tbl_product_code->discount))
                    {
                        $discount = $tbl_product_code->discount;
                        $discountamount = ($totalSale * $tbl_product_code->discount) / 100;
                    }
                    else
                    {
                        $tbl_brand = DB::table('tbl_brand')->where('product_type', $tbl_product_code->product_type)->where('brand_name', $tbl_product_code->Company)->first();
                        if(!empty($tbl_brand->discount))
                        {
                            $discount = $tbl_brand->discount;
                            $discountamount = ($totalSale * $tbl_brand->discount) / 100;
                        }
                        else
                        {
                            $discount = 0;
                            $discountamount = 0;
                        }
                    }
                    
                }
                
                $finaltotalsale = $totalSale - $discountamount;
                
                
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'product_type'    => $product->product_type,
                        'product_id'      => $product->product_id,
                        'product_details' => $product->product_details ?? '', 
                        'product_company' => $tbl_product_code->Company,
                        'product_quality' => $tbl_product_code->Quality,
                        'product_color'   => $tbl_product_code->Color,
                        'product_material'=> $tbl_product_code->Material,
                        'product_coating' => $tbl_product_code->Coating,
                        'product_design'  => $tbl_product_code->Design,
                        'product_index'   => $tbl_product_code->Index,
                        'product_number'  => $tbl_product_code->Number,
                        'product_ct'      => $tbl_product_code->CT,
                        'product_validity'=> $tbl_product_code->Validity,
                        'product_typesss' => $tbl_product_code->Type,
                        'product_variant' => $tbl_product_code->Variant,
                        'product_shape'   => $tbl_product_code->Shape,
                        'product_size'    => $tbl_product_code->Size,
                        'purchase_price'  => $product->purchase_price,
                        'retail_price'    => $product->retail_price,
                        'product_code'    => $product->product_code,
                        'is_pair'    => $product->is_pair,
                        'product_qty'     => 1,
                        'gstRate'         => $gstRate,
                        'hsn_code'        => $hsn_code,
                        'gstAmount'       => round($gstAmount, 2),
                        'basePrice'       => round($basePrice, 2),
                        'discount'        => $discount,
                        'discountamt'     => round($discountamount, 2),
                        'totalSale'       => round($finaltotalsale, 2),
                        'tax_rule'        => $tax_rule,
                    ]
                ]);
            }
            else 
            {
                return response()->json(['success' => false]);
            }
        }
    }
    
    
    public function getProductByProductCode(Request $request)
    {
    	$store_id     = auth()->user()->store_id;
    	$selectedCode      = $request->input('selectedCode');
    	$tax_rule     = $request->input('tax_rule');
    	
    	$rightLeft     = $request->input('rightLeft');
    	$productType    = $request->input('productType');
    

		$user = auth()->user();
		$product = DB::table('tbl_product_code')
		->where('productdetails', $selectedCode)
		/*->where('store_id', $user->store_id)*/
		->first(); // not get()
	
		if ($product)
		{
			$basePrice = 0;
			$gstAmount = 0;
			$totalSale = 0;

			$tbl_tax = DB::table('tbl_tax')
			->where('product_type', $product->product_type)
			->where('set_default', '1')
			->first(); 
			 
			 
			
			
			
			if ($tax_rule === "Include") 
			{
				$gstRate   = $tbl_tax->percentage ?? '';
				$hsn_code   = $tbl_tax->hsn_code ?? '';
			    
			    if($productType =='Glass')
			    {
			        $value = $rightLeft; 
                    $count = count($value);
                    
                    $Purchase_Price = ($product->Purchase_Price)*$count;
                    $price = ($product->Retail_Price)*$count;
                    
                    $basePrice = $price  / (1 + ($gstRate / 100));
    				$gstAmount = ($price - $basePrice);
    				$totalSale = ($basePrice+$gstAmount); 
			    }
			    else
			    {
			        $Purchase_Price = $product->Purchase_Price;
                    $price = $product->Retail_Price;
                    
			        $basePrice = $price  / (1 + ($gstRate / 100));
    				$gstAmount = $price - $basePrice;
    				$totalSale = $basePrice+$gstAmount; 
			    }
				 
			}
			elseif ($tax_rule === "Exclude") 
			{
			    if($productType =='Glass')
			    {
			        $value = $rightLeft; 
                    $count = count($value);
                    
                    $Purchase_Price = ($product->Purchase_Price)*$count;
                    $price = ($product->Retail_Price)*$count;
                    
                    $basePrice = $price;
    				$gstAmount = (($basePrice * $gstRate) / 100);
    				$totalSale = ($basePrice+$gstAmount); 
			    }
			    else
			    {
			        $Purchase_Price = $product->Purchase_Price;
                    $price = $product->Retail_Price;
                    
    				$gstRate   = $tbl_tax->percentage ?? '';
    				$hsn_code   = $tbl_tax->hsn_code ?? '';
    			
    				$basePrice = $price;
    				$gstAmount = ($basePrice * $gstRate) / 100;
    				$totalSale = $basePrice + $gstAmount;
			    }	
			} 
			else 
			{
			    if($productType =='Glass')
			    {
			        $value = $rightLeft; 
                    $count = count($value);
                    
                    $Purchase_Price = ($product->Purchase_Price)*$count;
                    $price = ($product->Retail_Price)*$count;
                        
    				$gstRate   =  0;
    				$hsn_code   =  '';
    				$basePrice = $price;
    				$gstAmount = 0;
    				$totalSale = $price;
			    }
			    else
			    {
			        $Purchase_Price = $product->Purchase_Price;
                    $price = $product->Retail_Price;
                        
    				$gstRate   =  0;
    				$hsn_code   =  '';
    				$basePrice = $price;
    				$gstAmount = 0;
    				$totalSale = $price;
    			        
			    }
			    
			}
			
			$tbl_product_code = DB::table('tbl_product_code')->where('product_code', $product->product_code)->first();
			

            if(!empty($tbl_product_code->discount))
            {
                $discount = $tbl_product_code->discount;
                $discountamount = ($totalSale * $tbl_product_code->discount) / 100;
            }
            else
            {
                $tbl_brand = DB::table('tbl_brand')->where('product_type', $tbl_product_code->product_type)->where('brand_name', $tbl_product_code->Company)->first();
                if(!empty($tbl_brand->discount))
                {
                    $discount = $tbl_brand->discount;
                    $discountamount = ($totalSale * $tbl_brand->discount) / 100;
                }
                else
                {
                    $discount = 0;
                    $discountamount = 0;
                }
            }
                
        
            
            $finaltotalsale = $totalSale - $discountamount;
			
			
			
			return response()->json([
				'success' => true,
				'data' => [
					'product_type'    => $product->product_type,
					'product_id'    => $product->product_id,
					'product_details' => $product->productdetails ?? '', 
					'product_company' => $tbl_product_code->Company,
					'product_quality' => $tbl_product_code->Quality,
					'product_color'   => $tbl_product_code->Color,
					'product_material'=> $tbl_product_code->Material,
					'product_coating' => $tbl_product_code->Coating,
					'product_design'  => $tbl_product_code->Design,
					'product_index'   => $tbl_product_code->Index,
					'product_number'   => $tbl_product_code->Number,
					'product_ct'   => $tbl_product_code->CT,
					'product_validity'   => $tbl_product_code->Validity,
					'product_typesss'   => $tbl_product_code->Type,
					'product_variant'   => $tbl_product_code->Variant,
					'product_shape'   => $tbl_product_code->Shape,
					'product_size'   => $tbl_product_code->Size,
					'purchase_price'  => $Purchase_Price,
					'retail_price'    => $price,
					'product_code'    => $product->product_code,
					'gstRate'         => $gstRate,
					'hsn_code'        => $hsn_code,
					'gstAmount'       => round($gstAmount, 2),
					'basePrice'       => round($basePrice, 2),
					'discount'        => $discount,
                    'discountamt'     => round($discountamount, 2),
                    'totalSale'       => round($finaltotalsale, 2),
					'tax_rule'        => $tax_rule,
					'product_qty'    => 1,
				]
			]);
		}
		else 
		{
			return response()->json(['success' => false]);
		}
    	
    }
    
    
    public function storedinterSaleOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bill_no'        => 'required',
            'to_store'       => 'required|different:from_store',
            'from_store'     => 'required',
            'product_type'   => 'required|array',
            'product_code'   => 'required|array',
            'product_qty'    => 'required|array',
            'barcode'        => 'required|array',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        DB::beginTransaction();
    
        try {
            $user = auth()->user();
            $data = $request->all();
    
            $tbl_to_store = DB::table('tbl_store')->where('id', $data['to_store'])->first();
    
            // Create Sale
            $sale = Sale::create([
                'sale_date'         => $data['sale_date'],
                'delivery_date'     => date('Y-m-d', strtotime($data['sale_date']. ' + 1 day')),
                'order_no'          => $data['bill_no'],
                'cust_id'           => $tbl_to_store->store_id,
                'contact_no'        => $tbl_to_store->contact_no,
                'cust_name'         => $tbl_to_store->store_name,
                'email_id'          => $tbl_to_store->email_id,
                'cust_address'      => $tbl_to_store->store_address,
                'state_id'          => $tbl_to_store->state_id,
                'city_id'           => $tbl_to_store->city_id,
                'pincode'           => $tbl_to_store->pincode,
                'gst_no'            => $data['to_gst_no'],
                'total_basic_amount'=> $data['total_basic_amount'] ?? 0,
                'total_gst_amount'  => $data['total_gst_amount'] ?? 0,
                'total_item_price'  => $data['total_sale_amount'] ?? 0,
                'total_payable'     => $data['total_payable_amount'] ?? 0,
                'from_store'        => $data['from_store'],
                'sale_person'       => $data['sale_person'],
                'inter_sale'        => 1,
                'sales_status'      => 1,
                'added_by'          => $user->id,
                'store_id'          => $user->store_id,
                'tax_rule'          => $data['tax_rule'],
                
            ]);
    
            foreach ($data['product_type'] as $i => $type) {
                $qty = (int) $data['product_qty'][$i];
                $productDetails = $data['product_description'][$i];
                $productCode = $data['product_code'][$i];
                $barcodeNo = $data['barcode'][$i];
    
                // Create SaleProduct
                SaleProduct::create([
                    'sale_id'         => $sale->id,
                    'order_no'        => $data['bill_no'],
                    'product_type'    => $type,
                    'product_id'        => $data['product_id'][$i],
                    'product_code'    => $productCode,
                    'product_deatils' => $productDetails,
                    'barcode_use'     => $barcodeNo,
                    'qty'             => $qty,
                    'hsn_code'        => $data['hsn_code'][$i],
                    'gst'             => $data['gst'][$i],
                    'gst_amount'      => $data['gst_amount'][$i],
                    'margin_amt'      => $data['margin_amt'][$i],
                    'margin'          => $data['margin'][$i],
                    'base_price'      => $data['base_price'][$i],
                    'retail_price'    => $data['retail_price'][$i],
                    'sale_price'      => $data['sale_price'][$i],
                    'store_id'        => $user->store_id,
                ]);
    
                $perbox = 1;
                if ($type === 'Lens') {
                    $tbl_barcode = DB::table('tbl_barcode')
                        ->where('barcode_no', $barcodeNo)
                        ->where('store_id', $data['from_store'])
                        ->first();
                    $perbox = $tbl_barcode->perbox ?? 1;
                }
    
                // Update FROM store inventory
                $fromInventoryQuery = DB::table('tbl_inventory_levels')
                    ->where('store_id', $data['from_store'])
                    ->where('product_id', $data['product_id'][$i])
                    ->where('product_type', $type)
                    ->where('product_code', $productCode);
                
                if ($type === 'Lens') $fromInventoryQuery->where('perbox', $perbox);
    
                $fromInventory = $fromInventoryQuery->first();
                if ($fromInventory) {
                    $updateData = ['available_quantity' => max(0, $fromInventory->available_quantity - $qty), 'updated_at' => now()];
                    if ($type === 'Lens') {
                        $updateData['tota_lens_qty'] = max(0, $fromInventory->tota_lens_qty - ($qty * $perbox));
                    }
                    DB::table('tbl_inventory_levels')->where('id', $fromInventory->id)->update($updateData);
                }
    
                // Update TO store inventory
                $toInventoryQuery = DB::table('tbl_inventory_levels')
                    ->where('store_id', $data['to_store'])
                    ->where('product_id', $data['product_id'][$i])
                    ->where('product_type', $type)
                    ->where('product_code', $productCode);
                
                if ($type === 'Lens') $toInventoryQuery->where('perbox', $perbox);
    
                $toInventory = $toInventoryQuery->first();
                if ($toInventory) {
                    $updateData = ['available_quantity' => $toInventory->available_quantity + $qty, 'updated_at' => now()];
                    if ($type === 'Lens') {
                        $updateData['tota_lens_qty'] = $toInventory->tota_lens_qty + ($qty * $perbox);
                    }
                    DB::table('tbl_inventory_levels')->where('id', $toInventory->id)->update($updateData);
                } else {
                    // Insert if not exists
                    $insertData = [
                        'store_id' => $data['to_store'],
                        'product_id' => $data['product_id'][$i],
                        'product_details' => $productDetails,
                        'product_type' => $type,
                        'product_code' => $productCode,
                        'available_quantity' => $qty,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    if ($type === 'Lens') {
                        $insertData['perbox'] = $perbox;
                        $insertData['tota_lens_qty'] = $qty * $perbox;
                    }
                    DB::table('tbl_inventory_levels')->insert($insertData);
                }
    
                // Update barcode transfer
                DB::table('tbl_barcode')
                    ->where(function ($q) use ($type, $barcodeNo) {
                        if ($type === 'Lens') $q->where('lens_box', $barcodeNo);
                        $q->orWhere('barcode_no', $barcodeNo);
                    })
                    ->where('store_id', $data['from_store'])
                    ->update([
                        'store_id' => $data['to_store'],
                        'refrence_no' => $data['bill_no'],
                        'updated_at' => now()
                    ]);
            }
    
            DB::commit();
    
            return response()->json(['success' => 'Sales and Products saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the sales save process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    

    
    public function saleReturn()
    {
        $setting['page_title'] = 'Create Sale Return';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/sale-return',$setting);
    }
    
    
    public function saleProductList(Request $request)
    {
        $store_id = auth()->user()->store_id;
        
        $order_no = $request->input('order_no');
        
        $tbl_sales = DB::table('tbl_sales')->where('order_no', $order_no)->first();
    
       $results = DB::table('tbl_sales_product')
            ->where('order_no', $order_no)
            ->where('return_status', '0')
            ->get() // fetch from DB
            ->unique(function ($item) {
                return $item->product_type . '|' .
                       $item->product_code . '|' .
                       $item->barcode_use . '|' .
                       $item->base_price . '|' .
                       $item->discount_amt . '|' .
                       $item->return_status . '|' .
                       $item->qty . '|' .
                       $item->no_of_glass . '|' .
                       $item->product_deatils;
            })
            ->values(); // reindex

    
        if (!$results) {
            return response()->json('<div class="alert alert-danger">No data found.</div>');
        }

        
        $data = '';
        
        $data .= '
        <div class="container">
            <div class="col-lg-3" style="margin-top: 10px;">
                <label>Return Product Recevied Store </label>
                <select class="form-control select" style="height: 32px !important;" id="storeid" name="storeid">
                    <option value="">Select  Store</option>';
                   $tbl_store =  DB::table("tbl_store")->where('status',1)->get();
                   foreach($tbl_store as $tbl_store)
                   {
                       $selected = ($store_id == $tbl_store->id) ? 'selected' : '';
                    $data .= '<option value="'.$tbl_store->id.'" '.$selected.'>'.$tbl_store->store_name.' / ('.$tbl_store->store_id.')</option>';
                   }
                $data .= '</select>
            </div>
            <br>
            <table id="datatable2" class="table card-table table-vcenter text-nowrap" style="color: #000;">
                <thead>
                  <tr>
                    <th><input type="checkbox" id="select-all" onclick="toggleAll(this)"></th>
                    <th style="color: #6b6f80;">Order No</th>
                    <th style="color: #6b6f80;">Product</th>
                    <th style="color: #6b6f80;">Product Code</th>
                    <th style="color: #6b6f80;">Description</th>
                    <th style="color: #6b6f80;">Amount</th>
                    <th style="color: #6b6f80;">Barcode</th>
                  </tr>
                </thead>
                <tbody>';
                foreach ($results as $product)
                {
                  
                   $data .= '
                        <tr> 
                             <td><input type="checkbox" class="row-checkbox" value="'.$product->id.'"></td>
                             <td>' . $product->order_no . '</td>
                             <td>' . $product->product_type . '</td>
                             <td>' . $product->product_code . '</td>
                             <td>' . $product->product_deatils . '</td>
                             <td>' . $product->sale_price*$product->qty . '</td>
                             <td>' . $product->barcode_use . '</td>
                        </tr>
                    ';
                }
                $data .= '</tbody>
            </table>
            <hr/>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Photo </label>
                        <input type="file" class="form-control input" id="return_photo" name="return_photo">
                        <input type="hidden" class="form-control input"  id="orderid" name="orderid" value="'.$order_no.'">
                        <input type="hidden" class="form-control input"  id="contact_no" name="contact_no" value="'.$tbl_sales->contact_no.'">
                        <input type="hidden" class="form-control input"  id="cust_id" name="cust_id" value="'.$tbl_sales->cust_id.'">
                        <input type="hidden" class="form-control input"  id="store_id" name="store_id" value="'.$tbl_sales->store_id.'">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="">Remark <span class="text-danger">*</span></label>
                    <textarea class="form-control"   id="return_remark" name="return_remark"></textarea>
                </div>
                <div class="col-md-3">
                   <button class="btn btn-success" id="submitapprovalBtn" type="button" style="margin-top: 29px;">Send Approval Return</button>
                </div>
            </div>

        </div>
        <script>
            $(document).ready(function() {
                $(".select").select2({
                  allowClear: true
                });
            });
            function toggleAll(source) 
            {
                const checkboxes = document.querySelectorAll(".row-checkbox");
                checkboxes.forEach(cb => cb.checked = source.checked);
            }
        </script>';

        return response()->json($data);
    }
    

    
    public function saleReturenRequest(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|array',
            'orderid'      => 'required',
            'storeid'      => 'required',
            'return_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'return_remark'      => 'required',
        ]);
    
        $user = auth()->user();
    
        DB::beginTransaction();
    
        try {
    
            /* =========================
               UPLOAD PHOTO
            ========================== */
    
            $photoPath = null;
    
            if ($request->hasFile('return_photo')) {
    
                $file = $request->file('return_photo');
    
                $filename = time().'_return.'.$file->getClientOriginalExtension();
    
                $photoPath = $file->storeAs(
                    'sale_returns',
                    $filename,
                    'public'
                );
            }
    
            $product_ids = $request->product_id;
            $storeid     = $request->storeid;
            $return_remark     = $request->return_remark;
    
            $sale_price = 0;
    
            foreach ($product_ids as $product_id)
            {
                $product = DB::table('tbl_sales_product')
                    ->where('id', $product_id)
                    ->where('return_status', 0)
                    ->first();
    
                if (!$product) {
                    continue;
                }
    
                $sale_price += ($product->sale_price ?? 0) * ($product->qty ?? 1);
    
                /* =========================
                   GLASS SPECIAL CASE
                ========================== */
    
                if ($product->product_type === 'Glass' && $product->qty == 2)
                {
                    DB::table('tbl_sales_product')
                        ->where('order_no', $product->order_no)
                        ->where('product_type', $product->product_type)
                        ->where('product_code', $product->product_code)
                        ->where('barcode_use', $product->barcode_use)
                        ->where('no_of_glass', $product->no_of_glass)
                        ->where('return_status', 0)
                        ->update([
                            'return_status'   => 2,
                            'return_date'     => now(),
                            'return_store'    => $storeid,
                            'return_added_by' => $user->id,
                            'return_photo'    => $photoPath,
                            'return_remark'    => $return_remark,
                            'updated_at'      => now(),
                        ]);
                }
                else
                {
                    DB::table('tbl_sales_product')
                        ->where('id', $product_id)
                        ->update([
                            'return_status'   => 2,
                            'return_date'     => now(),
                            'return_store'    => $storeid,
                            'return_added_by' => $user->id,
                            'return_photo'    => $photoPath,
                            'return_remark'    => $return_remark,
                            'updated_at'      => now(),
                        ]);
                }
            }
    
            if ($sale_price <= 0) {
                throw new \Exception('No valid products selected.');
            }
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Return request generated successfully.'
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    

    
    public function getReturnrequestdata(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $date_from    = $request->input('date_from');
        $date_to      = $request->input('date_to');
        $product_type = $request->input('product_type');
        $search       = $request->input('search');
        $stid         = $request->input('store_id');
    
        /* ---------------------------
           Base Query
        ----------------------------*/
        $query = SaleProduct::whereIn('return_status', [1,2,3]);
    
        // login store filter
        if ($store_id != 0) {
            $query->where('store_id', $store_id);
        }
    
        // selected store filter
        if (!empty($stid)) {
            $query->where('store_id', $stid);
        }
    
        /* ---------------------------
           Fetch & Unique Records
        ----------------------------*/
        $collection = $query->get()
            ->unique(function ($item) {
                return implode('|', [
                    $item->product_type,
                    $item->product_code,
                    $item->barcode_use,
                    $item->base_price,
                    $item->discount_amt,
                    $item->return_status,
                    $item->qty,
                    $item->no_of_glass,
                    $item->product_deatils
                ]);
            })
            ->values();
    
        /* ---------------------------
           Product Type Filter
        ----------------------------*/
        if (!empty($product_type)) {
            $collection = $collection->where('product_type', $product_type);
        }
    
        /* ---------------------------
           Date Filter (From / To)
        ----------------------------*/
        if (!empty($date_from) || !empty($date_to)) {
    
            $collection = $collection->filter(function ($item) use ($date_from, $date_to) {
    
                if (empty($item->return_date)) {
                    return false;
                }
    
                $returnDate = Carbon::parse($item->return_date)->format('Y-m-d');
    
                if ($date_from && !$date_to) {
                    return $returnDate >= $date_from;
                }
    
                if (!$date_from && $date_to) {
                    return $returnDate <= $date_to;
                }
    
                if ($date_from && $date_to) {
                    return $returnDate >= $date_from &&
                           $returnDate <= $date_to;
                }
    
                return true;
            });
        }
    
        /* ---------------------------
           Search Filter
        ----------------------------*/
        if (!empty($search)) {
            $collection = $collection->filter(function ($item) use ($search) {
                return str_contains($item->order_no, $search) ||
                       str_contains($item->barcode_use ?? '', $search);
            });
        }
    
        /* ---------------------------
           Counts
        ----------------------------*/
        $totalRequest  = $collection->count();
        $totalPending  = $collection->where('return_status', 2)->count();
        $totalRejected = $collection->where('return_status', 3)->count();
    
        /* ---------------------------
           HTML Section
        ----------------------------*/
        $data = '
        <div class="row">
    
            <div class="col-xl-3 col-sm-6 d-flex mb-5 mb-xl-0">
                <div class="feature">
                    <i class="si si-briefcase primary feature-icon bg-primary"></i>
                </div>
                <div class="ml-3">
                    <small>Total Request</small><br>
                    <h3 class="font-weight-semibold mb-0">'.$totalRequest.'</h3>
                </div>
            </div>
    
            <div class="col-xl-3 col-sm-6 d-flex mb-5 mb-xl-0">
                <div class="feature">
                    <i class="si si-layers feature-icon bg-warning"></i>
                </div>
                <div class="ml-3">
                    <small>Total Request Pending</small>
                    <h3 class="font-weight-semibold mb-0">'.$totalPending.'</h3>
                </div>
            </div>
    
            <div class="col-xl-3 col-sm-6 d-flex mb-5 mb-sm-0">
                <div class="feature">
                    <i class="fa fa-thumbs-down feature-icon bg-danger"></i>
                </div>
                <div class="ml-3">
                    <small>Total Request Reject</small>
                    <h3 class="font-weight-semibold mb-0">'.$totalRejected.'</h3>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 d-flex mb-5 mb-sm-0">
                <div class="feature">
                    <i class="fa fa-thumbs-up success feature-icon bg-success"></i>
                </div>
                <div class="ml-3">
                    <small>Total Request Approved</small>
                    <h3 class="font-weight-semibold mb-0">'.$totalRequest - $totalRejected.'</h3>
                </div>
            </div>
    
        </div>';
    
        /* ---------------------------
           Response
        ----------------------------*/
        return response()->json([
            'status' => 'success',
            'requestdata_section' => $data
        ]);
    }  
    
    public function saleReturnRequesthistory()
    {
        $setting['page_title'] = 'Sale Return Request History';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/sale-return-request-list',$setting);
    }
    
    
    public function saleReturnRequestDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit  = (int) $request->input('length', 10);
        $start  = (int) $request->input('start', 0);
        $draw   = (int) $request->input('draw');
    
        $date_from   = $request->input('date_from');
        $date_to     = $request->input('date_to');
        $search      = $request->input('search1');
        $productType = $request->input('product_type');
        $stid = $request->input('store_id');
    
        /* ---------------------------
           Base Query
        ----------------------------*/
        $query = SaleProduct::whereIn('return_status', [1,2,3]);
    
        if ($store_id != 0) {
            $query->where('store_id', $store_id);
        }
        
        if (!empty($stid)) {
            $query->where('store_id', $stid);
        }
    
        /* ---------------------------
           Fetch & Unique
        ----------------------------*/
        $collection = $query->get()
            ->unique(function ($item) {
                return implode('|', [
                    $item->product_type,
                    $item->product_code,
                    $item->barcode_use,
                    $item->base_price,
                    $item->discount_amt,
                    $item->return_status,
                    $item->qty,
                    $item->no_of_glass,
                    $item->product_deatils
                ]);
            })
            ->values();
    
        /* ---------------------------
           Filters (Collection)
        ----------------------------*/
        if (!empty($productType)) {
            $collection = $collection->where('product_type', $productType);
        }
    
        if (!empty($date_from) || !empty($date_to)) {
        
               $collection = $collection->filter(function ($item) use ($date_from, $date_to) {
        
                $returnDate = Carbon::parse($item->return_date)->format('Y-m-d');
        
                if ($date_from && !$date_to) {
                    return $returnDate >= $date_from;
                }
        
                if (!$date_from && $date_to) {
                    return $returnDate <= $date_to;
                }
        
                if ($date_from && $date_to) {
                    return $returnDate >= $date_from &&
                           $returnDate <= $date_to;
                }
        
            });
        }
    
        if (!empty($search)) {
            $collection = $collection->filter(function ($item) use ($search) {
                return str_contains($item->order_no, $search) ||
                       str_contains($item->barcode_use ?? '', $search);
            });
        }
    
        /* ---------------------------
           Counts
        ----------------------------*/
        $recordsTotal    = $collection->count();
        $recordsFiltered = $recordsTotal;
    
        /* ---------------------------
           Pagination
        ----------------------------*/
        $paginated = $collection
            ->sortByDesc('id')
            ->slice($start, $limit)
            ->values();
    
        /* ---------------------------
           Data Formatting
        ----------------------------*/
        $data = [];
        $i = $start + 1;
    
        foreach ($paginated as $row) {
    
            $sale         = DB::table('tbl_sales')->where('sale_id', $row->sale_id)->first();
            $store        = Store::find($row->store_id);
            $returnStore  = Store::find($row->return_store);
            
            $encryptedId = base64_encode($row->sale_id);
    
            $data[] = [
                
                
                'sr_no' => $i++,
                'store_details' =>
                    '<strong>Store Name:</strong> ' . ($store->store_name ?? '-') .
                    '<br><strong>Received Store:</strong> ' . ($returnStore->store_name ?? '-'),
    
                'order_details' =>
                    '<strong>Order Date:</strong> ' . date('d M, Y h:i A', strtotime($sale->created_at)) .
                    '<br><strong>Order No:</strong> ' . $row->order_no,
    
                'customer_details' =>
                    '<strong>Customer Name:</strong> ' . $sale->cust_name .
                    '<br><strong>Mobile:</strong> ' . $sale->contact_no .
                    '<br><strong>Cust ID:</strong> ' . $sale->cust_id,
    
                'product_type' => $row->product_type .
                '<br><img src="https://speckart.apnashyam.com/storage/app/public/'.$row->return_photo.'" width="80"
                 class="return-img"
                 data-img="https://speckart.apnashyam.com/storage/app/public/'.$row->return_photo.'"
                 data-remark="'.htmlspecialchars($row->return_remark, ENT_QUOTES, 'UTF-8').'"
                 style="cursor:pointer;border-radius:6px;border:1px solid #ddd;">',
                'product_code' => $row->product_code,
                'description'  => $row->product_deatils,
                'amount'       => $row->sale_price*$row->qty,
                'return_date'  => date('d M, Y', strtotime($row->return_date)),
                'encryptedId'  => $encryptedId,
                'oid'  => $row->order_no,
                'ptype'  => $row->product_type,
                 'status_value' => $row->return_status, // ⭐ IMPORTANT
                'request_status' => 
                $row->return_status == 2
                    ? '<span class="badge badge-warning">Request Pending</span>'
                    : ($row->return_status == 3
                        ? '<span class="badge badge-danger">Request Rejected</span>'
                        : '<span class="badge badge-success">Approved</span>'),
                
                'store_id' => $row->store_id,
                'return_store' => $row->return_store,
                'return_remark'  => $row->return_remark,
                'pid' => $row->id,
            ];
        }
    
        /* ---------------------------
           Response
        ----------------------------*/
        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
    
    
    
    public function saleReturenStored(Request $request)
    {
        $request->validate([
            'uid' => 'required',
            'return_status' => 'required'
        ]);
    
        $user = auth()->user();
    
        DB::beginTransaction();
    
        try {
    
            /* ===============================
               GET PRODUCT
            =============================== */
            $product = DB::table('tbl_sales_product')
                ->where('id', $request->uid)
                ->first();
    
            if (!$product) {
                throw new \Exception('Product not found');
            }
    
            $product_id = $product->id;
            $storeid    = $product->store_id;
            $pay_method = 'Return';
    
            /* ===============================
               UPDATE RETURN STATUS
            =============================== */
    
            if ($product->product_type === 'Glass' && $product->qty == 2) {
    
                DB::table('tbl_sales_product')
                    ->where('order_no', $product->order_no)
                    ->where('product_type', $product->product_type)
                    ->where('product_code', $product->product_code)
                    ->where('product_deatils', $product->product_deatils)
                    ->where('no_of_glass', $product->no_of_glass)
                    ->update([
                        'return_status' => $request->return_status,
                        'return_remark_back' => $request->return_remark_back,
                        'return_request_updated_by' => $user->id,
                        'updated_at' => now(),
                    ]);
            } else {
    
                DB::table('tbl_sales_product')
                    ->where('id', $product_id)
                    ->update([
                        'return_status' => $request->return_status,
                        'return_remark_back' => $request->return_remark_back,
                        'return_request_updated_by' => $user->id,
                        'updated_at' => now(),
                    ]);
            }
    
            /* ===============================
               INVENTORY UPDATE
            =============================== */
    
            if ($request->return_status == 1) {
    
                /* ---------- SKIP LENS ---------- */
                if ($product->product_type !== 'Lens') {
    
                    /* ================= GLASS ================= */
                    if ($product->product_type === 'Glass') {
    
                        $glasses = DB::table('tbl_sales_product')
                            ->where('no_of_glass', $product->no_of_glass)
                            ->where('order_no', $product->order_no)
                            ->get();
    
                        foreach ($glasses as $glass) {
    
                            /* -------- RIGHT GLASS -------- */
                            if ($glass->right_purchase == 1) {
    
                                $inventory = DB::table('tbl_inventory_levels')
                                    ->where('product_details', $glass->right_glass)
                                    ->where('store_id', $glass->store_id)
                                    ->first();
    
                                if ($inventory) {
                                    DB::table('tbl_inventory_levels')
                                        ->where('id', $inventory->id)
                                        ->update([
                                            'available_quantity' => $inventory->available_quantity + 1,
                                            'updated_at' => now()
                                        ]);
                                }
    
                                DB::table('tbl_inventory_record')->insert([
                                    'product_code' => $glass->product_code,
                                    'product_id' => $glass->product_id,
                                    'product_type' => $glass->product_type,
                                    'product_details' => $glass->right_glass,
                                    'store_id' => $glass->store_id,
                                    'qty' => 1,
                                    'added_date' => date('Y-m-d'),
                                    'outward_status' => 2,
                                    'added_by' => $user->id,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }
    
                            /* -------- LEFT GLASS -------- */
                            if ($glass->left_purchase == 1) {
    
                                $inventory = DB::table('tbl_inventory_levels')
                                    ->where('product_details', $glass->left_glass)
                                    ->where('store_id', $glass->store_id)
                                    ->first();
    
                                if ($inventory) {
                                    DB::table('tbl_inventory_levels')
                                        ->where('id', $inventory->id)
                                        ->update([
                                            'available_quantity' => $inventory->available_quantity + 1,
                                            'updated_at' => now()
                                        ]);
                                }
    
                                DB::table('tbl_inventory_record')->insert([
                                    'product_code' => $glass->product_code,
                                    'product_id' => $glass->product_id,
                                    'product_type' => $glass->product_type,
                                    'product_details' => $glass->left_glass,
                                    'store_id' => $glass->store_id,
                                    'qty' => 1,
                                    'added_date' => date('Y-m-d'),
                                    'outward_status' => 2,
                                    'added_by' => $user->id,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }
    
                            
                        }
                    }
    
                    /* ================= OTHER PRODUCTS ================= */
                    else {
    
                        $inventory = DB::table('tbl_inventory_levels')
                            ->where('product_details', $product->product_deatils)
                            ->where('product_code', $product->product_code)
                            ->where('product_type', $product->product_type)
                            ->where('store_id', $product->store_id)
                            ->first();
    
                        if ($inventory) {
    
                            DB::table('tbl_inventory_levels')
                                ->where('id', $inventory->id)
                                ->update([
                                    'available_quantity' =>
                                        $inventory->available_quantity + $product->qty,
                                    'updated_at' => now()
                                ]);
    
                            DB::table('tbl_inventory_record')->insert([
                                'product_code' => $product->product_code,
                                'product_id' => $product->product_id,
                                'product_type' => $product->product_type,
                                'product_details' => $product->product_deatils,
                                'store_id' => $product->store_id,
                                'qty' => $product->qty,
                                'added_date' => date('Y-m-d'),
                                'outward_status' => 2,
                                'added_by' => $user->id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            }
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Return Product Request updated successfully.'
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong during return process.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saleReturnhistory()
    {
        $setting['page_title'] = 'Sale Return History';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/sale-return-list',$setting);
    }
    
    
    public function saleReturnDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit  = (int) $request->input('length', 10);
        $start  = (int) $request->input('start', 0);
        $draw   = (int) $request->input('draw');
    
        $date_from   = $request->input('date_from');
        $date_to     = $request->input('date_to');
        $search      = $request->input('search1');
        $productType = $request->input('product_type');
    
        /* ---------------------------
           Base Query
        ----------------------------*/
        $query = SaleProduct::where('return_status', 1);
    
        if ($store_id != 0) {
            $query->where('store_id', $store_id);
        }
    
        /* ---------------------------
           Fetch & Unique
        ----------------------------*/
        $collection = $query->get()
            ->unique(function ($item) {
                return implode('|', [
                    $item->product_type,
                    $item->product_code,
                    $item->barcode_use,
                    $item->base_price,
                    $item->discount_amt,
                    $item->return_status,
                    $item->qty,
                    $item->no_of_glass,
                    $item->product_deatils
                ]);
            })
            ->values();
    
        /* ---------------------------
           Filters (Collection)
        ----------------------------*/
        if (!empty($productType)) {
            $collection = $collection->where('product_type', $productType);
        }
    
        if (!empty($date_from) && !empty($date_to)) {
            $collection = $collection->filter(function ($item) use ($date_from, $date_to) {
        
                $returnDate = Carbon::parse($item->return_date)->format('Y-m-d');
        
                return $returnDate >= $date_from &&
                       $returnDate <= $date_to;
            });
        }
    
        if (!empty($search)) {
            $collection = $collection->filter(function ($item) use ($search) {
                return str_contains($item->order_no, $search) ||
                       str_contains($item->barcode_use ?? '', $search);
            });
        }
    
        /* ---------------------------
           Counts
        ----------------------------*/
        $recordsTotal    = $collection->count();
        $recordsFiltered = $recordsTotal;
    
        /* ---------------------------
           Pagination
        ----------------------------*/
        $paginated = $collection
            ->sortByDesc('return_date')
            ->slice($start, $limit)
            ->values();
    
        /* ---------------------------
           Data Formatting
        ----------------------------*/
        $data = [];
        $i = $start + 1;
    
        foreach ($paginated as $row) {
    
            $sale         = DB::table('tbl_sales')->where('sale_id', $row->sale_id)->first();
            $store        = Store::find($row->store_id);
            $returnStore  = Store::find($row->return_store);
            
            $encryptedId = base64_encode($row->sale_id);
    
            $data[] = [
                'sr_no' => $i++,
                'store_details' =>
                    '<strong>Store Name:</strong> ' . ($store->store_name ?? '-') .
                    '<br><strong>Received Store:</strong> ' . ($returnStore->store_name ?? '-'),
    
                'order_details' =>
                    '<strong>Order Date:</strong> ' . date('d M, Y h:i A', strtotime($sale->created_at)) .
                    '<br><strong>Order No:</strong> ' . $row->order_no,
    
                'customer_details' =>
                    '<strong>Customer Name:</strong> ' . $sale->cust_name .
                    '<br><strong>Mobile:</strong> ' . $sale->contact_no .
                    '<br><strong>Cust ID:</strong> ' . $sale->cust_id,
                    
                'return_payment_status' => 
                $row->return_payment_status == 0
                    ? '<span class="badge badge-info">Pement Pending</span>'
                    : ($row->return_payment_status == 3
                        ? '<span class="badge badge-danger">Request Rejected</span>'
                        : '<span class="badge badge-success">Payment Done</span>'),    
    
                'product_type' => $row->product_type,
                'product_code' => $row->product_code,
                'description'  => $row->product_deatils,
                'amount'       => $row->sale_price*$row->qty,
                'return_date'  => date('d M, Y', strtotime($row->return_date)),
                'encryptedId'  => $row->id,
                'encryptedIdss'  => $encryptedId,
                'store_id' => $row->store_id,
                'return_store' => $row->return_store,
                'gatepass_status' => $row->gatepass_status,
                'return_payment_statusss' => $row->return_payment_status,
                'order_no' => $row->order_no,
                'pid' => $row->id,
            ];
        }
    
        /* ---------------------------
           Response
        ----------------------------*/
        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
    
    
    
    public function saleReturenPaymentStored(Request $request)
    {
        $request->validate([
            'uid' => 'required',
            'pay_method' => 'required'
        ]);
    
        $user = auth()->user();
    
        DB::beginTransaction();
    
        try {
    
            /* ===============================
               GET PRODUCT
            =============================== */
            $product = DB::table('tbl_sales_product')
                ->where('id', $request->uid)
                ->first();
    
            if (!$product) {
                throw new \Exception('Product not found');
            }
    
            $product_id = $product->id;
            $storeid    = $product->store_id;
    
            /* ===============================
               UPDATE RETURN PAYMENT STATUS
            =============================== */
    
            if ($product->product_type === 'Glass' && $product->qty == 2) {
    
                /* ---- TOTAL AMOUNT ---- */
                $totalAmount = DB::table('tbl_sales_product')
                    ->where('order_no', $product->order_no)
                    ->where('product_type', $product->product_type)
                    ->where('product_code', $product->product_code)
                    ->where('product_deatils', $product->product_deatils)
                    ->where('no_of_glass', $product->no_of_glass)
                    ->sum(DB::raw('sale_price'));
    
                DB::table('tbl_sales_product')
                    ->where('order_no', $product->order_no)
                    ->where('product_type', $product->product_type)
                    ->where('product_code', $product->product_code)
                    ->where('product_deatils', $product->product_deatils)
                    ->where('no_of_glass', $product->no_of_glass)
                    ->update([
                        'pay_return_method' => $request->pay_method,
                        'pay_return_details' => $request->pay_deatils,
                        'return_payment_status' => 1,
                        'updated_at' => now(),
                    ]);
    
            } else {
    
                $totalAmount = $product->sale_price;
    
                DB::table('tbl_sales_product')
                    ->where('id', $product_id)
                    ->update([
                        'pay_return_method' => $request->pay_method,
                        'pay_return_details' => $request->pay_deatils,
                        'return_payment_status' => 1,
                        'updated_at' => now(),
                    ]);
            }
    
            /* ===============================
               PAYMENT PROCESS
            =============================== */
    
            if ($request->pay_method != 'Credit Issue') {
    
                
    
                /* -------- PAYMENT ENTRY -------- */
                SalePayment::create([
                    'sale_id'    => $product->sale_id,
                    'order_no'   => $product->order_no,
                    'pay_amount' => $totalAmount ?? 0,
                    'pay_details'=> $request->pay_deatils,
                    'pay_method' => $request->pay_method,
                    'pay_date'   => now()->format('Y-m-d'),
                    'added_by'   => $user->id,
                    'store_id'   => $storeid,
                    'pay_type'   => 2,
                ]);
    
            } else {
    
                /* ===============================
                   CUSTOMER CREDIT ISSUE
                =============================== */
    
                $tbl_sales = DB::table('tbl_sales')
                    ->where('sale_id', $product->sale_id)
                    ->first();
    
                $custData = DB::table('tbl_customer')
                    ->where('contact_no', $tbl_sales->contact_no)
                    ->first();
    
                $credit_amount = $totalAmount ?? 0;
    
                DB::table('tbl_customer')
                    ->where('contact_no', $custData->contact_no)
                    ->update([
                        'credit_amount' => $custData->credit_amount + $credit_amount,
                        'updated_at' => now()
                    ]);
    
                DB::table('tbl_wallet_history')->insert([
                    'customer_id' => $custData->customer_id,
                    'contact_no'  => $custData->contact_no,
                    'credit'      => $credit_amount,
                    'order_no'    => $product->order_no,
                    'store_id'    => $storeid,
                    'added_by'    => $user->id,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Return payment updated successfully.'
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong during return process.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    
    public function creategatepass($id)
    {
        
        $user = auth()->user();
    
        DB::beginTransaction();
    
        try {
    
            /* ===============================
               GET PRODUCT
            =============================== */
            $product = DB::table('tbl_sales_product')
                ->where('id', $id)
                ->first();
    
            if (!$product) {
                throw new \Exception('Product not found');
            }
    
            $product_id = $product->id;
            $storeid    = $product->store_id;
            $pay_method = 'Return';
    
            /* ===============================
               UPDATE RETURN STATUS
            =============================== */
    
            if ($product->product_type === 'Glass' && $product->qty == 2) {
    
                DB::table('tbl_sales_product')
                    ->where('order_no', $product->order_no)
                    ->where('product_type', $product->product_type)
                    ->where('product_code', $product->product_code)
                    ->where('product_deatils', $product->product_deatils)
                    ->where('no_of_glass', $product->no_of_glass)
                    ->update([
                        'gatepass_status' => 1,
                        'gatepass_create_date' => now(),
                    ]);
            } else {
    
                DB::table('tbl_sales_product')
                    ->where('id', $product_id)
                    ->update([
                        'gatepass_status' => 1,
                        'gatepass_create_date' => now(),
                    ]);
            }
    

    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Gatepass create successfully.'
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong during Gatepass process.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function gatepassHistory()
    {
        $setting['page_title'] = 'Gatepass List';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/gatepass-list',$setting);
    }
    
    

    
    public function getgatepassdata(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $date_from    = $request->input('date_from');
        $date_to      = $request->input('date_to');
        $product_type = $request->input('product_type');
        $search       = $request->input('search');
        $stid         = $request->input('store_id');
    
        /* =====================================================
           BASE QUERY
        ======================================================*/
        $query = SaleProduct::where('gatepass_status', 1);
    
        // Login store filter
        if ($store_id != 0) {
            $query->where('return_store', $store_id);
        }
    
        // Selected store filter
        if (!empty($stid)) {
            $query->where('return_store', $stid);
        }
    
        /* =====================================================
           FETCH DATA
        ======================================================*/
        $collection = $query->get();
    
        /* =====================================================
           UNIQUE RECORDS
        ======================================================*/
        $collection = $collection->unique(function ($item) {
            return implode('|', [
                $item->product_type,
                $item->product_code,
                $item->barcode_use,
                $item->base_price,
                $item->discount_amt,
                $item->return_status,
                $item->qty,
                $item->no_of_glass,
                $item->product_deatils
            ]);
        })->values();
    
        /* =====================================================
           PRODUCT TYPE FILTER
        ======================================================*/
        if (!empty($product_type)) {
            $collection = $collection->where('product_type', $product_type);
        }
    
        /* =====================================================
           DATE FILTER
        ======================================================*/
        if (!empty($date_from) || !empty($date_to)) {
    
            $collection = $collection->filter(function ($item) use ($date_from, $date_to) {
    
                if (!$item->gatepass_create_date) {
                    return false;
                }
    
                $returnDate = Carbon::parse($item->gatepass_create_date)
                    ->format('Y-m-d');
    
                if ($date_from && !$date_to) {
                    return $returnDate >= $date_from;
                }
    
                if (!$date_from && $date_to) {
                    return $returnDate <= $date_to;
                }
    
                if ($date_from && $date_to) {
                    return $returnDate >= $date_from &&
                           $returnDate <= $date_to;
                }
    
                return true;
            });
        }
    
        /* =====================================================
           SEARCH FILTER
        ======================================================*/
        if (!empty($search)) {
    
            $search = strtolower($search);
    
            $collection = $collection->filter(function ($item) use ($search) {
    
                return str_contains(strtolower($item->order_no), $search) ||
                       str_contains(strtolower($item->barcode_use ?? ''), $search);
            });
        }
    
        /* =====================================================
           COUNTS
        ======================================================*/
        $totalRequest   = $collection->count();
        $totalPending   = $collection->where('warehouse_status', 0)->count();
        $totalReceieved = $collection->where('warehouse_status', 1)->count();
    
        /* =====================================================
           HTML SECTION
        ======================================================*/
        $data = '
        <div class="row">
    
            <div class="col-xl-4 col-sm-6 d-flex mb-5 mb-xl-0">
                <div class="feature">
                    <i class="si si-briefcase primary feature-icon bg-primary"></i>
                </div>
                <div class="ml-3">
                    <small>Total Gatepass</small><br>
                    <h3 class="font-weight-semibold mb-0">'.$totalRequest.'</h3>
                </div>
            </div>
    
            <div class="col-xl-4 col-sm-6 d-flex mb-5 mb-xl-0">
                <div class="feature">
                    <i class="si si-layers feature-icon bg-warning"></i>
                </div>
                <div class="ml-3">
                    <small>Total Request Pending</small>
                    <h3 class="font-weight-semibold mb-0">'.$totalPending.'</h3>
                </div>
            </div>
    
            <div class="col-xl-4 col-sm-6 d-flex mb-5 mb-sm-0">
                <div class="feature">
                    <i class="fa fa-thumbs-down feature-icon bg-danger"></i>
                </div>
                <div class="ml-3">
                    <small>Total Received</small>
                    <h3 class="font-weight-semibold mb-0">'.$totalReceieved.'</h3>
                </div>
            </div>
    
        </div>';
    
        /* =====================================================
           RESPONSE
        ======================================================*/
        return response()->json([
            'status' => 'success',
            'requestdata_section' => $data
        ]);
    }
    
    
    public function gatepassDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit  = (int) $request->input('length', 10);
        $start  = (int) $request->input('start', 0);
        $draw   = (int) $request->input('draw');
    
        $date_from   = $request->input('date_from');
        $date_to     = $request->input('date_to');
        $search      = $request->input('search1');
        $productType = $request->input('product_type');
        $stid = $request->input('store_id');
    
        /* ---------------------------
           Base Query
        ----------------------------*/
        $query = SaleProduct::whereIn('gatepass_status', [1]);
    
        if ($store_id != 0) {
            $query->where('return_store', $store_id);
        }
        
        if (!empty($stid)) {
            $query->where('return_store', $stid);
        }
    
        /* ---------------------------
           Fetch & Unique
        ----------------------------*/
        $collection = $query->get()
            ->unique(function ($item) {
                return implode('|', [
                    $item->product_type,
                    $item->product_code,
                    $item->barcode_use,
                    $item->base_price,
                    $item->discount_amt,
                    $item->return_status,
                    $item->qty,
                    $item->no_of_glass,
                    $item->product_deatils
                ]);
            })
            ->values();
    
        /* ---------------------------
           Filters (Collection)
        ----------------------------*/
        if (!empty($productType)) {
            $collection = $collection->where('product_type', $productType);
        }
    
        if (!empty($date_from) || !empty($date_to)) {
        
               $collection = $collection->filter(function ($item) use ($date_from, $date_to) {
        
                $returnDate = Carbon::parse($item->gatepass_create_date)->format('Y-m-d');
        
                if ($date_from && !$date_to) {
                    return $returnDate >= $date_from;
                }
        
                if (!$date_from && $date_to) {
                    return $returnDate <= $date_to;
                }
        
                if ($date_from && $date_to) {
                    return $returnDate >= $date_from &&
                           $returnDate <= $date_to;
                }
        
            });
        }
    
        if (!empty($search)) {
            $collection = $collection->filter(function ($item) use ($search) {
                return str_contains($item->order_no, $search) ||
                       str_contains($item->barcode_use ?? '', $search);
            });
        }
    
        /* ---------------------------
           Counts
        ----------------------------*/
        $recordsTotal    = $collection->count();
        $recordsFiltered = $recordsTotal;
    
        /* ---------------------------
           Pagination
        ----------------------------*/
        $paginated = $collection
            ->sortByDesc('id')
            ->slice($start, $limit)
            ->values();
    
        /* ---------------------------
           Data Formatting
        ----------------------------*/
        $data = [];
        $i = $start + 1;
    
        foreach ($paginated as $row) {
    
            $sale         = DB::table('tbl_sales')->where('sale_id', $row->sale_id)->first();
            $store        = Store::find($row->store_id);
            $returnStore  = Store::find($row->return_store);
            $encryptedId = base64_encode($row->sale_id);
            

            $data[] = [
                'sr_no' => $i++,
                'store_details' =>
                    '<strong>Store Name:</strong> ' . ($store->store_name ?? '-') .
                    '<br><strong>Received Store:</strong> ' . ($returnStore->store_name ?? '-'),
    
                'order_details' =>
                    '<strong>Order Date:</strong> ' . date('d M, Y h:i A', strtotime($sale->created_at)) .
                    '<br><strong>Order No:</strong> ' . $row->order_no,
    
                'customer_details' =>
                    '<strong>Customer Name:</strong> ' . $sale->cust_name .
                    '<br><strong>Mobile:</strong> ' . $sale->contact_no .
                    '<br><strong>Cust ID:</strong> ' . $sale->cust_id,
                
                 'responsive_id' => '',
                'product_type' => $row->product_type,
                'product_code' => $row->product_code,
                'description'  => $row->product_deatils,
                'amount'       => $row->sale_price*$row->qty,
                'gatepass_create_date'  => date('d M, Y', strtotime($row->gatepass_create_date)),
                'encryptedId'  => $encryptedId,
                'oid'  => $row->order_no,
                'ptype'  => $row->product_type,
                 'status_value' => $row->warehouse_status, // ⭐ IMPORTANT
                'warehouse_status' => 
                $row->warehouse_status == 0
                    ? '<span class="badge badge-danger">Not Recevied</span>'
                    : ($row->return_status == 3
                        ? '<span class="badge badge-success">Recevied</span>'
                        : '<span class="badge badge-success">Recevied</span>'),
                
                'store_id' => $row->store_id,
                'return_store' => $row->return_store,
                'pid' => $row->id,
            ];
        }
    
        /* ---------------------------
           Response
        ----------------------------*/
        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
    
    
    
    public function bulkConfirmGatepass(Request $request)
    {
        $user = auth()->user();
        $barcode_ids = $request->ids;
        $errorIDs = $successIds = 0;
        $errorIDs = count($barcode_ids);
        $productdetailsCount = DB::table('tbl_sales_product')->whereIn('id', $barcode_ids)->get();

        foreach ($productdetailsCount as $product) 
        {
            if ($product->product_type === 'Glass' && $product->qty == 2) {
    
                DB::table('tbl_sales_product')
                    ->where('order_no', $product->order_no)
                    ->where('product_type', $product->product_type)
                    ->where('product_code', $product->product_code)
                    ->where('product_deatils', $product->product_deatils)
                    ->where('no_of_glass', $product->no_of_glass)
                    ->update([
                        'warehouse_status' => 1,
                    ]);
            } else {
    
                DB::table('tbl_sales_product')
                    ->where('id', $product_id)
                    ->update([
                        'warehouse_status' => 1,
                    ]);
            }
           
            $successIds++;
            $errorIDs--;
        }
        return response()->json([
            'status'  => true,
            'code'  => '200',
            'message' => $successIds . ' Gatepass Status Updated',
        ]);
      
    }
    
    public function barcodeWiseDiscount()
    {
        $setting['page_title'] = 'Barcode Wise Discount';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/barcode-discount',$setting);
    }
    
    
    public function applyDiscountBarcode(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $user = auth()->user();
        
        $from_store = $request->from_store;
        $discount = $request->apply_discount;
        $items = $request->items;
    
        if (empty($items)) 
        {
            return response()->json(['success' => false, 'message' => 'No items.']);
        }
        DB::beginTransaction();
    
        try 
        {
            foreach ($items as $item) 
            {
                $transfer_barcode = DB::table('tbl_barcode')
                ->where('barcode_no', $item['barcode_no'])
                ->where('store_id', $from_store)
                ->update([
                    'retail_price'     => $item['retail_price'],
                    'discount'         => $discount,
                    'updated_at_discount' => now(),
                    'discount_updated_by' => $user->id
                ]);
            }
            
            DB::commit();
            return response()->json(['success' => true]);
		} 
		catch (\Exception $e) 
		{
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the apply discount process.',
                'error'   => $e->getMessage()
            ], 500);
        }    
    
        return response()->json(['success' => true]);
    }
    
    
    public function discountbarcodeDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        $search = $request->input('search_input');
        $product_type = $request->input('product_type');
        $storeid = $request->input('store_id');
        
        if ($store_id == '0') 
        {
            $query = DB::table('tbl_barcode')
            ->where('discount', '!=', NULL);
        }
        else
        {
            $query = DB::table('tbl_barcode')
            ->where('store_id', $store_id)
            ->where('discount', '!=', NULL);
        }
            
        
    
        // Total records (before filtering)
        $totalData = $query->count();
    

         // Apply filters
        if ($product_type != '') {
            $query->where('product_type', $product_type);
        }
    
        if ($storeid != '') {
            $query->where('store_id', $storeid);
        }
    
        if ($search != '') {
            $search = trim($search);
            $searchValues = array_filter(array_map('trim', explode(',', $search)));
    
            if (count($searchValues) > 1 && count($searchValues) <= 100) {
                $query->where(function ($q) use ($searchValues) {
                    $q->whereIn('p_bill_no', $searchValues)
                      ->orWhereIn('product_code', $searchValues)
                      ->orWhereIn('barcode_no', $searchValues);
                });
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('p_bill_no', 'like', "%{$search}%")
                      ->orWhere('product_code', 'like', "%{$search}%")
                      ->orWhere('barcode_no', 'like', "%{$search}%");
                });
            }
        }
    
        // Count after filtering
        $totalFiltered = $query->count();
    
        // Pagination & ordering
        $templates = $query->offset($start)
            ->limit($limit)
            ->orderBy('id', 'ASC')
            ->get();
    
        // Prepare data
        $data = [];
        foreach ($templates as $template) {
            $tbl_purchase = DB::table('tbl_purchase')->where('purchase_id', $template->purchase_id)->first();
            $tbl_purchase_deatils = DB::table('tbl_purchase_deatils')->where('id', $template->purchase_product_id)->first();
            $tbl_store = DB::table('tbl_store')->where('id', $template->store_id)->first();

            
            if($template->refrence_type == '1')
            {
                $bstatus = '<span class="badge badge-info">In Store</span>';
                
            }elseif($template->refrence_type == '2')
            {
                $bstatus = '<span class="badge badge-success">Sale</span>';
                
            }
            
            if($template->product_type == 'Lens')
            {

                $description = '<a href="#"><strong style="color:red"> Box per peice :  '.$tbl_purchase_deatils->perbox_detail.'</strong></a>';
            }
            else
            {
                $description = '';
            }
            $discount_updated_by = User::find($template->discount_updated_by);
            $encryptedId = base64_encode($template->id);
            $nestedData['store_name'] = $tbl_store->store_name ?? '';
            $nestedData['responsive_id'] = '';
            $nestedData['barcode_id'] = $template->id;
            $nestedData['barcode'] = $template->barcode_no;
            $nestedData['purchase_details'] =
                'Purchase Date :' . $template->purchase_date .
                '<br>Purchase Bill Number :<span class="badge badge-info">' . $template->p_bill_no . '</span>' .
                '<br>Supplier :' . ($tbl_purchase->supplier_name ?? '');
             $nestedData['product_details'] = 'Product  : <span class="badge badge-info">'.$tbl_purchase_deatils->product_type.'</span><BR>Product Code : '.$tbl_purchase_deatils->product_code.'<BR>Product ID : '.$tbl_purchase_deatils->product_id.'<BR>Description  :'.$tbl_purchase_deatils->product_details.'<BR>'.$description;
            $nestedData['purchase_price'] = 'Rs ' . $template->purchase_price;
            $nestedData['retail_price'] = 'Rs ' . $template->retail_price;
            $nestedData['pdeatils'] = $tbl_purchase_deatils->product_details ?? '';
            $nestedData['store_status'] = $bstatus;
            $nestedData['encryptedId'] = $encryptedId;
            $nestedData['retail_pricee'] =  $template->retail_price;
            $nestedData['product_detailsss'] =  $template->product_details;
            $nestedData['discount'] =  $template->discount;
            $nestedData['discount_updated_by'] =  $discount_updated_by->name;
            $nestedData['updated_at_discount']  = date("d-m-Y h:i:A", strtotime($template->updated_at_discount));
            
    
            $data[] = $nestedData;
        }
    
        // Return JSON
        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ];
    
        return response()->json($json_data);
    }
    
    
    public function bulkBarcodeDiscount(Request $request)
    {
        $user = auth()->user();
    
        $validator = Validator::make($request->all(), [
            'myFile'   => 'required|file|mimes:csv,txt',
            'store_id' => 'required|integer|exists:tbl_store,id',
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        $file = $request->file('myFile');
        $storeId = $request->store_id;
        $rows = Excel::toArray(null, $file)[0] ?? [];
    
        if (count($rows) <= 1) {
            return back()->with('error', 'The uploaded file is empty or missing data.');
        }
    
        $header = $rows[0];
        $dataRows = array_slice($rows, 1);
        $dataRows = array_filter($dataRows, fn($row) => !empty(array_filter($row)));
    
        $validProducts = [];
        $invalidProducts = [];
    

        foreach ($dataRows as $rowIndex => $data) 
        {
            $productErrors = [];
            $productData = [];
    
            $barcodeVal = trim((string)($data[0] ?? ''));
            $discountVal = trim((string)($data[1] ?? ''));

            if (empty($barcodeVal)) {
                $productErrors[] = 'Barcode is required.';
            }
            if (empty($discountVal)) {
                $productErrors[] = "Discount is required.";
            }
            
            $existing = DB::table('tbl_barcode')
            ->where('barcode_no', $barcodeVal)
            ->where('store_id', $storeId)
            ->first();
                    
            if (empty($existing)) {
                $productErrors[] = "Barcode not found this store.";
            }        

            $productData = 
            [
                'barcode' => $data[0],
                'discount_price' => $data[1],
   
            ];

            if (empty($productErrors)) 
            {
                $validProducts[] = ['data' => $productData, 'original' => $data];
            }
            else 
            {
                $data[] = implode(', ', $productErrors);
                $invalidProducts[] = array_combine(array_merge($header, ['Error']), $data);
            }
        }
    
        DB::beginTransaction();
        try {
            foreach ($validProducts as $productWrap) {
                $data = $productWrap['data'];
                $barcode = $data['barcode'] ?? null;
                if (!$barcode) continue;
                
                $existing = DB::table('tbl_barcode')
                ->where('barcode_no', $barcode)
                ->where('store_id', $storeId)
                ->first();
                
                if ($existing) {
       
                    DB::table('tbl_barcode')
                    ->where('id', $existing->id)
                    ->update([
                        'discount' => $data['discount_price'],
                        'updated_at_discount' => now(),
                        'discount_updated_by' => $user->id
                    ]);
                }    
                
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Database operation failed: ' . $e->getMessage());
        }
    
        if (!empty($invalidProducts))
        {
            $errorFileName = 'invalid_barcode_' . time() . '.xlsx';
            Excel::store(new class($invalidProducts) implements FromCollection, WithHeadings {
                protected $rows;
                public function __construct($rows) { $this->rows = $rows; }
                public function collection() { return collect($this->rows); }
                public function headings(): array { return array_keys($this->rows[0] ?? []); }
            }, $errorFileName, 'public');
    
            return response()->download(storage_path("app/public/{$errorFileName}"));
        }
    
        return back()->with('success', 'Bulk Barcode Discount uploaded and processed successfully.');
    }
    
    
    
    public function productidWiseDiscount()
    {
        $setting['page_title'] = 'Product Id Wise Discount';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/productid-discount',$setting);
    }
    
    
    public function discountproductDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        $search = $request->input('search_input');
        $product_type = $request->input('product_type');


        $query = DB::table('tbl_product_code')
        ->where('status', '1');
        

        // Total records (before filtering)
        $totalData = $query->count();
    

         // Apply filters
        if ($product_type != '') {
            $query->where('product_type', $product_type);
        }
    

    
        if ($search != '') {
            $search = trim($search);
            $searchValues = array_filter(array_map('trim', explode(',', $search)));
    
            if (count($searchValues) > 1 && count($searchValues) <= 100) {
                $query->where(function ($q) use ($searchValues) {
                     $q->whereIn('product_code', $searchValues)
                      ->orWhereIn('product_type', $searchValues)
                      ->orWhereIn('productdetails', $searchValues);
                });
            } else {
                $query->where(function ($q) use ($search) {
                      $q->where('product_code', 'like', "%{$search}%")
                      ->orWhere('product_type', 'like', "%{$search}%")
                      ->orWhere('productdetails', 'like', "%{$search}%");
                });
            }
        }
    
        // Count after filtering
        $totalFiltered = $query->count();
    
        // Pagination & ordering
        $templates = $query->offset($start)
            ->limit($limit)
            ->orderBy('id', 'ASC')
            ->get();
    
        // Prepare data
        $data = [];
        foreach ($templates as $template) 
        {

            $discount_updated_by = User::find($template->discount_updated_by);
            if(empty($discount_updated_by))
            {
                $discount_updated_by = '';
            }
            else
            {
               $discount_updated_by =  $discount_updated_by->name;
            }
            
            
            if(empty($template->updated_at_discount))
            {
                $updated_at_discount = '';
            }
            else
            {
               $updated_at_discount =   date("d-m-Y h:i:A", strtotime($template->updated_at_discount));
            }
            
            $nestedData['id'] = $template->id;
            
            $nestedData['product_id'] = $template->product_id;
            $nestedData['product_code'] = $template->product_code;
            $nestedData['productdetails'] = $template->productdetails;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['discount'] =  $template->discount;
            $nestedData['discount_updated_by'] =  $discount_updated_by;
            $nestedData['updated_at_discount']  = $updated_at_discount;
            
    
            $data[] = $nestedData;
        }
    
        // Return JSON
        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ];
    
        return response()->json($json_data);
    }
    
    
    public function updateDiscount(Request $request)
    {
        try {
    
            DB::table('tbl_product_code')
                ->where('id', $request->id)
                ->update([
                    'discount' => $request->discount,
                    'discount_updated_by' => auth()->id(),
                    'updated_at_discount' => now()
                ]);
    
            return response()->json([
                'status' => 'success'
            ]);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
    
    
    public function bulkProductDiscount(Request $request)
    {
        $user = auth()->user();
    
        $validator = Validator::make($request->all(), [
            'myFile'   => 'required|file|mimes:csv,txt',
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        $file = $request->file('myFile');
        $rows = Excel::toArray(null, $file)[0] ?? [];
    
        if (count($rows) <= 1) {
            return back()->with('error', 'The uploaded file is empty or missing data.');
        }
    
        $header = $rows[0];
        $dataRows = array_slice($rows, 1);
        $dataRows = array_filter($dataRows, fn($row) => !empty(array_filter($row)));
    
        $validProducts = [];
        $invalidProducts = [];
    

        foreach ($dataRows as $rowIndex => $data) 
        {
            $productErrors = [];
            $productData = [];
    
            $productVal = trim((string)($data[0] ?? ''));
            $discountVal = trim((string)($data[1] ?? ''));

            if (empty($productVal)) {
                $productErrors[] = 'Productid is required.';
            }
            if (empty($discountVal)) {
                $productErrors[] = "Discount is required.";
            }
            
            $existing = DB::table('tbl_product_code')
            ->where('product_id', $productVal)
            ->first();
                    
            if (empty($existing)) {
                $productErrors[] = "Product Id not found.";
            }        

            $productData = 
            [
                'product_id' => $data[0],
                'discount_price' => $data[1],
   
            ];

            if (empty($productErrors)) 
            {
                $validProducts[] = ['data' => $productData, 'original' => $data];
            }
            else 
            {
                $data[] = implode(', ', $productErrors);
                $invalidProducts[] = array_combine(array_merge($header, ['Error']), $data);
            }
        }
    
        DB::beginTransaction();
        try {
            foreach ($validProducts as $productWrap) {
                $data = $productWrap['data'];
                $product_id = $data['product_id'] ?? null;
                if (!$product_id) continue;
                
                $existing = DB::table('tbl_product_code')
                ->where('product_id', $product_id)
                ->first();
                
                if ($existing) {
       
                    DB::table('tbl_product_code')
                    ->where('id', $existing->id)
                    ->update([
                        'discount' => $data['discount_price'],
                        'updated_at_discount' => now(),
                        'discount_updated_by' => $user->id
                    ]);
                }    
                
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Database operation failed: ' . $e->getMessage());
        }
    
        if (!empty($invalidProducts))
        {
            $errorFileName = 'invalid_product_' . time() . '.xlsx';
            Excel::store(new class($invalidProducts) implements FromCollection, WithHeadings {
                protected $rows;
                public function __construct($rows) { $this->rows = $rows; }
                public function collection() { return collect($this->rows); }
                public function headings(): array { return array_keys($this->rows[0] ?? []); }
            }, $errorFileName, 'public');
    
            return response()->download(storage_path("app/public/{$errorFileName}"));
        }
    
        return back()->with('success', 'Bulk Product Discount uploaded and processed successfully.');
    }
    
    
    public function brandWiseDiscount()
    {
        $setting['page_title'] = 'Brand Wise Discount';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/brand-discount',$setting);
    }
    
    
    public function discountbrandDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        $search = $request->input('search_input');
        $product_type = $request->input('product_type');


        $query = DB::table('tbl_brand')
        ->where('discount', '!=', NULL)
        ->where('status', '1');
        

        // Total records (before filtering)
        $totalData = $query->count();
    

         // Apply filters
        if ($product_type != '') {
            $query->where('product_type', $product_type);
        }
    

    
        if ($search != '') {
            $search = trim($search);
            $searchValues = array_filter(array_map('trim', explode(',', $search)));
    
            if (count($searchValues) > 1 && count($searchValues) <= 100) {
                $query->where(function ($q) use ($searchValues) {
                     $q->whereIn('brand_name', $searchValues)
                      ->orWhereIn('product_type', $searchValues);
                });
            } else {
                $query->where(function ($q) use ($search) {
                      $q->where('brand_name', 'like', "%{$search}%")
                      ->orWhere('product_type', 'like', "%{$search}%");
                });
            }
        }
    
        // Count after filtering
        $totalFiltered = $query->count();
    
        // Pagination & ordering
        $templates = $query->offset($start)
            ->limit($limit)
            ->orderBy('brand_id', 'ASC')
            ->get();
    
        // Prepare data
        $data = [];
        foreach ($templates as $template) 
        {

            $discount_updated_by = User::find($template->discount_updated_by);
            
            $nestedData['brand_name'] = $template->brand_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['discount'] =  $template->discount;
            $nestedData['discount_updated_by'] =  $discount_updated_by->name;
            $nestedData['updated_at_discount']  = date("d-m-Y h:i:A", strtotime($template->updated_at_discount));
            
    
            $data[] = $nestedData;
        }
    
        // Return JSON
        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ];
    
        return response()->json($json_data);
    }
    
    
    public function bulkBrandDiscount(Request $request)
    {
        $user = auth()->user();
    
        $validator = Validator::make($request->all(), [
            'myFile'   => 'required|file|mimes:csv,txt',
            'product_type' => 'required',
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        $file = $request->file('myFile');
        $product_type = $request->product_type;
        $rows = Excel::toArray(null, $file)[0] ?? [];
    
        if (count($rows) <= 1) {
            return back()->with('error', 'The uploaded file is empty or missing data.');
        }
    
        $header = $rows[0];
        $dataRows = array_slice($rows, 1);
        $dataRows = array_filter($dataRows, fn($row) => !empty(array_filter($row)));
    
        $validProducts = [];
        $invalidProducts = [];
    

        foreach ($dataRows as $rowIndex => $data) 
        {
            $productErrors = [];
            $productData = [];
    
            $brandVal = trim((string)($data[0] ?? ''));
            $discountVal = trim((string)($data[1] ?? ''));

            if (empty($brandVal)) {
                $productErrors[] = 'Brand is required.';
            }
            if (empty($discountVal)) {
                $productErrors[] = "Discount is required.";
            }
            
            $existing = DB::table('tbl_brand')
            ->where('brand_name', $brandVal)
            ->where('product_type', $product_type)
            ->first();
                    
            if (empty($existing)) {
                $productErrors[] = "Brand not found.";
            }        

            $productData = 
            [
                'brand' => $data[0],
                'discount_price' => $data[1],
   
            ];

            if (empty($productErrors)) 
            {
                $validProducts[] = ['data' => $productData, 'original' => $data];
            }
            else 
            {
                $data[] = implode(', ', $productErrors);
                $invalidProducts[] = array_combine(array_merge($header, ['Error']), $data);
            }
        }
    
        DB::beginTransaction();
        try {
            foreach ($validProducts as $productWrap) {
                $data = $productWrap['data'];
                $brand = $data['brand'] ?? null;
                if (!$brand) continue;
                
                $existing = DB::table('tbl_brand')
                ->where('brand_name', $brand)
                ->where('product_type', $product_type)
                ->first();
                
                if ($existing) {
       
                    DB::table('tbl_brand')
                    ->where('brand_id', $existing->brand_id)
                    ->update([
                        'discount' => $data['discount_price'],
                        'updated_at_discount' => now(),
                        'discount_updated_by' => $user->id
                    ]);
                }    
                
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Database operation failed: ' . $e->getMessage());
        }
    
        if (!empty($invalidProducts))
        {
            $errorFileName = 'invalid_brand_' . time() . '.xlsx';
            Excel::store(new class($invalidProducts) implements FromCollection, WithHeadings {
                protected $rows;
                public function __construct($rows) { $this->rows = $rows; }
                public function collection() { return collect($this->rows); }
                public function headings(): array { return array_keys($this->rows[0] ?? []); }
            }, $errorFileName, 'public');
    
            return response()->download(storage_path("app/public/{$errorFileName}"));
        }
    
        return back()->with('success', 'Bulk Brand Discount uploaded and processed successfully.');
    }
    
    
    public function orderItemTracking()
    {
        $setting['page_title'] = 'Order Item Tracking';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/order-item-tracking',$setting);
    }
    
    
    public function itemTrackingList(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
    
        $order_no      = $request->input('order_no');
        $contact_no    = $request->input('contact_no');
        $date_from     = $request->input('date_from');
        $date_to       = $request->input('date_to');
        $cust_name     = $request->input('cust_name');
        $membership_id = $request->input('membership_id');
        $stid          = $request->input('store_id');
    
        /* -------------------------------------------------
           Base Query with JOIN
        ------------------------------------------------- */
        $templates = DB::table('tbl_sales_product as tsp')
            ->join('tbl_sales as ts', 'ts.order_no', '=', 'tsp.order_no')
            ->where('tsp.return_status', '0');
    
        /* -------------------------------------------------
           Store Filter
        ------------------------------------------------- */
        if ($store_id != 0) {
            $templates->where('ts.store_id', $store_id);
        }
    
        /* -------------------------------------------------
           Filters
        ------------------------------------------------- */
        if (!empty($order_no)) {
            $templates->where('ts.order_no', $order_no);
        }
    
        if (!empty($cust_name)) {
            $templates->where('ts.cust_name', 'like', '%' . $cust_name . '%');
        }
    
        if (!empty($membership_id)) {
            $templates->where('ts.membership_id', $membership_id);
        }
    
        if (!empty($contact_no)) {
            $templates->where('ts.contact_no', 'like', '%' . $contact_no . '%');
        }
    
        if (!empty($date_from) && !empty($date_to)) {
            $templates->whereBetween('tsp.created_at', [
                $date_from,
                $date_to . ' 23:59:59'
            ]);
        }
    
        /* -------------------------------------------------
           Count Records
        ------------------------------------------------- */
        $totalData = (clone $templates)->count();
        $totalFiltered = $totalData;
    
        /* -------------------------------------------------
           Pagination + Data
        ------------------------------------------------- */
        $templates = $templates
            ->select(
                'tsp.*',
                'ts.cust_name',
                'ts.contact_no',
                'ts.membership_id'
            )
            ->offset($start)
            ->limit($limit)
            ->orderBy('tsp.id', 'DESC')
            ->get();
    
        /* -------------------------------------------------
           Format Data for DataTable
        ------------------------------------------------- */
        $data = [];
    
        foreach ($templates as $template) {
    
            $store = Store::find($template->store_id);
    
            if ($template->product_type == 'Glass') {
                $rightleft = $template->right_purchase == null
                    ? '<span class="badge badge-danger">Left</span>'
                    : '<span class="badge badge-danger">Right</span>';
         
            } else {
                $rightleft = '';
            }
            
            $brand = $template->package_id == null
                    ? ''
                    : '<span class="badge badge-info">INHOUSE BRAND</span><BR>INDEX:'.$template->product_index ;
                    
                    
    
            $nestedData['responsive_id'] = '';
            $nestedData['pid'] = $template->id;
            $nestedData['store_name'] = $store->store_name ?? '-';
    
            $nestedData['order_details'] =
                'Order Date : ' . $template->created_at .
                '<br>Order Number : ' . $template->order_no;
    
            $nestedData['customer_name'] =
                $template->cust_name . '<br>' . $template->contact_no;
    
            $nestedData['product_details'] =
                'Product Type : ' . $template->product_type .
                '<br>Product Code : ' . $template->product_code .
                '<br>Description : ' . $template->product_deatils .
                '<br>' . $rightleft. $brand ;
    
            $nestedData['courier'] = $template->courier;
            $nestedData['tracking_status'] = $template->product_tracking;
            $nestedData['product_index'] = $template->product_index;
            $nestedData['oid'] = $template->order_no;
    
            $nestedData['encryptedId'] = base64_encode($template->sale_id);
    
            $data[] = $nestedData;
        }
    
        /* -------------------------------------------------
           JSON Response
        ------------------------------------------------- */
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }

    public function getTrackingHistory(Request $request)
    {
        $pid = $request->pid;
    
        if (!$pid) {
            return response()->json([
                'error' => 'Product ID is required'
            ], 400);
        }
    
        $saleProduct = DB::table('tbl_sales_product')
            ->where('id', $pid)
            ->first();
    
        if (!$saleProduct) {
            return response()->json([
                'error' => 'Sale product not found'
            ], 404);
        }
    
        $trackingList = DB::table('tbl_order_item_tracking')
            ->where('sale_product_id', $pid)
            ->orderBy('id', 'DESC')
            ->get();
    
        return response()->json([
            'sale_product' => $saleProduct,
            'tracking_history' => $trackingList
        ]);
    }
    
    
    
    public function trackingStatusUpdate(Request $request)
    {
        $user = auth()->user();
        
        if(empty($request->sender_id))
        {
            $tbl_sales_product= DB::table('tbl_sales_product')->where('id', $request->uid)->first();
                    
    
            $update_status =  DB::table('tbl_sales_product')->where('id', $tbl_sales_product->id)->update([
                'product_tracking'      => $request->tracking_status,
            ]);
            
            
            $tracking_activity = DB::table('tbl_order_item_tracking')->insert([
                'order_no' => $tbl_sales_product->order_no,
                'sale_product_id' => $tbl_sales_product->id,
                'product_code' => $tbl_sales_product->product_code,
                'product_type' => $tbl_sales_product->product_type,
                'description' => $tbl_sales_product->product_deatils,
                'tracking_status' => $request->tracking_status,
                'store_id' => $tbl_sales_product->store_id,
                'tracking_comment' => $request->tracking_comment,
            ]);
            
        }
        else
        {
            
            $sender_ids = explode(',', $request->sender_id);
        
            $successIds = 0;
            $errorIDs   = count($sender_ids);
        
            $tbl_sales_products = DB::table('tbl_sales_product')
                ->whereIn('id', $sender_ids)
                ->get();
        
            foreach ($tbl_sales_products as $tbl_sales_product)
            {
                DB::table('tbl_sales_product')
                    ->where('id', $tbl_sales_product->id)
                    ->update([
                        'product_tracking' => $request->tracking_status,
                    ]);
        
                DB::table('tbl_order_item_tracking')->insert([
                    'order_no'          => $tbl_sales_product->order_no,
                    'sale_product_id'   => $tbl_sales_product->id,
                    'product_code'      => $tbl_sales_product->product_code,
                    'product_type'      => $tbl_sales_product->product_type,
                    'description'       => $tbl_sales_product->product_deatils,
                    'tracking_status'   => $request->tracking_status,
                    'store_id'          => $tbl_sales_product->store_id,
                    'tracking_comment'  => $request->tracking_comment,
                ]);
        
                $successIds++;
                $errorIDs--;
            }
        }
        


        return response()->json([
            'status'  => 'success',
            'message' => 'Tracking status update  successfully',
        ]);
    }
    
    
    public function updateIndex(Request $request)
    {
        // 1️⃣ Validate required inputs
        $request->validate([
            'product_index' => 'required',
            'sender_ids'    => 'required|string',
        ]);
    
        // 2️⃣ Make sure sender IDs are not empty
        $senderIds = array_filter(explode(',', $request->sender_ids));
    
        if (empty($senderIds)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No products selected',
            ], 422);
        }
    
        DB::beginTransaction();
    
        try {
            // 3️⃣ Fetch products
            $salesProducts = DB::table('tbl_sales_product')
                ->whereIn('id', $senderIds)
                ->get();
    
            foreach ($salesProducts as $product) {
    
                // 4️⃣ Skip if product_index already updated
                if (
                    !empty($product->product_index) &&
                    (string) $product->product_index === (string) $request->product_index
                ) {
                    continue;
                }
    
                // 5️⃣ Build product details safely
                // Remove old index if already present at the end
                $baseDetails = preg_replace('/-\d+(\.\d+)?$/', '', $product->product_deatils);
                $productDetails = $baseDetails . '-' . $request->product_index;
    
                // 6️⃣ Update sales product
                DB::table('tbl_sales_product')
                    ->where('id', $product->id)
                    ->update([
                        'product_index'   => $request->product_index,
                        'product_deatils' => $productDetails,
                        'updated_at'      => now(),
                    ]);
    
                // 7️⃣ Inventory query (row-level lock)
                $inventoryQuery = DB::table('tbl_inventory_levels')
                    ->where('product_type', $product->product_type)
                    ->where('product_code', $product->product_code)
                    ->where('product_details', $productDetails)
                    ->where('product_id', $product->product_id)
                    ->where('store_id', $product->store_id);
    
                $inventory = $inventoryQuery->lockForUpdate()->first();
    
                if ($inventory) {
                    // 8️⃣ Decrement inventory safely
                    $inventoryQuery->decrement('available_quantity', 1);
                } else {
                    // 9️⃣ Insert new inventory record
                    DB::table('tbl_inventory_levels')->insert([
                        'product_code'       => $product->product_code,
                        'product_id'         => $product->product_id,
                        'product_type'       => $product->product_type,
                        'product_details'    => $productDetails,
                        'store_id'           => $product->store_id,
                        'available_quantity' => -1,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }
    
            DB::commit();
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Index updated successfully',
            ]);
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    
    
    
    public function createbulkinvoice()
    {
        $setting['page_title'] = 'Create Bulk Invoice';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/create-bulk-invoice',$setting);
    }
    
    
    public function checkinventory(Request $request)
    {
        $product = DB::table('tbl_inventory_levels')->where('product_code', $request->product_code)->where('store_id', $request->store_id)->first();

        if (!$product) 
        {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }

        if ($request->quantity > $product->available_quantity) {
            return response()->json([
                'status' => false,
                'message' => 'Only ' . $product->available_quantity . ' items available in stock'
            ]);
        }

        return response()->json([
            'status' => true
        ]);
    }
    
    
    public function bulkSaleRecord(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $user = auth()->user();
    
            /** -------------------------
             *   Handle Customer
             *  ------------------------- */
            $customer = DB::table('tbl_customer')->where('contact_no', $request->contact_no)->first();
    
            if (!$customer) {
                $customerId = $this->generateUniqueRandomId(6, 'tbl_customer', 'cust_unique_id');
                $customer = Customer::create([
                    'cust_unique_id' => $customerId,
                    'cust_type'      => $request->cust_type,
                    'cust_name'      => $request->cust_name,
                    'contact_no'     => $request->contact_no,
                    'email_id'       => $request->email_id,
                    'cust_category'  => $request->cust_category,
                    'gender'         => $request->gender,
                    'cust_address'   => $request->cust_address,
                    'state_id'       => $request->state_id,
                    'city_id'        => $request->city_id,
                    'pincode'        => $request->pincode,
                    'dob'            => $request->dob,
                    'doa'            => $request->doa,
                    'cust_note'      => $request->cust_note,
                    'added_by'       => $user->id,
                    'store_id'       => $request->store_id,
                ]);
            }
    
            /** -------------------------
             *  Create Sale
             *  ------------------------- */
            $sale = Sale::create([
                'sale_date'           => $request->sale_date,
                'order_no'            => $request->order_no,
                'sale_person'         => $request->sale_person,
                'tax_rule'            => $request->taxrule,
                'contact_no'          => $request->contact_no,
                'cust_id'             => $customer->cust_unique_id,
                'cust_name'           => $request->cust_name,
                'membership_id'       => $request->membership_id,
                'email_id'            => $request->email_id,
                'cust_address'        => $request->cust_address,
                'state_id'            => $request->state_id,
                'city_id'             => $request->city_id,
                'pincode'             => $request->pincode,
                'total_basic_amount'  => $request->total_basic_amount ?? 0,
                'total_gst_amount'    => $request->total_gst_amount ?? 0,
                'total_item_price'    => $request->total_item_price ?? 0,
                'total_discount'      => $request->total_discount ?? 0,
                'roundoff'            => $request->roundoff ?? 0,
                'total_payable'       => $request->total_payable ?? 0,
                'pay_amount'          => $request->pay_amount ?? 0,
                'pending_amount'      => $request->pending_amount ?? 0,
                'pay_method'    => $request->pay_method,
                'pay_deatils'    => $request->pay_deatils,
                'added_by'            => $user->id,
                'store_id'            => $request->store_id,
                'sales_type'          => 0,
                'sales_status'    => 1,
                'customer_account'       => $request->customer_account,
                'advance_amount'       => $request->advance_amount,
            ]);
            
            $custData = DB::table('tbl_customer')->where('contact_no', $request->contact_no)->first();
            
            if($request->customer_account > 0)
            {
                $credit_amount = $custData->credit_amount;
                
                DB::table('tbl_customer')
                ->where('contact_no', $request->contact_no)
                ->update([
                    'contact_no' => $credit_amount - $request->customer_account ,
                    'updated_at' => now()
                ]);
                
                DB::table('tbl_wallet_history')->insert([
                    'customer_id'    => $custData->customer_id,
                    'contact_no'     => $custData->contact_no,
                    'debit'          => $request->customer_account,
                    'order_no'       => $request->order_no,
                    'store_id'       => $data['store_id'],
                    'added_by'       => $user->id,
                    'created_at'     => now(),
                    'updated_at'     => now()
                ]);
                
            }
    
            /** -------------------------
             *   Save Sale Products
             *  ------------------------- */
            $data = $request->all();
    
            foreach (($data['product_type'] ?? []) as $i => $type)
            {
               /* ---------------------------------------------------------
                    HANDLE FRAME OR GOGGLES PRODUCT
                --------------------------------------------------------- */
                if ($type === 'Frame' || $type === 'Goggles') {
            
                    $saleProduct = SaleProduct::create([
                        'sale_id'        => $sale->id,
                        'order_no'       => $data['order_no'],
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'product_id'     => $data['product_id'][$i] ?? null,
                        'product_company'=> $data['product_company'][$i] ?? null,
                        'product_quality'=> $data['product_quality'][$i] ?? null,
                        'product_deatils'=> $data['product_description'][$i] ?? null,
                        'qty'            => $data['product_qty'][$i] ?? 1,
                        'hsn_code'       => $data['hsn_code'][$i] ?? null,
                        'gst'            => $data['gst'][$i] ?? 0,
                        'gst_amount'     => $data['gst_amount'][$i] ?? 0,
                        'discount_amt'   => $data['discount_amt'][$i] ?? 0,
                        'product_discount'=> $data['discount'][$i] ?? 0,
                        'purchase_price'     => $data['purchase_price'][$i] ?? 0,
                        'base_price'     => $data['base_price'][$i] ?? 0,
                        'retail_price'   => $data['retail_price'][$i] ?? 0,
                        'sale_price'     => $data['sale_price'][$i] ?? 0,
                        'store_id'       => $data['store_id'],
                        'product_tracking'       => 'ORDER PLACED AND READY TO SHIP',
                    ]);
            
                    $this->UpdateFrameGogglesSolutuionInventory(
                        $data['store_id'],
                        $data['product_description'][$i],
                        $type,
                        $data['product_code'][$i],
                        $data['product_id'][$i],
                        $data['product_qty'][$i],
                        $data['sale_date'],
                        $saleProduct->id
                    );
                    
                    $tracking_status = 'ORDER PLACED AND READY TO SHIP';
                    $OrderTracking = OrderTracking::create([
                        'order_no'       => $data['order_no'],
                        'sale_product_id'       => $saleProduct->id,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'description'=> $data['product_description'][$i] ?? null,
                        'tracking_status'   => $tracking_status,
                        'store_id'       => $data['store_id'],
                    ]);
                }
 
                
                /* ---------------------------------------------------------
                HANDLE SOLUTION PRODUCT
                --------------------------------------------------------- */
                
                elseif ($type === 'Solution') 
                {
                    $saleProduct = SaleProduct::create([
                        'sale_id'        => $sale->id,
                        'order_no'       => $data['order_no'],
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'product_id'     => $data['product_id'][$i] ?? null,
                        'product_company'=> $data['product_company'][$i] ?? null,
                        'product_quality'=> $data['product_quality'][$i] ?? null,
                        'product_deatils'=> $data['product_description'][$i] ?? null,
                        'product_color'=> $data['product_color'][$i] ?? null,
                        'product_typesss'=> $data['product_typesss'][$i] ?? null,
                        'product_variant'=> $data['product_variant'][$i] ?? null,
                        'qty'            => $data['product_qty'][$i] ?? 1,
                        'hsn_code'       => $data['hsn_code'][$i] ?? null,
                        'gst'            => $data['gst'][$i] ?? 0,
                        'gst_amount'     => $data['gst_amount'][$i] ?? 0,
                        'discount_amt'   => $data['discount_amt'][$i] ?? 0,
                        'product_discount'=> $data['discount'][$i] ?? 0,
                        'purchase_price'     => $data['purchase_price'][$i] ?? 0,
                        'base_price'     => $data['base_price'][$i] ?? 0,
                        'retail_price'   => $data['retail_price'][$i] ?? 0,
                        'sale_price'     => $data['sale_price'][$i] ?? 0,
                        'store_id'       => $data['store_id'],
                        'product_tracking'       => 'ORDER PLACED AND READY TO SHIP',
                    ]);

                    $this->UpdateFrameGogglesSolutuionInventory(
                        $data['store_id'],
                        $data['product_description'][$i],
                        $type,
                        $data['product_code'][$i],
                        $data['product_id'][$i],
                        $data['product_qty'][$i],
                        $data['sale_date'],
                        $saleProduct->id
                    );
                    
                    $tracking_status = 'ORDER PLACED AND READY TO SHIP';
                    $OrderTracking = OrderTracking::create([
                        'order_no'       => $data['order_no'],
                        'sale_product_id'       => $saleProduct->id,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'description'=> $data['product_description'][$i] ?? null,
                        'tracking_status'   => $tracking_status,
                        'store_id'       => $data['store_id'],
                    ]);
                }
                
            /* ---------------------------------------------------------
                HANDLE OTHER PRODUCT
            --------------------------------------------------------- */
                
                elseif ($type === 'Other') 
                {
                    $saleProduct = SaleProduct::create([
                        'sale_id'        => $sale->id,
                        'order_no'       => $data['order_no'],
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'product_id'     => $data['product_id'][$i] ?? null,
                        'product_company'=> $data['product_company'][$i] ?? null,
                        'product_quality'=> $data['product_quality'][$i] ?? null,
                        'product_deatils'=> $data['product_description'][$i] ?? null,
                        'product_color'=> $data['product_color'][$i] ?? null,
                        'product_typesss'=> $data['product_typesss'][$i] ?? null,
                        'product_shape'=> $data['product_variant'][$i] ?? null,
                        'product_shape'=> $data['product_shape'][$i] ?? null,
                        'product_size'            => $data['product_size'][$i] ?? 1,
                        'qty'            => $data['product_qty'][$i] ?? 1,
                        'hsn_code'       => $data['hsn_code'][$i] ?? null,
                        'gst'            => $data['gst'][$i] ?? 0,
                        'gst_amount'     => $data['gst_amount'][$i] ?? 0,
                        'discount_amt'   => $data['discount_amt'][$i] ?? 0,
                        'product_discount'=> $data['discount'][$i] ?? 0,
                        'purchase_price'     => $data['purchase_price'][$i] ?? 0,
                        'base_price'     => $data['base_price'][$i] ?? 0,
                        'retail_price'   => $data['retail_price'][$i] ?? 0,
                        'sale_price'     => $data['sale_price'][$i] ?? 0,
                        'store_id'       => $data['store_id'],
                        'product_tracking'       => 'ORDER PLACED AND READY TO SHIP',
                    ]);

                    $this->UpdateOtherInventory(
                        $data['store_id'],
                        $data['product_description'][$i],
                        $type,
                        $data['product_code'][$i],
                        $data['product_id'][$i],
                        $data['product_qty'][$i],
                        $data['sale_date'],
                        $saleProduct->id
                    );
                    
                    $tracking_status = 'ORDER PLACED AND READY TO SHIP';
                    $OrderTracking = OrderTracking::create([
                        'order_no'       => $data['order_no'],
                        'sale_product_id'       => $saleProduct->id,
                        'product_type'   => $type,
                        'product_code'   => $data['product_code'][$i] ?? null,
                        'description'=> $data['product_description'][$i] ?? null,
                        'tracking_status'   => $tracking_status,
                        'store_id'       => $data['store_id'],
                    ]);
                }
                
                
                $this->UpdateBulkBarcodes(
                    $data['store_id'],
                    $data['product_description'][$i],
                    $type,
                    $data['product_qty'][$i],
                    $data['product_code'][$i],
                    $data['order_no']
                );
            }
    
            /** -------------------------
             *  Payment Entry
             *  ------------------------- */
            SalePayment::create([
                'sale_id'     => $sale->id,
                'order_no'    => $data['order_no'],
                'total_price'    => $data['total_payable']?? 0,
                'pay_amount'  => $data['pay_amount'] ?? 0,
                'bal_amount'  => $data['pending_amount'] ?? 0,
                'pay_details' => $data['pay_deatils'],
                'pay_method'  => $data['pay_method'],
                'pay_date'    => $data['sale_date'],
                'added_by'    => $user->id,
                'store_id'    => $data['store_id'],
                'pay_type'    => 0,
            ]);
    
   
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'message' => 'Bulk Sale invoice saved successfully.',
                'sale_id' => $sale->id,
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the sales save process.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    
    private function UpdateBulkBarcodes($store_id, $product_description, $product_type, $qty, $product_code, $order_no)
    {
        $user = auth()->user();
    
        $products = DB::table('tbl_barcode')
            ->where('t_status', '0')
            ->where('store_id', $store_id)
            ->where('product_details', $product_description)
            ->where('product_type', $product_type)
            ->where('product_code', $product_code)
            ->limit($qty)
            ->get();
    
        $totalFound = $products->count();
    
        if ($totalFound < $qty) {
            $remainingQty = $qty - $totalFound;
    
            $transferProducts = DB::table('tbl_barcode')
                ->where('product_details', $product_description)
                ->where('product_type', $product_type)
                ->where('product_code', $product_code)
                ->where('transfer_store_id', $store_id)
                ->limit($remainingQty)
                ->get();
    
            $products = $products->concat($transferProducts);
            $totalFound = $products->count();
        }
    
       
        foreach ($products as $product) {
    
            $updateData = [
                'refrence_no' => $order_no,
                'updated_at' => now()
            ];
    
            if (isset($product->store_id) && $product->store_id == $store_id) {
                $updateData['outward_status'] = 0;
            } else {
                $updateData['transfer_outward_status'] = 0;
            }
    
            DB::table('tbl_barcode')->where('id', $product->id)->update($updateData);
    
            // Track activity
            DB::table('tbl_barcode_track_record')->insert([
                'barcode_no' => $product->barcode_no,
                'store_id' => $store_id,
                'reference_type' => 'Sale',
                'action_perform' => 'Order',
                'added_by' => $user->id,
                'created_at' => now()
            ]);
        }
 
    }

    
    public function dailyStatement()
    {
        $setting['page_title'] = 'Daily Statement PDF';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/daily-statement',$setting);
    } 
    
    public function saleStatementRecord(Request $request)
    {
        // ---------- Store Details ----------
        $store = Store::findOrFail($request->store_id);
        $state = State::find($store->state_id);
        $city  = City::find($store->city_id);
    
        // ---------- COLLECTIONS (Cash + UPI in ONE query) ----------
        $collections = DB::table('tbl_sale_payment')
            ->selectRaw("
                SUM(CASE WHEN pay_method = 'cash' THEN pay_amount ELSE 0 END) AS cash_total,
                SUM(CASE WHEN pay_method = 'upi'  THEN pay_amount ELSE 0 END) AS upi_total
            ")
            ->where('store_id', $request->store_id)
            ->where('pay_type','!=', 2)
            ->whereBetween('pay_date', [$request->date_from, $request->date_to])
            ->first();
    
        $totalCollationCash = $collections->cash_total ?? 0;
        $totalCollationUPI  = $collections->upi_total ?? 0;
        
        
        $returncollections = DB::table('tbl_sale_payment')
            ->selectRaw("
                SUM(CASE WHEN pay_method = 'cash' THEN pay_amount ELSE 0 END) AS cash_total,
                SUM(CASE WHEN pay_method = 'upi'  THEN pay_amount ELSE 0 END) AS upi_total
            ")
            ->where('store_id', $request->store_id)
            ->where('pay_type', 2)
            ->whereBetween('pay_date', [$request->date_from, $request->date_to])
            ->first();
    
        $totalReturnCash = $returncollections->cash_total ?? 0;
        $totalReturnUPI  = $returncollections->upi_total ?? 0;
    
        // ---------- EXPENSES (Cash + UPI in ONE query) ----------
        $expenses = DB::table('tbl_voucher')
            ->selectRaw("
                SUM(CASE WHEN pay_method = 'cash' THEN total_amount ELSE 0 END) AS cash_expense,
                SUM(CASE WHEN pay_method = 'upi'  THEN total_amount ELSE 0 END) AS upi_expense
            ")
            ->where('store_id', $request->store_id)
            ->where('voucher_type', 'expense')
            ->whereBetween('voucher_date', [$request->date_from, $request->date_to])
            ->first();
    
        $totalExpenseCash = $expenses->cash_expense ?? 0;
        $totalExpenseUPI  = $expenses->upi_expense ?? 0;
    
        // ---------- ADVANCE ORDER COLLECTIONS ----------
        $advanceOrders = DB::table('tbl_sale_payment')
            ->where('store_id', $request->store_id)
            ->where('sales_type', $request->sales_type)
            ->where('pay_type', 0) // Advance
            ->whereBetween('pay_date', [$request->date_from, $request->date_to])
            ->get();
            
        // ---------- BALANCED ORDER COLLECTIONS ----------
        $balanceOrders = DB::table('tbl_sale_payment')
            ->where('store_id', $request->store_id)
            ->where('sales_type', $request->sales_type)
            ->where('pay_type', 1) // Advance
            ->whereBetween('pay_date', [$request->date_from, $request->date_to])
            ->get();  
            
        // ---------- RETURN HISTORY  ----------
        $returnOrders = DB::table('tbl_sales_product as sp')
            ->join('tbl_sales as s', 's.order_no', '=', 'sp.order_no')
            ->leftJoin('users as u', 'u.id', '=', 's.sale_person')
            ->where('sp.store_id', $request->store_id)
            ->where('sp.return_status', 1)
            ->where('s.sales_type', $request->sales_type)
            ->whereBetween('sp.return_date', [
                $request->date_from,
                $request->date_to
            ])
            ->select(
                'sp.order_no',
                's.sale_date',
                's.cust_name',
                'sp.retail_price',
                'sp.product_discount',
                'sp.discount_amt',
                'sp.sale_price',
                'u.name as sales_staff',
                'sp.product_type',
                'sp.product_code',
                'sp.product_deatils'
            )
            ->get();  
            
        // ---------- EXPENSE HISTORY  ----------
        $expensehistory = DB::table('tbl_voucher as sp')
            ->leftJoin('users as u', 'u.id', '=', 'sp.added_by')
            ->where('sp.store_id', $request->store_id)
            ->where('sp.voucher_type', 'expense')
            ->whereBetween('sp.voucher_date', [
                $request->date_from,
                $request->date_to
            ])
            ->select(
                'sp.voucher_no',
                'sp.voucher_date',
                'sp.total_amount',
                'sp.purpose',
                'sp.pay_remark',
                'u.name as sales_staff'
            )
            ->get();  
        
        // ---------- CUSTOMER CREDIT ----------    
        $customercredit = DB::table('tbl_wallet_history')
            ->selectRaw('SUM(credit) as credit_total')
            ->where('store_id', $request->store_id)
            ->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ])
            ->first();
        
        $credit_total = $customercredit->credit_total ?? 0;

        $salesSummary = DB::table('tbl_sales')
        ->selectRaw("
            COUNT(*) as total_sales_count,
            SUM(total_item_price) as total_sales,
            SUM(total_discount) as total_item_discount,
            SUM(cart_discount) as total_cart_discount,
            SUM(loyalty_point_amount) as total_loyalty_points,
            SUM(coupon_amount) as total_discount_coupon,
            SUM(total_item_price + total_gst_amount) as total_gross_sales,
            SUM(total_gst_amount) as total_gst_amount,
            SUM(roundoff) as total_roundoff,
            SUM(total_payable) as total_net_sales
        ")
        ->where('store_id', $request->store_id)
        ->whereBetween('sale_date', [$request->date_from, $request->date_to])
        ->first();

        // ---------- HTML START ----------
        $data = '
        <div class="" style="margin:20px;">
            <div class="format-a4">
    
                <table width="100%" class="print-header-table">
                    <tr>
                        <td>
                            <span style="font-size:22px;font-weight:bold;">'.$store->store_name.'</span><br>
                            <span>'.$store->store_address.', '.$city->city_name.' - '.$store->pincode.', '.$state->state_name.'</span><br>
                            <span>Mobile : '.$store->contact_no.'</span><br>
                            <span>Email : '.$store->email_id.'</span><br>
                            <span>GST : '.$store->gst_no.'</span><br>
                            <span>Generated : '.date('d-m-Y H:i:s').'</span>
                        </td>
                    </tr>
                </table>
    
                <hr>
    
                <h3 align="center">
                    STATEMENT FROM '.date('d-m-Y', strtotime($request->date_from)).' TO '.date('d-m-Y', strtotime($request->date_to)).'
                </h3>
    
                <h4>Summary</h4>
                <table border="11" width="100%" cellpadding="5">
                    <tr>
                        <th>Payment Mode</th>
                        <th>Collection</th>
                        <th>Expenses</th>
                    </tr>
                    <tr>
                        <td>Cash</td>
                        <td>'.number_format($totalCollationCash, 2).'</td>
                        <td>'.number_format($totalExpenseCash, 2).'</td>
                    </tr>
                    <tr>
                        <td>UPI</td>
                        <td>'.number_format($totalCollationUPI, 2).'</td>
                        <td>'.number_format($totalExpenseUPI, 2).'</td>
                    </tr>
                </table>
    
                <br>
    
                <h4>New Order Advance Collections</h4>
                <table border="11" width="100%" cellpadding="5">
                    <tr>
                        <td class="print-td-table"><span>Sr.No</span></td>
                        <td class="print-td-table"><span>Order Date</span></td>
                        <td class="print-td-table"><span>Sales Staff</span></td>
                        <td class="print-td-table"><span>Order No</span></td>
                        <td class="print-td-table"><span>Customer Name</span></td>
                        <td class="print-td-table"><span>Order Value</span></td>
                        <td class="print-td-table"><span>Total Discount</span></td>
                        <td class="print-td-table"><span>Round Off</span></td>
                        <td class="print-td-table"><span>Net Value</span></td>
                        <td class="print-td-table"><span>Amount Collected</span></td>
                        <td class="print-td-table"><span>Balance</span></td>
                    </tr>';
    
        $sr = 1;
       $totalAdvanceCollection = 0;
        foreach ($advanceOrders as $row) 
        {
            $tbl_sales = DB::table('tbl_sales')->where('order_no', $row->order_no)->first();
            $sale_person = User::find($tbl_sales->sale_person);
            
            $totalAdvanceCollection += $row->pay_amount;
            $sumdiscount = $tbl_sales->total_discount+$tbl_sales->coupon_amount+$tbl_sales->loyalty_point_amount+$tbl_sales->cart_discount;
            $data .= '
            <tr>
                <td>'.$sr++.'</td>
                <td>'.date('d-m-Y H:i:A', strtotime($tbl_sales->sale_date)).'</td>
                <td>'.$sale_person->name.'</td>
                <td>'.$row->order_no.'</td>
                <td>'.$tbl_sales->cust_name.'</td>
                <td>'.number_format($tbl_sales->total_item_price, 2).'</td>
                <td>'.number_format($sumdiscount, 2).'</td>
                <td>'.number_format($tbl_sales->roundoff, 2).'</td>
                <td>'.number_format($tbl_sales->total_payable, 2).'</td>
                <td>'.$row->pay_amount.' - '.$row->pay_method.'</td>
                <td>'.number_format($tbl_sales->pending_amount, 2).'</td>
            </tr>';
        }

        if ($sr > 1) {
            $data .= '
            <tr style="font-weight:bold; background:#f2f2f2;">
                <td colspan="9" align="right">ADVANCE COLLECTION</td>
                <td style="text-align:right;">'.number_format($totalAdvanceCollection, 2).'</td>
                <td></td>
            </tr>';
        } else {
            $data .= '
            <tr>
                <td colspan="6" align="center">No Records Found</td>
            </tr>';
        }
    
        $data .= '
                </table>
                <br>
    
                <h4>Balance Payment Collections</h4>
                <table border="11" width="100%" cellpadding="5">
                    <tr>
                        <td class="print-td-table"><span>Sr.No</span></td>
                        <td class="print-td-table"><span>Order Date</span></td>
                        <td class="print-td-table"><span>Sales Staff</span></td>
                        <td class="print-td-table"><span>Order No</span></td>
                        <td class="print-td-table"><span>Customer Name</span></td>
                        <td class="print-td-table"><span>Order Value</span></td>
                        <td class="print-td-table"><span>Total Discount</span></td>
                        <td class="print-td-table"><span>Round Off</span></td>
                        <td class="print-td-table"><span>Net Value</span></td>
                        <td class="print-td-table"><span>Amount Collected</span></td>
                        <td class="print-td-table"><span>Balance</span></td>
                    </tr>';
    
        $sr = 1;
       $totalbalanceCollection = 0;
        foreach ($balanceOrders as $row) 
        {
            $tbl_sales = DB::table('tbl_sales')->where('order_no', $row->order_no)->first();
            $sale_person = User::find($tbl_sales->sale_person);
            
            $totalbalanceCollection += $row->pay_amount;
            $sumdiscount = $tbl_sales->total_discount+$tbl_sales->coupon_amount+$tbl_sales->loyalty_point_amount+$tbl_sales->cart_discount;
            $data .= '
            <tr>
                <td>'.$sr++.'</td>
                <td>'.date('d-m-Y H:i:A', strtotime($tbl_sales->sale_date)).'</td>
                <td>'.$sale_person->name.'</td>
                <td>'.$row->order_no.'</td>
                <td>'.$tbl_sales->cust_name.'</td>
                <td>'.number_format($tbl_sales->total_item_price, 2).'</td>
                <td>'.number_format($sumdiscount, 2).'</td>
                <td>'.number_format($tbl_sales->roundoff, 2).'</td>
                <td>'.number_format($tbl_sales->total_payable, 2).'</td>
                <td>'.$row->pay_amount.' - '.$row->pay_method.'</td>
                <td>'.number_format($tbl_sales->pending_amount, 2).'</td>
            </tr>';
        }

        if ($sr > 1) {
            $data .= '
            <tr style="font-weight:bold; background:#f2f2f2;">
                <td colspan="9" align="right">ADVANCE COLLECTION</td>
                <td style="text-align:right;">'.number_format($totalbalanceCollection, 2).'</td>
                <td></td>
            </tr>';
        } else {
            $data .= '
            <tr>
                <td colspan="11" align="center">No Records Found</td>
            </tr>';
        }
    
        $data .= '
                </table>
                 <br>
    
                <h4>Return Payments</h4>
                <table border="11" width="100%" cellpadding="5">
                    <tr>
                        <td class="print-td-table"><span>Sr.No</span></td>
                        <td class="print-td-table"><span>Order Date</span></td>
                        <td class="print-td-table"><span>Sales Staff</span></td>
                        <td class="print-td-table"><span>Order No</span></td>
                        <td class="print-td-table"><span>Customer Name</span></td>
                        <td class="print-td-table"><span>Order Value</span></td>
                        <td class="print-td-table"><span>Total Discount</span></td>
                        <td class="print-td-table"><span>Net Value</span></td>
                        <td class="print-td-table"><span>Return Amount</span></td>
                    </tr>';

        $sr = 1;
        $totalreturn = 0;
        
        foreach ($returnOrders as $row) 
        {
        
            $sumdiscount = 
                $row->product_discount +
                $row->discount_amt;
        
            $totalreturn += $row->sale_price;
        
            $data .= '
            <tr>
                <td>'.$sr++.'</td>
                <td>'.date('d-m-Y h:i A', strtotime($row->sale_date)).'</td>
                <td>'.$row->sales_staff.'</td>
                <td>'.$row->order_no.'</td>
                <td>'.$row->cust_name.'</td>
                <td>'.number_format($row->retail_price, 2).'</td>
                <td>'.number_format($sumdiscount, 2).'</td>
                <td>'.number_format($row->sale_price, 2).'</td>
                <td>'.number_format($row->sale_price, 2).'</td>
            </tr>';
        }

        if ($sr > 1) {
            $data .= '
            <tr style="font-weight:bold; background:#f2f2f2;">
                <td colspan="7" align="right">TOTAL RETURN PAYMENT</td>
                <td style="text-align:right;">'.number_format($totalreturn, 2).'</td>
                <td></td>
            </tr>';
        } else {
            $data .= '
            <tr>
                <td colspan="9" align="center">No Records Found</td>
            </tr>';
        }
    
        $data .= '
                </table>
                 <br>
    
                <h4>Sales Return History</h4>
                <table border="11" width="100%" cellpadding="5">
                    <tr>
                        <td class="print-td-table"><span>Sr.No</span></td>
                        <td class="print-td-table"><span>Order Date</span></td>
                        <td class="print-td-table"><span>Order No</span></td>
                        <td class="print-td-table"><span>Product Type</span></td>
                        <td class="print-td-table"><span>Product Code</span></td>
                        <td class="print-td-table"><span>Description</span></td>
                        <td class="print-td-table"><span>Return Amount</span></td>
                    </tr>';

        $sr = 1;
        $totalreturnhist = 0;
        
        foreach ($returnOrders as $row) {
        
           
        
            $totalreturnhist += $row->sale_price;
        
            $data .= '
            <tr>
                <td>'.$sr++.'</td>
                <td>'.date('d-m-Y h:i A', strtotime($row->sale_date)).'</td>
                <td>'.$row->order_no.'</td>
                <td>'.$row->product_type.'</td>
                <td>'.$row->product_code.'</td>
                <td>'.$row->product_deatils.'</td>
                <td>'.number_format($row->sale_price, 2).'</td>
            </tr>';
        }

        if ($sr > 1) {
            $data .= '
            <tr style="font-weight:bold; background:#f2f2f2;">
                <td colspan="5" align="right">Total Sales Return Amount</td>
                <td style="text-align:right;">'.number_format($totalreturnhist, 2).'</td>
                <td></td>
            </tr>';
        } else {
            $data .= '
            <tr>
                <td colspan="7" align="center">No Records Found</td>
            </tr>';
        }
    
        $data .= '
                </table>
                
            <br>
    
                <h4>Expenses Payments</h4>
                <table border="11" width="100%" cellpadding="5">
                    <tr>
                        <td class="print-td-table"><span>Sr.No</span></td>
                        <td class="print-td-table"><span>Date</span></td>
                        <td class="print-td-table"><span>Staff Name</span></td>
                        <td class="print-td-table"><span>Voucher Number</span></td>
                        <td class="print-td-table"><span>Purpose</span></td>
                        <td class="print-td-table"><span>Remarks</span></td>
                        <td class="print-td-table"><span>Payment Amount</span></td>
                    </tr>';

        $sr = 1;
        $totalexpense = 0;
        
        foreach ($expensehistory as $row) {
        
           
        
            $totalexpense += $row->total_amount;
        
            $data .= '
            <tr>
                <td>'.$sr++.'</td>
                <td>'.date('d-m-Y h:i A', strtotime($row->voucher_date)).'</td>
                <td>'.$row->sales_staff.'</td>
                <td>'.$row->voucher_no.'</td>
                <td>'.$row->purpose.'</td>
                <td>'.$row->pay_remark.'</td>
                <td>'.number_format($row->total_amount, 2).'</td>
            </tr>';
        }

        if ($sr > 1) {
            $data .= '
            <tr style="font-weight:bold; background:#f2f2f2;">
                <td colspan="5" align="right">TOTAL EXPENSES</td>
                <td style="text-align:right;">'.number_format($totalexpense, 2).'</td>
                <td></td>
            </tr>';
        } else {
            $data .= '
            <tr>
                <td colspan="7" align="center">No Records Found</td>
            </tr>';
        }
    
        $data .= '
                </table>  
                <br>
                <h4>Payment Summary</h4>
                <table border="11" width="100%" cellpadding="5">
                    <tr>
                        <th>Payment Mode</th>
                        <th>Collected Payment</th>
                        <th>Return Payment</th>
                        <th>Difference</th>
                    </tr>
                    <tr>
                        <td>Cash</td>
                        <td>'.number_format($totalCollationCash, 2).'</td>
                        <td>'.number_format($totalReturnCash, 2).'</td>
                        <td>'.number_format(($totalCollationCash - $totalReturnCash), 2).'</td>
                    </tr>
                    <tr>
                        <td>UPI</td>
                        <td>'.number_format($totalCollationUPI, 2).'</td>
                        <td>'.number_format($totalReturnUPI, 2).'</td>
                        <td>'.number_format(($totalCollationUPI - $totalReturnUPI), 2).'</td>
                    </tr>
                </table>  
                <br>
                <h4>Other Summary</h4>
                <table border="11" width="100%" cellpadding="5">
                    <tr>
                        <th>Customer Credit</th>
                        <th>'.number_format($credit_total, 2).'</th>
                    </tr>
                </table> 
                <br>
                <h4>Sales Summary</h4>
                <table border="11" width="100%" cellpadding="5">
                <tr><th>Total No. of Sales</th><td>'.$salesSummary->total_sales_count.'</td></tr>
                <tr><th>Total Sales</th><td>'.number_format($salesSummary->total_sales, 2).'</td></tr>
                <tr><th>Total Item Discount</th><td>'.number_format($salesSummary->total_item_discount, 2).'</td></tr>
                <tr><th>Total Cart Discount</th><td>'.number_format($salesSummary->total_cart_discount, 2).'</td></tr>
                <tr><th>Total Loyalty Points Amount</th><td>'.number_format($salesSummary->total_loyalty_points, 2).'</td></tr>
                <tr><th>Total Discount Coupon</th><td>'.number_format($salesSummary->total_discount_coupon, 2).'</td></tr>
                <tr><th>Total Gross Sales</th><td>'.number_format($salesSummary->total_gross_sales, 2).'</td></tr>
                <tr><th>Total GST Amount</th><td>'.number_format($salesSummary->total_gst_amount, 2).'</td></tr>
                <tr><th>Total Round Off</th><td>'.number_format($salesSummary->total_roundoff, 2).'</td></tr>
                <tr><th>Total Net Sales</th><td>'.number_format($salesSummary->total_net_sales, 2).'</td></tr>
                <tr><th>Total Sales Return </th><td>'.number_format(($total_return_amt = $totalReturnCash + $totalReturnUPI), 2).'</td></tr>
                <tr><th>Total Final Sales</th><td>'.number_format(($salesSummary->total_net_sales-$total_return_amt), 2).'</td></tr>
                </table> 
            </div>
        </div>';
    
        return $data;
    }
    
    
    
    public function pendingCourier()
    {
        $setting['page_title'] = 'Pending Courier';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/pending-courier',$setting);
    }
    
    
    
    public function pendingCourierDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit = $request->input('length');
        $start = $request->input('start');
        $search1 = $request->input('search1');
        $sid = $request->input('store_id');
    
        /*
        |--------------------------------------------------------------------------
        | Base Query with Join & Counts
        |--------------------------------------------------------------------------
        */
        
        $ready = (int) config('constants.PRODUCT_READY');

        $query = DB::table('tbl_sales as s')
            ->join('tbl_sales_product as sp', 'sp.sale_id', '=', 's.sale_id')
            ->select(
                's.*',
                DB::raw('COUNT(sp.id) as total_product'),
                DB::raw("SUM(CASE WHEN sp.product_tracking = {$ready} THEN 1 ELSE 0 END) as total_product_ready"),
                DB::raw('SUM(CASE WHEN sp.courier_status = 0 THEN 1 ELSE 0 END) as total_courier_status')
            )
            ->where('s.sales_status', 1)
            ->where('s.is_deleted', 0)
            ->groupBy('s.sale_id')
            ->having('total_courier_status', '>', 0);

    
        /*
        |--------------------------------------------------------------------------
        | Store Filtering
        |--------------------------------------------------------------------------
        */
        if ($store_id != '0') {
            $query->where('s.store_id', $store_id);
        }
    
        if ($sid != '') {
            $query->where('s.store_id', $sid);
        }
    
        /*
        |--------------------------------------------------------------------------
        | Search Filtering
        |--------------------------------------------------------------------------
        */
        if ($search1 != '') {
            $query->where(function ($q) use ($search1) {
                $q->where('s.order_no', 'like', "%$search1%")
                  ->orWhere('s.cust_id', 'like', "%$search1%")
                  ->orWhere('s.contact_no', 'like', "%$search1%")
                  ->orWhere('s.cust_name', 'like', "%$search1%");
                  
            });
        }
    
        /*
        |--------------------------------------------------------------------------
        | Total Records Count
        |--------------------------------------------------------------------------
        */
        $totalData = (clone $query)->count();
    
        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $templates = $query
            ->orderBy('s.sale_id', 'DESC')
            ->offset($start)
            ->limit($limit)
            ->get();
    
        $totalFiltered = $templates->count();
    
        /*
        |--------------------------------------------------------------------------
        | Data Formatting
        |--------------------------------------------------------------------------
        */
        $data = [];
        $i = 1;
    
        foreach ($templates as $template) {
    
            $tbl_store = Store::find($template->store_id);
    
            $nestedData['sr_no'] = $i++;
            $nestedData['order_date'] = date('d M, Y h:i A', strtotime($template->sale_date));
            $nestedData['delivery_date'] = date('d M, Y h:i A', strtotime($template->delivery_date));
    
            $nestedData['customer_details'] =
                '<strong>Customer Name</strong>: '.$template->cust_name.
                '<br><strong>Mobile No</strong>: '.$template->contact_no.
                '<br><strong>Cust ID</strong>: '.$template->cust_id;
    
            $nestedData['store_name'] = $tbl_store->store_name ?? '';
            $nestedData['order_no'] = $template->order_no;
            $nestedData['order_amount'] = $template->total_payable;
    
            $nestedData['total_product'] = $template->total_product;
            $nestedData['total_product_ready'] = $template->total_product_ready;
            $nestedData['total_courier_status'] = $template->total_courier_status;
    
            $nestedData['encryptedId'] = base64_encode($template->sale_id);
            $nestedData['oid'] = $template->order_no;
    
            $data[] = $nestedData;
        }
    
        /*
        |--------------------------------------------------------------------------
        | Datatable Response
        |--------------------------------------------------------------------------
        */
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }
    
    
    public function getpendingcourierproduct(Request $request)
    {
        $oid = $request->oid;
    
        $saleproducts = DB::table('tbl_sales_product')
            ->where('order_no', $oid)
            ->where('courier_status', 0)
            ->orderBy('id', 'ASC')
            ->get()
            ->groupBy(function ($item) {
                return $item->product_type . '|' .
                       $item->product_code . '|' .
                       $item->barcode_use . '|' .
                       $item->base_price . '|' .
                       $item->discount_amt . '|' .
                       $item->qty . '|' .
                       $item->no_of_glass . '|' .
                       $item->product_deatils;
            });
    
        $data = $saleproducts->map(function ($group) {
    
            $p = $group->first(); // representative row
    
            // Ã¢Å“â€¦ Collect ALL product IDs in this group
            $ids = $group->pluck('id')->values();
    
            if ($p->product_type == 'Glass') {
                if ($p->qty == 2) {
                    $ispair = '<span class="badge badge-danger">Pair</span>';
                } else {
                    if ($p->right_glass == NULL) {
                        $ispair = '<span class="badge badge-danger">Left</span>';
                    } else {
                        $ispair = '<span class="badge badge-danger">Right</span>';
                    }
                }
            } else {
                $ispair = '';
            }
    
            return [
                'pids' => $ids, // Ã°Å¸â€˜Ë† ARRAY OF IDS
                'order_no' => $p->order_no,
                'qty' => $p->qty,
                'product_type' => $p->product_type,
                'ispair' => $ispair,
                'product_deatils' => $p->product_deatils,
            ];
        })->values();
    
        return response()->json([
            'data' => $data
        ]);
    }
    
    
    
    public function updateCourier(Request $request)
    {
        if (empty($request->product_ids) && empty($request->tracking_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Select at least one product or enter tracking ID'
            ]);
        }
    
        // Ã¢Å“â€¦ FLATTEN IDS
        $productIds = [];
    
        if (!empty($request->product_ids)) {
            foreach ($request->product_ids as $id) {
                if (str_contains($id, ',')) {
                    $productIds = array_merge($productIds, explode(',', $id));
                } else {
                    $productIds[] = $id;
                }
            }
        }
    
        $productIds = array_unique(array_map('intval', $productIds));
    

        if (!empty($productIds)) {
            DB::table('tbl_sales_product')
                ->whereIn('id', $productIds)
                ->update([
                    'courier_status'   => 1,
                    'tracking_details' => $request->tracking_details,
                    'courier_partner'  => $request->courier_partner,
                    'tracking_id'      => $request->tracking_id,
                    'updated_at'       => now()
                ]);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Courier details updated successfully'
        ]);
    }
    
    
    public function courierHistory()
    {
        $setting['page_title'] = 'Courier History';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/courier-history',$setting);
    }
    
    
    
    public function historyCourierDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit = $request->input('length');
        $start = $request->input('start');
        $search1 = $request->input('search1');
        $sid = $request->input('store_id');
    
        /*
        |--------------------------------------------------------------------------
        | Base Query with Join & Counts
        |--------------------------------------------------------------------------
        */
        
        $ready = (int) config('constants.PRODUCT_READY');

        $query = DB::table('tbl_sales as s')
            ->join('tbl_sales_product as sp', 'sp.sale_id', '=', 's.sale_id')
            ->where('sp.courier_status', 1);

    
        /*
        |--------------------------------------------------------------------------
        | Store Filtering
        |--------------------------------------------------------------------------
        */
        if ($store_id != '0') {
            $query->where('s.store_id', $store_id);
        }
    
        if ($sid != '') {
            $query->where('s.store_id', $sid);
        }
    
        /*
        |--------------------------------------------------------------------------
        | Search Filtering
        |--------------------------------------------------------------------------
        */
        if ($search1 != '') {
            $query->where(function ($q) use ($search1) {
                $q->where('s.order_no', 'like', "%$search1%")
                  ->orWhere('s.cust_id', 'like', "%$search1%")
                  ->orWhere('s.contact_no', 'like', "%$search1%")
                  ->orWhere('s.cust_name', 'like', "%$search1%");
                  
            });
        }
    
        /*
        |--------------------------------------------------------------------------
        | Total Records Count
        |--------------------------------------------------------------------------
        */
        $totalData = (clone $query)->count();
    
        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $templates = $query
            ->orderBy('sp.id', 'DESC')
            ->offset($start)
            ->limit($limit)
            ->get();
    
        $totalFiltered = $templates->count();
    
        /*
        |--------------------------------------------------------------------------
        | Data Formatting
        |--------------------------------------------------------------------------
        */
        $data = [];
        $i = 1;
    
        foreach ($templates as $template) {
    
            $tbl_store = Store::find($template->store_id);
    
            $nestedData['sr_no'] = $i++;

            $nestedData['customer_details'] =
                '<strong>Customer Name</strong>: '.$template->cust_name.
                '<br><strong>Mobile No</strong>: '.$template->contact_no.
                '<br><strong>Cust ID</strong>: '.$template->cust_id;
            
            $nestedData['order_no'] = $tbl_store->order_no ?? '';
            $nestedData['store_name'] = $tbl_store->store_name ?? '';
            $nestedData['product'] = $template->product_deatils;
            $nestedData['courier_partner'] = $template->courier_partner;
    
            $nestedData['tracking_id'] = $template->tracking_id;
            $nestedData['courier_date'] = $template->courier_date;

    
            $data[] = $nestedData;
        }
    
        /*
        |--------------------------------------------------------------------------
        | Datatable Response
        |--------------------------------------------------------------------------
        */
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }
    
    
    
    public function handoverHistory()
    {
        $setting['page_title'] = 'Product Handover';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/handover-history',$setting);
    }
    
    
    
    public function saleHandoverDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit  = (int) $request->input('length', 10);
        $start  = (int) $request->input('start', 0);
        $draw   = (int) $request->input('draw');
    
        $date_from   = $request->input('date_from');
        $date_to     = $request->input('date_to');
        $search      = $request->input('search1');
        $productType = $request->input('product_type');
    
        /* ---------------------------
           Base Query
        ----------------------------*/
        $query = SaleProduct::where('handover_status', 1);
    
        if ($store_id != 0) {
            $query->where('store_id', $store_id);
        }
    
        /* ---------------------------
           Fetch & Unique
        ----------------------------*/
        $collection = $query->get()
            ->unique(function ($item) {
                return implode('|', [
                    $item->product_type,
                    $item->product_code,
                    $item->barcode_use,
                    $item->base_price,
                    $item->discount_amt,
                    $item->handover_status,
                    $item->qty,
                    $item->no_of_glass,
                    $item->product_deatils
                ]);
            })
            ->values();
    
        /* ---------------------------
           Filters (Collection)
        ----------------------------*/
        if (!empty($productType)) {
            $collection = $collection->where('product_type', $productType);
        }
    
        if (!empty($date_from) && !empty($date_to)) {
            $from = Carbon::parse($date_from)->startOfDay();
            $to   = Carbon::parse($date_to)->endOfDay();
        
            $collection = $collection->filter(function ($item) use ($from, $to) {
                return Carbon::parse($item->handover_date)->between($from, $to);
            });
        }
    
        if (!empty($search)) {
            $collection = $collection->filter(function ($item) use ($search) {
                return str_contains($item->order_no, $search) ||
                       str_contains($item->barcode_use ?? '', $search);
            });
        }
    
        /* ---------------------------
           Counts
        ----------------------------*/
        $recordsTotal    = $collection->count();
        $recordsFiltered = $recordsTotal;
    
        /* ---------------------------
           Pagination
        ----------------------------*/
        $paginated = $collection
            ->sortByDesc('id')
            ->slice($start, $limit)
            ->values();
    
        /* ---------------------------
           Data Formatting
        ----------------------------*/
        $data = [];
        $i = $start + 1;
    
        foreach ($paginated as $row) {
            
            $sale         = DB::table('tbl_sales')->where('sale_id', $row->sale_id)->first();
            $store        = Store::find($row->store_id);
            $handover_by        = User::find($row->handover_by);
            
            $encryptedId = base64_encode($sale->sale_id);

            $data[] = [
                'sr_no' => $i++,
                'store_details' =>
                    '<strong>Store Name:</strong> ' . ($store->store_name ?? '-'),
    
                'order_details' =>
                    '<strong>Order Date:</strong> ' . date('d M, Y h:i A', strtotime($sale->created_at)) .
                    '<br><strong>Order No:</strong> ' . $row->order_no,
    
                'customer_details' =>
                    '<strong>Customer Name:</strong> ' . $sale->cust_name .
                    '<br><strong>Mobile:</strong> ' . $sale->contact_no .
                    '<br><strong>Cust ID:</strong> ' . $sale->cust_id,
    
                'product_type' => $row->product_type,
                'product_code' => $row->product_code,
                'description'  => $row->product_deatils,
                'handover_by'       => $handover_by->name,
                'encryptedId'       => $encryptedId,
                'handover_date'  => date('d M, Y H:i:s', strtotime($row->handover_date)),
            ];
        }
    
        /* ---------------------------
           Response
        ----------------------------*/
        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
    
    
    
    public function saleHandoverProductList(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $order_no = $request->input('order_no');
    
        $tbl_sales = DB::table('tbl_sales')
            ->where('order_no', $order_no)
            ->first();
    
        $results = DB::table('tbl_sales_product')
            ->where('order_no', $order_no)
            ->get()
            ->unique(function ($item) {
                return $item->product_type . '|' .
                       $item->product_code . '|' .
                       $item->barcode_use . '|' .
                       $item->base_price . '|' .
                       $item->discount_amt . '|' .
                       $item->handover_status . '|' .
                       $item->return_status . '|' .
                       $item->qty . '|' .
                       $item->no_of_glass . '|' .
                       $item->product_deatils;
            })
            ->values();
    
        if ($results->isEmpty()) {
            return response()->json('<div class="alert alert-danger">No data found.</div>');
        }
    
        $data = '
        <div class="container">
            <table id="datatable2" class="table card-table table-vcenter text-nowrap" style="color:#000;">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" onclick="toggleAll(this)"></th>
                        <th style="color:#000;">Order No</th>
                        <th style="color:#000;">Product</th>
                        <th style="color:#000;">Product Code</th>
                        <th style="color:#000;">Description</th>
                        <th style="color:#000;">Barcode</th>
                        <th style="color:#000;">Status</th>
                    </tr>
                </thead>
                <tbody>';
    
        foreach ($results as $product) {
    
            // Row color priority: Return > Handover
            if ($product->return_status == 1) {
                $rowClass = 'table-danger';
            } elseif ($product->handover_status == 1) {
                $rowClass = 'table-success';
            } else {
                $rowClass = '';
            }
    
            // Disable checkbox if handed over OR returned
            $isDisabled = ($product->handover_status == 1 || $product->return_status == 1)
                ? 'disabled'
                : '';
    
            // Status badge
            if ($product->return_status == 1) {
                $statusHtml = 'Returned';
            } elseif ($product->handover_status == 1) {
                $statusHtml = '
                   
                        Handed Over Date : '.date('d M, Y', strtotime($product->handover_date)).'
                    ';
            } else {
                $statusHtml = '<span class="badge bg-warning">Pending</span>';
            }
    
            $data .= '
                <tr class="'.$rowClass.'">
                    <td>
                        <input type="checkbox"
                               class="row-checkbox"
                               value="'.$product->id.'"
                               '.$isDisabled.'>
                    </td>
                    <td>'.$product->order_no.'</td>
                    <td>'.$product->product_type.'</td>
                    <td>'.$product->product_code.'</td>
                    <td>
                        <textarea class="form-control" readonly>'.$product->product_deatils.'</textarea>
                    </td>
                    <td>'.$product->barcode_use.'</td>
                    <td>'.$statusHtml.'</td>
                </tr>';
        }
    
        $data .= '
                </tbody>
            </table>
    
            <hr/>
    
            <div class="row">
                <div class="col-lg-4">
                    <label>Handover By</label>
                    <select class="form-control select" id="handover_by" name="handover_by">
                        <option value="">Select Person</option>';
    
        $tbl_users = DB::table("users")->where('status', 1)->get();
        foreach ($tbl_users as $user) {
            $data .= '<option value="'.$user->id.'">'.$user->name.' ('.$user->user_type.')</option>';
        }
    
        $data .= '
                    </select>
                </div>
    
                <div class="col-md-4">
                    <label>Handover Date</label>
                    <input type="datetime-local" class="form-control" id="handover_date" name="handover_date">
                </div>
            </div>
    
            <button class="btn btn-gradient mt-3" id="submitHanoverBtn" type="button">
                Submit
            </button>
        </div>
    
        <script>
            function toggleAll(source) {
                const checkboxes = document.querySelectorAll(".row-checkbox:not(:disabled)");
                checkboxes.forEach(cb => cb.checked = source.checked);
            }
        </script>';
    
        return response()->json($data);
    }


    
    
    public function saleHandoverStored(Request $request)
    {
        $user = auth()->user();
    
        DB::beginTransaction();
    
        try {
    
            $product_ids    = $request->input('product_id', []);
            $handover_date = $request->input('handover_date');
            $handover_by       = $request->input('handover_by');


            foreach ($product_ids as $product_id)
            {
    
                $product = DB::table('tbl_sales_product')
                    ->where('id', $product_id)
                    ->where('handover_status', 0) // prevent double return
                    ->first();
    
                if (!$product) {
                    continue;
                }
                
                if($product->product_type == 'Glass')
                {
                    if ($product->qty == 2) 
                    {
                        DB::table('tbl_sales_product')
                        ->where('order_no', $product->order_no)
                        ->where('product_type', $product->product_type)
                        ->where('product_code', $product->product_code)
                        ->where('product_deatils', $product->product_deatils)
                        ->where('barcode_use', $product->barcode_use)
                        ->where('base_price', $product->base_price)
                        ->where('handover_status', $product->handover_status)
                        ->where('return_status', $product->return_status)
                        ->where('qty', $product->qty)
                        ->where('no_of_glass', $product->no_of_glass)
                        
                        ->update([
                            'handover_status'   => 1,
                            'handover_by'    => $handover_by,
                            'handover_date' => $handover_date,
                            'updated_at'      => now(),
                        ]);
                    }
                    else
                    {
                        DB::table('tbl_sales_product')
                        ->where('id', $product_id)
                        ->update([
                            'handover_status'   => 1,
                            'handover_by'    => $handover_by,
                            'handover_date' => $handover_date,
                            'updated_at'      => now(),
                        ]);
                    }
                }
                else
                {
                    DB::table('tbl_sales_product')
                    ->where('id', $product_id)
                    ->update([
                        'handover_status'   => 1,
                        'handover_by'    => $handover_by,
                        'handover_date' => $handover_date,
                        'updated_at'      => now(),
                    ]);
                }
    


            }
    


            DB::commit();
    
            return response()->json([
                'status'  => true,
                'message' => 'Sale products handover successfully.'
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the sale handover process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



}