<?php
namespace App\Http\Controllers\Admin\product\other;

use App\Http\Controllers\Controller;

use Hash;
use DB;
use App\Models\product\Product;
use Illuminate\Http\Request;
use App\Models\Store;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Storage;



class OthermasterController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:Product-Other', ['only' => ['index']]);
    }
    
    public $view_route = 'products';
    public $view_lens = 'other';
    
    public function index()
    {
        $store_id = auth()->user()->store_id;
        $setting['page_title'] = 'other';
        $setting['active'] = 'product code';
        $Product = Product::where('status', '1')->get();
        return view($this->view_route . '/' . $this->view_lens . '/index-1', $setting, compact('Product'));
    }
    

    
    
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_code' => [
                'required',
                'string',
                'max:225',
                Rule::unique('tbl_product_code')
            ],
            
            'product_name'        => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }
        
        $idgenerate = $this->generateUniqueRandomId(6, 'tbl_product_code', 'product_id');
        $folderName = 'other/product/'.$idgenerate;
        $folderPath = public_path('uploads/' . $folderName);

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true); // `true` allows recursive folder creation
        }
        
        $product_image = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) 
            {
                $productImg = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($folderPath, $productImg);
                $product_image[] = $productImg;
            }
        
            // Save as JSON or handle according to your needs
            $data1['product_image'] = json_encode($product_image);
        } 
        else 
        {
            $data1['product_image'] = json_encode([]);
        }
        
        if ($request->hasFile('main_image'))
        {
            $imgName = time() . '.' . $request->main_image->getClientOriginalExtension();
            $request->main_image->move($folderPath, $imgName);

            $main_image = $imgName;
        }
        else
        {
            $main_image = '';
        }
        
        $fields = [
            $request->product_name ?? null,
            $request->Company ?? null,
            $request->Gender ?? null,
            $request->Type ?? null,
            $request->Color ?? null,
            $request->Shape ?? null,
            $request->Size ?? null,
            $request->Color ?? null,
            $request->Material ?? null,
            $request->Quality ?? null
        ];
        $productdetails = implode(' - ', array_filter($fields));
        $product = Product::create([
            
            'product_id' => $idgenerate,
            'product_type' => 'Other',
            'product_image' => $data1['product_image'],
            'product_code' => $request->product_code,
            'product_name' => $request->product_name,
            'productdetails' => $productdetails,
            'Company' => $request->Company,
            'Quality' => $request->Quality,
            'Track_Inventory' => $request->Track_Inventory,
            'Allow_Negative_Inventory' => $request->Allow_Negative_Inventory,
            'Purchase_Base_Price' => $request->Purchase_Base_Price,
            'Purchase_Price' => $request->Purchase_Price,
            'Retail_Price' => $request->Retail_Price,
            'BB_Price' => $request->BB_Price,
            'Color' => $request->Color,
            'Shape' => $request->Shape,
            'Size' => $request->Size,
            'otherType' => $request->otherType,
            'added_by' => auth()->id(),
            'store_id' => auth()->user()->store_id,
            'main_image' => $main_image,
        ]);
        
        $product->save();

        return response()->json(['success' => 'Other product created successfully.']);
    }
    
    
     public function update(Request $request, $uid)
    {
        $validator = Validator::make($request->all(), [
            'product_code' => [
                'required',
                'string',
                'max:225',
            ],
            
            'product_name'        => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }

         $fields = [
            $request->product_name ?? null,
            $request->Company ?? null,
            $request->Gender ?? null,
            $request->Type ?? null,
            $request->Color ?? null,
            $request->Shape ?? null,
            $request->Size ?? null,
            $request->Color ?? null,
            $request->Material ?? null,
            $request->Quality ?? null
        ];
        $productdetails = implode(' - ', array_filter($fields));
        $product = Product::findOrFail($uid);
    
        $folderName = 'other/product/' . $request->product_id;
        $folderPath = public_path('uploads/' . $folderName);
    
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
    
        // Handle gallery images
        $existingImages = $product->product_image ? json_decode($product->product_image, true) : [];
        $product_image = $existingImages;
    
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $productImg = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->move($folderPath, $productImg);
                $product_image[] = $productImg;
            }
        }
    
        // Prepare data to update
        $data = $request->except(['_token', '_method', 'images', 'main_image']);
        $data['product_image'] = !empty($product_image) ? json_encode($product_image) : null;
        $data['product_details'] = $productdetails;
    
        // Handle main image
        if ($request->hasFile('main_image')) {
            $imgName = time() . '.' . $request->main_image->getClientOriginalExtension();
            $request->main_image->move($folderPath, $imgName);
    
            if ($product->main_image && is_file($folderPath . '/' . $product->main_image)) {
                unlink($folderPath . '/' . $product->main_image);
            }
    
            $data['main_image'] = $imgName;
        } else {
            $data['main_image'] = $product->main_image;
        }
    
        $product->update($data);

         return response()->json(['success' => 'Other product updated successfully.']);
    }
    
    public function search(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $totalData = Product::where('status', '1')->where('product_type', 'Other')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search1')))
        {
            $templates = Product::where('status', '1')->where('product_type', 'Other')->offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();
        } else 
        {
            $search = $request->input('search1');
            $templates = Product::where('status', '1')->where('product_type', 'Other')->where('product_code', 'like', '%' . $search . '%')
                ->orWhere('product_name', 'like', '%' . $search . '%')
                ->orWhere('Company', 'like', '%' . $search . '%')
                ->orWhere('Quality', 'like', '%' . $search . '%')
                ->orWhere('Purchase_Base_Price', 'like', '%' . $search . '%')
                ->orWhere('Purchase_Price', 'like', '%' . $search . '%')
                ->orWhere('Retail_Price', 'like', '%' . $search . '%')
                ->orWhere('BB_Price', 'like', '%' . $search . '%')
                ->offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();

            $totalFiltered = Product::where('status', '1')->where('product_type', 'Other')->where('product_code', 'like', '%' . $search . '%')
                ->orWhere('product_name', 'like', '%' . $search . '%')
                ->orWhere('Company', 'like', '%' . $search . '%')
                ->orWhere('Quality', 'like', '%' . $search . '%')
                ->orWhere('Purchase_Base_Price', 'like', '%' . $search . '%')
                ->orWhere('Purchase_Price', 'like', '%' . $search . '%')
                ->orWhere('Retail_Price', 'like', '%' . $search . '%')
                ->orWhere('BB_Price', 'like', '%' . $search . '%')
            ->count();
        }
        
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                if ($template->Track_Inventory == '1') 
                {
                    $track_int =  '<span class="badge badge-success">YES</span>';
                } 
                else
                {
                    $track_int =  '<span class="badge badge-danger">NO</span>';
                }
                if ($template->Allow_Negative_Inventory == '1') 
                {
                    $neg_int =  '<span class="badge badge-success">YES</span>';
                } 
                else
                {
                    $neg_int =  '<span class="badge badge-danger">NO</span>';
                }
                
        
                $created_by = User::find($template->added_by);

                
                $pimage = '';

                if (!empty($template->product_image)) {
                    $product_images = json_decode($template->product_image, true);
                
                    if (is_array($product_images)) {
                        $filePath = asset('uploads/other/product/' . $template->product_id . '/');
                        $pimage = '<div class="row">';
                
                        foreach ($product_images as $filename) {
                            $filename = trim($filename);
                            $image_url = $filePath .'/'. $filename;
                            $pimage .= '<div class="col-md-2"><img src="' . htmlspecialchars($image_url) . '" alt="Product Image" style="max-width: 50px; margin: 5px;" /></div>';
                        }
                
                        $pimage .= '</div>';
                    } else {
                        $pimage = 'No images available';
                    }
                } else {
                    $pimage = 'No images available';
                }
                

                
                $nestedData['product_id'] =$template->product_id; 
                $nestedData['id'] =$template->id; 
                 $nestedData['product_details'] =$template->productdetails;
                $nestedData['price'] = '<strong>Purchase Base Price :'.$template->Purchase_Base_Price.'<BR>Purchase Price : '.$template->Purchase_Price.'
                <BR>Retail Price :' .$template->Retail_Price.'<BR>B2B Price : '.$template->BB_Price.'</strong>'; 
                $nestedData['Inventory'] =$track_int; 
                $nestedData['Neg_Inventory'] =$neg_int;
                $nestedData['created_at'] = date('d M,Y h:i A', strtotime($template->created_at)) . '<BR> (' . ($created_by->name ?? '') . ')';
                $nestedData['Allow_Negative_Inventory'] =$template->Allow_Negative_Inventory; 
                $nestedData['Track_Inventory'] =$template->Track_Inventory; 
                $nestedData['Purchase_Base_Price'] =$template->Purchase_Base_Price;
                $nestedData['Purchase_Price'] =$template->Purchase_Price;
                $nestedData['Retail_Price'] =$template->Retail_Price;
                $nestedData['BB_Price'] =$template->BB_Price;
                $nestedData['product_code'] =$template->product_code; 
                $nestedData['product_name'] =$template->product_name; 
                $nestedData['Quality'] =$template->Quality; 
                $nestedData['Company'] =$template->Company;
                $nestedData['Color'] =$template->Color;
                $nestedData['Shape'] =$template->Shape;
                $nestedData['Size'] =$template->Size;
                $nestedData['Type'] =$template->Type;
                $nestedData['Material'] =$template->Material;
                $nestedData['Description'] =$template->Description;
                $nestedData['product_image'] =$template->product_image;
                $nestedData['pimage'] =$pimage;
                $nestedData['main_image'] =$template->main_image;

                
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
        $Is_delted = DB::table('tbl_product_code')->where('id', $id)->update(['status' => 0]);
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Product was successfully deleted',
        ]);
    }
    
    public function generateUniqueRandomId($length = 6, $table = 'tbl_product_code', $column = 'product_id', $min = 100000, $max = 999999)
    {
        do {
            $id = random_int($min, $max);
        } while (
            DB::table($table)->where($column, $id)->exists()
        );
    
        return $id;
    }
    
    

}