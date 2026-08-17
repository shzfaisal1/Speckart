<?php
namespace App\Http\Controllers;

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
use App\Models\State;
use App\Models\City;
use App\Models\sale\Sale;
use App\Models\sale\SaleProduct;
use Carbon\Carbon;
use App\Models\Store;
use App\Models\User;
use App\Models\Customer;
use App\Models\Eyetest;


class OtherController extends Controller
{
    public $view_route = 'feedback';
    
    
    public function walkinDashboard()
    {
        $setting['page_title'] = 'Walkin Dashboard';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view('layouts/walkin',$setting);
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
    
    public function addWalkinRecord(Request $request)
    {
        //dd($request);
        DB::beginTransaction();
        
        
        try 
        {
            $user = auth()->user();
            $store_id = auth()->user()->store_id;
            /** -------------------------
             *   Handle Customer
             *  ------------------------- */
            $customer = DB::table('tbl_customer')->where('contact_no', $request->contact_no)->first();
            if (!$customer) {
                $customerId = $this->generateUniqueRandomId(6, 'tbl_customer', 'cust_unique_id');
                $customer = Customer::create([
                    'cust_unique_id' => $customerId,
                    'cust_type'      => 'B2C',
                    'cust_name'      => $request->cust_name,
                    'contact_no'     => $request->contact_no,
                    'cust_category'  => $request->cust_status,
                    'cust_address'   => $request->location,
                    'added_by'       => $user->id,
                    'store_id'       => $store_id,
                ]);
            }

            
            
            /** -------------------------
             *  Create Walkin
             *  ------------------------- */
             
            if($request->visit_purpose == 'Eye Test')
            { 
                $cust_status = 'EYE TEST';
            }
            else
            {
                $cust_status = 'ORDER PENDING';
            }
            $walkin_id = DB::table('tbl_walkin')->insertGetId([
                'added_by'          => $user->id,
                'store_id'          => $store_id,
                'walkin_date'       => date('Y-m-d'),
                'mobile_no'         => $request->contact_no,
                'cust_name'         => $request->cust_name,
                'location'          => $request->location,
                'visit_purpose'     => $request->visit_purpose,
                'cust_status'     => $cust_status,


            ]);
            
            
            if($request->visit_purpose == 'Eye Test')
            {
                 $checkcount = DB::table('tbl_eye_test')
                ->where('store_id', $store_id)
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
                    'added_by'       => $user->id,
                    'store_id'       => $store_id,
                    'visit_purpose' => 'Full Eye Test',
                    'token_no'      => $token_no,
                    'waiting_time'  => $waiting_time,
                    'contact_no'    => $request->contact_no,
                    'cust_name'     => $request->cust_name,
                    'age_group'     => '',
                    'city_id'       => '',
                    'gender'        =>'',
                    'status'        => 0,
                ]);
        
                $tokengenerate->save();
                    
            }
            

            
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'message' => 'Walkin entry add successfully.',
            ]);
            
            
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the walkin save process.',
                'error'   => $e->getMessage(),
            ], 500);
        }
        
    }
    
    
    public function updateWalkinRecord(Request $request)
    {
        DB::beginTransaction();
    
        try {
    
            $user = auth()->user();
            $store_id = $user->store_id;
    
            $walkin_id = $request->walkin_id;
    
            /** Get existing walkin */
            $walkin = DB::table('tbl_walkin')->where('id',$walkin_id)->first();
    
            /** Photo Upload */
            $photoPath = $walkin->upload_photo ?? null;
    
            if ($request->hasFile('photo')) {
    
                $file = $request->file('photo');
                $filename = time().'_repair.'.$file->getClientOriginalExtension();
    
                $photoPath = $file->storeAs('repair',$filename,'public');
            }
    
            /** Generate Repair Job ID only if repairing */
            $repair_job_id = $walkin->repair_job_id;
    
            if($request->walkintype == 'REPAIRING' && empty($repair_job_id))
            {
                $lastRepair = DB::table('tbl_walkin')
                    ->whereNotNull('repair_job_id')
                    ->orderBy('id','desc')
                    ->first();
    
                $nextId = $lastRepair ? (intval(substr($lastRepair->repair_job_id,8)) + 1) : 1;
    
                $repair_job_id = 'SPK-REP-' . str_pad($nextId,5,'0',STR_PAD_LEFT);
            }
    
            /** Update Walkin */
            DB::table('tbl_walkin')
                ->where('id',$walkin_id)
                ->update([
    
                    'frame_category' => $request->frame_category,
                    'brand_intrest' => $request->brand_intrest,
                    'budget_range' => $request->budget_range,
                    'eye_test' => $request->eye_test,
    
                    'cust_status' => $request->walkintype,
    
                    'Frame' => $request->Frame,
                    'Lens_Type' => $request->Lens_Type,
                    'order_amount' => $request->order_amount ?? 0,
                    'payment_mode' => $request->payment_mode,
                    'delivery_date' => $request->delivery_date,
    
                    'walkout_reason' => $request->walkout_reason,
                    'product_interest' => $request->product_interest,
                    'Lead_priority' => $request->Lead_priority,
                    'Follow_up_date' => $request->Follow_up_date,
    
                    'repair_product' => $request->repair_product,
                    'complaint' => $request->complaint,
                    'product_condition' => $request->product_condition,
                    'repair_type' => $request->repair_type,
                    'repair_delivery_date' => $request->repair_delivery_date,
    
                    'upload_photo' => $photoPath,
                    'repair_job_id' => $repair_job_id,
    
                    'updated_at' => now()
    
                ]);
    
            /** Walkout Followup */
            if($request->walkintype == 'WALKOUT')
            {
                DB::table('tbl_walkout_followup')->insert([
                    'walkin_id' => $walkin_id,
                    'Follow_up_date' => $request->Follow_up_date,
                    'walkout_reason' => $request->walkout_reason,
                    'added_by' => $user->id,
                    'store_id' => $store_id,
                ]);
            }
            
            
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Walkin entry updated successfully.'
            ]);
    
        }
        catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong during the walkin update.',
                'error' => $e->getMessage()
            ],500);
        }
    }
    
    
    public function updateFollowup(Request $request)
    {
        DB::table('tbl_walkout_followup')
            ->where('id',$request->id)
            ->update([
                $request->column => $request->value
            ]);
            
        if($request->value == 'Converted Customers')  
        {
            DB::table('tbl_walkin')
                ->where('id',$request->id)
                ->update([
                    'cust_status' => $request->walkintype,
                    'updated_at' => now()
                ]); 
        }
    
        return response()->json([
            'status' => 1,
            'msg' => 'Updated Successfully'
        ]);
    }
    
    
    public function getStorePerformance(Request $request)
    {
        $store_id = $request->store_id;
    
        $walkinQuery = DB::table('tbl_walkin')
            ->whereDate('walkin_date', date('Y-m-d'));
    
        if(!empty($store_id)){
            $walkinQuery->where('store_id', $store_id);
        }
    
        $walkins = $walkinQuery->count();
    
        $sales = DB::table('tbl_walkin')
            ->whereDate('walkin_date', date('Y-m-d'))
            ->where('cust_status','PURCHASED')
            ->when(!empty($store_id), function($q) use ($store_id){
                return $q->where('store_id',$store_id);
            })
            ->count();
    
        $walkouts = DB::table('tbl_walkin')
            ->whereDate('walkin_date', date('Y-m-d'))
            ->where('cust_status','WALKOUT')
            ->when(!empty($store_id), function($q) use ($store_id){
                return $q->where('store_id',$store_id);
            })
            ->count();
    
        $repairs = DB::table('tbl_walkin')
            ->where('cust_status','REPAIRING')
             ->whereDate('walkin_date', date('Y-m-d'))
            ->when(!empty($store_id), function($q) use ($store_id){
                return $q->where('store_id',$store_id);
            })
            ->count();
    
        $followups = DB::table('tbl_walkout_followup')
            ->whereDate('Follow_up_date', date('Y-m-d'))
            ->when(!empty($store_id), function($q) use ($store_id){
                return $q->where('store_id',$store_id);
            })
            ->count();
    
        $conversion = $walkins > 0 ? round(($sales/$walkins)*100,2) : 0;
    
        return response()->json([
            'walkins' => $walkins,
            'sales' => $sales,
            'walkouts' => $walkouts,
            'repairs' => $repairs,
            'followups' => $followups,
            'conversion' => $conversion
        ]);
    }
    
    
    public function getStaffPerformance(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');
        $store_id = $request->store_id;
    
        $query = DB::table('tbl_walkin')
            ->select(
                'added_by',
                DB::raw('COUNT(*) as walkins'),
                DB::raw("SUM(CASE WHEN cust_status='PURCHASED' THEN 1 ELSE 0 END) as sales"),
                DB::raw("SUM(CASE WHEN cust_status='WALKOUT' THEN 1 ELSE 0 END) as walkouts")
            )
            ->whereDate('walkin_date',$date)
            ->groupBy('added_by');
    
        if(!empty($store_id)){
            $query->where('store_id',$store_id);
        }
    
        $data = $query->get();
    
        $staff = [];
        $walkins = [];
        $sales = [];
        $conversion = [];
        $table = [];
    
        foreach($data as $row){
    
            $name = DB::table('users')->where('id',$row->added_by)->value('name');
    
            $conv = $row->walkins > 0 ? round(($row->sales/$row->walkins)*100,2) : 0;
    
            $staff[] = $name;
            $walkins[] = $row->walkins;
            $sales[] = $row->sales;
            $conversion[] = $conv;
    
            $table[] = [
                'name'=>$name,
                'walkins'=>$row->walkins,
                'sales'=>$row->sales,
                'walkouts'=>$row->walkouts
            ];
        }
    
        return response()->json([
            'staff'=>$staff,
            'walkins'=>$walkins,
            'sales'=>$sales,
            'conversion'=>$conversion,
            'table'=>$table
        ]);
    }
    
    
    
    public function getHeadOfficePerformance(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');
    
        $data = DB::table('tbl_walkin')
            ->select(
                'tbl_store.store_name',
                DB::raw('COUNT(tbl_walkin.id) as walkins'),
                DB::raw("SUM(CASE WHEN cust_status='PURCHASED' THEN 1 ELSE 0 END) as sales")
            )
            ->join('tbl_store','tbl_store.id','=','tbl_walkin.store_id')
            ->whereDate('walkin_date',$date)
            ->groupBy('tbl_walkin.store_id','tbl_store.store_name')
            ->get();
    
        $stores = [];
        $walkins = [];
        $sales = [];
        $conversion = [];
        $table = [];
    
        foreach($data as $row){
    
            $conv = $row->walkins > 0 ? round(($row->sales/$row->walkins)*100) : 0;
    
            $stores[] = $row->store_name;
            $walkins[] = $row->walkins;
            $sales[] = $row->sales;
            $conversion[] = $conv;
    
            $table[] = [
                'store'=>$row->store_name,
                'walkins'=>$row->walkins,
                'sales'=>$row->sales,
                'conversion'=>$conv
            ];
        }
    
        return response()->json([
            'stores'=>$stores,
            'walkins'=>$walkins,
            'sales'=>$sales,
            'conversion'=>$conversion,
            'table'=>$table
        ]);
    }
    
    
    public function storePerformanceMetrics(Request $request)
    {
    
        $store_id = $request->store_id;
    
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $monthStart = date('Y-m-01');
    
        $query = DB::table('tbl_walkin');
    
        if($store_id){
            $query->where('store_id',$store_id);
        }
    
        $walkins_today = (clone $query)->whereDate('walkin_date',$today)->count();
        $walkins_week = (clone $query)->whereBetween('walkin_date',[$weekStart,$today])->count();
        $walkins_month = (clone $query)->whereBetween('walkin_date',[$monthStart,$today])->count();
    
        $sales_today = (clone $query)->whereDate('walkin_date',$today)->where('cust_status','PURCHASED')->count();
        $sales_week = (clone $query)->whereBetween('walkin_date',[$weekStart,$today])->where('cust_status','PURCHASED')->count();
        $sales_month = (clone $query)->whereBetween('walkin_date',[$monthStart,$today])->where('cust_status','PURCHASED')->count();
    
        $walkout_today = (clone $query)->whereDate('walkin_date',$today)->where('cust_status','WALKOUT')->count();
        $walkout_week = (clone $query)->whereBetween('walkin_date',[$weekStart,$today])->where('cust_status','WALKOUT')->count();
        $walkout_month = (clone $query)->whereBetween('walkin_date',[$monthStart,$today])->where('cust_status','WALKOUT')->count();
    
        $conversion_today = $walkins_today>0 ? round(($sales_today/$walkins_today)*100) : 0;
        $conversion_week = $walkins_week>0 ? round(($sales_week/$walkins_week)*100) : 0;
        $conversion_month = $walkins_month>0 ? round(($sales_month/$walkins_month)*100) : 0;
    
        return response()->json([
            'walkins_today'=>$walkins_today,
            'walkins_week'=>$walkins_week,
            'walkins_month'=>$walkins_month,
    
            'sales_today'=>$sales_today,
            'sales_week'=>$sales_week,
            'sales_month'=>$sales_month,
    
            'walkout_today'=>$walkout_today,
            'walkout_week'=>$walkout_week,
            'walkout_month'=>$walkout_month,
    
            'conversion_today'=>$conversion_today,
            'conversion_week'=>$conversion_week,
            'conversion_month'=>$conversion_month,
    
            'follow_today'=>0,
            'follow_week'=>0,
            'follow_month'=>0
        ]);
    }
    
    
    public function walkoutReasonData(Request $request)
    {
    
        $store_id = $request->store_id;
        $date = $request->date;
    
        $query = DB::table('tbl_walkin')
            ->select('walkout_reason', DB::raw('count(*) as total'))
            ->where('cust_status','WALKOUT');
    
        if($store_id){
            $query->where('store_id',$store_id);
        }
    
        if($date){
            $query->whereDate('walkin_date',$date);
        }
    
        $reasons = $query->groupBy('walkout_reason')->get();
    
        $total = $reasons->sum('total');
    
        $data = [];
    
        foreach($reasons as $row){
    
            $percent = $total > 0 ? round(($row->total/$total)*100) : 0;
    
            $data[] = [
                'reason'=>$row->walkout_reason,
                'count'=>$row->total,
                'percent'=>$percent
            ];
        }
    
        return response()->json($data);
    }
    
    
    public function getStaffPerformanceoverall(Request $request)
    {
    
        $store_id = $request->store_id;
        $date = $request->date;
    
        $query = DB::table('tbl_walkin')
            ->join('users','users.id','=','tbl_walkin.added_by')
            ->join('tbl_store','tbl_store.id','=','tbl_walkin.store_id')
            ->select(
                'users.id as user_id',
                'users.name as staff',
                'tbl_store.store_name',
                DB::raw("COUNT(tbl_walkin.id) as walkins"),
                DB::raw("SUM(CASE WHEN cust_status='PURCHASED' THEN 1 ELSE 0 END) as sales"),
                DB::raw("SUM(CASE WHEN cust_status='WALKOUT' THEN 1 ELSE 0 END) as walkouts")
            );
    
        if($store_id){
            $query->where('tbl_walkin.store_id',$store_id);
        }
    
        if($date){
            $query->whereDate('tbl_walkin.walkin_date',$date);
        }
    
        $data = $query->groupBy('users.id','users.name','tbl_store.store_name')->get();
    
    
        $result = [];
    
        foreach($data as $row){
    
            $followups = DB::table('tbl_walkout_followup')
                ->where('added_by',$row->user_id)
                ->count();
    
            $result[] = [
                'staff'=>$row->staff.' ('.$row->store_name.')',
                'walkins'=>$row->walkins,
                'sales'=>$row->sales,
                'walkouts'=>$row->walkouts,
                'followups'=>$followups
            ];
        }
    
        return response()->json($result);
    }
    
    
    public function storeComparison(Request $request)
    {
    
        $date = $request->date;
    
        $data = DB::table('tbl_store')
            ->leftJoin('tbl_walkin','tbl_walkin.store_id','=','tbl_store.id')
            ->select(
                'tbl_store.store_name',
                DB::raw("COUNT(tbl_walkin.id) as walkins"),
                DB::raw("SUM(CASE WHEN cust_status='PURCHASED' THEN 1 ELSE 0 END) as sales"),
                DB::raw("SUM(CASE WHEN cust_status='WALKOUT' THEN 1 ELSE 0 END) as walkouts")
            )
            ->where('tbl_store.status',1)
            ->when($date,function($q) use ($date){
                $q->whereDate('tbl_walkin.walkin_date',$date);
            })
            ->groupBy('tbl_store.store_name')
            ->get();
    
        $result = [];
    
        foreach($data as $row){
    
            $conversion = $row->walkins > 0 
                ? round(($row->sales/$row->walkins)*100) 
                : 0;
    
            $result[] = [
                'store'=>$row->store_name,
                'walkins'=>$row->walkins,
                'sales'=>$row->sales,
                'walkouts'=>$row->walkouts,
                'conversion'=>$conversion
            ];
        }
    
        return response()->json($result);
    }
    
    public function walkoutAllReasons(Request $request)
    {
    
        $date = $request->date;
    
        $data = DB::table('tbl_walkin')
            ->select(
                'walkout_reason',
                DB::raw("COUNT(*) as total")
            )
            ->where('cust_status','WALKOUT')
            ->when($date,function($q) use ($date){
                $q->whereDate('walkin_date',$date);
            })
            ->groupBy('walkout_reason')
            ->get();
    
        $result = [];
    
        foreach($data as $row){
    
            $result[] = [
                'reason'=>$row->walkout_reason,
                'count'=>$row->total
            ];
        }
    
        return response()->json($result);
    }
    
    
    public function followupDashboard(Request $request)
    {
    
        $store_id = $request->store_id;
        $date = $request->date;
    
        $query = DB::table('tbl_walkout_followup');
    
        if(!empty($store_id)){
            $query->where('store_id',$store_id);
        }
    
        if(!empty($date)){
            $query->whereDate('follow_up_date',$date);
        }
    
        $pending = (clone $query)
            ->where('followup_response','Pending Follow-ups')
            ->count();
    
        $calls_done = (clone $query)
            ->where('followup_response','Calls Done Today')
            ->count();
    
        $converted = (clone $query)
            ->where('followup_response','Converted Customers')
            ->count();
    
        $conversion = $calls_done > 0 
            ? round(($converted / $calls_done) * 100) 
            : 0;
    
        return response()->json([
            'pending' => $pending ?? 0,
            'calls_done' => $calls_done ?? 0,
            'converted' => $converted ?? 0,
            'conversion' => $conversion ?? 0
        ]);
    
    }
    
    public function feedbackDashboardA()
    {
        $setting['page_title'] = 'After 48 Hours Sales Dashboard';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/feedback-A',$setting);
    }
    
    
    public function walkinDatatable(Request $request)
    {
        $user_store = auth()->user()->store_id;
    
        $limit = $request->input('length');
        $start = $request->input('start');
    
        $date_from = $request->input('date_from');
        $date_to   = $request->input('date_to');
        $search    = $request->input('search1');
        $store_id  = $request->input('store_id');
    
        $query = DB::table('tbl_walkin');
    
        /* Store Filter */
        if ($user_store != 0) {
            $query->where('store_id', $user_store);
        }
    
        if (!empty($store_id)) {
            $query->where('store_id', $store_id);
        }
    
        /* Date Filter */
        if (!empty($date_from) && !empty($date_to)) {
            $query->whereBetween('walkin_date', [$date_from, $date_to]);
        }
    
        /* Search Filter */
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('mobile_no', 'like', "%$search%")
                  ->orWhere('cust_name', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%")
                  ->orWhere('visit_purpose', 'like', "%$search%");
            });
        }
    
        /* Total Records */
        $totalData = $query->count();
    
        /* Fetch Data */
        $walkins = $query->offset($start)
            ->limit($limit)
            ->orderBy('id', 'DESC')
            ->get();
    
        $data = [];
        $i = $start + 1;
    
        foreach ($walkins as $row) {
    
            $storeName = DB::table('tbl_store')
                ->where('id', $row->store_id)
                ->value('store_name');
    
            $staffName = DB::table('users')
                ->where('id', $row->added_by)
                ->value('name');
    
            $nestedData = [];
    
            $nestedData['sr_no'] = $i++;
    
            $nestedData['cust_details'] = '
                <b>'.$row->cust_name.'</b><br>
                '.$row->mobile_no.'<br>
                '.$row->location.'
            ';
    
            $nestedData['visit_purpose'] = $row->visit_purpose;
    

            $nestedData['final_stage'] =$row->cust_status ;
    
            $nestedData['store_name'] = $storeName ?? '-';
    
            $nestedData['staff_name'] = $staffName ?? '-';
    
            $nestedData['walkin_date'] = date('d M Y', strtotime($row->walkin_date));
    
            $nestedData['id'] = $row->id;
            
            $nestedData['mobile_no'] =$row->mobile_no ;
            $nestedData['cust_name'] =$row->cust_name ;
            $nestedData['location'] =$row->location ;
            $nestedData['frame_category'] =$row->frame_category ;
            $nestedData['brand_intrest'] =$row->brand_intrest ;
            $nestedData['budget_range'] =$row->budget_range ;
            $nestedData['eye_test'] =$row->eye_test ;
            $nestedData['walkout_reason'] =$row->walkout_reason ;
            $nestedData['product_interest'] =$row->product_interest ;
            $nestedData['Lead_priority'] =$row->Lead_priority ;
            $nestedData['Follow_up_date'] =$row->Follow_up_date ;
            
            $nestedData['Frame'] =$row->Frame ;
            $nestedData['Lens_Type'] =$row->Lens_Type ;
            $nestedData['order_amount'] =$row->order_amount ;
            $nestedData['payment_mode'] =$row->payment_mode ;
            $nestedData['delivery_date'] =$row->delivery_date ;
            
            $nestedData['repair_product'] =$row->repair_product ;
            $nestedData['complaint'] =$row->complaint ;
            $nestedData['product_condition'] =$row->product_condition ;
            $nestedData['upload_photo'] =$row->upload_photo ;
            $nestedData['repair_type'] =$row->repair_type ;
            $nestedData['repair_delivery_date'] =$row->repair_delivery_date ;
            
    
            $nestedData['action'] = '';
    
            $data[] = $nestedData;
        }
    
        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalData,
            "data" => $data
        ];
    
        return response()->json($json_data);
    }
    
    
    public function walkinDestroy($id)
    {
        $tbl_walkin = DB::table('tbl_walkin')->find(1);
        if ($tbl_walkin) {
            $tbl_walkin->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Waklin entry was successfully deleted',
        ]);
    }
    
    
    public function followupList(Request $request)
    {
        $store_id = $request->store_id;
        $date = $request->date;
    
        $query = DB::table('tbl_walkout_followup as f')
            ->leftJoin('tbl_walkin as w','w.id','=','f.walkin_id')
            ->leftJoin('users as u','u.id','=','f.added_by')
            ->leftJoin('tbl_store as s','s.id','=','f.store_id')
            ->select(
                'f.*',
                'w.cust_name',
                'w.mobile_no',
                'w.walkin_date',
                'u.name as staff_name',
                's.store_name'
            );
    
        if($store_id != ''){
            $query->where('f.store_id',$store_id);
        }
    
        if($date != ''){
            $query->whereDate('f.follow_up_date',$date);
        }
    
        $data = $query->get();
    
        return response()->json($data);
    }
    

    
    public function dahsboardADatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit  = (int) $request->input('length', 10);
        $start  = (int) $request->input('start', 0);
        $draw   = (int) $request->input('draw');
    
        $date_from   = $request->input('date_from');
        $date_to     = $request->input('date_to');
        $search      = $request->input('search1');
        $productType = $request->input('product_type');
    
        /* =====================================================
           STEP 1: COMPLETED ORDERS (ALL PRODUCTS HANDED OVER)
        ======================================================*/
        $completedSales = DB::table('tbl_sales_product')
            ->select('sale_id')
            ->groupBy('sale_id')
            ->havingRaw('COUNT(*) = SUM(CASE WHEN handover_status = 1 THEN 1 ELSE 0 END)')
            ->pluck('sale_id');
    
        /* =====================================================
           STEP 2: BASE QUERY
        ======================================================*/
        $query = SaleProduct::whereIn('sale_id', $completedSales);
    
        if ($store_id != 0) {
            $query->where('store_id', $store_id);
        }
    
        /* =====================================================
           STEP 3: ONE ROW PER ORDER (sale_id wise)
        ======================================================*/
        $collection = $query->get()
            ->groupBy('sale_id')
            ->map(function ($items) {
                return $items->first(); // one record per order
            })
            ->values();
    
        /* =====================================================
           STEP 4: SHOW ONLY AFTER HANDOVER + 2 DAYS
        ======================================================*/
        $now = Carbon::now();
    
        $collection = $collection->filter(function ($item) use ($now) {
    
            if (empty($item->handover_date)) {
                return false;
            }
    
            $eligibleDate = Carbon::parse($item->handover_date)
                ->addDays(2);
    
            return $eligibleDate->lte($now);
        });
    
        /* =====================================================
           STEP 5: OTHER FILTERS
        ======================================================*/
        if (!empty($productType)) {
            $collection = $collection->where('product_type', $productType);
        }
    
        if (!empty($date_from) && !empty($date_to)) {
            $from = Carbon::parse($date_from)->startOfDay();
            $to   = Carbon::parse($date_to)->endOfDay();
    
            $collection = $collection->filter(function ($item) use ($from, $to) {
                return Carbon::parse($item->handover_date)
                    ->between($from, $to);
            });
        }
    
        if (!empty($search)) {
            $collection = $collection->filter(function ($item) use ($search) {
                return str_contains($item->order_no, $search);
            });
        }
    
        /* =====================================================
           STEP 6: RECORD COUNTS
        ======================================================*/
        $recordsTotal    = $collection->count();
        $recordsFiltered = $recordsTotal;
    
        /* =====================================================
           STEP 7: STATUS COUNTS
        ======================================================*/
        $saleIds = $collection->pluck('sale_id')->unique();
    
        $feedbackRows = DB::table('tbl_sales')
            ->select('sale_id','feedback_status_a')
            ->whereIn('sale_id', $saleIds)
            ->get();
    
        $totalNotConnected = 0;
        $totalConnected    = 0;
        $totalRinging      = 0;
        $totalFollowup     = 0;
    
        foreach ($feedbackRows as $fb) {
    
            $status = strtolower(trim($fb->feedback_status_a ?? 'not connected'));
    
            switch ($status) {
                case 'connected': $totalConnected++; break;
                case 'ringing':   $totalRinging++; break;
                case 'followup':  $totalFollowup++; break;
                default:          $totalNotConnected++;
            }
        }
    
        /* =====================================================
           STEP 8: PAGINATION
        ======================================================*/
        $paginated = $collection
            ->slice($start, $limit)
            ->values();
    
        /* =====================================================
           STEP 9: DATA FORMAT
        ======================================================*/
        $data = [];
        $i = $start + 1;
    
        foreach ($paginated as $row) {
    
            $sale = DB::table('tbl_sales')
                ->where('sale_id', $row->sale_id)
                ->first();
    
            $store = Store::find($row->store_id);
            $handover_by = User::find($row->handover_by);
    
            /* HANDOVER + 2 DAYS DATE */
            $saleDateTime = $row->handover_date
                ? Carbon::parse($row->handover_date)
                    ->addDays(2)
                    ->format('d M, Y h:i A')
                : '-';
    
            $encryptedId = $sale ? base64_encode($sale->sale_id) : '';
    
            /* Feedback Badge */
            $statusRaw = strtolower($sale->feedback_status_a ?? 'not connected');
    
            switch ($statusRaw) {
                case 'connected':
                    $feedbackStatus = '<span class="badge bg-success">Connected</span>';
                    break;
                case 'ringing':
                    $feedbackStatus = '<span class="badge bg-warning text-dark">Ringing</span>';
                    break;
                case 'followup':
                    $feedbackStatus = '<span class="badge bg-primary">Followup</span>';
                    break;
                default:
                    $feedbackStatus = '<span class="badge bg-danger">Not Connected</span>';
            }
    
            $feedbackDate = !empty($sale->feedback_a_datetime)
                ? Carbon::parse($sale->feedback_a_datetime)->format('d M, Y h:i A')
                : '-';
    
            $feedbackText = $sale->feedback_a ?? '-';
    
            $data[] = [
                'sr_no' => $i++,
    
                'order_details' =>
                    '<strong>Order Date:</strong> ' .
                    ($sale ? date('d M, Y h:i A', strtotime($sale->sale_date)) : '-') .
                    '<br><strong>Order No:</strong> ' . ($row->order_no ?? '-'),
    
                'store_details' =>
                    '<strong>Store Name:</strong> ' . ($store->store_name ?? '-'),
    
                'sale_datetime' => $saleDateTime,
    
                'customer_details' =>
                    '<strong>Customer Name:</strong> ' . ($sale->cust_name ?? '-') .
                    '<br><strong>Mobile:</strong> ' . ($sale->contact_no ?? '-') .
                    '<br><strong>Cust ID:</strong> ' . ($sale->cust_id ?? '-'),
    
                'handover_by' => $handover_by->name ?? '-',
    
                'feedback_status' => $feedbackStatus.'<br>'.$feedbackDate,
                'feedback'        => $feedbackText,
            ];
        }
    
        /* =====================================================
           FINAL RESPONSE
        ======================================================*/
        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
    
            'status_counts' => [
                'not_connected' => $totalNotConnected,
                'connected'     => $totalConnected,
                'ringing'       => $totalRinging,
                'followup'      => $totalFollowup,
            ]
        ]);
    }
    
    
    
    public function saleFeedbackUpdated(Request $request)
    {
        $request->validate([
            'uid' => 'required',
            'feedback_a' => 'required'
        ]);
    
        $user = auth()->user();
    
        DB::beginTransaction();
    
        try {
    
           
    
            if ($request->ftype === 'twodays')
            {
    
                DB::table('tbl_sales')
                    ->where('sale_id', $request->uid)
                    ->update([
                        'feedback_status_a' => $request->feedback_status_a,
                        'feedback_a_datetime' => $request->feedback_a_datetime,
                        'feedback_a' => $request->feedback_a,
                        'feedback_a_added_by' => $user->id,
                    ]);
            } else {
    
                DB::table('tbl_sales')
                    ->where('sale_id', $request->uid)
                    ->update([
                        'feedback_status_b' => $request->feedback_status_b,
                        'feedback_b_datetime' => $request->feedback_b_datetime,
                        'feedback_b' => $request->feedback_b,
                        'feedback_b_added_by' => $user->id,
                    ]);
            }
    

    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Feedback updated successfully.'
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong during Feedback process.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function feedbackDashboardB()
    {
        $setting['page_title'] = 'After 6 Month Sales Dashboard';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/feedback-B',$setting);
    }
    
    
    public function dahsboardBDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit  = (int) $request->input('length', 10);
        $start  = (int) $request->input('start', 0);
        $draw   = (int) $request->input('draw');
    
        $date_from   = $request->input('date_from');
        $date_to     = $request->input('date_to');
        $search      = $request->input('search1');
        $productType = $request->input('product_type');
    
        /* =====================================================
           STEP 1: COMPLETED ORDERS
        ======================================================*/
        $completedSales = DB::table('tbl_sales_product')
            ->select('sale_id')
            ->groupBy('sale_id')
            ->havingRaw('COUNT(*) = SUM(CASE WHEN handover_status = 1 THEN 1 ELSE 0 END)')
            ->pluck('sale_id');
    
        /* =====================================================
           STEP 2: BASE QUERY
        ======================================================*/
        $query = SaleProduct::whereIn('sale_id', $completedSales);
    
        if ($store_id != 0) {
            $query->where('store_id', $store_id);
        }
    
        /* =====================================================
           ✅ STEP 3: ONE ROW PER ORDER (IMPORTANT FIX)
        ======================================================*/
        $collection = $query->get()
            ->groupBy('sale_id')
            ->map(function ($items) {
                return $items->first();
            })
            ->values();
    
        /* =====================================================
           STEP 4: 6 MONTH ELIGIBILITY FILTER
        ======================================================*/
        $now = Carbon::now();
    
        $collection = $collection->filter(function ($item) use ($now) {
    
            if (empty($item->handover_date)) {
                return false;
            }
    
            $eligibleDate = Carbon::parse($item->handover_date)
                ->addMonths(6);
    
            return $eligibleDate->lte($now);
        });
    
        /* =====================================================
           STEP 5: OTHER FILTERS
        ======================================================*/
        if (!empty($productType)) {
            $collection = $collection->where('product_type', $productType);
        }
    
        if (!empty($date_from) && !empty($date_to)) {
            $from = Carbon::parse($date_from)->startOfDay();
            $to   = Carbon::parse($date_to)->endOfDay();
    
            $collection = $collection->filter(function ($item) use ($from, $to) {
    
                $nextDate = Carbon::parse($item->handover_date)
                    ->addMonths(6);
    
                return $nextDate->between($from, $to);
            });
        }
    
        if (!empty($search)) {
            $collection = $collection->filter(function ($item) use ($search) {
                return str_contains($item->order_no, $search);
            });
        }
    
        /* =====================================================
           STEP 6: RECORD COUNTS
        ======================================================*/
        $recordsTotal    = $collection->count();
        $recordsFiltered = $recordsTotal;
    
        /* =====================================================
           STATUS COUNTS
        ======================================================*/
        $saleIds = $collection->pluck('sale_id')->unique();
    
        $feedbackRows = DB::table('tbl_sales')
            ->select('sale_id','feedback_status_a')
            ->whereIn('sale_id', $saleIds)
            ->get();
    
        $totalNotConnected = 0;
        $totalConnected    = 0;
        $totalRinging      = 0;
        $totalFollowup     = 0;
    
        foreach ($feedbackRows as $fb) {
    
            $status = strtolower(trim($fb->feedback_status_a ?? 'not connected'));
    
            switch ($status) {
                case 'Connected': $totalConnected++; break;
                case 'Ringing':   $totalRinging++; break;
                case 'Followup':  $totalFollowup++; break;
                default:          $totalNotConnected++;
            }
        }
    
        /* =====================================================
           STEP 7: PAGINATION
        ======================================================*/
        $paginated = $collection->slice($start, $limit)->values();
    
        /* =====================================================
           STEP 8: DATA FORMAT
        ======================================================*/
        $data = [];
        $i = $start + 1;
    
        foreach ($paginated as $row) {
    
            $sale = DB::table('tbl_sales')
                ->where('sale_id', $row->sale_id)
                ->first();
    
            $store = Store::find($row->store_id);
            $handover_by = User::find($row->handover_by);
    
            /* HANDOVER + 6 MONTHS */
            $saleDateTime = $row->handover_date
                ? Carbon::parse($row->handover_date)
                    ->addMonths(6)
                    ->format('d M, Y h:i A')
                : '-';
    
            $encryptedId = $sale ? base64_encode($sale->sale_id) : '';
    
            /* Feedback Badge */
            $statusRaw = strtolower($sale->feedback_status_a ?? 'not connected');
    
            switch ($statusRaw) {
                case 'Connected':
                    $feedbackStatus = '<span class="badge bg-success">Connected</span>';
                    break;
                case 'Ringing':
                    $feedbackStatus = '<span class="badge bg-warning text-dark">Ringing</span>';
                    break;
                case 'Followup':
                    $feedbackStatus = '<span class="badge bg-primary">Followup</span>';
                    break;
                default:
                    $feedbackStatus = '<span class="badge bg-danger">Not Connected</span>';
            }
    
            $feedbackDate = !empty($sale->feedback_a_datetime)
                ? Carbon::parse($sale->feedback_a_datetime)->format('d M, Y h:i A')
                : '-';
    
            $feedbackText = $sale->feedback_a ?? '-';
    
            $data[] = [
                'sr_no' => $i++,
    
                'order_details' =>
                    '<strong>Order Date:</strong> ' .
                    ($sale ? date('d M, Y h:i A', strtotime($sale->sale_date)) : '-') .
                    '<br><strong>Order No:</strong> ' . ($row->order_no ?? '-'),
    
                'store_details' =>
                    '<strong>Store Name:</strong> ' . ($store->store_name ?? '-'),
    
                'sale_datetime' => $saleDateTime,
    
                'customer_details' =>
                    '<strong>Customer Name:</strong> ' . ($sale->cust_name ?? '-') .
                    '<br><strong>Mobile:</strong> ' . ($sale->contact_no ?? '-') .
                    '<br><strong>Cust ID:</strong> ' . ($sale->cust_id ?? '-'),
    
                'handover_by' => $handover_by->name ?? '-',
    
                'feedback_status' => $feedbackStatus.'<br>'.$feedbackDate,
                'feedback'        => $feedbackText,
            ];
        }
    
        /* =====================================================
           RESPONSE
        ======================================================*/
        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
    
            'status_counts' => [
                'not_connected' => $totalNotConnected,
                'connected'     => $totalConnected,
                'ringing'       => $totalRinging,
                'followup'      => $totalFollowup,
            ]
        ]);
    }

    
    public function getState()
    {
        $state = State::all();
        return response()->json($state);
    }
    
    public function getCityByState(Request $request)
    {
        $state_id = $request->state_id;
    
        if (!$state_id) {
            return response()->json([], 400);
        }

        $city = City::where('state_id', $state_id)->get(['id', 'name']);
    
        return response()->json($city);
    }

    public function countingDashboard()
    {
        $setting['page_title'] = 'Product Counting Dashboard';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view('layouts/counting-product',$setting);
    }
    
    
    
    public function countingDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        if($store_id == '0')
        {
            $totalData = DB::table('tbl_counting_product');
        }
        else
        {
            $totalData = DB::table('tbl_counting_product')->where('store_id', $store_id);
        }

        if ($date_from != '' && $date_to != '') {
            $totalData->whereBetween('counting_date', [$date_from, $date_to]);
        }


        $totalData = $totalData->count();
        
        if($store_id == '0')
        {
            $templates = DB::table('tbl_counting_product');
        }
        else
        {
            $templates = DB::table('tbl_counting_product')->where('store_id', $store_id);
        }

        if ($date_from != '' && $date_to != '') 
        {
            $templates->whereBetween('counting_date', [$date_from, $date_to]);
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
                $nestedData['counting_date'] = date('d M, Y', strtotime($template->counting_date));
                $nestedData['product_type']     = $template->product_type;
                $nestedData['product_code']  = $template->product_code;
                $nestedData['product_details']   = $template->product_details;
                $nestedData['count_record']   = $template->count_record;
                $nestedData['available_quantity']   = $template->available_quantity;
                $nestedData['missing_total']   = $template->missing_total;
                $nestedData['added_by']   = $person->name;
                $nestedData['store_name']        = $tbl_store->store_name;
                $nestedData['encryptedId']  = $encryptedId;
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
    
    
    public function countingStore(Request $request)
    {
        DB::beginTransaction();
        

        try {
    
            $user = auth()->user();
            
            if($request->store_ids == '' )
            {
                $store_id = $user->store_id;
            }
            else
            {
                $store_id = $request->store_ids;
            }

            $countingData = [];

            foreach ($request->product_type as $index => $name) {

                if (!empty($name) && !empty($request->product_type[$index])) 
                {
                    
                    $inv = DB::table('tbl_inventory_levels')
                            ->where('store_id', $store_id)
                            ->where('product_type', $request->product_type[$index])
                            ->where('product_code', $request->product_code[$index])
                            ->where('product_details', $request->product_details[$index])
                            ->first();
                            
                    if(!empty($inv))
                    {
                        $available_quantity = $inv->available_quantity;
                        
                    }
                    else
                    {
                        $available_quantity = 0;
                    }

                    $countingData[] = [
                        'product_type'    => $request->product_type[$index],
                        'product_code'    => $request->product_code[$index],
                        'product_details' => $request->product_details[$index],
                        'count_record'    => $request->count_record[$index],
                        'available_quantity'  => $available_quantity,
                        'missing_total' => abs($available_quantity - $request->count_record[$index]),
                        'counting_date'  => $request->counting_date,
                        'added_by'       => $user->id,
                        'store_id'       => $store_id,
                        'created_at'    => now()
                    ];
                }
            }

            if (!empty($countingData)) {
                DB::table('tbl_counting_product')->insert($countingData);
            }
        

            DB::commit();
            return response()->json(['success' => 'Counting created successfully.']);
            

    
        } catch (\Exception $e) {
    
            DB::rollBack();
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function countingrecorddestroy($id)
    {
        $decryptedId = base64_decode($id);

        $Is_delted=DB::table('tbl_counting_product')->where('id',$decryptedId)->delete();

        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Record was successfully deleted',
        ]);
    }
    
}