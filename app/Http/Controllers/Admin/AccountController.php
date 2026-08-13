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
use PDF;
use Carbon\Carbon;
use DateTime; 


class AccountController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:Account-Expenses', ['only' => ['storeExpenses','expensesDatatable']]);
    }

    public $view_route = 'account';
    
    
    public function accountDashboard()
    {
        $setting['page_title'] = 'account Dashboard';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view('layouts/account',$setting);
    }
    
    public function storeExpenses()
    {
        $setting['page_title'] = 'Store Expenses';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/store-expenses',$setting);
    }
    
    public function expensesDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        if($store_id == '0')
        {
            $totalData = DB::table('tbl_voucher')->where('voucher_type', 'expense');
        }
        else
        {
            $totalData = DB::table('tbl_voucher')->where('store_id', $store_id)->where('voucher_type', 'expense');
        }

        if ($date_from != '' && $date_to != '') {
            $totalData->whereBetween('voucher_date', [$date_from, $date_to]);
        }


        $totalData = $totalData->count();
        
        if($store_id == '0')
        {
            $templates = DB::table('tbl_voucher')->where('voucher_type', 'expense');
        }
        else
        {
            $templates = DB::table('tbl_voucher')->where('store_id', $store_id)->where('voucher_type', 'expense');
        }

        if ($date_from != '' && $date_to != '') 
        {
            $templates->whereBetween('voucher_date', [$date_from, $date_to]);
        }




        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (!empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                $tbl_store = Store::find($template->store_id);
                $person = User::find($template->added_by);
                $encryptedId = base64_encode($template->id);

                $nestedData['sr_no']    = $i++;
                $nestedData['exp_date'] = date('d M, Y', strtotime($template->voucher_date));
                $nestedData['voucher_no']     = $template->voucher_no;
                $nestedData['total_amount']  = $template->total_amount;
                $nestedData['purpose']   = $template->purpose;
                $nestedData['pay_remark']   = $template->pay_remark;
                $nestedData['added_by']   = $person->name;
                $nestedData['store_name']        = $tbl_store->store_name;
                $nestedData['encryptedId']  = $encryptedId;
                $nestedData['voucher_id']        = $template->id;
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
    
    
    public function voucherDestroy($id)
    {
        $user_id = auth()->user()->id;
        $decryptedId = base64_decode($id);
        
        $Is_delted = DB::table('tbl_voucher')->where('id', $decryptedId)->delete();
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Voucher was successfully deleted',
        ]);
    }
    
    
    public function expenseStored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'total_amount'      => 'required|string',
            'purpose'    => 'required|string',
        ]);
    
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        if($request->uid == '')
        {
            $lastId = DB::table('tbl_voucher')->max('id'); 
            $nextId = $lastId ? $lastId + 1 : 1;
            $voucherNo = 'VN' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            $brandId = DB::table('tbl_voucher')->insertGetId([
                'voucher_date'   => $request->voucher_date,
                'total_amount'   => $request->total_amount,
                'voucher_type'   => 'expense',
                'purpose'        => $request->purpose,
                'pay_remark'     => $request->pay_remark,
                'voucher_no'     => $voucherNo,
                'added_by'       => $user->id,
                'store_id'       => $user->store_id,
            ]);
            
            return response()->json(['success' => 'Expense created successfully.']);
        }
        else
        {
            DB::table('tbl_voucher')
            ->where('id', $request->uid)
            ->update([
                'voucher_date'   => $request->voucher_date,
                'total_amount'   => $request->total_amount,
                'purpose'   => $request->purpose,
                'pay_remark'   => $request->pay_remark,
                'updated_at'   => now(),
            ]);
        
          return response()->json(['success' => 'Expense update successfully.']);
        }
        
    }
    
    
    public function voucherRecepit($id)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Voucher Recepit';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        
        $voucher = DB::table('tbl_voucher')->where('id', $decryptedId)->first();
        $store= Store::where('id', $voucher->store_id)->first();

        $setting['voucher'] = $voucher;
        $setting['store'] = Store::find($voucher->store_id);
        $setting['voucher_id'] = $id;
        $setting['state'] = State::find($store->state_id);
        $setting['city'] = City::find($store->city_id);

        return view($this->view_route.'/recepit',$setting);
    }
    
    
    public function recepitpdf($id)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Sale invoice';
        
        $voucher = DB::table('tbl_voucher')->where('id', $decryptedId)->first();
        $store= Store::where('id', $voucher->store_id)->first();

        $setting['voucher'] = $voucher;
        $setting['store'] = Store::find($voucher->store_id);
        $setting['voucher_id'] = $id;
        $setting['state'] = State::find($store->state_id);
        $setting['city'] = City::find($store->city_id);
        
        $pdf = Pdf::loadView($this->view_route . '/recepit-pdf',$setting)
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);

        return $pdf->stream($decryptedId . '.pdf');
    }
    
    
    
    public function accountReceivable()
    {
        $setting['page_title'] = 'Account Receivable';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/account-receivable',$setting);
    }
    
    
    
}    