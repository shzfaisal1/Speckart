<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $setting['page_title'] = 'User List';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => 'All Users'],
        ];
        $users = User::withoutRole('Franchise')->latest()->paginate(10);
        return view('users.index', $setting, compact('users'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $setting['page_title'] = 'Create User';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['link' => url("/users"), 'name' => 'All Users'],
            ['name' => 'Create User'],
        ];
        $roles = Role::where('status','0')->where('name', '!=', 'Admin')->pluck('name', 'name')->all();

        return view('users.create', $setting, compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     
    protected function signupOtp(Request $request)
    {
        if(empty($request->contact))
        {
            $response['status_code'] = '201';
        }
        else
        {
            $user =User::where('phone', $request->contact)->first();
            if ($user) 
            {
                $response['status_code'] = '202';
               
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
                    'signupotp' => $otp,
                    'signupotp_stored_at' => now(),
                ]);   
                $response['status_code'] = '200';
            }    
        }
        
        return response()->json($response);
    }
    
    
    protected function checksignupOtp(Request $request)
    {
        if(empty($request->sotp))
        {
            $response['status'] = 'error';
        }
        else
        {
            $storedAt = session('signupotp_stored_at');
            if ($storedAt && now()->diffInSeconds($storedAt) < 60) 
            {
                 $signupotp = session('signupotp');
                 if($signupotp == $request->sotp)
                 {
                    session()->forget(['signupotp', 'signupotp_stored_at']); 
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
    
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'roles' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'mobile_no' => 'required',
            'sotp' => 'required',
            'address' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'pincode' => 'required',
        ]);
        $input = $request->all();
        
        $idgenerate = $this->generateUniqueRandomId(8, 'users', 'staff_id');
        
        if($input['roles'] == 'Warehouse' || $input['roles'] == 'Admin')
        {
            $store_id = 0;
        }
        else
        {
           $store_id = implode(',', $input['store_id']);implode(',', $request->store_id);
        }
        
        
        if (!empty($input['photo'])) {
                $chqName = Str::uuid() . '.' . $input['photo']->getClientOriginalExtension();
                $input['photo']->move(public_path('user-kyc/Photo'), $chqName);
    
                $photo_img = $chqName;
            } else {
                $photo_img =  NULL;
            }
            
            if (!empty($input['pan_img'])) {
                $chqName = Str::uuid() . '.' . $input['pan_img']->getClientOriginalExtension();
                $input['pan_img']->move(public_path('user-kyc/PAN'), $chqName);
    
                $pan_img = $chqName;
            } else {
                $pan_img =  NULL;
            }
    
    
    
            if (!empty($input['aadhar_front'])) {
                $chqName = Str::uuid() . '.' . $input['aadhar_front']->getClientOriginalExtension();
                $input['aadhar_front']->move(public_path('user-kyc/ADHAAR'), $chqName);
    
                $aadhar_front = $chqName;
            } else {
                $aadhar_front =  NULL;
            }
    
    
    
            if (!empty($input['aadhar_back'])) {
                $chqName = Str::uuid() . '.' . $input['aadhar_back']->getClientOriginalExtension();
                $input['aadhar_back']->move(public_path('user-kyc/ADHAAR'), $chqName);
    
                $aadhar_back = $chqName;
            } else {
                $aadhar_back =  NULL;
            }
        $input['staff_id']     = $idgenerate;
        $input['name']     = $input['name'];
        $input['email']    = $input['email'];
        $input['phone']    = $input['mobile_no'];
        $input['gender']   = $input['gender'];
        $input['dob']      = $input['date_from'];
        $input['doj']      = $input['date_from1'];
        $input['address']  = $input['address'];
        $input['state_id'] = $input['state_id'];
        $input['city_id']  = $input['city_id'];
        $input['pincode']  = $input['pincode'];
        $input['aadhar_no']= $input['aadhar_no'];
        $input['pan_no']   = $input['pan_no'];
        $input['password'] = Hash::make($input['password']);
        $input['user_type']= $input['roles'];
        $input['store_id'] = $store_id;
        $input['approve_discount']= $input['approve_discount'];
        $input['photo']   = $photo_img;
        $input['pan_img']   = $pan_img;
        $input['aadhar_front']   = $aadhar_front;
        $input['aadhar_back']   = $aadhar_back;
        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect('users')->with('success', 'User created successfully');
    }
    
    public function generateUniqueRandomId($length = 8, $table = 'users', $column = 'staff_id', $min = 100000, $max = 999999)
    {
        do {
            $id = random_int($min, $max);
        } while (
            DB::table($table)->where($column, $id)->exists()
        );
    
        return $id;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $setting['page_title'] = 'Show User';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['link' => url("/users"), 'name' => 'All Users'],
            ['name' => 'Show User'],
        ];
        $user = User::find($id);

        return view('users.show', $setting, compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $setting['page_title'] = 'User Edit';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['link' => url("/users"), 'name' => 'All Users'],
            ['name' => 'Edit User'],
        ];
        $user = User::find($id);
        $roles = Role::where('status','0')->where('name', '!=', 'Admin')->pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('users.edit', $setting, compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'roles' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'mobile_no' => 'required',
            'address' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'pincode' => 'required',
        ]);
        
        $input = $request->all();

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }
        
        if($input['roles'] == 'Warehouse' || $input['roles'] == 'Admin')
        {
            $store_id = 0;
        }
        else
        {
           $store_id = implode(',', $input['store_id']);
        }
        
        $checkdata =  DB::table("users")->where('id', $id)->first();

        if (empty($checkdata) || empty($id)) {
            return redirect()->back()->with('error', 'something went wrong!');
        } else {
        
            if (!empty($input['photo'])) {
                $chqName = Str::uuid() . '.' . $input['photo']->getClientOriginalExtension();
                $input['photo']->move(public_path('user-kyc/Photo'), $chqName);
    
                $photo_img = $chqName;
            } else {
                $photo_img =  $checkdata->photo;
            }
            
            if (!empty($input['pan_img'])) {
                $chqName = Str::uuid() . '.' . $input['pan_img']->getClientOriginalExtension();
                $input['pan_img']->move(public_path('user-kyc/PAN'), $chqName);
    
                $pan_img = $chqName;
            } else {
                $pan_img =  $checkdata->pan_img;
            }
    
    
    
            if (!empty($input['aadhar_front'])) {
                $chqName = Str::uuid() . '.' . $input['aadhar_front']->getClientOriginalExtension();
                $input['aadhar_front']->move(public_path('user-kyc/ADHAAR'), $chqName);
    
                $aadhar_front = $chqName;
            } else {
                $aadhar_front =  $checkdata->aadhar_front;
            }
    
    
    
            if (!empty($input['aadhar_back'])) {
                $chqName = Str::uuid() . '.' . $input['aadhar_back']->getClientOriginalExtension();
                $input['aadhar_back']->move(public_path('user-kyc/ADHAAR'), $chqName);
    
                $aadhar_back = $chqName;
            } else {
                $aadhar_back =  $checkdata->aadhar_back;
            }
            
        }    
        
        $input['name']     = $input['name'];
        $input['email']    = $input['email'];
        $input['phone']    = $input['mobile_no'];
        $input['gender']   = $input['gender'];
        $input['dob']      = $input['date_from'];
        $input['doj']      = $input['date_from1'];
        $input['address']  = $input['address'];
        $input['state_id'] = $input['state_id'];
        $input['city_id']  = $input['city_id'];
        $input['pincode']  = $input['pincode'];
        $input['aadhar_no']= $input['aadhar_no'];
        $input['pan_no']   = $input['pan_no'];
        $input['approve_discount']= $input['approve_discount'];
        $input['user_type'] = $input['roles'];
        $input['photo']   = $photo_img;
        $input['pan_img']   = $pan_img;
        $input['aadhar_front']   = $aadhar_front;
        $input['aadhar_back']   = $aadhar_back;
        $input['store_id']  = $store_id;

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        $user->assignRole($request->input('roles'));


            return redirect('users')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();

            return redirect('users')->with('success', 'User deleted successfully');
    }
    
    
    
    public function updateUserToggle(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:tbl_store,id',
            'field' => 'required|in:status',
            'value' => 'required|boolean',
        ]);
        
        //dd($request->id,$request->field,$request->value);
        

        $store = User::findOrFail($request->id);
        $store->update([
            $request->field => $request->value
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $request->field)) . ' updated successfully!',
        ]);
    }
}
