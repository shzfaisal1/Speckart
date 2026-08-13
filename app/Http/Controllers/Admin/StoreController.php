<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Store;
use App\Models\State;
use App\Models\City;
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


class StoreController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:Store-list|Store-Edit|Store-Create', ['only' => ['storeList', 'show']]);
        $this->middleware('permission:Store-Create', ['only' => ['StoreAddPage', 'StoreAdd']]);
        $this->middleware('permission:Store-Edit', ['only' => ['storeEditPage', 'StoreUpdate']]);
    }
    
    public $view_route = 'store';

    public function storeList()
    {
        $setting['page_title'] = 'Store';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/index', $setting);
    }
    
    public function StoreAddPage()
    {
        $setting['page_title'] = 'Create Store';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/store-add', $setting);
    }
    
    public function StoreAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_name'     => 'required|string',
            'contact_no'     => 'required|string',   
            'email_id'       => 'required|email',
            'state_id'       => 'required|integer',  
            'city_id'        => 'required|integer', 
            'store_address'  => 'required|string|max:255',
            'pincode'        => 'required|digits:6'
        ]);
    
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
       // dd($request->all());
       
       if($request->sales_tax_type == '0')
       {
            $tax_rule = '';
       }
       else
       {
           $tax_rule = $request->tax_rule;
       }
        
        // Create store entry
        $store = Store::create([
            'store_id' => 'SPECKART-'.mt_rand(1111, 9999),
            'store_name' => $request->store_name,
            'contact_no' => $request->contact_no,
            'email_id' => $request->email_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'store_address' => $request->store_address,
            'pincode' => $request->pincode,
            'barcode_name' => $request->barcode_name,
            'order_no_prefix' => $request->order_no_prefix,
            'Is_same_orderon' => $request->Is_same_orderon,
            'invoice_no_prefix' => $request->invoice_no_prefix,
            'next_order_no' => $request->next_order_no,
            'Is_orderno_autofill' => $request->Is_orderno_autofill,
            'Is_orderno_editable' => $request->Is_orderno_editable,
            'Is_bill_editable' => $request->Is_bill_editable,
            'sales_tax_type' => $request->sales_tax_type,
            'tax_rule' => $request->tax_rule,
            'sales_text_per' => $request->sales_text_per,
            'tax_voucher_entry' => $request->tax_voucher_entry,
            'min_advance_amt' => $request->min_advance_amt,
            'gst_no' => $request->gst_no,
            'bb_mobile_no' => $request->bb_mobile_no,
            'bb_email' => $request->bb_email,
            'print_cust_challan' => $request->print_cust_challan,
            'print_cust_invoice' => $request->print_cust_invoice,
            'terms_cond' => $request->terms_cond,
        ]);

        $store->save();

        return response()->json(['success' => 'Store created successfully.']);
        
    }
    
    
    
    public function storeData(Request $request)
    {
        $totalData = Store::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search1'))) {
            $templates = Store::offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $search = $request->input('search1');
            $templates = Store::where('store_id', 'like', '%' . $search . '%')
                ->orWhere('store_name', 'like', '%' . $search . '%')
                ->offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();
                // dd($search);

            $totalFiltered = Store::where('store_id', 'like', '%' . $search . '%')
                ->orWhere('store_name', 'like', '%' . $search . '%')
                ->count();
        }

        $data = [];
        if (! empty($templates))
        {
            foreach ($templates as $template) 
            {
                $encryptedId = Crypt::encrypt($template->id);
                $status = '<div class="toggle-btn">
                    <input type="checkbox" id="store_' . $template->id . '" class="toggle-switch" data-id="' . $template->id . '" data-field="status" ' . ($template->status ? 'checked' : '') . '>
                    <label for="store_' . $template->id . '">Toggle</label>
                </div>';
                $nestedData['store_id']   = $template->store_id;
                $nestedData['store_name'] = $template->store_name;
                $nestedData['contact_no'] = $template->contact_no;
                $nestedData['email_id']   = $template->email_id;
                $nestedData['gst_no']   = $template->gst_no;
                $nestedData['city_id']    = City::where('id',$template->city_id)->value('name');
                $nestedData['status']     = $status;
                $nestedData['created_at'] = date('d M, Y h:i A', strtotime($template->created_at));
                $nestedData['action']     = '<a href="'. route('admin.store-edit-page',[ "store_id" => $encryptedId]).'"><span class="badge badge-info">Edit</span></a>';
                $data[]                   = $nestedData;
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
    
    
    
    public function updateStoreToggle(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:tbl_store,id',
            'field' => 'required|in:status',
            'value' => 'required|boolean',
        ]);
        

        $store = Store::findOrFail($request->id);
        $store->update([
            $request->field => $request->value
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $request->field)) . ' updated successfully!',
        ]);
    }
    
    
    public function storeEditPage($store_id)
    {
        $storeId = Crypt::decrypt($store_id);
        if(empty($storeId))
        {
            return redirect()->back()->with('error', 'something went wrong!');
        }
        else
        {
            $store = Store::findOrFail($storeId);
            $setting['page_title'] = 'Update Store';
            $setting['breadcrumbs'] = [
                ['link' => url("/"), 'name' => 'Home'],
                ['name' => $setting['page_title']],
            ];
            return view($this->view_route.'/store-edit', $setting, compact('store'));
        }
        
    }
    
    
    public function StoreUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_name'     => 'required|string',
            'contact_no'     => 'required|string',   
            'email_id'       => 'required|email',
            'state_id'       => 'required|integer',  
            'city_id'        => 'required|integer', 
            'store_address'  => 'required|string|max:255',
            'pincode'        => 'required|digits:6'
        ]);
    
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        

        
        Store::where('id', $request->store_id)->update($request->except(['_token','_method']));

        return response()->json(['success' => 'Store update successfully.']);
        
    }
    
    
    public function changePassword()
    {
        $setting['page_title'] = 'Change Password';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view('auth/change-password',$setting);
    }
    
}