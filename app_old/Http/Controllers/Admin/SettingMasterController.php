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
use App\Models\setting\Supplier;
use App\Models\setting\Tax;
use App\Models\setting\SmsSetting;
use PDF;

class SettingMasterController extends Controller
{
    public $view_route = 'setting';
    
    public function settingMaster()
    {
        $setting['page_title'] = 'Setting Master';
        $setting['active'] = 'Supplier';
        return view($this->view_route.'/setting-master',$setting);
    }
    
    public function index()
    {
        $store_id = auth()->user()->store_id;
        $setting['page_title'] = 'Setting';
        $setting['active'] = 'Supplier';
        $Supplier = Supplier::where('status', '1')->get();
        return view($this->view_route.'/supplier', $setting, compact('Supplier'));
    }
    
    public function stateNameList(Request $request)
    {
        $search = $request->get('name');
        $products = State::where('name', 'LIKE', "%{$search}%")->get(['name']);
    
        return response()->json($products);
    }
    
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_company'        => 'required|string|max:255',
            'contact_name'            => 'required|string|max:100',
            'contact_no' => ['required', 'string', 'min:10', 'max:10', 'unique:tbl_suppliers,contact_no'],
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }
        
        $idgenerate = $this->generateUniqueRandomId(5, 'tbl_suppliers', 'supplier_id');
        
        $product = Supplier::create([
            
            'supplier_id' => $idgenerate,
            'supplier_company' => $request->supplier_company,
            'contact_name' => $request->contact_name,
            'contact_no' => $request->contact_no,
            'gst_no' => $request->gst_no,
            'state' => $request->state,
            'added_by' => auth()->id(),
            'store_id' => auth()->user()->store_id
        ]);
        
        $product->save();

        return response()->json(['success' => 'Supplier created successfully.']);
    }
    
    
    public function update(Request $request, $uid)
    {
        $validator = Validator::make($request->all(), [
            'supplier_company'        => 'required|string|max:255',
            'contact_name'            => 'required|string|max:100',
            'contact_no' => ['required', 'string', 'min:10', 'max:10'],
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }

        Supplier::where('id', $uid)->update($request->except(['_token','_method']));

        return response()->json(['success' => 'Supplier updated successfully.']);
    }
    
    
    
    public function search(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $totalData = Supplier::where('status', '1')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search1')))
        {
            $templates = Supplier::where('status', '1')->offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();
        } else 
        {
            $search = $request->input('search1');
            $templates = Supplier::where('status', '1')->where('supplier_company', 'like', '%' . $search . '%')
                ->where('contact_name', 'like', '%' . $search . '%')
                ->where('contact_no', 'like', '%' . $search . '%')
                ->where('gst_no', 'like', '%' . $search . '%')
                ->where('state', 'like', '%' . $search . '%')
                ->offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();

            $totalFiltered = Supplier::where('status', '1')->where('supplier_company', 'like', '%' . $search . '%')
                ->where('contact_name', 'like', '%' . $search . '%')
                ->where('contact_no', 'like', '%' . $search . '%')
                ->where('gst_no', 'like', '%' . $search . '%')
                ->where('state', 'like', '%' . $search . '%')
                ->count();
        }
        
         
        $data = [];
        if (! empty($templates))
        {
            foreach ($templates as $template) 
            {
                $created_by = User::find($template->added_by);
                $updated_by = User::find($template->updated_by);
                
                $nestedData['id'] =$template->id; 
                $nestedData['supplier_id'] =$template->supplier_id;
                $nestedData['supplier_company'] =$template->supplier_company;
                $nestedData['contact_name'] =$template->contact_name;
                $nestedData['contact_no'] =$template->contact_no;
                $nestedData['gst_no'] = $template->gst_no;
                $nestedData['state'] = $template->state;
                $nestedData['created_at'] = date('d M,Y h:i A', strtotime($template->created_at)) . ' (' . ($created_by->name ?? '') . ')';
                $nestedData['update_at']  = date('d M,Y h:i A', strtotime($template->updated_at)) . ' (' . ($updated_by->name ?? '') . ')'; 

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
        $Is_delted = DB::table('tbl_suppliers')->where('id', $id)->update(['status' => 0]);
        if (!$Is_delted)
        {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Supplier was successfully deleted',
        ]);
    }
    
    
    public function generateUniqueRandomId($length = 5, $table = 'tbl_suppliers', $column = 'supplier_id', $min = 100000, $max = 999999)
    {
        do 
        {
            $id = random_int($min, $max);
        } 
        while
        (
            DB::table($table)->where($column, $id)->exists()
        );
    
        return $id;
    }
    
    
    
    public function barcode()
    {
        $setting['page_title'] = 'Setting';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/barcode', $setting);
    }
    
    
    public function barcodeUpdate(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'setting_name'  => 'required|string|max:255',
            'paper_width'   => 'required|string|max:100',
            'paper_height'  => 'required|string|max:100',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }
        
        $update_new = DB::table('tbl_barcode_setting')->where('id', $request->barcode_id)
            ->update([
                'setting_name'      => $request->setting_name,
                'paper_width'       => $request->paper_width,
                'paper_height'      => $request->paper_height,
                'label_width'       => $request->label_width,
                'label_height'      => $request->label_height,
                'no_columns'        => $request->no_columns,
                'no_rows'           => $request->no_rows,
                'updated_by'        => $user->id,
                'updated_at' => now()
        ]);

        return response()->json(['success' => 'Barcode updated successfully.']);
    }
    
    
    public function productCode()
    {
        $setting['page_title'] = 'Setting';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/product-code', $setting);
    }
    
    public function productCodeSettingUpdate(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
    
        $settingsMap = [
            'Frame' => [
                'prefix' => 'f', 'fields' => [
                    'product_code', 'product_name', 'company_name', 'quality', 'color', 'size', 'type',
                    'gender', 'Shape', 'Material', 'Temple_Detail', 'Bridge_Size', 'Description'
                ]
            ],
            'Goggles' => [
                'prefix' => 'g', 'fields' => [
                    'product_code', 'product_name', 'company_name', 'quality', 'color', 'size', 'type',
                    'gender', 'Shape', 'Material', 'Temple_Detail', 'Bridge_Size', 'Description'
                ]
            ],
            'Glass' => [
                'prefix' => 'gg', 'fields' => [
                    'product_code', 'product_name', 'company_name', 'quality', 'color', 'Material', 'Coating',
                    'Design', 'Product_Index', 'Description', 'Numbers', 'Product_Range'
                ]
            ],
            'Lens' => [
                'prefix' => 'l', 'fields' => [
                    'product_code', 'product_name', 'company_name', 'quality', 'color', 'Numbers', 'CT',
                    'type', 'Material', 'Modality', 'Validity_In_Days', 'WC', 'Dk_t', 'Description',
                    'Prescription_Parameters', 'SPH', 'CYL', 'AXIS', 'ADDITIONAL', 'BC', 'DIA', 'POWER_TYPE'
                ]
            ],
            'Solution' => [
                'prefix' => 's', 'fields' => [
                    'product_code', 'product_name', 'company_name', 'quality', 'Color',
                    'Variant', 'Packing_Type', 'Description'
                ]
            ],
            'Other' => [
                'prefix' => 'o', 'fields' => [
                    'product_code', 'product_name', 'company_name', 'quality', 'Color',
                    'type', 'Shape', 'size', 'Description'
                ]
            ],
            'Non Chargeable' => [
                'prefix' => 'c', 'fields' => [
                    'product_code', 'product_name', 'company_name', 'quality', 'Color',
                    'type', 'Material', 'size', 'Description'
                ]
            ],
        ];
    
        foreach ($data['product_type'] as $type) {
            if (!isset($settingsMap[$type])) {
                continue; 
            }
    
            $map = $settingsMap[$type];
            $updateData = [];
    
            foreach ($map['fields'] as $field) {
                $key = $map['prefix'] . $field;
                $updateData[$field] = $data[$key] ?? '1';
            }
    
            $updateData['updated_by'] = $user->id;
            $updateData['updated_at'] = now();
    
            DB::table('tbl_product_code_setting')->where('product_type', $type)->update($updateData);
        }
    
        return response()->json(['success' => 'Product and Inventory Settings updated successfully.']);
    }
    
    
    
    public function sales()
    {
        $setting['page_title'] = 'Sales Setting';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/sales', $setting);
    }
    
    
    public function salessettingUpdate(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'frame_margin'        => 'required',
            'goggles_margin'            => 'required',
            'glass_margin' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }
        
        $update_new = DB::table('tbl_sales_setting')->where('id', $request->sales_id)
            ->update([
                'frame_margin'      => $request->frame_margin,
                'goggles_margin'    => $request->goggles_margin,
                'solution_margin'   => $request->solution_margin,
                'glass_margin'      => $request->glass_margin,
                'lens_margin'       => $request->lens_margin,
                'other_margin'      => $request->other_margin,
                'bb_frame_margin'   => $request->bb_frame_margin,
                'bb_goggles_margin' => $request->bb_goggles_margin,
                'bb_glass_margin'   => $request->bb_glass_margin,
                'bb_lens_margin'    => $request->bb_lens_margin,
                'bb_solution_margin'=> $request->bb_solution_margin,
                'bb_other_margin'   => $request->bb_other_margin,
                'updated_by'        => $user->id,
                'updated_at' => now()
        ]);


         return response()->json(['success' => 'Sales setting updated successfully.']);
    }
    
    
    
    public function package()
    {
        $setting['page_title'] = 'Package Setting';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/package', $setting);
    }
    
    
    
    public function packageSearch(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $totalData = DB::table('tbl_product_code')->where('in_house',1)->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search1')))
        {
            $templates = DB::table('tbl_product_code')->where('in_house',1)->offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();
        } 
        else 
        {
            $search = $request->input('search1');
            $templates = DB::table('tbl_lens_package')->where('product_id', 'like', '%' . $search . '%')
                ->where('product_code', 'like', '%' . $search . '%')
                ->where('product_name', 'like', '%' . $search . '%')
                ->offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();

            $totalFiltered = DB::table('tbl_lens_package')->where('product_id', 'like', '%' . $search . '%')
            ->where('product_code', 'like', '%' . $search . '%')
                ->where('product_name', 'like', '%' . $search . '%')
            ->count();
        }
        
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                $pimage = '';

                if (!empty($template->product_image)) {
                    $product_images = json_decode($template->product_image, true);
                
                    if (is_array($product_images)) {
                         $filePath = asset('uploads/glass/product/' . $template->product_id . '/');
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

                $nestedData['sr_no'] =$i++; 
                $nestedData['lens_type'] = $template->product_name;
                $nestedData['package_name'] = $template->productdetails;
                $nestedData['package_details'] = $template->Description;
                $nestedData['lens_price'] = $template->Retail_Price;
                $nestedData['package_id'] = $template->id;
                $nestedData['pid'] = $template->product_id;
                $nestedData['is_coating'] = $template->is_coating;
                $nestedData['pimage'] = $pimage;
                $nestedData['package_image'] = $template->product_image;

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
    
    
    
    public function packageStore(Request $request)
    {
        DB::beginTransaction();
    
        try {
    
            $user = auth()->user();
    
            /* ===============================
               INSERT CASE
            =============================== */
            if (empty($request->uid)) {
    
                // Generate product id
                $idgenerate = $this->generateUniqueRandomIdProduct(6, 'tbl_product_code', 'product_id');
    
                /* ---------- Image Upload ---------- */
                $folderName = 'glass/product/' . $idgenerate;
                $folderPath = public_path('uploads/' . $folderName);
    
                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0777, true);
                }
    
                $packageImages = [];
    
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $file) {
                        $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($folderPath, $imageName);
                        $packageImages[] = $imageName;
                    }
                }
    
                $packageImagesJson = json_encode($packageImages);
    
                /* ---------- Insert Product ---------- */
                $productTableId = DB::table('tbl_product_code')->insertGetId([
                    'product_id'     => $idgenerate,
                    'product_type'   => 'Glass',
                    'product_code'   => $request->product_code,
                    'product_name'   => $request->lens_type,
                    'productdetails' => $request->package_name,
                    'product_image'  => $packageImagesJson,
                    'Retail_Price'   => $request->lens_price,
                    'is_coating'   => $request->is_coating,
                    'Description'   => $request->package_details,
                    'added_by'       => $user->id,
                    'store_id'       => $user->store_id,
                    'in_house'       => 1,
                    'created_at'     => now()
                ]);
    
                /* ---------- Insert Coatings ---------- */
                if ($request->is_coating == 1 && $request->has('coating_name')) {
    
                    $coatingData = [];
    
                    foreach ($request->coating_name as $index => $name) {
    
                        if (!empty($name) && !empty($request->coating_price[$index])) {
    
                            $coatingData[] = [
                                'product_id'    => $productTableId,
                                'coating_name'  => $name,
                                'coating_price' => $request->coating_price[$index],
                                'created_at'    => now()
                            ];
                        }
                    }
    
                    if (!empty($coatingData)) {
                        DB::table('tbl_product_coating')->insert($coatingData);
                    }
                }
    
                DB::commit();
                return response()->json(['success' => 'Package created successfully.']);
            }
    
            /* ===============================
               UPDATE CASE
            =============================== */
            else {
    
                /* ---------- Update Product ---------- */
                DB::table('tbl_product_code')
                    ->where('id', $request->uid)
                    ->update([
                        'product_type'   => 'Glass',
                        'product_code'   => $request->product_code,
                        'product_name'   => $request->lens_type,
                        'productdetails' => $request->package_name,
                        'Retail_Price'   => $request->lens_price,
                        'is_coating'   => $request->is_coating,
                        'Description'   => $request->package_details,
                        'updated_by'     => $user->id,
                        'updated_at'     => now()
                    ]);
    
                /* ---------- Update Images (optional) ---------- */
                if ($request->hasFile('images')) {
    
                    $folderName = 'glass/product/' . $request->uid;
                    $folderPath = public_path('uploads/' . $folderName);
    
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }
    
                    $packageImages = [];
    
                    foreach ($request->file('images') as $file) {
                        $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($folderPath, $imageName);
                        $packageImages[] = $imageName;
                    }
    
                    DB::table('tbl_product_code')
                        ->where('id', $request->uid)
                        ->update([
                            'product_image' => json_encode($packageImages)
                        ]);
                }
    
                /* ---------- Delete Old Coatings ---------- */
                DB::table('tbl_product_coating')
                    ->where('product_id', $request->uid)
                    ->delete();
    
                /* ---------- Insert New Coatings ---------- */
                if ($request->is_coating == 1 && $request->has('coating_name')) {
    
                    $coatingData = [];
    
                    foreach ($request->coating_name as $index => $name) {
    
                        if (!empty($name) && !empty($request->coating_price[$index])) {
    
                            $coatingData[] = [
                                'product_id'    => $request->uid,
                                'coating_name'  => $name,
                                'coating_price' => $request->coating_price[$index],
                                'created_at'    => now()
                            ];
                        }
                    }
    
                    if (!empty($coatingData)) {
                        DB::table('tbl_product_coatings')->insert($coatingData);
                    }
                }
    
                DB::commit();
                return response()->json(['success' => 'Package updated successfully.']);
            }
    
        } catch (\Exception $e) {
    
            DB::rollBack();
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    
    public function generateUniqueRandomIdProduct($length = 6, $table = 'tbl_product_code', $column = 'product_id', $min = 100000, $max = 999999)
    {
        do {
            $id = random_int($min, $max);
        } while (
            DB::table($table)->where($column, $id)->exists()
        );
    
        return $id;
    }

    
    
    public function packagedestroy($id)
    {
        $Is_delted = DB::table('tbl_lens_package')->where('package_id', $id)->delete();
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Package was successfully deleted',
        ]);
    }
    
    
    public function smstemplate()
    {
        $setting['page_title'] = 'SMS Template';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/sms-template', $setting);
    }
    
    
    
    public function smsTemplateUpdate(Request $request)
    {
        $user = auth()->user();
        $smsIds = $request->sms_id;

        $errors = [];
        foreach ($smsIds as $i => $id)
        {
            DB::table('tbl_sms_template')->where('id', $id)->update([
                'send_status'   => $request->send_status[$i] ?? null,
                'Template'      => $request->Template[$i] ?? null,
                'Template_id'   => $request->Template_id[$i] ?? null,
                'entity_id'     => $request->entity_id[$i] ?? null,
                'sender_id'     => $request->sender_id[$i] ?? null,
                'sms_url'       => $request->sms_url[$i] ?? null,
                'message_type'  => $request->message_type[$i] ?? null,
                'updated_by' => $user->id,
            ]);
                
            
        }
    
        if(!empty($errors)) {
            return response()->json(['error' => $errors]);
        }
    
        return response()->json(['success' => 'SMS Templates Updated Successfully']);
    }
    
    
    public function smsSetting()
    {
        $setting['page_title'] = 'SMS Setting';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/sms-setting', $setting);
    }
    
    
    public function smsUpdate(Request $request)
    {
        $validated = $request->validate([
            'welcome_sms' => 'required|in:0,1',
            'important_otp_status' => 'required|in:0,1',
            'secure_otp_option' => 'required|in:0,1,2',
            'manually_mobile_no' => 'nullable|required_if:secure_otp_option,2|digits_between:10,15',
            'secure_download_option' => 'required|in:0,1,2',
            'manually_mobile_no_report' => 'nullable|required_if:secure_download_option,2|digits_between:10,15',
            // Add other checkboxes validation if needed
        ]);
    
        $sms = SmsSetting::first(); // Or find by ID if multi-row
        $sms->fill($validated);
        
        // Save checkboxes
        $checkboxes = ['deleteOrder','deleteStock','deleteChallan','deleteExpense','deleteVouchers',
                       'deleteProductCode','deleteCustomer','deletePrescription','userFranchise',
                       'loyaltyProgram','discountCoupons','customerAccountOption','allowNegativeInventoryInProducts'];
    
        foreach ($checkboxes as $field) {
            $sms->$field = $request->has($field) ? 1 : 0;
        }
    
        $sms->save();
    
        return response()->json(['success' => 'SMS settings updated successfully']);
    }
    
    
    
    public function whatsapptemplateSetting()
    {
        $setting['page_title'] = 'WhatsApp Template';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/whatsapp-template', $setting);
    }
    

    public function whatsappTemplateUpdate(Request $request)
    {
        $user = auth()->user();
        $smsIds = $request->sms_id;

        $errors = [];
        foreach ($smsIds as $i => $id)
        {
            DB::table('tbl_whatsapp_template')->where('id', $id)->update([
                'send_status'   => $request->send_status[$i] ?? null,
                'Template'      => $request->Template[$i] ?? null,
                'send_method'  => $request->send_method[$i] ?? null,
                'updated_by' => $user->id,
            ]);
                
        }
    
        if(!empty($errors)) {
            return response()->json(['error' => $errors]);
        }
    
        return response()->json(['success' => 'Whatsapp Templates Updated Successfully']);
    }
    
    
    public function whatsappSetting()
    {
        $setting['page_title'] = 'WhatsApp Setting';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/whatsapp-setting', $setting);
    }
    

    public function whatsappUpdate(Request $request)
    {
        $user = auth()->user();
        $store_id = $request->store_id;
        $tbl_whatsapp = DB::table('tbl_whatsapp')->where('store_id', $store_id)->count();
        
        if($tbl_whatsapp > 0)
        {
            DB::table('tbl_whatsapp')->where('store_id', $store_id)->update([
                'integration_status'   => $request->integration_status ?? null,
                'whatsapp_mesg'      => $request->whatsapp_mesg ?? null,
                'whatsapp_instance'  => $request->whatsapp_instance ?? null,
                'prefix'  => $request->prefix?? null,
                'whatsapp_no'  => $request->whatsapp_no ?? null,
                'updated_by' => $user->id,
            ]);
        }
        else
        {
            DB::table('tbl_whatsapp')->insert([
                'store_id'   => $request->store_id ?? null,
                'integration_status'   => $request->integration_status ?? null,
                'whatsapp_mesg'      => $request->whatsapp_mesg ?? null,
                'whatsapp_instance'  => $request->whatsapp_instance ?? null,
                'prefix'  => $request->prefix ?? null,
                'whatsapp_no'  => $request->whatsapp_no ?? null,
                'updated_by' => $user->id,
            ]);
        }
                
        return response()->json(['success' => 'Whatsapp Setting Updated Successfully']);
    }
    
    
    public function getWhatsappDetails($id)
    {
        $store = DB::table('tbl_whatsapp')
            ->where('store_id', $id)
            ->first();

        if ($store) {
            return response()->json([
                'status' => true,
                'data' => $store
            ]);
        }

        return response()->json([
            'status' => false
        ]);
    }
    
    
    
    public function mystryAuditEntry()
    {
        $setting['page_title'] = 'Mystry Audit Entry';
        $mystry_adit = DB::table('tbl_mystry_adit_setting')->get();
        return view($this->view_route.'/mystry-audit-entry',$setting, compact('mystry_adit'));
    }
    
    
    
    public function mystryAuditAdd(Request $request)
    {
        $request->validate([
            'store_id'   => 'required',
            'Auditor_id' => 'required',
            'audit'      => 'required|array'
        ]);
    
        DB::beginTransaction();
    
        try {
    
            /* ================= MAIN AUDIT ================= */
    
            $audit_id = DB::table('tbl_mystry_audit')->insertGetId([
                'store_id'     => $request->store_id,
                'auditor_id'   => $request->Auditor_id,
                'audit_date'   => $request->mystry_audit_date,
                'final_score'  => $request->final_score,
                'audit_result' => $request->audit_result,
                'created_at'   => now()
            ]);
    
    
            /* ================= CHECKPOINT DETAILS ================= */
    
            foreach ($request->audit as $setting_id => $checkpoints)
            {
                $setting = DB::table('tbl_mystry_adit_setting')
                            ->where('id', $setting_id)
                            ->first();
    
                if(!$setting) continue;
    
                foreach ($checkpoints as $checkpoint_key => $entered_marks)
                {
                    if($entered_marks === null || $entered_marks === '')
                        continue;
    
                    $max_mark_column = $checkpoint_key.'_Mark';
                    $max_marks = $setting->$max_mark_column ?? 0;
    
                    /* ===== MARK VALIDATION ===== */
                    if($entered_marks > $max_marks){
                        throw new \Exception(
                            "$checkpoint_key marks cannot exceed $max_marks"
                        );
                    }
    
                    $checkpoint_name = $setting->$checkpoint_key ?? '';
    
                    /* ================= PHOTO UPLOAD ================= */
    
                    $photo_name = null;
    
                    if ($request->hasFile("audit_photo.$setting_id.$checkpoint_key"))
                    {
                        $file = $request->file("audit_photo.$setting_id.$checkpoint_key");
    
                        if ($file->isValid()) {
    
                            $photo_name = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
    
                            $file->move(
                                public_path('audit_photos'),
                                $photo_name
                            );
                        }
                    }
    
                    /* ================= INSERT DETAIL ================= */
    
                    DB::table('tbl_mystry_audit_details')->insert([
                        'audit_id'        => $audit_id,
                        'setting_id'      => $setting_id,
                        'checkpoint_name' => $checkpoint_name,
                        'marks'           => $entered_marks,
                        'photo'           => $photo_name, 
                        'created_at'      => now()
                    ]);
                }
            }
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Audit Saved Successfully'
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    
    
    public function mystryAudithistory()
    {
        $setting['page_title'] = 'Mystry Audit History';
        return view($this->view_route.'/mystry-audit-history',$setting);
    }
    
    
    
    public function mystryAuditDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $storeid = $request->input('store_id');
        
        if($store_id == '0')
        {
            $totalData = DB::table('tbl_mystry_audit');
        }
        else
        {
            $totalData = DB::table('tbl_mystry_audit')->where('store_id', $store_id);
        }
        
        if ($storeid != '')
        {
            $totalData->where('store_id', $storeid);
        }
        
        if ($date_from != '' && $date_to != '')
        {
            $totalData->whereBetween('audit_date', [$date_from,  $date_to . ' 23:59:59']);
        }

        $totalData = $totalData->count();
        
        if($store_id == '0')
        {
            $templates = DB::table('tbl_mystry_audit');
        }
        else
        {
            $templates = DB::table('tbl_mystry_audit')->where('store_id', $store_id);
        }
        if ($storeid != '')
        {
            $templates->where('store_id', $storeid);
        }
        if ($date_from != '' && $date_to != '') 
        {
           $templates->whereBetween('audit_date', [$date_from,  $date_to . ' 23:59:59']);
        }



        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('mystry_audit_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                
                $created_by = User::find($template->auditor_id);
                
                $store_name = Store::find($template->store_id);
       
                
                $encryptedId = base64_encode($template->mystry_audit_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['audit_date']  = date("d-m-Y H:i:A", strtotime($template->audit_date));
                $nestedData['final_score']  = $template->final_score.' / 120';
                $nestedData['audit_result']  = $template->audit_result;
                $nestedData['encryptedId']  = $encryptedId;
                $nestedData['auditor']  = $created_by->name;
                $nestedData['store_name']  = $store_name->store_name;
                $nestedData['mystry_audit_id']  = $template->mystry_audit_id;
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
    
    
    public function mystryauditdestroy($id)
    {
        $Is_delted = DB::table('tbl_mystry_audit')->where('mystry_audit_id', $id)->delete();
        $Is_delted_De = DB::table('tbl_mystry_audit_details')->where('audit_id', $id)->delete();
        if (!$Is_delted && !$Is_delted_De) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Audit was successfully deleted',
        ]);
    }
    
    
    public function editmystryaudit($id)
    {
        $decryptedId = base64_decode($id);
    
        $data['page_title'] = 'Edit Mystry-Audit';
    
        /* ================= MAIN AUDIT ================= */
        $data['mystryaudit'] = DB::table('tbl_mystry_audit')
            ->where('mystry_audit_id', $decryptedId)
            ->first();
    
    
        /* ================= SETTINGS ================= */
        $mystry_adit = DB::table('tbl_mystry_adit_setting')->get();
    
    
        /* ================= DETAILS ================= */
        $details = DB::table('tbl_mystry_audit_details')
            ->where('audit_id', $decryptedId)
            ->get();
    
    
        /*
            Convert to usable format:
    
            auditDetails[
                setting_id
            ][
                checkpoint_key
            ] = marks
        */
    
        $auditDetails = [];
    
        foreach ($details as $row)
        {
            // find which checkpoint column matches name
            $setting = $mystry_adit->where('id', $row->setting_id)->first();
    
            if(!$setting) continue;
    
            foreach (['A','B','C','D','E'] as $cp)
            {
                $col = 'Checkpoint_'.$cp;
    
                if($setting->$col == $row->checkpoint_name)
                {
                    $auditDetails[$row->setting_id][$col] = $row->marks;
                }
            }
        }
    
        return view(
            $this->view_route.'/edit-mystry-audit',
            $data,
            compact('mystry_adit','auditDetails')
        );
    }

    
    public function mystryAuditupdate(Request $request)
    {
        $request->validate([
            'audit_id'   => 'required',
            'store_id'   => 'required',
            'Auditor_id' => 'required',
            'audit'      => 'required|array'
        ]);
    
        DB::beginTransaction();
    
        try {
    
            $audit_id = $request->audit_id;
            /*
            |--------------------------------------------------------------------------
            | UPDATE MAIN AUDIT
            |--------------------------------------------------------------------------
            */
            DB::table('tbl_mystry_audit')
                ->where('mystry_audit_id', $audit_id)
                ->update([
                    'store_id'     => $request->store_id,
                    'auditor_id'   => $request->Auditor_id,
                    'final_score'  => $request->final_score,
                    'audit_result' => $request->audit_result,
                    'updated_at'   => now()
                ]);
            /*
            |--------------------------------------------------------------------------
            | DELETE OLD DETAILS
            |--------------------------------------------------------------------------
            */
            DB::table('tbl_mystry_audit_details')
                ->where('audit_id', $audit_id)
                ->delete();
            /*
            |--------------------------------------------------------------------------
            | INSERT NEW CHECKPOINT MARKS
            |--------------------------------------------------------------------------
            */
            foreach ($request->audit as $setting_id => $checkpoints)
            {
                // get checkpoint master row
                $setting = DB::table('tbl_mystry_adit_setting')
                    ->where('id', $setting_id)
                    ->first();
    
                if(!$setting) continue;
    
                foreach ($checkpoints as $checkpoint_key => $entered_marks)
                {
                    if($entered_marks === '' || $entered_marks === null)
                        continue;
    
                    // Max marks column
                    $max_column = $checkpoint_key.'_Mark';
                    $max_marks  = $setting->$max_column ?? 0;
    
                    /*
                    |--------------------------------------------------------------------------
                    | BACKEND VALIDATION
                    |--------------------------------------------------------------------------
                    */
                    if($entered_marks > $max_marks)
                    {
                        throw new \Exception(
                            "$checkpoint_key marks cannot exceed $max_marks"
                        );
                    }
    
                    // Get checkpoint name
                    $checkpoint_name = $setting->$checkpoint_key ?? '';
    
                    DB::table('tbl_mystry_audit_details')->insert([
                        'audit_id'        => $audit_id,
                        'setting_id'      => $setting_id,
                        'checkpoint_name' => $checkpoint_name,
                        'marks'           => $entered_marks,
                        'created_at'      => now()
                    ]);
                }
            }
    
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'message' => 'Audit Updated Successfully'
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    
    
    public function viewmystryaudit($id)
    {
        
        $audit_id = $id;
        // Decode ID
        $id = base64_decode($id);
    
        /*
        |--------------------------------------------------------------------------
        | GET MAIN AUDIT DATA
        |--------------------------------------------------------------------------
        */
        $audit = DB::table('tbl_mystry_audit as a')
            ->leftJoin('tbl_store as s', 's.id', '=', 'a.store_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.auditor_id')
            ->select(
                'a.*',
                's.store_name',
                'u.name as auditor_name'
            )
            ->where('a.mystry_audit_id', $id)
            ->first();
    
    
        // Safety check (important)
        if (!$audit) 
        {
            abort(404, 'Audit not found');
        }
    
    
        /*
        |--------------------------------------------------------------------------
        | GET AUDIT DETAILS (GROUP BY SECTION)
        |--------------------------------------------------------------------------
        */
        $details = DB::table('tbl_mystry_audit_details')
            ->where('audit_id', $audit->mystry_audit_id)
            ->orderBy('setting_id')
            ->get()
            ->groupBy('setting_id');
        /*
        |--------------------------------------------------------------------------
        | LOAD VIEW
        |--------------------------------------------------------------------------
        */
        
        $store= Store::where('id', $audit->store_id)->first();
        $state = State::find($store->state_id);
        $city = City::find($store->city_id);
        
        
        return view(
            $this->view_route . '/view-mystry-audit',
            compact('audit', 'details','store','state','city','audit_id')
        );
    }
    
    
    
    public function auditPdf($id)
    {
        
        $audit_id = $id;
        // Decode ID
        $id = base64_decode($id);
    
        /*
        |--------------------------------------------------------------------------
        | GET MAIN AUDIT DATA
        |--------------------------------------------------------------------------
        */
        $audit = DB::table('tbl_mystry_audit as a')
            ->leftJoin('tbl_store as s', 's.id', '=', 'a.store_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.auditor_id')
            ->select(
                'a.*',
                's.store_name',
                'u.name as auditor_name'
            )
            ->where('a.mystry_audit_id', $id)
            ->first();
    
    
        // Safety check (important)
        if (!$audit) 
        {
            abort(404, 'Audit not found');
        }
    
    
        /*
        |--------------------------------------------------------------------------
        | GET AUDIT DETAILS (GROUP BY SECTION)
        |--------------------------------------------------------------------------
        */
        $details = DB::table('tbl_mystry_audit_details')
            ->where('audit_id', $audit->mystry_audit_id)
            ->orderBy('setting_id')
            ->get()
            ->groupBy('setting_id');
        /*
        |--------------------------------------------------------------------------
        | LOAD VIEW
        |--------------------------------------------------------------------------
        */
        
        $store= Store::where('id', $audit->store_id)->first();
        $state = State::find($store->state_id);
        $city = City::find($store->city_id);
        

        $pdf = Pdf::loadView($this->view_route . '/pdf-mystry-audit',compact('audit', 'details','store','state','city','audit_id'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);

        return $pdf->stream($id . '.pdf');
    }
    
    
    public function mystryauditSetting()
    {
        $setting['page_title'] = 'Mystry Audit Setting';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        $auditSetting  = DB::table('tbl_mystry_adit_setting')->get();
        
        return view($this->view_route.'/mystry-audit-setting',$setting, compact('auditSetting'));
    }
    
    
    
    public function settingAuditupdate(Request $request)
    {
        if(isset($request->audit))
        {
            foreach ($request->audit as $id => $data)
            {
                DB::table('tbl_mystry_adit_setting')
                ->where('id', $id)
                ->update([

                    'title' => $data['title'] ?? null,

                    // CHECKPOINT A
                    'Checkpoint_A'        => $data['Checkpoint_A'] ?? null,
                    'Checkpoint_A_Mark'   => $data['Checkpoint_A_Mark'] ?? 0,
                    'Checkpoint_A_answer' => $data['Checkpoint_A_answer'] ?? null,

                    // CHECKPOINT B
                    'Checkpoint_B'        => $data['Checkpoint_B'] ?? null,
                    'Checkpoint_B_Mark'   => $data['Checkpoint_B_Mark'] ?? 0,
                    'Checkpoint_B_answer' => $data['Checkpoint_B_answer'] ?? null,

                    // CHECKPOINT C
                    'Checkpoint_C'        => $data['Checkpoint_C'] ?? null,
                    'Checkpoint_C_Mark'   => $data['Checkpoint_C_Mark'] ?? 0,
                    'Checkpoint_C_answer' => $data['Checkpoint_C_answer'] ?? null,

                    // CHECKPOINT D
                    'Checkpoint_D'        => $data['Checkpoint_D'] ?? null,
                    'Checkpoint_D_Mark'   => $data['Checkpoint_D_Mark'] ?? 0,
                    'Checkpoint_D_answer' => $data['Checkpoint_D_answer'] ?? null,

                    // CHECKPOINT E
                    'Checkpoint_E'        => $data['Checkpoint_E'] ?? null,
                    'Checkpoint_E_Mark'   => $data['Checkpoint_E_Mark'] ?? 0,
                    'Checkpoint_E_answer' => $data['Checkpoint_E_answer'] ?? null,
                    'updated_at' => now()
                ]);
            }
        }
    
        return response()->json([
            'status'  => true,
            'message' => 'Audit Setting Updated Successfully'
        ]);
    }
    
    
    public function auditDashboard()
    {
        $setting['page_title'] = 'Mystery Audit Dashboard';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view('layouts/mystery_audit',$setting);
    }


    public function npsDashboard()
    {
        $setting['page_title'] = 'Nps Dashboard';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view('layouts/nps',$setting);
    }

    
    public function membershipSetting()
    {
        $setting['page_title'] = 'Setting membership';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        $cards = DB::table('tbl_membership_card')->where('flag',0)->get();
        return view($this->view_route.'/setting-membership',$setting, compact('cards'));
    }
    
    
    
    public function membershipcardAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_name'        => 'required|string|max:255',
            'price'            => 'required|string|max:100',
            'validity_days'            => 'required|string|max:100',
            'loyalty_earn_first'            => 'required|string|max:100',
            'loyalty_earn_repeat'            => 'required|string|max:100',
            'loyalty_use_percent'            => 'required|string|max:100',
            'coupon_percent'            => 'required|string|max:100',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }
        
        
        if ($request->card_id) {
    
            DB::table('tbl_membership_card')
                ->where('card_id', $request->card_id)
                ->update([
                    'card_name' => $request->card_name,
                    'price' => $request->price,
                    'validity_days' => $request->validity_days,
                    'loyalty_earn_first' => $request->loyalty_earn_first,
                    'loyalty_earn_repeat' => $request->loyalty_earn_repeat,
                    'loyalty_use_percent' => $request->loyalty_use_percent,
                    'coupon_percent' => $request->coupon_percent,
                    'voucher_validity_days' => $request->voucher_validity_days,
                    'updated_at'      => now()
                ]);
    
        } else {
    
            DB::table('tbl_membership_card')->insert([
                'card_name' => $request->card_name,
                'price' => $request->price,
                'validity_days' => $request->validity_days,
                'loyalty_earn_first' => $request->loyalty_earn_first,
                'loyalty_earn_repeat' => $request->loyalty_earn_repeat,
                'loyalty_use_percent' => $request->loyalty_use_percent,
                'coupon_percent' => $request->coupon_percent,
                'voucher_validity_days' => $request->voucher_validity_days,
                'added_by' => auth()->id(),
                'created_at'      => now(),
                'updated_at'      => now()
            ]);
        }

   
        

        return response()->json(['success' => 'Card created successfully.']);
    }
    
    
    public function membsershipDelete($id)
    {
        DB::table('tbl_membership_card')
            ->where('card_id', $id)
            ->update(['flag' => 1]); // or 'status' => 0 / 1 based on your column
    
        return redirect()->back()->with('success', 'Membership deleted successfully');
    }


}