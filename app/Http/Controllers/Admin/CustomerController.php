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
use App\Models\Customer;
use App\Models\Prescription;
use App\Models\Eyetest;
use DateTime; 
use App\Jobs\ProcessCouponCreate;
use App\Models\CouponAuto;


class CustomerController extends Controller
{
    function __construct()
    {
        
    }
    
    public $view_route = 'customer';
    
    public function customerAdd()
    {
        $setting['page_title'] = 'Add Customer';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/add-customer',$setting);
    }
    
    
    public function customerStored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'cust_name'      => 'required|string',
            'email_id'       => 'required|email',
            'state_id'       => 'required|integer',  
            'city_id'        => 'required|integer', 
            'cust_address'   => 'required|string|max:255',
            'pincode'        => 'required|digits:6'
        ]);
    
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $idgenerate = $this->generateUniqueRandomId(6, 'tbl_customer', 'cust_unique_id');
        
        $customer = Customer::create([
            'cust_unique_id' => $idgenerate,
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
            'cust_note'            => $request->cust_note,
            'added_by'       => $user->id,
            'store_id'       => $user->store_id,

        ]);

        $customer->save();

        return response()->json(['success' => 'Customer created successfully.']);
        
    }
    
    public function generateUniqueRandomId($length = 6, $table = 'tbl_customer', $column = 'cust_unique_id', $min = 100000, $max = 999999)
    {
        do 
        {
            $id = 'C'.random_int($min, $max);
        } 
        while (
            DB::table($table)->where($column, $id)->exists()
        );
    
        return $id;
    }
    
    
    public function customerList()
    {
        $setting['page_title'] = 'Customer List';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/customer-list',$setting);
    }
    
    
    public function customerDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $search1 = $request->input('search1');

        $totalData = DB::table('tbl_customer')->where('is_Deleted', '0')->where('store_id', $store_id);
        
        if($store_id == 0)
        {
            $totalData = DB::table('tbl_customer')->where('is_Deleted', '0'); 
        }
        else
        {
             $totalData = DB::table('tbl_customer')->where('is_Deleted', '0')->where('store_id', $store_id);
        }
        if ($search1 != '') 
        {
            $totalData->where('cust_unique_id', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('email_id', 'like', '%' . $search1 . '%')
            ;
        }
        $totalData = $totalData->count();
        

        if($store_id == 0)
        {
            $templates = DB::table('tbl_customer')->where('is_Deleted', '0'); 
        }
        else
        {
             $templates = DB::table('tbl_customer')->where('is_Deleted', '0')->where('store_id', $store_id);
        }
       
        if ($search1 != '') 
        {
            $templates->where('cust_unique_id', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('email_id', 'like', '%' . $search1 . '%')
            ;
        }


        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('customer_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                $customer_id = base64_encode($template->customer_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['cust_unique_id'] = '<span class="badge badge-success">'.$template->cust_unique_id.'</span>';
                $nestedData['cust_name']  = $template->cust_name;
                $nestedData['cust_category'] = $template->cust_category;
                $nestedData['contact_no']  = $template->contact_no;
                $nestedData['email_id']    = $template->email_id;
                $nestedData['city_id']     = $template->city_id;
                $nestedData['cust_type']   = $template->cust_type;
                $nestedData['last_visit']  = '';
                $nestedData['customer_id'] = $customer_id;
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
    
    
    public function customerdestroy($id)
    {
        $user_id = auth()->user()->id;
        $decryptedId = base64_decode($id);
        $Is_delted = DB::table('tbl_customer')->where('customer_id', $decryptedId)->update(['is_Deleted' => 1]);
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Customer was successfully deleted',
        ]);
    }
    
    
    public function editCustomer($id)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Edit Customer';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        $setting['customer']         = Customer::where('customer_id', $decryptedId)->first();
        $setting['membership_cards'] = DB::table('tbl_membership_card')->where('flag', 0)->get();
        return view($this->view_route.'/edit-customer', $setting);
    }
    

    public function customerUpdate(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'cust_name'     => 'required|string',
            'email_id'       => 'required|email',
            'state_id'       => 'required|integer',  
            'city_id'        => 'required|integer', 
            'cust_address'   => 'required|string|max:255',
            'pincode'        => 'required|digits:6'
        ]);
    
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $idgenerate = $this->generateUniqueRandomId(6, 'tbl_customer', 'cust_unique_id');
        
        Customer::where('customer_id', $request->customer_id)->update([
            'cust_type'           => $request->cust_type,
            'cust_name'           => $request->cust_name,
            'contact_no'          => $request->contact_no,
            'email_id'            => $request->email_id,
            'cust_category'       => $request->cust_category,
            'gender'              => $request->gender,
            'cust_address'        => $request->cust_address,
            'state_id'            => $request->state_id,
            'city_id'             => $request->city_id,
            'pincode'             => $request->pincode,
            'dob'                 => $request->dob,
            'doa'                 => $request->doa,
            'cust_note'           => $request->cust_note,
            'membership_card_id'  => $request->membership_card_id ?: null,
            'membership_expiry'   => $request->membership_expiry ?: null,
            'updated_by'          => $user->id,
            'updated_at'          => now(),
        ]);
        
        return response()->json(['success' => 'Customer update successfully.']);
        
    }
    
    
    public function customerBirthdayList()
    {
        $setting['page_title'] = 'Customer Birthday List';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/customer-birthday-list',$setting);
    }
    

    public function customerBirthdayDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $search1 = $request->input('search1');

        $totalData = DB::table('tbl_customer')->where('is_Deleted', '0')->where('dob','!=', '')->where('store_id', $store_id);
        if ($search1 != '') 
        {
            $totalData->where('cust_unique_id', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('email_id', 'like', '%' . $search1 . '%')
            ;
        }
        $totalData = $totalData->count();
        
        $templates = DB::table('tbl_customer')->where('is_Deleted', '0')->where('dob','!=', '')->where('store_id', $store_id);
       
        if ($search1 != '') 
        {
            $templates->where('cust_unique_id', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('email_id', 'like', '%' . $search1 . '%')
            ;
        }


        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('customer_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                $customer_id = base64_encode($template->customer_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['cust_unique_id'] = '<span class="badge badge-success">'.$template->cust_unique_id.'</span>';
                $nestedData['cust_name']  = $template->cust_name;
                $nestedData['cust_category'] = $template->cust_category;
                $nestedData['contact_no']  = $template->contact_no;
                $nestedData['email_id']  = $template->email_id;
                $nestedData['city_id']  = $template->city_id;
                $nestedData['cust_type']  = $template->cust_type;
                $nestedData['dob']  = date("d-m-Y", strtotime($template->dob));
                $nestedData['customer_id']  = $customer_id;
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
    

    public function customerAnniversaryList()
    {
        $setting['page_title'] = 'Customer Anniversary List';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/customer-anniversary-list',$setting);
    }
    
    public function customerAnniversaryDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $search1 = $request->input('search1');

        $totalData = DB::table('tbl_customer')->where('is_Deleted', '0')->where('doa','!=', '')->where('store_id', $store_id);
        if ($search1 != '') 
        {
            $totalData->where('cust_unique_id', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('email_id', 'like', '%' . $search1 . '%')
            ;
        }
        $totalData = $totalData->count();
        
        $templates = DB::table('tbl_customer')->where('is_Deleted', '0')->where('doa','!=', '')->where('store_id', $store_id);
       
        if ($search1 != '') 
        {
            $templates->where('cust_unique_id', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('email_id', 'like', '%' . $search1 . '%')
            ;
        }


        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('customer_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                $customer_id = base64_encode($template->customer_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['cust_unique_id'] = '<span class="badge badge-success">'.$template->cust_unique_id.'</span>';
                $nestedData['cust_name']  = $template->cust_name;
                $nestedData['cust_category'] = $template->cust_category;
                $nestedData['contact_no']  = $template->contact_no;
                $nestedData['email_id']  = $template->email_id;
                $nestedData['city_id']  = $template->city_id;
                $nestedData['cust_type']  = $template->cust_type;
                $nestedData['doa']  = date("d-m-Y", strtotime($template->doa));
                $nestedData['customer_id']  = $customer_id;
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

    public function loyaltyProgram()
    {
        $store_id = auth()->user()->store_id;
        if($store_id ==0)
        {
            $user = auth()->user();
            $tbl_store =  DB::table("users")->where('id',$user->id)->first();
            $contact_no = $tbl_store->phone;
        }
        else
        {
            $tbl_store =  DB::table("tbl_store")->where('id',$store_id)->first();
            $contact_no = $tbl_store->contact_no;
        }
        $setting['page_title'] = 'Loyalty Program';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        $setting['contact_no'] =$contact_no;
        return view($this->view_route.'/loyalty-program',$setting);
    }
    
    
    public function loyaltyprogramDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $search1 = $request->input('search1');

        $totalData = DB::table('tbl_customer')->where('is_Deleted', '0')->where('cust_type', 'B2C')->where('store_id', $store_id);
        if ($search1 != '') 
        {
            $totalData->where('cust_unique_id', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('email_id', 'like', '%' . $search1 . '%')
            ;
        }
        $totalData = $totalData->count();
        
        $templates = DB::table('tbl_customer')->where('is_Deleted', '0')->where('cust_type', 'B2C')->where('store_id', $store_id);
       
        if ($search1 != '') 
        {
            $templates->where('cust_unique_id', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('email_id', 'like', '%' . $search1 . '%')
            ;
        }


        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('customer_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                $customer_id = base64_encode($template->customer_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['cust_unique_id'] = '<span class="badge badge-success">'.$template->cust_unique_id.'</span>';
                $nestedData['cust_name']  = $template->cust_name;
                $nestedData['cust_category'] = $template->cust_category;
                $nestedData['contact_no']  = $template->contact_no;
                $nestedData['Loyalty_Points']  = $template->Loyalty_Points;
                $nestedData['Loyalty_Points_Redeem']  = $template->Loyalty_Points_Redeem;
                $nestedData['Loyalty_Points_Bal']  = $template->Loyalty_Points_Bal;
                $nestedData['last_visit']  = '';
                $nestedData['customer_id']  = $customer_id;
                $nestedData['cid']  = $template->customer_id;
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
    
    
    public function viewloyaltyrogram($id)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Loyalty Points Statement';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        $setting['customer_id'] = $decryptedId;
        return view($this->view_route.'/view-loyaltyrogram',$setting);
    }
    

    public function statementloyaltyDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $customer_id = $request->input('customer_id');

        $totalData = DB::table('tbl_loyaltyrogram_histroy')->where('customer_id', $customer_id);
        $totalData = $totalData->count();
        $templates = DB::table('tbl_loyaltyrogram_histroy')->where('customer_id', $customer_id);



        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                if($template->add_remove == '1')
                {
                    $loyalty_type = '<span class="badge badge-info">Add</span>';
                    $Loyalty_Points_Redeem = '+'.$template->redeem;
                }
                else
                {
                    $loyalty_type = '<span class="badge badge-danger">Remove</span>';
                    $Loyalty_Points_Redeem = '-'.$template->redeem;
                }
                $nestedData['sr_no']    = $i++;
                $nestedData['date'] = $template->created_at;
                $nestedData['loyalty_type']  = $loyalty_type;
                $nestedData['total_point'] = $template->opening_points;
                $nestedData['redeem']  = $Loyalty_Points_Redeem;
                $nestedData['bal_point']  = $template->bal_point;
                $nestedData['description']  = $template->description;
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
    
    
    public function loyaltyAddremove(Request $request)
    {
        $user = auth()->user();
        $store_id = auth()->user()->store_id;
        
        $validator = Validator::make($request->all(), [
            'add_remove' => 'required',
            'Loyalty_Points_Redeem'      => 'required|numeric',
            'description'        => 'required',
        ]);
        
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }
        
        $tbl_customer = DB::table('tbl_customer')->where('customer_id', $request->customer_id)->first();
        
        $total_points = $tbl_customer->Loyalty_Points;
        
        if($request->add_remove == '2')
        {
            if($total_points == '0')
            {
                return response()->json(['error' => 'Loyalty point alredy 0 you can not remove points.']);
            }
            else
            {
               if($request->Loyalty_Points_Redeem > $total_points) 
               {
                   return response()->json(['error' => 'Pending Point should not be grather then loyalty point.']);
               }
               else
               {
                   $bal_point =  $total_points - $request->Loyalty_Points_Redeem;
               }
               
            }
        }
        elseif($request->add_remove == '1')
        {
            $bal_point =  $total_points + $request->Loyalty_Points_Redeem;
        }
        
        
        
        $transfer_id = DB::table('tbl_loyaltyrogram_histroy')->insertGetId([
            'customer_id'      => $request->customer_id,
            'opening_points'   => $tbl_customer->Loyalty_Points_Bal,
            'redeem'           => $request->Loyalty_Points_Redeem,
            'bal_point'        => $bal_point,
            'description'      => $request->description,
            'add_remove'       => $request->add_remove,
            'store_id'         => $user->store_id,
            'added_by'         => $user->id,
            'created_at'       => now(),
            'updated_at'       => now()
        ]);
        
        
        if($request->add_remove == '2')
        {
            DB::table('tbl_customer')->where('customer_id', $request->customer_id)->update([
                'Loyalty_Points_Redeem'      => $tbl_customer->Loyalty_Points_Redeem + $request->Loyalty_Points_Redeem,
                'Loyalty_Points_Bal'      => $total_points - $request->Loyalty_Points_Redeem,
                'updated_at' => now()
            ]);
        }
        elseif($request->add_remove == '1')
        {
            DB::table('tbl_customer')->where('customer_id', $request->customer_id)->update([
                'Loyalty_Points'      => $total_points + $request->Loyalty_Points_Redeem,
                'Loyalty_Points_Bal'      => $total_points + $request->Loyalty_Points_Redeem,
                'updated_at' => now()
            ]);
        }
        return response()->json(['success' => 'Loyalty point update successfully.']);
        
    } 
    

    public function generateToken()
    {
        $setting['page_title'] = 'Generate Token';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/generate-token',$setting);
    }
    
    
    public function getCustomer(Request $request)
    {
        $contact_no = $request->contact_no;
    
        $customer = DB::table('tbl_eye_test')->where('contact_no', $contact_no)->first();
    
        if ($customer) {
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $customer->cust_name,
                    'gender' => $customer->gender,
                    'age_group' => $customer->age_group,
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ]);
        }
    }
    
    
    
    public function tokenDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $search1 = $request->input('search1');
        $date_from= $request->input('date_from');
        

        $totalData = DB::table('tbl_eye_test')->where('store_id', $store_id);
        if ($search1 != '') 
        {
            $totalData->where('token_no', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%');
        }
        if ($date_from != '')
        {
            $totalData->whereBetween('created_at', [
                $date_from . ' 00:00:00',
                $date_from . ' 23:59:59'
            ]);
        }
        $totalData = $totalData->count();
        $templates = DB::table('tbl_eye_test')->where('store_id', $store_id);
        if ($search1 != '') 
        {
            $templates->where('token_no', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%');
        }
        if ($date_from != '')
        {
            $templates->whereBetween('created_at', [
                $date_from . ' 00:00:00',
                $date_from . ' 23:59:59'
            ]);
        }

        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('test_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                if($template->status == '0')
                {
                    $status = '<span class="badge badge-info">AR Pending</span>';
                }
                elseif($template->status == '1')
                {
                    $status = '<span class="badge badge-info">In Queue</span>';
                }
                elseif($template->status == '2')
                {
                    $status = '<span class="badge badge-success">Complete</span>';
                }
                elseif($template->status == '3')
                {
                    $status = '<span class="badge badge-warning">Skip</span>';
                }
                elseif($template->status == '4')
                {
                    $status = '<span class="badge badge-dark">Hold</span>';
                }
                elseif($template->status == '5')
                {
                    $status = '<span class="badge badge-danger">Cancel</span>';
                }
                
                $created_at =  Carbon::parse($template->created_at)->format('d-m-Y H:i A');
                $t_id= base64_encode($template->test_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['cust_details'] = $template->cust_name.'<BR>'.$template->contact_no;
                $nestedData['token_no']  = '<strong>S'.$template->token_no.'</strong>';
                $nestedData['wating_time'] = $template->waiting_time.' min';
                $nestedData['status']  = $status;
                $nestedData['created_at']  = $created_at;
                $nestedData['test_id']  = $template->test_id;
                $nestedData['tid']  = $t_id;
                $nestedData['estatus']  = $template->status;
                $nestedData['cust_name']  = $template->cust_name;
                $nestedData['contact_no']  = $template->contact_no;
                $nestedData['tokenno']  = $template->token_no;
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
    

    public function eyetesttokenStored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'cust_name'      => 'required|string',
            'contact_no'        => 'required'
        ]);
    
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        
        $checkcount = DB::table('tbl_eye_test')
        ->where('store_id', $user->store_id)
        ->whereDate('created_at', date('Y-m-d'))
        ->orderBy('test_id', 'desc')
        ->first();
        
        if(empty($checkcount))
        {
            $token_no = 1;
            $waiting_time = 0;
        }
        else
        {
            $token_no = $checkcount->token_no+1;
            
            
            $lastTime = new DateTime($checkcount->created_at);
            $currentTime = new DateTime();
            
            $diff = $currentTime->getTimestamp() - $lastTime->getTimestamp();
            $minutesPassed = floor($diff / 60); 
            
            if ($minutesPassed < 15) 
            {
                $extraMinutesNeeded = 15 - $minutesPassed;
                //echo "Only $minutesPassed minutes passed. Add $extraMinutesNeeded more minute(s) to reach 15.";
                
                $waiting_time = $extraMinutesNeeded+$minutesPassed;
            }
            else 
            {
                //echo "More than 15 minutes passed ($minutesPassed minutes).";
                $waiting_time = 0;
            }
            
           
        }
    
        $tokengenerate = Eyetest::create([
            'created_at'    => now(),
            'store_id'      => $user->store_id,
            'added_by'      => $user->id,
            'visit_purpose' => $request->visit_purpose,
            'token_no'      => $token_no,
            'waiting_time'  => $waiting_time,
            'contact_no'    => $request->contact_no,
            'cust_name'     => $request->cust_name,
            'age_group'     => $request->age_group,
            'city_id'       => $request->city_id,
            'gender'        => $request->gender,
            'status'        => 0,
        ]);

        $tokengenerate->save();

        return response()->json(['success' => 'Token generated  successfully.']);
        
    }
    
    
    public function printToken($id)
    {
        $user_id = auth()->user()->id;
        $decryptedId = base64_decode($id);
        $pdf = Pdf::loadView($this->view_route . '/token-pdf', ['test_id' => $decryptedId])
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);

        
        return $pdf->stream($decryptedId . '.pdf');
    }
    
    
    
    public function artestStored(Request $request)
    {
        $user = auth()->user();

        DB::table('tbl_eye_test')
        ->where('test_id', $request->id)
        ->update([
            'visit_rason'      => $request->visit_reason,
            'yob'              => $request->yob,
            'screen_time'      => $request->screen_time,
            'Occupation'       => $request->Occupation,
            'cust_carry'       => $request->cust_carry,
            'eye_test_before'  => $request->eye_test_before,
            're_sph'           => $request->re_sph,
            're_cyl'           => $request->re_cyl,
            're_axis'          => $request->re_axis,
            'le_sph'           => $request->le_sph,
            'le_cyl'           => $request->le_cyl,
            'le_axis'          => $request->le_axis,
            'remark_arpower'   => $request->remark_arpower,
            'right_eye'        => $request->right_eye,
            'left_eys'         => $request->left_eys,
            'both_eyes'        => $request->both_eyes,
            'status'           => 1,
            'updated_at' => now()
        ]);

        return response()->json(['success' => 'AR Test submit  successfully.']);
    }
    
    
    
    public function pretestQueue()
    {
        $setting['page_title'] = 'Pre Test Queue';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/pretest-queue',$setting);
    }
    
    
    public function prequeueDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $search1 = $request->input('search1');
        $date_from= $request->input('date_from');
        

        $totalData = DB::table('tbl_eye_test')->where('store_id', $store_id)->whereIn('status', [1,3,4]);
        if ($search1 != '') 
        {
            $totalData->where('token_no', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%');
        }
        if ($date_from != '')
        {
            $totalData->whereBetween('created_at', [
                $date_from . ' 00:00:00',
                $date_from . ' 23:59:59'
            ]);
        }
        $totalData = $totalData->count();
        $templates = DB::table('tbl_eye_test')->where('store_id', $store_id)->whereIn('status', [1,3,4]);
        if ($search1 != '') 
        {
            $templates->where('token_no', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%');
        }
        if ($date_from != '')
        {
            $templates->whereBetween('created_at', [
                $date_from . ' 00:00:00',
                $date_from . ' 23:59:59'
            ]);
        }

        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('test_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                if($template->status == '0')
                {
                    $status = '<span class="badge badge-info">AR Pending</span>';
                }
                elseif($template->status == '1')
                {
                    $status = '<span class="badge badge-info">In Queue</span>';
                }
                elseif($template->status == '2')
                {
                    $status = '<span class="badge badge-success">Complete</span>';
                }
                elseif($template->status == '3')
                {
                    $status = '<span class="badge badge-warning">Skip</span>';
                }
                elseif($template->status == '4')
                {
                    $status = '<span class="badge badge-dark">Hold</span>';
                }
                elseif($template->status == '5')
                {
                    $status = '<span class="badge badge-danger">Cancel</span>';
                }
                
                $created_at =  Carbon::parse($template->created_at)->format('d-m-Y H:i A');
                $t_id= base64_encode($template->test_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['cust_details'] = $template->cust_name.'<BR>'.$template->contact_no;
                $nestedData['token_no']  = '<strong>S'.$template->token_no.'</strong>';
                $nestedData['wating_time'] = $template->waiting_time.' min';
                $nestedData['status']  = $status;
                $nestedData['created_at']  = $created_at;
                $nestedData['test_id']  = $template->test_id;
                $nestedData['tid']  = $t_id;
                $nestedData['estatus']  = $template->status;
                $nestedData['cust_name']  = $template->cust_name;
                $nestedData['contact_no']  = $template->contact_no;
                $nestedData['tokenno']  = $template->token_no;

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
    
    
    public function eyetestholddestroy($id)
    {
        $Is_delted = DB::table('tbl_eye_test')->where('test_id', $id)->update(['status' => 4]);
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Eye Test On Hold Successfully',
        ]);
    }
    
    
    
    public function eyetestskipdestroy($id)
    {
        $Is_delted = DB::table('tbl_eye_test')->where('test_id', $id)->update(['status' => 3]);
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Eye Test Skip Successfully',
        ]);
    }
    
    
    
    public function eyetestStart($id)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Eye Test';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        $setting['Eyetest'] = Eyetest::where('test_id', $decryptedId)->first();
        return view($this->view_route.'/eye-test',$setting);
    }
    
    
    public function updatetestStep1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'optometrist' => 'required|string',
            're_sph' => 'required',
            're_cyl' => 'required',
            're_axis' => 'required|numeric',
            'le_sph' => 'required',
            'le_cyl' => 'required',
            'le_axis' => 'required|numeric',
        ]);
        
        
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

         DB::table('tbl_eye_test')->where('test_id', $request->test_id)
            ->update([
                'optometrist'      => $request->optometrist,
                're_sph'      => $request->re_sph,
                're_cyl'      => $request->re_cyl,
                're_axis'      => $request->re_axis,
                'le_sph'      => $request->le_sph,
                'le_cyl'      => $request->le_cyl,
                'le_axis'      => $request->le_axis,
                'updated_at' => now()
            ]);
    
        return response()->json(['success' => true]);
    }
    
    
   
    public function updatetestStep2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tbl_eye_test,test_id',
        ]);
        
        
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

         DB::table('tbl_eye_test')->where('test_id', $request->test_id)
            ->update([
                'test_status'      => $request->test_status,
                're_distance'      => $request->re_distance,
                'le_distance'      => $request->le_distance,
                're_pinhole'      => $request->re_pinhole,
                'le_pinhole'      => $request->le_pinhole,
                're_near'      => $request->re_near,
                'le_near'      => $request->le_near,
                'last_eye_test_date'      => $request->last_eye_test_date,
                'updated_at' => now()
            ]);
    
        return response()->json(['success' => true]);
    }
    
    
    public function updatetestStep3(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tbl_eye_test,test_id',
        ]);
        
        
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

         DB::table('tbl_eye_test')->where('test_id', $request->test_id)
            ->update([
                'torch_light'      => $request->torch_light,
                'cover_uncover'      => $request->cover_uncover,
                'convergence'      => $request->convergence,
                'right_eye'      => $request->right_eye,
                'left_eys'      => $request->left_eys,
                'both_eyes'      => $request->both_eyes,
                'reason_torch'      => $request->reason_torch,
                'reason_cover_uncover'      => $request->reason_cover_uncover,
                'reason_cover_uncover'      => $request->reason_cover_uncover,
                'reason_convergence'      => $request->reason_convergence,
                'updated_at' => now()
            ]);
    
        return response()->json(['success' => true]);
    }
    
    
    public function updatetestStep4(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tbl_eye_test,test_id',
        ]);
        
        
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

         DB::table('tbl_eye_test')->where('test_id', $request->test_id)
            ->update([
                're_green_red'      => $request->re_green_red,
                'le_green_red'      => $request->le_green_red,
                're_refined'      => $request->re_refined,
                'le_refined'      => $request->le_refined,
                're_balanced'      => $request->re_balanced,
                'le_balanced'      => $request->le_balanced,
                'updated_at' => now()
            ]);
    
        return response()->json(['success' => true]);
    }
    
    
    
    public function updatetestStep5(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tbl_eye_test,test_id',
            'additional_power' => 'required',
        ]);
        
        
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('tbl_eye_test')->where('test_id', $request->test_id)
            ->update([
                'additional_power'      => $request->additional_power,
                're_ap'      => $request->re_ap,
                'le_ap'      => $request->le_ap,
                'updated_at' => now()
            ]);
        return response()->json(['success' => true]);
    }
    
    
    public function updatetestStep6(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tbl_eye_test,test_id',
        ]);
        
        
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

         DB::table('tbl_eye_test')->where('test_id', $request->test_id)
            ->update([
                'p_verify_remark'      => $request->p_verify_remark,
                'updated_at' => now()
            ]);
    
        return response()->json(['success' => true]);
    }
    
    
    public function updatetestStep7(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tbl_eye_test,test_id',

        ]);
        
        
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

         DB::table('tbl_eye_test')->where('test_id', $request->test_id)
            ->update([
                're_sph_new'      => $request->re_sph_new,
                're_cyl_new'      => $request->re_cyl_new,
                're_axis_new'     => $request->re_axis_new,
                'pd_re_new'       => $request->pd_re_new,
                'le_sph_new'      => $request->le_sph_new,
                'le_cyl_new'      => $request->le_cyl_new,
                'le_axis_new'     => $request->le_axis_new,
                'pd_le_new'       => $request->pd_le_new,
                're_sph_bif'      => $request->re_sph_bif,
                're_cyl_bif'      => $request->re_cyl_bif,
                're_axis_bif'     => $request->re_axis_bif,
                'pd_re_bif'       => $request->pd_re_bif,
                'le_sph_bif'      => $request->le_sph_bif,
                'le_cyl_bif'      => $request->le_cyl_bif,
                'le_axis_bif'     => $request->le_axis_bif,
                'pd_le_bif'       => $request->pd_le_bif,
                're_distance_new' => $request->re_distance_new,
                'le_distance_new' => $request->le_distance_new,
                're_near_new'     => $request->re_near_new,
                'le_near_new'     => $request->le_near_new,
                'updated_at' => now()
            ]);
    
        return response()->json(['success' => true]);
    }
    
    
    public function updatetestStep8(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tbl_eye_test,test_id',
        ]);
        
        
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

         DB::table('tbl_eye_test')->where('test_id', $request->test_id)
            ->update([
                'frame_size'      => $request->frame_size,
                'followup_date'      => $request->followup_date,
                'updated_at' => now()
            ]);
    
        return response()->json(['success' => true]);
    }
    
    protected function testSendOtp(Request $request)
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
                    'eyetestotp' => $otp,
                    'eyetestotp_stored_at' => now(),
                ]);   
                $response['status_code'] = '200';
               
        }
        
        return response()->json($response);
    }
    
    
    protected function testotpVerify(Request $request)
    {
        if(empty($request->sotp))
        {
            $response['status'] = 'error';
        }
        else
        {
            $storedAt = session('eyetestotp_stored_at');
            if ($storedAt && now()->diffInSeconds($storedAt) < 60) 
            {
                 $eyetestotp = session('eyetestotp');
                 if($eyetestotp == $request->sotp)
                 {
                      DB::table('tbl_eye_test')->where('test_id', $request->otp_test_id)
                        ->update([
                            'status'      => 2,
                            'updated_at' => now()
                        ]);
            
                    session()->forget(['eyetestotp', 'eyetestotp_stored_at']); 
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
    
    
    
    public function eyeTestRecord()
    {
        $setting['page_title'] = 'Eye Test Record';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/test-record',$setting);
    }
    
    
    public function eyetestRecordDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $search1 = $request->input('search1');
        $date_from= $request->input('date_from');
        $date_to= $request->input('date_to');

        $totalData = DB::table('tbl_eye_test')->where('store_id', $store_id)->whereIn('status', [2]);
        if ($search1 != '') 
        {
            $totalData->where('token_no', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%');
        }
        if ($date_to != '' && $date_to != '') {
            $totalData->whereBetween('created_at', [$date_from,  $date_to . ' 23:59:59']);
        }
        $totalData = $totalData->count();
        $templates = DB::table('tbl_eye_test')->where('store_id', $store_id)->whereIn('status', [2]);
        if ($search1 != '') 
        {
            $templates->where('token_no', 'like', '%' . $search1 . '%')
            ->orWhere('contact_no', 'like', '%' . $search1 . '%')
            ->orWhere('cust_name', 'like', '%' . $search1 . '%');
        }
        if ($date_to != '' && $date_to != '') {
            $templates->whereBetween('created_at', [$date_from,  $date_to . ' 23:59:59']);
        }

        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('test_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                if($template->status == '0')
                {
                    $status = '<span class="badge badge-info">AR Pending</span>';
                }
                elseif($template->status == '1')
                {
                    $status = '<span class="badge badge-info">In Queue</span>';
                }
                elseif($template->status == '2')
                {
                    $status = '<span class="badge badge-success">Complete</span>';
                }
                elseif($template->status == '3')
                {
                    $status = '<span class="badge badge-warning">Skip</span>';
                }
                elseif($template->status == '4')
                {
                    $status = '<span class="badge badge-dark">Hold</span>';
                }
                elseif($template->status == '5')
                {
                    $status = '<span class="badge badge-danger">Cancel</span>';
                }
                
                $created_at =  Carbon::parse($template->created_at)->format('d-m-Y H:i A');
                $t_id= base64_encode($template->test_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['cust_details'] = $template->cust_name.'<BR>'.$template->contact_no;
                $nestedData['token_no']  = '<strong>S'.$template->token_no.'</strong>';
                $nestedData['status']  = $status;
                $nestedData['created_at']  = $created_at;
                $nestedData['test_id']  = $template->test_id;
                $nestedData['tid']  = $t_id;
                $nestedData['estatus']  = $template->status;
                $nestedData['cust_name']  = $template->cust_name;
                $nestedData['contact_no']  = $template->contact_no;
                $nestedData['tokenno']  = $template->token_no;

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
    
    
    public function eyetestPrescription($id,$idd)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Prescription';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        
        $eye_test = DB::table('tbl_eye_test')->where('test_id', $decryptedId)->first();
        $store= Store::where('id', $eye_test->store_id)->first();

        $setting['eye_test'] = $eye_test;
        $setting['salePerson'] = User::find($eye_test->added_by);
        $setting['state'] = State::find($store->state_id);
        $setting['city'] = City::find($store->city_id);
        $setting['testid'] = $id;
        $setting['printtype'] = $idd;
        $setting['store'] = Store::find($eye_test->store_id);

        return view($this->view_route.'/prescription',$setting);
    }
    
    
    public function prescriptionPdf($id,$idd)
    {
        $decryptedId = base64_decode($id);
        
        $eye_test = DB::table('tbl_eye_test')->where('test_id', $decryptedId)->first();
        $store= Store::where('id', $eye_test->store_id)->first();

        $setting['eye_test'] = $eye_test;
        $setting['salePerson'] = User::find($eye_test->added_by);
        $setting['state'] = State::find($store->state_id);
        $setting['city'] = City::find($store->city_id);
        $setting['testid'] = $id;
        $setting['printtype'] = $idd;
        $setting['store'] = Store::find($eye_test->store_id);
        
        $pdf = Pdf::loadView($this->view_route . '/prescription-pdf',$setting)
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);

        return $pdf->stream($decryptedId . '.pdf');
    }
    
    
    public function prescriptiondestroy($id)
    {
        $user_id = auth()->user()->id;
        $decryptedId = base64_decode($id);
        
        $Is_delted = DB::table('tbl_eye_test')->where('test_id', $decryptedId)->delete();
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Prescription was successfully deleted',
        ]);
    }
    
    
    
    public function setLoyaltypointOtp(Request $request)
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
                'lotp' => $otp,
                'lotp_stored_at' => now(),
            ]);   
            $response['status_code'] = '200';
        }
        
        return response()->json($response);
    }
    
    
    public function checksetloyaltypointvalueOtp(Request $request)
    {
        $user = auth()->user();
        if(empty($request->lotp))
        {
            $response['status'] = 'error';
        }
        else
        {
            $storedAt = session('lotp_stored_at');
            if ($storedAt && now()->diffInSeconds($storedAt) < 60) 
            {
                 $loyaltyotp = session('lotp');
                 if($loyaltyotp == $request->lotp)
                 {
                    session()->forget(['lotp', 'lotp_stored_at']); 
                    
                    DB::table('tbl_loyalty')
                    ->where('id', '1')
                    ->update([
                        'one_point_redem' => $request->one_point_redem,
                        'updated_by' => $user->id,
                        'updated_at' => now()
                    ]);
                    
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
    
    
    public function setautoLoyaltypointOtp(Request $request)
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
                'aotp' => $otp,
                'aotp_stored_at' => now(),
            ]);   
            $response['status_code'] = '200';
        }
        
        return response()->json($response);
    }
    
    
    public function setautoloyaltyprogram(Request $request)
    {
        $user = auth()->user();
        if(empty($request->aotp))
        {
            $response['status'] = 'error';
        }
        else
        {
            $storedAt = session('aotp_stored_at');
            if ($storedAt && now()->diffInSeconds($storedAt) < 60) 
            {
                 $loyaltyotp = session('aotp');
                 if($loyaltyotp == $request->aotp)
                 {
                    session()->forget(['aotp', 'aotp_stored_at']); 
                    
                    DB::table('tbl_loyalty')
                    ->where('id', '2')
                    ->update([
                        'auto_status' => $request->auto_status,
                        'sales_value' => $request->sales_value,
                        'auto_set_loyalty_point' => $request->auto_set_loyalty_point,
                        'no_of_points' => $request->no_of_points,
                        'x_number_sale_value' => $request->x_number_sale_value,
                        'fixed_per' => $request->fixed_per,
                        'order_use_loyalty' => $request->order_use_loyalty,
                        'updated_by' => $user->id,
                        'updated_at' => now()
                    ]);
                    
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
    
    
    public function discountCoupons()
    {
        $setting['page_title'] = 'Discount Coupons';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/discount-coupons',$setting);
    }
    
    
    public function discountCouponDatatable(Request $request)
    {

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $coupon_generate_type = $request->input('coupon_generate_type');
        $coupon_code = $request->input('coupon_code');
        $coupon_value = $request->input('coupon_value_manually');
        $date_to = $request->input('date_to');
        $date_from = $request->input('date_from');
        $mobile_no= $request->input('mobile_no');

        $templates = DB::table('tbl_coupon');

        $totalData = (clone $templates)->count();
        
        if (!empty($coupon_generate_type)) {
            $templates->where('coupon_generate_type', $coupon_generate_type);
        }
        
        if (!empty($date_from) && !empty($date_to)) {
            $templates->whereBetween('created_at', [$date_from,  $date_to . ' 23:59:59']);
        }
        
        if (!empty($coupon_code)) {
            $templates->where('coupon_code', $coupon_code);
        }
        
        if (!empty($coupon_value)) {
            $templates->where('coupon_value', $coupon_value);
        }
        
        if (!empty($mobile_no)) {
            $templates->where('contact_no', $mobile_no);
        }
        

        $totalFiltered = (clone $templates)->count();
        
        $templates = $templates
            ->offset($start)
            ->limit($limit)
            ->orderBy('coupon_id', 'DESC')
            ->get();


        $data = [];
        if (! empty($templates)) {
            foreach ($templates as $template) 
            {
                
                $nestedData['responsive_id']    = '';
                $nestedData['coupon_id']       = $template->coupon_id;
                
                if($template->coupon_generate_type == '0')
                {
                    $nestedData['coupon_generate_type'] = 'Auto'.' '.$template->sale_order_no;
                    $nestedData['valid_to']        = date("d-m-Y", strtotime($template->valid_to));
                }
                else
                {
                    $nestedData['coupon_generate_type'] = 'Manual';
                    $nestedData['valid_to']        = 'From : '.date("d-m-Y", strtotime($template->valid_from)).'To : '.date("d-m-Y", strtotime($template->valid_to));
                }
                $nestedData['coupon_code']        = $template->coupon_code;
                if($template->coupon_type == '0')
                {
                   $nestedData['coupon_value']        = $template->coupon_value.' %';
                }
                else
                {
                    $nestedData['coupon_value']        = 'Rs '.$template->coupon_value;
                }
                $nestedData['contact_no']        = $template->contact_no;
                $nestedData['min_sale_vale']        = $template->min_sale_vale;
                $nestedData['coupon_status']        = $template->coupon_status;
                
                if(!empty($template->coupon_usages_date))
                {
                     $nestedData['coupon_usages_date']        = date("d-m-Y", strtotime($template->coupon_usages_date));
                }
                else
                {
                    $nestedData['coupon_usages_date']        =  '-';
                }
                
                if($template->coupon_usages == '0')
                {
                     $nestedData['coupon_usages']        ='All Customer';
                }
                else
                {
                    $nestedData['coupon_usages']        =  'First Order';
                }
                
                $nestedData['created_at']        = date("d-m-Y h:i:A", strtotime($template->created_at));
                
                $data[]                        = $nestedData;
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
    
    
    public function manuallyCouponStored(Request $request)
    {
        $user = auth()->user();
    
        $no_of_coupon      = $request->no_of_coupon;
        $coupon_codes_type = $request->coupon_codes_type;
        $coupon_code       = $request->coupon_code;
        $coupon_type       = $request->coupon_type;
        $coupon_value      = $request->coupon_value_manually; // ✅ FIXED
        $min_sale_vale     = $request->min_sale_vale;
        $valid_from        = $request->valid_from;
        $valid_to          = $request->valid_to;
        $coupon_usages     = $request->coupon_usages;
        $couptype          = $request->couptype;
        $store_id          = $request->store_id;
        $cust_category     = $request->cust_category;
    
        $errors = [];
    
        // ✅ Validation
        if ($couptype == 0 && empty($no_of_coupon)) {
            $errors['no_of_coupon'] = "No of Coupons required";
        }
    
        if ($coupon_codes_type == 1 && empty($coupon_code)) {
            $errors['coupon_code'] = "Coupon code required";
        }
    
        if (empty($coupon_value)) {
            $errors['coupon_value_manually'] = "Coupon value required";
        }
    
        if (empty($min_sale_vale)) {
            $errors['min_sale_vale'] = "Minimum sale required";
        }
    
        if (empty($valid_from)) {
            $errors['valid_from'] = "Valid from required";
        }
    
        if (empty($valid_to)) {
            $errors['valid_to'] = "Valid to required";
        }
    

    
        // ❌ If errors
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'error' => $errors
            ]);
        }
    
        // ✅ Dispatch Job
        ProcessCouponCreate::dispatch(
            $user,
            $no_of_coupon,
            $coupon_codes_type,
            $coupon_code,
            $coupon_type,
            $coupon_value,
            $min_sale_vale,
            $valid_from,
            $valid_to,
            $coupon_usages,
            $couptype,
            $store_id,
            $cust_category
        );
    
        return response()->json([
            'success' => true,
            'message' => 'Coupon creation is being processed in background.'
        ]);
    }
    
    
    public function bulkDeleteCoupon(Request $request)
    {
        $user = auth()->user();
        $coupons_ids = $request->ids;
        $errorIDs = $successIds = 0;
        $errorIDs = count($coupons_ids);
        $coupondetailsCount = DB::table('tbl_coupon')->whereIn('coupon_id', $coupons_ids)->get();

        foreach ($coupondetailsCount as $coupondetails) 
        {
            $res=DB::table('tbl_coupon')->where('coupon_id',$coupondetails->coupon_id)->delete();

            $successIds++;
            $errorIDs--;
        }
        return response()->json([
            'status'  => true,
            'code'  => '200',
            'message' => $successIds . ' Coupon Deleted',
        ]);
   
    }
    
    
    public function deleteCouponRow(Request $request)
    {
        $coupon = DB::table('tbl_coupon_auto')->where('id',$request->couponId);
    
        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }
    
        $coupon->delete();
    
        return response()->json(['message' => 'Coupon deleted successfully']);
    }
    
    
    public function autoCouponStored(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'valid_dyas' => 'required|numeric|min:1',
    
            'from_range' => 'required|array|min:1',
            'to_range' => 'required|array|min:1',
            'coupon_value' => 'required|array|min:1',
            'sales_value_amount' => 'required|array|min:1',
    
            'from_range.*' => 'required|numeric',
            'to_range.*' => 'required|numeric|gte:from_range.*',
            'coupon_value.*' => 'required|numeric|min:1',
            'sales_value_amount.*' => 'required|numeric|min:1',
        ]);
        
       // dd($request);
    
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }
        
        CouponAuto::where('id', 1)->update([
            'auto_status' => $request->auto_status,
            'coupon_value_type' => $request->coupon_value_type,
            'sales_value' => $request->sales_value,
            'valid_dyas' => $request->valid_dyas,
        ]);
    
        foreach ($request->from_range as $index => $fromRange) {
        
            $data = [
                'from_range' => $fromRange,
                'to_range' => $request->to_range[$index],
                'coupon_value' => $request->coupon_value[$index],
                'sales_value_amount' => $request->sales_value_amount[$index] ?? 0,
            ];
        
            // UPDATE row
            if (!empty($request->auto_id[$index])) {
                CouponAuto::where('id', $request->auto_id[$index])->update($data);
            }
            // INSERT new row
            else {
                CouponAuto::create($data);
            }
        }
    
        return response()->json([
            'success' => 'Auto coupon configuration saved successfully'
        ]);
    }
    
    

}