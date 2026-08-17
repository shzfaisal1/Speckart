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


class MasterController extends Controller
{
    public $view_route = 'master';
    
    public function brandMaster()
    {
        $setting['page_title'] = 'Brand Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/brand',$setting);
    }
    
    
    public function brandDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir', 'DESC');
        $search1 = $request->input('search1');
    
        $query = DB::table('tbl_brand')->where('status', '1');
    
        if (!empty($search1))
        {
            $query->where('brand_name', 'like', '%' . $search1 . '%');
        }

        $totalData = DB::table('tbl_brand')->where('status', '1')->count();
        $totalFiltered = $query->count();
    
        
        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('brand_id', $dir)
                           ->get();
    
        $data = [];
        $i = $start + 1;
        foreach ($templates as $template) {
            $created_by = User::find($template->added_by);
            
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            
            if($template->by_one_get_one == '0')
            {
                $by_one_get_one = '<span class="badge badge-danger">No</span>';
            }
            else
            {
                $by_one_get_one = '<span class="badge badge-success">Yes</span>';
            }
            
    
            $nestedData['sr_no']        = $i++;
            $nestedData['brand_name']   = $template->brand_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . '<BR> (' . ($created_by->name ?? '') . ')';
            $nestedData['brand_id']     = $template->brand_id;
            $nestedData['store_name']   = $store_name;
            $nestedData['by_one_get_one']   = $by_one_get_one;
            $nestedData['byonegetone']   = $template->by_one_get_one;
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }

    
    public function brandstored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'brand_name'      => 'required|string',
            'product_type'    => 'nullabe',
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
            $brandId = DB::table('tbl_brand')->insertGetId([
                'brand_name'   => $request->brand_name,
                'product_type'   => $request->product_type,
                'by_one_get_one'   => $request->by_one_get_one,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            
            return response()->json(['success' => 'Brand created successfully.']);
        }
        else
        {
            DB::table('tbl_brand')
            ->where('brand_id', $request->uid)
            ->update([
                'brand_name'   => $request->brand_name,
                'product_type'   => $request->product_type,
                'by_one_get_one'   => $request->by_one_get_one,
                'updated_at'   => now(),
            ]);
        
          return response()->json(['success' => 'Brand update successfully.']);
        }
        
    }
    
    
    public function branddestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_brand')->where('brand_id', $id)->update(['status' => 0]);
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Brand was successfully deleted',
        ]);
    }
    
    
    public function sizeMaster()
    {
        $setting['page_title'] = 'Size Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/size',$setting);
    }
    
    
    public function sizeDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $dir      = $request->input('order.0.dir', 'DESC');
        $search1  = $request->input('search1');
    
        
        $query = DB::table('tbl_size')->where('status', '1');
    
        if (!empty($search1)) {
            $query->where('size_name', 'like', '%' . $search1 . '%');
        }
    
        
        $totalData = DB::table('tbl_size')->where('status', '1')->count();
    
        
        $totalFiltered = $query->count();
    
        
        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('size_id', $dir)
                           ->get();
    
        $data = [];
        $i = $start + 1; 
        foreach ($templates as $template) {
            $created_by = User::find($template->added_by);
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            $nestedData['sr_no']        = $i++;
            $nestedData['size_name']    = $template->size_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . ' (' . ($created_by->name ?? '') . ')';
            $nestedData['size_id']      = $template->size_id;
            $nestedData['store_name']      = $store_name;
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }

    
    
    
    public function sizestored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'size_name'      => 'required|string',
            'product_type'      => 'required|string',
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
            $brandId = DB::table('tbl_size')->insertGetId([
                'size_name'   => $request->size_name,
                'product_type'   => $request->product_type,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            
            return response()->json(['success' => 'Size created successfully.']);
        }
        else
        {
            DB::table('tbl_size')
            ->where('size_id', $request->uid)
            ->update([
                'size_name'   => $request->size_name,
                'product_type'   => $request->product_type,
                'updated_at'   => now(),
            ]);
        
          return response()->json(['success' => 'Size update successfully.']);
        }
        
    }
    
    
    public function sizedestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_size')->where('size_id', $id)->update(['status' => 0]);
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Size was successfully deleted',
        ]);
    }
    
    
    
    public function shapeMaster()
    {
        $setting['page_title'] = 'Shape Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
      
        return view($this->view_route.'/shape',$setting);
    }
    
    
    public function shapeDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $dir      = $request->input('order.0.dir', 'DESC');
        $search1  = $request->input('search1');
    
        $query = DB::table('tbl_shape')->where('status', '1');
    
        if (!empty($search1)) 
        {
            $query->where('shape_name', 'like', '%' . $search1 . '%');
        }

        $totalData = DB::table('tbl_shape')->where('status', '1')->count();
        $totalFiltered = $query->count();

        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('shape_id', $dir)
                           ->get();
    
        $data = [];
        $i = $start + 1; 
        foreach ($templates as $template)
        {
            $created_by = User::find($template->added_by);
            
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            
            $nestedData['sr_no']        = $i++;
            $nestedData['image']        = isset($template->image) && $template->image ? '<img src="'.asset($template->image).'" width="50" height="50" style="object-fit:cover; border-radius:5px;">' : '';
            $nestedData['shape_name']   = $template->shape_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . ' (' . ($created_by->name ?? '') . ')';
            $nestedData['shape_id']     = $template->shape_id;
            $nestedData['store_name']     = $store_name;
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }

    
    
    
    public function shapestored(Request $request)
    {
        $user = auth()->user();
        
        $rules = [
            'shape_name'      => 'required|string',
            'product_type'    => 'nullable|string',
        ];
        if ($request->uid == '') {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('assets/images/shape'), $imageName);
            $imagePath = 'assets/images/shape/' . $imageName;
        }

        if($request->uid == '')
        {
            $brandId = DB::table('tbl_shape')->insertGetId([
                'shape_name'   => $request->shape_name,
                'product_type' => $request->product_type,
                'image'        => $imagePath,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            return response()->json(['success' => 'Shape created successfully.']);
        }
        else
        {
            $updateData = [
                'shape_name'   => $request->shape_name,
                'product_type' => $request->product_type ?? '',
                'updated_at'   => now(),
            ];
            if ($imagePath) {
                $updateData['image'] = $imagePath;
            }
            DB::table('tbl_shape')
            ->where('shape_id', $request->uid)
            ->update($updateData);
            return response()->json(['success' => 'Shape update successfully.']);
        }
        
    }
    
    
    public function shapedestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_shape')->where('shape_id', $id)->update(['status' => 0]);
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Shape was successfully deleted',
        ]);
    }
    
    
    
    public function colorMaster()
    {
        $setting['page_title'] = 'Color Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/color',$setting);
    }
    
    
    public function colorDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $dir      = $request->input('order.0.dir', 'DESC');
        $search1  = $request->input('search1');
    
        $query = DB::table('tbl_color')->where('status', '1');
    
        if (!empty($search1))
        {
            $query->where('color_name', 'like', '%' . $search1 . '%');
        }

        $totalData = DB::table('tbl_color')->where('status', '1')->count();
        $totalFiltered = $query->count();

        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('color_id', $dir)
                           ->get();
        $data = [];
        $i = $start + 1; 
        foreach ($templates as $template) 
        {
            $created_by = User::find($template->added_by);
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            $nestedData['sr_no']        = $i++;
            $nestedData['color_name']   = $template->color_name;
            $nestedData['color_code']   = $template->color_code;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . ' (' . ($created_by->name ?? '') . ')';
            $nestedData['color_id']     = $template->color_id;
            $nestedData['store_name']   = $store_name;
            
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


    public function colorstored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'color_name'      => 'required|string',
            'product_type'    => 'required|string',
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
            $brandId = DB::table('tbl_color')->insertGetId([
                'color_name'   => $request->color_name,
                'product_type' => $request->product_type,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            
            return response()->json(['success' => 'Color created successfully.']);
        }
        else
        {
            DB::table('tbl_color')
            ->where('color_id', $request->uid)
            ->update([
                'color_name'   => $request->color_name,
                'product_type' => $request->product_type,
                'updated_at'   => now(),
            ]);
            return response()->json(['success' => 'Color update successfully.']);
        }
        
    }
    
    
    public function colordestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_color')->where('color_id', $id)->update(['status' => 0]);
        if (!$Is_delted) 
        {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Color was successfully deleted',
        ]);
    }
    
    
    public function materialMaster()
    {
        $setting['page_title'] = 'Material Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/material',$setting);
    }
    
    
    public function materialDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $dir      = $request->input('order.0.dir', 'DESC');
        $search1  = $request->input('search1');
    
        $query = DB::table('tbl_material')->where('status', '1');
    
        if (!empty($search1))
        {
            $query->where('material_name', 'like', '%' . $search1 . '%');
        }

        $totalData = DB::table('tbl_material')->where('status', '1')->count();
        $totalFiltered = $query->count();

        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('material_id', $dir)
                           ->get();
        $data = [];
        $i = $start + 1; 
        foreach ($templates as $template) 
        {
            $created_by = User::find($template->added_by);
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            $nestedData['sr_no']        = $i++;
            $nestedData['material_name']   = $template->material_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . ' (' . ($created_by->name ?? '') . ')';
            $nestedData['material_id']     = $template->material_id;
            $nestedData['store_name']   = $store_name;
            
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


    public function materialstored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'material_name'      => 'required|string',
            'product_type'    => 'required|string',
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
            $brandId = DB::table('tbl_material')->insertGetId([
                'material_name'   => $request->material_name,
                'product_type' => $request->product_type,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            
            return response()->json(['success' => 'Material created successfully.']);
        }
        else
        {
            DB::table('tbl_material')
            ->where('material_id', $request->uid)
            ->update([
                'material_name'   => $request->material_name,
                'product_type' => $request->product_type,
                'updated_at'   => now(),
            ]);
            return response()->json(['success' => 'Material update successfully.']);
        }
        
    }
    
    
    public function materialdestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_material')->where('material_id', $id)->update(['status' => 0]);
        if (!$Is_delted) 
        {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Material was successfully deleted',
        ]);
    }
    
    
    public function typeMaster()
    {
        $setting['page_title'] = 'Material Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/type',$setting);
    }
    
    
    public function typeDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $dir      = $request->input('order.0.dir', 'DESC');
        $search1  = $request->input('search1');
    
        $query = DB::table('tbl_type')->where('status', '1');
    
        if (!empty($search1))
        {
            $query->where('type_name', 'like', '%' . $search1 . '%');
        }

        $totalData = DB::table('tbl_type')->where('status', '1')->count();
        $totalFiltered = $query->count();

        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('type_id', $dir)
                           ->get();
        $data = [];
        $i = $start + 1; 
        foreach ($templates as $template) 
        {
            $created_by = User::find($template->added_by);
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            $nestedData['sr_no']        = $i++;
            $nestedData['image']        = isset($template->image) && $template->image ? '<img src="'.asset($template->image).'" width="50" height="50" style="object-fit:cover; border-radius:5px;">' : '';
            $nestedData['type_name']   = $template->type_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . ' (' . ($created_by->name ?? '') . ')';
            $nestedData['type_id']     = $template->type_id;
            $nestedData['store_name']   = $store_name;
            
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


    public function typestored(Request $request)
    {
        $user = auth()->user();
        
        $rules = [
            'type_name'      => 'required|string',
            'product_type'    => 'nullable|string',
        ];
        if ($request->uid == '') {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) 
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('assets/images/type'), $imageName);
            $imagePath = 'assets/images/type/' . $imageName;
        }

        if($request->uid == '')
        {
            $brandId = DB::table('tbl_type')->insertGetId([
                'type_name'   => $request->type_name,
                'product_type' => $request->product_type,
                'image'        => $imagePath,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            
            return response()->json(['success' => 'Type created successfully.']);
        }
        else
        {
            $updateData = [
                'type_name'   => $request->type_name,
                'product_type' => $request->product_type,
                'updated_at'   => now(),
            ];
            if ($imagePath) {
                $updateData['image'] = $imagePath;
            }
            DB::table('tbl_type')
            ->where('type_id', $request->uid)
            ->update($updateData);
            return response()->json(['success' => 'Type update successfully.']);
        }
        
    }
    
    
    public function typedestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_type')->where('type_id', $id)->update(['status' => 0]);
        if (!$Is_delted) 
        {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Type was successfully deleted',
        ]);
    }
    
    
    public function variantMaster()
    {
        $setting['page_title'] = 'Variants Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/variant',$setting);
    }
    
    
    public function variantDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $dir      = $request->input('order.0.dir', 'DESC');
        $search1  = $request->input('search1');
    
        $query = DB::table('tbl_variant')->where('status', '1');
    
        if (!empty($search1))
        {
            $query->where('variant_name', 'like', '%' . $search1 . '%');
        }

        $totalData = DB::table('tbl_variant')->where('status', '1')->count();
        $totalFiltered = $query->count();

        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('variant_id', $dir)
                           ->get();
        $data = [];
        $i = $start + 1; 
        foreach ($templates as $template) 
        {
            $created_by = User::find($template->added_by);
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            $nestedData['sr_no']        = $i++;
            $nestedData['variant_name']   = $template->variant_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . ' (' . ($created_by->name ?? '') . ')';
            $nestedData['variant_id']     = $template->variant_id;
            $nestedData['store_name']   = $store_name;
            
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


    public function variantstored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'variant_name'      => 'required|string',
            'product_type'    => 'required|string',
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
            $brandId = DB::table('tbl_variant')->insertGetId([
                'variant_name'   => $request->variant_name,
                'product_type' => $request->product_type,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            
            return response()->json(['success' => 'Type created successfully.']);
        }
        else
        {
            DB::table('tbl_variant')
            ->where('variant_id', $request->uid)
            ->update([
                'variant_name'   => $request->variant_name,
                'product_type' => $request->product_type,
                'updated_at'   => now(),
            ]);
            return response()->json(['success' => 'Variant update successfully.']);
        }
        
    }
    
    
    public function variantdestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_variant')->where('variant_id', $id)->update(['status' => 0]);
        if (!$Is_delted) 
        {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Variant was successfully deleted',
        ]);
    }
    
    
    public function companyListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $brand = DB::table('tbl_brand')->where('status', '1')->where('product_type',$product_type)->where('brand_name', 'LIKE', "%{$search}%")->get(['brand_name']);
    
        return response()->json($brand);
    }
    
    
    public function colorListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $color = DB::table('tbl_color')->where('status', '1')->where('product_type',$product_type)->where('color_name', 'LIKE', "%{$search}%")->get(['color_name']);
    
        return response()->json($color);
    }
    
    
    public function sizeListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $size = DB::table('tbl_size')->where('status', '1')->where('product_type',$product_type)->where('size_name', 'LIKE', "%{$search}%")->get(['size_name']);
    
        return response()->json($size);
    }
    
    
    public function typeListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $type = DB::table('tbl_type')->where('status', '1')->where('product_type',$product_type)->where('type_name', 'LIKE', "%{$search}%")->get(['type_name']);
    
        return response()->json($type);
    }
    
    
    public function shapeListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $shape = DB::table('tbl_shape')->where('status', '1')->where('product_type',$product_type)->where('shape_name', 'LIKE', "%{$search}%")->get(['shape_name']);
    
        return response()->json($shape);
    }
    
    public function materialListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $material = DB::table('tbl_material')->where('status', '1')->where('product_type',$product_type)->where('material_name', 'LIKE', "%{$search}%")->get(['material_name']);
    
        return response()->json($material);
    }
    
    
    public function variantListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $variant = DB::table('tbl_variant')->where('status', '1')->where('product_type',$product_type)->where('variant_name', 'LIKE', "%{$search}%")->get(['variant_name']);
    
        return response()->json($variant);
    }
    
    
    public function coatingListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $coating = DB::table('tbl_coating')->where('status', '1')->where('product_type',$product_type)->where('coating_name', 'LIKE', "%{$search}%")->get(['coating_name']);
    
        return response()->json($coating);
    }
    
    public function designListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $design = DB::table('tbl_design')->where('status', '1')->where('product_type',$product_type)->where('design_name', 'LIKE', "%{$search}%")->get(['design_name']);
    
        return response()->json($design);
    }
    
    
    public function indexListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $index = DB::table('tbl_index')->where('status', '1')->where('product_type',$product_type)->where('index_name', 'LIKE', "%{$search}%")->get(['index_name']);
    
        return response()->json($index);
    }
    
    
    public function ctListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $ct = DB::table('tbl_ct')->where('status', '1')->where('product_type',$product_type)->where('ct_name', 'LIKE', "%{$search}%")->get(['ct_name']);
    
        return response()->json($ct);
    }
    
    public function validityListdropdown(Request $request)
    {
        $search = $request->get('name');
        $product_type = $request->get('product_type');
        $validity = DB::table('tbl_validity')->where('status', '1')->where('product_type',$product_type)->where('validity_name', 'LIKE', "%{$search}%")->get(['validity_name']);
    
        return response()->json($validity);
    }
    
    
    
    public function coatingMaster()
    {
        $setting['page_title'] = 'Coating Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/coating',$setting);
    }
    
    
    public function coatingDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $dir      = $request->input('order.0.dir', 'DESC');
        $search1  = $request->input('search1');
    
        $query = DB::table('tbl_coating')->where('status', '1');
    
        if (!empty($search1))
        {
            $query->where('coating_name', 'like', '%' . $search1 . '%');
        }

        $totalData = DB::table('tbl_coating')->where('status', '1')->count();
        $totalFiltered = $query->count();

        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('coating_id', $dir)
                           ->get();
        $data = [];
        $i = $start + 1; 
        foreach ($templates as $template) 
        {
            $created_by = User::find($template->added_by);
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            $nestedData['sr_no']        = $i++;
            $nestedData['coating_name']   = $template->coating_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . ' (' . ($created_by->name ?? '') . ')';
            $nestedData['coating_id']     = $template->coating_id;
            $nestedData['store_name']   = $store_name;
            
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


    public function coatingstored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'coating_name'      => 'required|string',
            'product_type'    => 'required|string',
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
            $brandId = DB::table('tbl_coating')->insertGetId([
                'coating_name'   => $request->coating_name,
                'product_type' => $request->product_type,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            
            return response()->json(['success' => 'Coating created successfully.']);
        }
        else
        {
            DB::table('tbl_coating')
            ->where('coating_id', $request->uid)
            ->update([
                'coating_name'   => $request->coating_name,
                'product_type' => $request->product_type,
                'updated_at'   => now(),
            ]);
            return response()->json(['success' => 'Coating update successfully.']);
        }
        
    }
    
    
    public function coatingdestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_coating')->where('coating_id', $id)->update(['status' => 0]);
        if (!$Is_delted) 
        {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Coating was successfully deleted',
        ]);
    }
    
    
    
    public function designMaster()
    {
        $setting['page_title'] = 'Design Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/design',$setting);
    }
    
    
    public function designDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $dir      = $request->input('order.0.dir', 'DESC');
        $search1  = $request->input('search1');
    
        $query = DB::table('tbl_design')->where('status', '1');
    
        if (!empty($search1))
        {
            $query->where('design_name', 'like', '%' . $search1 . '%');
        }

        $totalData = DB::table('tbl_design')->where('status', '1')->count();
        $totalFiltered = $query->count();

        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('design_id', $dir)
                           ->get();
        $data = [];
        $i = $start + 1; 
        foreach ($templates as $template) 
        {
            $created_by = User::find($template->added_by);
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            $nestedData['sr_no']        = $i++;
            $nestedData['design_name']   = $template->design_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . ' (' . ($created_by->name ?? '') . ')';
            $nestedData['design_id']     = $template->design_id;
            $nestedData['store_name']   = $store_name;
            
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


    public function designstored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'design_name'      => 'required|string',
            'product_type'    => 'required|string',
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
            $brandId = DB::table('tbl_design')->insertGetId([
                'design_name'   => $request->design_name,
                'product_type' => $request->product_type,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            
            return response()->json(['success' => 'Design created successfully.']);
        }
        else
        {
            DB::table('tbl_design')
            ->where('design_id', $request->uid)
            ->update([
                'design_name'   => $request->design_name,
                'product_type' => $request->product_type,
                'updated_at'   => now(),
            ]);
            return response()->json(['success' => 'Design update successfully.']);
        }
        
    }
    
    
    public function designdestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_design')->where('design_id', $id)->update(['status' => 0]);
        if (!$Is_delted) 
        {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Design was successfully deleted',
        ]);
    }
    
    
    
    public function indexMaster()
    {
        $setting['page_title'] = 'Index Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/index',$setting);
    }
    
    
    public function indexDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $dir      = $request->input('order.0.dir', 'DESC');
        $search1  = $request->input('search1');
    
        $query = DB::table('tbl_index')->where('status', '1');
    
        if (!empty($search1))
        {
            $query->where('index_name', 'like', '%' . $search1 . '%');
        }

        $totalData = DB::table('tbl_index')->where('status', '1')->count();
        $totalFiltered = $query->count();

        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('index_id', $dir)
                           ->get();
        $data = [];
        $i = $start + 1; 
        foreach ($templates as $template) 
        {
            $created_by = User::find($template->added_by);
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            $nestedData['sr_no']        = $i++;
            $nestedData['index_name']   = $template->index_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . ' (' . ($created_by->name ?? '') . ')';
            $nestedData['index_id']     = $template->index_id;
            $nestedData['store_name']   = $store_name;
            
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


    public function indexstored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'index_name'      => 'required|string',
            'product_type'    => 'required|string',
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
            $brandId = DB::table('tbl_index')->insertGetId([
                'index_name'   => $request->index_name,
                'product_type' => $request->product_type,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            
            return response()->json(['success' => 'Index created successfully.']);
        }
        else
        {
            DB::table('tbl_index')
            ->where('index_id', $request->uid)
            ->update([
                'index_name'   => $request->index_name,
                'product_type' => $request->product_type,
                'updated_at'   => now(),
            ]);
            return response()->json(['success' => 'Index update successfully.']);
        }
        
    }
    
    
    public function indexdestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_index')->where('index_id', $id)->update(['status' => 0]);
        if (!$Is_delted) 
        {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Index was successfully deleted',
        ]);
    }
    
    
    
    public function ctMaster()
    {
        $setting['page_title'] = 'CT (Center Thickness) Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/ct',$setting);
    }
    
    
    public function ctDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $dir      = $request->input('order.0.dir', 'DESC');
        $search1  = $request->input('search1');
    
        $query = DB::table('tbl_ct')->where('status', '1');
    
        if (!empty($search1))
        {
            $query->where('ct_name', 'like', '%' . $search1 . '%');
        }

        $totalData = DB::table('tbl_ct')->where('status', '1')->count();
        $totalFiltered = $query->count();

        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('ct_id', $dir)
                           ->get();
        $data = [];
        $i = $start + 1; 
        foreach ($templates as $template) 
        {
            $created_by = User::find($template->added_by);
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            $nestedData['sr_no']        = $i++;
            $nestedData['ct_name']   = $template->ct_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . ' (' . ($created_by->name ?? '') . ')';
            $nestedData['ct_id']     = $template->ct_id;
            $nestedData['store_name']   = $store_name;
            
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


    public function ctstored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'ct_name'      => 'required|string',
            'product_type'    => 'required|string',
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
            $brandId = DB::table('tbl_ct')->insertGetId([
                'ct_name'   => $request->ct_name,
                'product_type' => $request->product_type,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            
            return response()->json(['success' => 'CT created successfully.']);
        }
        else
        {
            DB::table('tbl_ct')
            ->where('ct_id', $request->uid)
            ->update([
                'ct_name'   => $request->ct_name,
                'product_type' => $request->product_type,
                'updated_at'   => now(),
            ]);
            return response()->json(['success' => 'CT update successfully.']);
        }
        
    }
    
    
    public function ctdestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_ct')->where('ct_id', $id)->update(['status' => 0]);
        if (!$Is_delted) 
        {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'CT was successfully deleted',
        ]);
    }
    
    
    
    public function validityMaster()
    {
        $setting['page_title'] = 'Validity Master';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/validity',$setting);
    }
    
    
    public function validityDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $dir      = $request->input('order.0.dir', 'DESC');
        $search1  = $request->input('search1');
    
        $query = DB::table('tbl_validity')->where('status', '1');
    
        if (!empty($search1))
        {
            $query->where('validity_name', 'like', '%' . $search1 . '%');
        }

        $totalData = DB::table('tbl_validity')->where('status', '1')->count();
        $totalFiltered = $query->count();

        $templates = $query->offset($start)
                           ->limit($limit)
                           ->orderBy('validity_id', $dir)
                           ->get();
        $data = [];
        $i = $start + 1; 
        foreach ($templates as $template) 
        {
            $created_by = User::find($template->added_by);
            if($template->store_id == '0')
            {
                $store_name = $created_by->user_type;
            }
            else
            {
                $store_name = Store::find($template->store_id);
                $store_name = $store_name->store_id;
            }
            $nestedData['sr_no']        = $i++;
            $nestedData['validity_name']   = $template->validity_name;
            $nestedData['product_type'] = $template->product_type;
            $nestedData['created_at']   = date('d M, Y h:i A', strtotime($template->created_at))
                                         . ' (' . ($created_by->name ?? '') . ')';
            $nestedData['validity_id']     = $template->validity_id;
            $nestedData['store_name']   = $store_name;
            
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


    public function validitystored(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'validity_name'      => 'required|string',
            'product_type'    => 'required|string',
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
            $brandId = DB::table('tbl_validity')->insertGetId([
                'validity_name'   => $request->validity_name,
                'product_type' => $request->product_type,
                'added_by'     => $user->id,
                'store_id'     => $user->store_id,
            ]);
            
            return response()->json(['success' => 'CT created successfully.']);
        }
        else
        {
            DB::table('tbl_validity')
            ->where('validity_id', $request->uid)
            ->update([
                'validity_name'   => $request->validity_name,
                'product_type' => $request->product_type,
                'updated_at'   => now(),
            ]);
            return response()->json(['success' => 'validity update successfully.']);
        }
        
    }
    
    
    public function validitydestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_validity')->where('validity_id', $id)->update(['status' => 0]);
        if (!$Is_delted) 
        {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'validity was successfully deleted',
        ]);
    }
    
}    