<?php
namespace App\Http\Controllers\Admin\Setting;

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
use App\Models\setting\Tax;


class TaxMasterController extends Controller
{
    public $view_route = 'setting';
    

    public function index()
    {
        $store_id = auth()->user()->store_id;
        $setting['page_title'] = 'Setting';
        $setting['active'] = 'Tax Master';
        $Tax = Tax::where('status', '1')->get();
        return view($this->view_route.'/tax-master', $setting, compact('Tax'));
    }
    

    
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_type'        => 'required|string|max:255',
            'hsn_code'            => 'required|string|max:100',
            'percentage' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }
        

        $tax = Tax::create([
            
            'product_type' => $request->product_type,
            'hsn_code' => $request->hsn_code,
            'percentage' => $request->percentage,
            'description' => $request->description,
            'set_default' => $request->set_default,
            'added_by' => auth()->id(),
            'store_id' => auth()->user()->store_id
        ]);
        
        $tax->save();

        return response()->json(['success' => 'Tax master created successfully.']);
    }
    
    
    public function update(Request $request, $uid)
    {
        $validator = Validator::make($request->all(), [
            'product_type'        => 'required|string|max:255',
            'hsn_code'            => 'required|string|max:100',
            'percentage' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }

        Tax::where('id', $uid)->update($request->except(['_token','_method']));

         return response()->json(['success' => 'Tax updated successfully.']);
    }
    
    
    
    public function search(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $totalData = Tax::where('status', '1')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search1')))
        {
            $templates = Tax::where('status', '1')->offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();
        } else 
        {
            $search = $request->input('search1');
            $templates = Tax::where('status', '1')->where('hsn_code', 'like', '%' . $search . '%')
                ->orWhere('status', '1')->where('percentage', 'like', '%' . $search . '%')
                ->orWhere('status', '1')->where('description', 'like', '%' . $search . '%')
                ->offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();

            $totalFiltered = Tax::where('status', '1')->where('hsn_code', 'like', '%' . $search . '%')
            ->orWhere('status', '1')->where('percentage', 'like', '%' . $search . '%')
            ->orWhere('status', '1')->where('description', 'like', '%' . $search . '%')
            ->count();
        }
        
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                $created_by = User::find($template->added_by);
                $updated_by = User::find($template->updated_by);
                
                if($template->set_default == '1')
                {
                    $checkd = '<i class="fa fa-check" aria-hidden="true"></i>';
                }
                else
                {
                    $checkd ='';
                }
                $nestedData['sr_no'] =$i++; 
                $nestedData['id'] =$template->id; 
                $nestedData['product_type'] =$template->product_type;
                $nestedData['hsn_code'] =$template->hsn_code.' '.$checkd;
                $nestedData['percentage'] =$template->percentage.'%';
                $nestedData['description'] =$template->description;
                $nestedData['set_default'] =$template->set_default;
                $nestedData['created_at'] = date('d M,Y h:i A', strtotime($template->created_at)) . ' (' . ($created_by?->name ?? '') . ')';
                $nestedData['update_at'] = date('d M,Y h:i A', strtotime($template->updated_at)) . ' (' . ($updated_by?->name ?? '') . ')'; 
                $nestedData['percentage_t'] =$template->percentage;

                
                $data[]     = $nestedData;
            }
        }

        return response()->json([
             "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }
    
    
    public function destroy($id)
    {
        $Is_delted = DB::table('tbl_tax')->where('id', $id)->update(['status' => 0]);
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Tax was successfully deleted',
        ]);
    }

    
    
    
    
}