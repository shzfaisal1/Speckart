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
use App\Models\purchases\Purchase;
use App\Models\purchases\PurchaseProduct;
use App\Models\purchases\Barcode;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use PDF;
use Carbon\Carbon;
use App\Models\product\Product;
use App\Models\purchases\Challan;
use App\Models\purchases\ChallanProduct;
use App\Models\sale\Sale;
use App\Models\sale\SaleProduct;



class PurchaseController extends Controller
{
    function __construct()
    {
        
    }
    
    public $view_route = 'purchase';
    

    public function addPurchase()
    {
        $setting['page_title'] = 'add-purchase';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/add-purchase',$setting);
    }
    
    public function supplierListdropdown(Request $request)
    {
        $search = $request->get('name');
        $supplier = Supplier::where('status', '1')->where('supplier_company', 'LIKE', "%{$search}%")->get(['supplier_company']);
    
        return response()->json($supplier);
    }
    
    
    public function getProductWiseCode(Request $request)
    {
        $productType = $request->input('product_type');
        $query = $request->input('query');
    
        $products = DB::table('tbl_product_code')
            ->select('productdetails')
            ->where('product_type', $productType)
            ->where('product_code', 'LIKE', '%' . $query . '%')
            ->get();
    
        return response()->json($products);
    }
    
    
    
    public function getGSTDetails(Request $request)
    {
        $productType = $request->input('product_type');
        
        if($productType =='Repair')
        {
            return response()->json([
                    'hsn_code'   =>  '',
                    'percentage'        =>  '',
                    'productType'   => $productType,
    
                ]);
        }
        else
        {
            $product = DB::table('tbl_tax')
                ->where('product_type', $productType)
                ->where('set_default', 1)
                ->first();
                
            if($product) 
            {
                
                return response()->json([
                    'hsn_code'   => $product->hsn_code ?? '',
                    'percentage'        => $product->percentage ?? '',
                    'productType'   => $productType,
    
                ]);
            } 
            else
            {
                return response()->json([], 404);
            }
        }    
    }
    
    public function getProductDetails(Request $request)
    {
        $productType = $request->input('product_type');
        $productdetails = $request->input('productdetails');
    
        $product = DB::table('tbl_product_code')
            ->where('product_type', $productType)
            ->where('productdetails', $productdetails)
            ->orderby('id', 'DESC')
            ->first();
    
        return response()->json([
            'product_id'   => $product->product_id ?? '',
            'product_code' => $product->product_code ?? '',
            'product_name' => $product->product_name ?? '',
            'Company'      => $product->Company ?? '',
            'Quality'      => $product->Quality ?? '',
            'Track_Inventory'      => $product->Track_Inventory ?? '',
            'Allow_Negative_Inventory'      => $product->Allow_Negative_Inventory ?? '',
            'Purchase_Price'      => $product->Purchase_Price ?? '0',
            'Retail_Price'      => $product->Retail_Price ?? '0',
            'Color'        => $product->Color ?? '',
            'Material'     => $product->Material ?? '',
            'Coating'      => $product->Coating ?? '',
            'Design'       => $product->Design ?? '',
            'Index'        => $product->Index ?? '',
            'SPH'          => $product->SPH ?? '',
            'CYL'          => $product->CYL ?? '',
            'AXIS'         => $product->AXIS ?? '',
            'ADD'          => $product->ADD ?? '',
            'LNumber'      => $product->Number ?? '',
            'CT'           => $product->CT ?? '',
            'PType'        => $product->Type ?? '',
            'Validity'     => $product->Validity ?? '',
            'base_carve'   => $product->base_carve ?? '',
            'Diameter'     => $product->Diameter ?? '',
            'Power_Type'   => $product->Power_Type ?? '',
            'Batch_Number' => $product->Batch_Number ?? '',
            'Mfg_Date'     => $product->Mfg_Date ?? '',
            'Expiry_Date'  => $product->Expiry_Date ?? '',
            'Variant'      => $product->Variant ?? '',
            'Packing_Type' => $product->Packing_Type ?? '',
            'Shape'        => $product->Shape ?? '',
            'Size'         => $product->Size ?? '',
        ]);
    }
    
    

    public function getOldValue(Request $request)
    {
        $productType = $request->input('productType');
        $productCode = $request->input('productCode');
        $store_id= $request->input('store_id');
        

        $product = DB::table('tbl_purchase_deatils as pd')
              ->leftJoin('tbl_purchase as p', 'p.purchase_id', '=', 'pd.purchase_id')
            ->where('pd.product_type', $productType)
            ->where('pd.product_code', $productCode)
            ->where('pd.store_id', $store_id)
            ->orderBy('pd.id', 'desc')
            ->get(['pd.bill_no', 'pd.product_price', 'pd.product_retail_price', 'pd.product_details', 'pd.hsn_code', 'pd.gst', 'p.purchase_date', 'p.supplier_name']);

        return response()->json([
            'data' => $product->map(function ($p) {
                return [
                    'bill_no' => $p->bill_no,
                    'product_price' => $p->product_price,
                    'product_retail_price' => $p->product_retail_price,
                    'product_details' => $p->product_details,
                    'purchase_date' => $p->purchase_date,
                    'supplier_name' => $p->supplier_name,
                    'hsn_code' => $p->hsn_code,
                    'gst' => $p->gst,
                ];
            })
        ]);
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
    
    public function StoreOrder(Request $request)
    {
        $user = auth()->user();
        
        // Validation
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255',
            'p_bill_no'     => 'required|string|max:255',
            'date_from'     => 'required',
            'tax_rule'     => 'required',
            'store_id'     => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        // At least one valid product
        $hasValidProduct = false;
        foreach ($request->input('product_type', []) as $i => $type) {
            if (!empty($type) && !empty($request->input("product_code.$i"))) {
                $hasValidProduct = true;
                break;
            }
        }
    
        if (!$hasValidProduct) {
            return response()->json([
                'status' => false,
                'errors' => 'Please add at least one valid product.'
            ], 422);
        }
    

    
        DB::beginTransaction();
        try {
            $data = $request->all();
    
            // Create Purchase record
            $purchase = Purchase::create([
                'supplier_name'       => $data['supplier_name'],
                'p_bill_no'           => $data['p_bill_no'],
                'purchase_date'       => $data['date_from'],
                'tax_rule'            => $data['tax_rule'] ?? null,
                'total_qty'           => $data['total_qty'] ?? 0,
                'total_unit_amount'   => $data['total_unit_amount'] ?? 0,
                'total_base_amount'   => $data['total_base_amount'] ?? 0,
                'total_gst_amount'    => $data['total_gst_amount'] ?? 0,
                'total_p_amount'      => $data['total_p_amount'] ?? 0,
                'round_off'           => $data['round_off'] ?? 0,
                'net_purchase_amount' => $data['net_purchase_amount'] ?? 0,
                'added_by'            => $user->id,
                'store_id'            => $data['store_id'],
            ]);
    
            // Loop through each product
            foreach ($data['product_type'] as $i => $type) 
            {
                $code = $data['product_code'][$i] ?? null;
                if (empty($type) || empty($code)) continue;
                
                $tCount = DB::table('tbl_product_code')
                         ->where('product_type', $type)
                         ->where('product_code', $code)
                         ->where('productdetails', $data['product_details'][$i])->count();
                         
                
                $PCount = DB::table('tbl_product_code')->where('product_code', $code)->count();
                    
                if($tCount == 0)
                {
                    
                    $idgenerate = $this->generateUniqueRandomIdProduct(6, 'tbl_product_code', 'product_id');
                    
                    if($PCount == 0)
                    {
                        $product_id = $idgenerate;
                    }
                    else
                    {
                        $tbl_product_code = DB::table('tbl_product_code')->where('product_code', $code)->first();
                        $product_id = $tbl_product_code->product_id;
                    }
                    
                    $Product = Product::create([
                        'product_type'         => $type,
                        'product_code'         => $code,
                        'product_id' => $product_id,
                        'productdetails'       => $data['product_details'][$i] ?? '',
                        'product_name'         => $data['product_name'][$i] ?? '',
                        'Company'              => $data['product_company'][$i] ?? '',
                        'Quality'              => $data['product_quality'][$i] ?? '',
                        'Color'                => $data['product_color'][$i] ?? '',
                        'Type'                 => $data['product_lenstype'][$i] ?? '',
                        'Material'             => $data['product_material'][$i] ?? '',
                        'Coating'              => $data['product_coating'][$i] ?? '',
                        'Design'               => $data['product_design'][$i] ?? '',
                        'Index'                => $data['product_index'][$i] ?? '',
                        'SPH'                  => $data['product_sph'][$i] ?? '',
                        'CYL'                  => $data['product_cyl'][$i] ?? '',
                        'AXIS'                 => $data['product_axis'][$i] ?? '',
                        'ADD'                  => $data['product_addition'][$i] ?? '',
                        'Number'               => $data['product_number'][$i] ?? '',
                        'CT'                   => $data['product_shape'][$i] ?? '',
                        'Validity'             => $data['product_validity'][$i] ?? '',
                        'Variant'              => $data['product_variant'][$i] ?? '',
                        'Shape'                => $data['product_shape'][$i] ?? '',
                        'Size'                 => $data['product_size'][$i] ?? '',
                        'base_carve'           => $data['product_bc'][$i] ?? '',
                        'Diameter'             => $data['product_diameter'][$i] ?? '',
                        'Power_Type'           => $data['product_powertype'][$i] ?? '',
                        'No_Of_Boxes'          => $data['product_noofbox'][$i] ?? '',
                        'Pieces_Per_Box'       => $data['product_perbox'][$i] ?? '',
                        'Batch_Number'         => $data['product_batch'][$i] ?? '',
                        'Mfg_Date'             => $data['product_mfg'][$i] ?? '',
                        'Expiry_Date'          => $data['product_expiry'][$i] ?? '',
                        'Track_Inventory'      => $data['track_inventory'][$i] ?? '',
                        'Allow_Negative_Inventory'        => $data['negative_inventory'][$i] ?? '',
                        'Purchase_Base_Price'   => (float)($data['product_base_price'][$i] ?? 0),
                        'Purchase_Price'        => (float)($data['product_purchase_price'][$i] ?? 0),
                        'Retail_Price'          => (float)($data['product_retail_price'][$i] ?? 0),
                        'store_id'              => $data['store_id'],
                        'added_by'              => $user->id,
                    ]);

                    
            
                }
                else
                {
                    $tbl_product_code = DB::table('tbl_product_code')->where('product_code', $code)->first();
                    $product_id = $tbl_product_code->product_id;
                }
                


                $purchaseProduct = PurchaseProduct::create([
                    'purchase_id'          => $purchase->id,
                    'bill_no'              => $data['p_bill_no'],
                    'product_type'         => $type,
                    'product_code'         => $code,
                    'product_id'           => $product_id,
                    'product_name'      => $data['product_name'][$i] ?? '',
                    'product_details'      => $data['product_description'][$i] ?? '',
                    'quality_detail'       => $data['product_quality'][$i] ?? '',
                    'company_detail'       => $data['product_company'][$i] ?? '',
                    'product_price'        => (float)($data['product_price'][$i] ?? 0),
                    'product_base_price'   => (float)($data['product_base_price'][$i] ?? 0),
                    'product_purchase_price'=> (float)($data['product_purchase_price'][$i] ?? 0),
                    'hsn_code'             => $data['hsn_code'][$i] ?? '',
                    'gst_amt'              => (float)($data['gst_amt'][$i] ?? 0),
                    'gst'                  => (float)($data['gst'][$i] ?? 0),
                    'qty'                  => (int)($data['product_qty'][$i] ?? 0),
                    'total_purchase_price' => (float)($data['total_purchase_price'][$i] ?? 0),
                    'product_retail_price' => (float)($data['product_retail_price'][$i] ?? 0),
                    'color_details'        => $data['product_color'][$i] ?? '',
                    'material_detail'      => $data['product_material'][$i] ?? '',
                    'size_details'         => $data['product_size'][$i] ?? '',
                    'Type_details'         => $data['product_lenstype'][$i] ?? '',
                    'shape_details'        => $data['product_shape'][$i] ?? '',
                    'coating_detail'       => $data['product_coating'][$i] ?? '',
                    'design_details'       => $data['product_design'][$i] ?? '',
                    'index_detail'         => $data['product_index'][$i] ?? '',
                    'Number_detail'        => $data['product_number'][$i] ?? '',
                    'ct_detail'            => $data['product_tc'][$i] ?? '',
                    'validity_detail'      => $data['product_validity'][$i] ?? '',
                    'sph_detail'           => $data['product_sph'][$i] ?? '',
                    'cyl_details'          => $data['product_cyl'][$i] ?? '',
                    'axis_detail'          => $data['product_axis'][$i] ?? '',
                    'addiional_detail'     => $data['product_addition'][$i] ?? '',
                    'bc_detail'            => $data['product_bc'][$i] ?? '',
                    'diameter_detail'      => $data['product_diameter'][$i] ?? '',
                    'powertype_details'    => $data['product_powertype'][$i] ?? '',
                    'batchno_details'        => $data['product_batch'][$i] ?? '',
                    'modality_details'     => $data['modality_details'][$i] ?? '',
                    'box_detail'           => $data['product_noofbox'][$i] ?? '',
                    'perbox_detail'        => $data['product_perbox'][$i] ?? '',
                    'mfg_detail'           => $data['product_mfg'][$i] ?? '',
                    'expiry_detail'        => $data['product_expiry'][$i] ?? '',
                    'variant_detail'       => $data['product_variant'][$i] ?? '',
                    'description_details'  => $data['product_invoicedescription'][$i] ?? '',
                    'store_id'             => $data['store_id'],
                    'added_by'             => $user->id,
                    'is_pair'              => $data['ispairglass'][$i] ?? '',
                ]);
                
                
                 $add_record = DB::table('tbl_inventory_record')->insert([
                    'product_code' => $code,
                    'product_id' => $product_id,
                    'perbox' => $data['product_perbox'][$i],
                    'product_type' => $type, 
                    'product_details' => $data['product_description'][$i],
                    'store_id' =>  $data['store_id'],
                    'qty' => (int)($data['product_qty'][$i] ?? 0),
                    'added_date' => $data['date_from'],
                    'inward_status' => 0,
                    'added_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
                // Update inventory
                $this->updateInventory($type, $code, $data['product_description'][$i], $request, $i, $data['store_id'],$product_id);
    
                // Generate barcodes
                if($data['barcode_option'][$i] == '1')
                {
                    $this->generatePurchaseBarcodes($type, $purchase, $purchaseProduct, $data['product_description'][$i], $request, $i, $data['store_id'],$product_id);
                }
                
            }
    
            DB::commit();
            return response()->json(['success' => 'Purchase and Products saved successfully!']);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the purchase save process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }





    private function updateInventory($type, $code, $product_details, $request, $i, $store_id, $pid)
    {
        $qty = (int)($request->input("product_qty.$i", 0));
        $perbox = (int)($request->input("product_perbox.$i", 1));
        $box_detail = (int)($request->input("product_noofbox.$i", $qty));
    
        $query = DB::table('tbl_inventory_levels')
            ->where('product_code', $code)
            ->where('product_details', $product_details)
            ->where('product_id', $pid)
            ->where('store_id', $store_id);
    
        $inventory = $query->first();
    
        if ($type === 'Lens') {
            if ($inventory) {
                $query->update([
                    'available_quantity' => $inventory->available_quantity + $box_detail,
                    'tota_lens_qty' => $inventory->tota_lens_qty + ($perbox*$box_detail),
                    'updated_at' => now()
                ]);
            } else {
                DB::table('tbl_inventory_levels')->insert([
                    'product_code' => $code,
                    'product_id' => $pid,
                    'product_type' => $type,
                    'product_details' => $product_details,
                    'perbox' => $perbox,
                    'store_id' => $store_id,
                    'available_quantity' => $box_detail,
                    'tota_lens_qty' => ($perbox*$box_detail),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } else {
            if ($inventory) {
                $query->update([
                    'available_quantity' => $inventory->available_quantity + $qty,
                    'updated_at' => now()
                ]);
            } else {
                DB::table('tbl_inventory_levels')->insert([
                    'product_code' => $code,
                    'product_id' => $pid,
                    'product_type' => $type,
                    'product_details' => $product_details,
                    'store_id' => $store_id,
                    'available_quantity' => $qty,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    private function generatePurchaseBarcodes($type, $purchase, $purchaseProduct,$product_details, $request, $i, $store_id, $pid)
    {
        $user = auth()->user();
        $qty = (int)($request->input("product_qty.$i", 0));
        $box_detail = (int)($request->input("product_noofbox.$i", $qty));
        $perbox = (int)($request->input("product_perbox.$i", 1));
        $purchase_price = (float)($request->input("product_purchase_price.$i", 0));
        $retail_price = (float)($request->input("product_retail_price.$i", 0));
        $code = $request->input("product_code.$i");


        if ($type === 'Lens') 
        {
            for ($b = 0; $b < $box_detail; $b++) 
            {
                $box_barcode = $this->generateUniqueRandomId(6);
    
                DB::table('tbl_barcode')->insert([
                    'purchase_id' => $purchase->id,
                    'purchase_product_id' => $purchaseProduct->id,
                    'purchase_date' => $request->input('date_from') ?? now(),
                    'p_bill_no' => $request->input('p_bill_no'),
                    'product_code' => $code,
                    'product_id' => $pid,
                    'perbox' => $perbox,
                    'product_type' => $type,
                    'barcode_no' => $box_barcode,
                    'product_details' => $product_details,
                    'mfg_date' => $purchaseProduct->mfg_detail,
                    'expiry_date' => $purchaseProduct->expiry_detail,
                    'batch_no' => $purchaseProduct->batchno_details,
                    'purchase_price' => $purchase_price,
                    'retail_price' => $retail_price,
                    'store_id' => $store_id,
                    'inward_status' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
                $piece_price = round($purchase_price / max($perbox, 1), 2);
                $piece_retail = round($retail_price / max($perbox, 1), 2);
    
                for ($p = 0; $p < $perbox; $p++) {
                    DB::table('tbl_barcode')->insert([
                        'purchase_id' => $purchase->id,
                        'purchase_product_id' => $purchaseProduct->id,
                        'purchase_date' => $request->input('date_from') ?? now(),
                        'p_bill_no' => $request->input('p_bill_no'),
                        'product_code' => $code,
                        'product_id' => $pid,
                        'product_type' => $type,
                        'barcode_no' => $this->generateUniqueRandomId1(7),
                        'lens_box' => $box_barcode,
                        'product_details' => $product_details,
                        'mfg_date' => $purchaseProduct->mfg_detail,
                        'expiry_date' => $purchaseProduct->expiry_detail,
                        'batch_no' => $purchaseProduct->batchno_details,
                        'purchase_price' => $piece_price,
                        'retail_price' => $piece_retail,
                        'store_id' => $store_id,
                        'inward_status' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                        'barcode_no' => $box_barcode,
                        'store_id' => $store_id,
                        'reference_type' => 'Purchase',
                        'action_perform' => 'Add',
                        'added_by' => $user->id,
                ]);
            }
            
            
        } 
        else {
            for ($q = 0; $q < $qty; $q++) 
            {
                $barcode = $this->generateUniqueRandomId(6);
                DB::table('tbl_barcode')->insert([
                    'purchase_id' => $purchase->id,
                    'purchase_product_id' => $purchaseProduct->id,
                    'purchase_date' => $request->input('date_from') ?? now(),
                    'p_bill_no' => $request->input('p_bill_no'),
                    'product_code' => $code,
                    'product_id' => $pid,
                    'product_type' => $type,
                    'barcode_no' => $barcode,
                    'product_details' => $product_details,
                    'purchase_price' => $purchase_price,
                    'is_pair' => $purchaseProduct->is_pair,
                    'retail_price' => $retail_price,
                    'store_id' => $store_id,
                    'inward_status' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                        'barcode_no' => $barcode,
                        'store_id' => $store_id,
                        'reference_type' => 'Purchase',
                        'action_perform' => 'Add',
                        'added_by' => $user->id,

                    ]);
            }
            
            
        }
        
        
    }

    private function generateUniqueRandomId($length = 6, $table = 'tbl_barcode', $column = 'barcode_no', $min = 100000, $max = 999999)
    {
        do 
        {
          $id = random_int($min, $max);
        } 
        while (DB::table($table)->where($column, $id)->exists());
        return $id;
    }
    
    private function generateUniqueRandomId1($length = 7, $table = 'tbl_barcode', $column = 'barcode_no', $min = 1000000, $max = 9999999)
    {
        do 
        {
          $id = random_int($min, $max);
        } while (DB::table($table)->where($column, $id)->exists());
        return $id;
    }
    
    
    public function purchaseHistory()
    {
        $setting['page_title'] = 'Purchase-History';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/purchase-history',$setting);
    }
    
    
    public function purchaseDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $search1 = $request->input('search1');
        $storeid = $request->input('store_id');
        
        if($store_id == '0')
        {
            $totalData = DB::table('tbl_purchase')->where('is_Deleted', '0');
        }
        else
        {
            $totalData = DB::table('tbl_purchase')->where('store_id', $store_id)->where('is_Deleted', '0');
        }
        
        if ($storeid != '')
        {
            $totalData->where('store_id', $storeid);
        }
        
        if ($date_from != '' && $date_to != '')
        {
            $totalData->whereBetween('purchase_date', [$date_from, $date_to]);
        }
        if ($search1 != '') 
        {
            $totalData->where('supplier_name', 'like', '%' . $search1 . '%')
            ->orWhere('p_bill_no', 'like', '%' . $search1 . '%');
        }
        $totalData = $totalData->count();
        
        if($store_id == '0')
        {
            $templates = DB::table('tbl_purchase')->where('is_Deleted', '0');
        }
        else
        {
            $templates = DB::table('tbl_purchase')->where('store_id', $store_id)->where('is_Deleted', '0');
        }
        if ($storeid != '')
        {
            $templates->where('store_id', $storeid);
        }
        if ($date_from != '' && $date_to != '') 
        {
           $templates->whereBetween('purchase_date', [$date_from,  $date_to]);
        }
        if ($search1 != '') 
        {
            $templates->where('supplier_name', 'like', '%' . $search1 . '%')
            ->orWhere('p_bill_no', 'like', '%' . $search1 . '%');
        }


        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('purchase_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
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
                
                $encryptedId = base64_encode($template->p_bill_no);
                $purchase_id = base64_encode($template->purchase_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['supplier_name'] = $template->supplier_name;
                $nestedData['p_date']  = date("d-m-Y", strtotime($template->purchase_date));
                $nestedData['bill_no'] = '<span class="badge badge-success">'.$template->p_bill_no.'</span>';
                $nestedData['unit_price']  = $template->total_unit_amount;
                $nestedData['gst_amount']  = $template->total_gst_amount;
                $nestedData['qty']  = $template->total_qty;
                $nestedData['total_purchase']  = $template->total_p_amount;
                $nestedData['roundoff']  = $template->round_off;
                $nestedData['net_purchase']  = $template->net_purchase_amount;
                $nestedData['encryptedId']  = $encryptedId;
                $nestedData['purchase_id']  = $purchase_id;
                $nestedData['entry_from']  = $store_name;
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
    
    public function viewPurchase($id)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'View Purchase';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        $setting['purchase'] = Purchase::where('p_bill_no', $decryptedId)->first();
        $setting['purchaseproduct'] = PurchaseProduct::where('bill_no', $decryptedId)->get();
        return view($this->view_route.'/view-purchase',$setting);
    }
    
    
    public function editPurchase($id)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Edit Purchase';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        $setting['purchase'] = Purchase::where('p_bill_no', $decryptedId)->first();
        $setting['purchaseproduct'] = PurchaseProduct::where('bill_no', $decryptedId)->get();
        return view($this->view_route.'/edit-purchase',$setting);
    }
    
    
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255',
            'p_bill_no'     => 'required|string|max:255',
            'date_from'  => 'required',

        ]);
        
        dd($request);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        

    
        DB::beginTransaction();
    
        try {
            $data = $request->all();
            $user = auth()->user();
    
            /***************** Update Purchase Table *****************/
            
            $summary = DB::table('tbl_purchase')->where('purchase_id', $data['purchase_id']);
            $summary = $summary->first();
            
            $edit_history= DB::table('tbl_purchase_edit_history')->insert([
                    'purchase_id' => $summary->purchase_id,
                    'old_supplier_name' => $summary->supplier_name,
                    'old_purchase_date' => $summary->purchase_date,
                    'old_p_bill_no' => $summary->p_bill_no,
                    'supplier_name'      => $data['supplier_name'],
                    'p_bill_no'      => $data['p_bill_no'],
                    'purchase_date'      => $data['date_from'],
                    'updated_by' => $user->id,

                ]);
            
            $update_purchase=  DB::table('tbl_purchase')->where('purchase_id', $data['purchase_id'])->update([
                'supplier_name'      => $data['supplier_name'],
                'p_bill_no'      => $data['p_bill_no'],
                'purchase_date'      => $data['date_from'],
                'updated_at' => now()
            ]);
                
            $update_purchasedeatils =  DB::table('tbl_purchase_deatils')->where('purchase_id', $data['purchase_id'])->update([
                'bill_no'      => $data['p_bill_no'],
                'updated_at' => now()
            ]); 
            
            
            $tCount = DB::table('tbl_barcode')
                         ->where('purchase_id', $data['purchase_id'])->count();
             
            if($tCount > 0)
            {
                $update_barcode =  DB::table('tbl_barcode')->where('purchase_id', $data['purchase_id'])->update([
                    'p_bill_no'      => $data['p_bill_no'],
                    'updated_at' => now()
                ]);
            }
                

    
            DB::commit();
    
            return response()->json(['success' => 'Purchase and Products saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the purchase update process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    
    public function destroy($id)
    {
        $user_id = auth()->user()->id;
        $decryptedId = base64_decode($id);
        $Is_delted = DB::table('tbl_purchase')->where('purchase_id', $decryptedId)->update(['is_Deleted' => 1]);
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }
        
        $purchaseproduct = PurchaseProduct::where('purchase_id', $decryptedId)->get();
        
        foreach($purchaseproduct as $product)
        {
            // Qty minus from stock
            $inventory = DB::table('tbl_inventory_levels')->where('product_details', $purchaseproduct->product_details)
            ->where('product_code', $purchaseproduct->product_code)
            ->where('product_type', $purchaseproduct->product_type)
            ->where('perbox', $purchaseproduct->perbox_detail)
            ->where('store_id', $user->store_id)->first();
            
            // update perivous qty
            if($purchaseproduct->product_type == 'Lens')
            {
                $query->update([
                    'available_quantity' => $inventory->available_quantity - $purchaseproduct->qty,
                    'tota_lens_qty' => $inventory->tota_lens_qty - ($purchaseproduct->perbox_detail*$purchaseproduct->qty),
                    'updated_at' => now()
                ]);
            }
            else
            {
                $remove_old = DB::table('tbl_inventory_levels')
                ->where('id', $inventory->id)
                ->update([
                    'available_quantity'      => $inventory->available_quantity - $purchaseproduct->qty,
                    'updated_at' => now()
                ]);
            }
            
        }
        
        $barcode = Barcode::where('purchase_id', $decryptedId)->get();

        if (!$barcode) {
            return response()->json([
                'status' => false,
                'error' => 'Barcode not found.'
            ], 404);
        }
        
        // Delete the barcode

        DB::table('tbl_barcode')
            ->where('purchase_id', $decryptedId)
            ->update([
                'barcode_status'      => 2,
                'updated_at' => now()
            ]);
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Purchase Order was successfully deleted',
        ]);
    }
    
    
    public function purchaseeditHistory()
    {
        $setting['page_title'] = 'Purchase Edit History';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/purchase-edit-history',$setting);
    }
    
    
    public function purchaseeditDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $search1 = $request->input('search1');


        $totalData = DB::table('tbl_purchase_edit_history');

        
        if ($date_from != '' && $date_to != '')
        {
            $totalData->whereBetween('old_purchase_date', [$date_from, $date_to]);
        }
        if ($search1 != '') 
        {
            $totalData->where('supplier_name', 'like', '%' . $search1 . '%')
            ->orWhere('p_bill_no', 'like', '%' . $search1 . '%');
        }
        $totalData = $totalData->count();
        

        $templates = DB::table('tbl_purchase_edit_history');

        if ($date_from != '' && $date_to != '') 
        {
           $templates->whereBetween('old_purchase_date', [$date_from,  $date_to]);
        }
        if ($search1 != '') 
        {
            $templates->where('supplier_name', 'like', '%' . $search1 . '%')
            ->orWhere('p_bill_no', 'like', '%' . $search1 . '%');
        }


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
                
                $updated_by = User::find($template->updated_by);

                $summary = DB::table('tbl_purchase')->where('purchase_id', $template->purchase_id);
                $summary = $summary->first();
                $encryptedId = base64_encode($template->purchase_id);
                
                $created_at = User::find($summary->added_by);
                
                $Store = Store::find($summary->store_id);
                
                $nestedData['sr_no']    = $i++;
                $nestedData['supplier_name'] = $template->supplier_name;
                $nestedData['p_date']  = date("d-m-Y", strtotime($template->purchase_date));
                $nestedData['bill_no'] = '<span class="badge badge-success">'.$template->p_bill_no.'</span>';
                $nestedData['c_date']  = date("d-m-Y", strtotime($summary->created_at));
                $nestedData['created_by'] = $created_at->name;
                $nestedData['last_date']  = date("d-m-Y", strtotime($template->created_at));
                $nestedData['updated_by'] = $updated_by->name;
                $nestedData['store_name'] = $Store->store_name;
                $nestedData['encryptedId']= $encryptedId;

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
    
    
    public function getedithistroy(Request $request)
    {
        $purchaseId = $request->input('purchaseid');
        $purchaseId = base64_decode($purchaseId);
        

        if (!$purchaseId || !is_numeric($purchaseId)) {
            return response()->json([], 400);
        }
    
        // Join necessary tables to avoid N+1 queries
        $history = DB::table('tbl_purchase_edit_history as phe')
            ->leftJoin('tbl_purchase as p', 'phe.purchase_id', '=', 'p.purchase_id')
            ->leftJoin('users as u1', 'p.added_by', '=', 'u1.id')          // creator
            ->leftJoin('users as u2', 'phe.updated_by', '=', 'u2.id')      // last editor
            ->leftJoin('tbl_store as s', 'p.store_id', '=', 's.id')
            ->where('phe.purchase_id', $purchaseId)
            ->select(
                's.store_name',
                'p.p_bill_no',
                'phe.purchase_date',
                'p.created_at as created_date',
                'u1.name as created_by',
                'phe.created_at as last_edit_date',
                'phe.supplier_name as supplier_name',
                'u2.name as last_edit_by'
            )
            ->get()
            ->map(function ($item) {
                return [
                    'store_name'      => $item->store_name ?? '-',
                    'supplier_name'   => $item->supplier_name ?? '-',
                    'bill_number'     => $item->p_bill_no ?? '-',
                    'purchase_date'   => $item->purchase_date ? date("d-m-Y", strtotime($item->purchase_date)) : '-',
                    'created_date'    => $item->created_date ? date("d-m-Y", strtotime($item->created_date)) : '-',
                    'created_by'      => $item->created_by ?? '-',
                    'last_edit_date'  => $item->last_edit_date ? date("d-m-Y", strtotime($item->last_edit_date)) : '-',
                    'last_edit_by'    => $item->last_edit_by ?? '-',
                ];
            });
    
        return response()->json($history);
    }

    
    
   
    public function generateBarcode()
    {
        $setting['page_title'] = 'Generate New Barcode';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/generate-new-barcode',$setting);
    }
    
    
    public function newBarcodeDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $search = $request->input('search_input');
        $product_type = $request->input('product_type');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $stid = $request->input('store_id');
        
        $activeStoreId = !empty($stid) ? $stid : $store_id;
        
        $templates = DB::table('tbl_barcode')
            ->where('barcode_status', '2')
            ->where('t_status', '0')
            ->where('outward_status', NULL)
            ->whereNull('lens_box');
        
 
        
        if (!$templates)
        {
            if ($activeStoreId > 0) {
                $templates->where('store_id', $activeStoreId);
            } else {
                // store_id = 0 → do not apply store_id filter
                // optional: include transfer conditions if needed
                $templates->where('transfer_outward_status', NULL);
            }
        }    
        
        $totalData = (clone $templates)->count();
        
        if (!empty($product_type)) {
            $templates->where('product_type', $product_type);
        }
        
        if (!empty($date_from) && !empty($date_to)) {
            $templates->whereBetween('purchase_date', [$date_from, $date_to]);
        }
        
        if (!empty($search)) {
            $search = trim($search);
            $searchValues = array_filter(array_map('trim', explode(',', $search)));
        
            if (count($searchValues) > 1 && count($searchValues) <= 100) {
                $templates->where(function ($query) use ($searchValues) {
                    $query->whereIn('challan_no', $searchValues)
                          ->orWhereIn('product_code', $searchValues)
                          ->orWhereIn('barcode_no', $searchValues);
                });
            } else {
                $templates->where(function ($query) use ($search) {
                    $query->where('p_bill_no', 'like', "%{$search}%")
                          ->orWhere('product_code', 'like', "%{$search}%")
                          ->orWhere('barcode_no', 'like', "%{$search}%");
                });
            }
        }
        
        $totalFiltered = (clone $templates)->count();
        
        $templates = $templates
            ->offset($start)
            ->limit($limit)
            ->orderBy('id', 'DESC')
            ->get();


        $data = [];
        if (! empty($templates)) {
            foreach ($templates as $template) 
            {
                if($template->product_type == 'Lens')
                {
                    $description = '<strong style="color:red"> Box per peice :  '.$template->perbox.'</strong>';
                }
                else
                {
                    $description = '';
                }
                
                if($template->t_status == 0)
                {
                    $store= Store::where('id', $template->store_id)->first();
                }
                else
                {
                    $store= Store::where('id', $template->transfer_store_id)->first();
                }
                $nestedData['store_name'] = $store->store_name;
                $nestedData['responsive_id']    = '';
                $nestedData['barcode_id']       = $template->id;
                $nestedData['barcode'] = $template->barcode_no;
                if($template->inward_status == 0)
                {
                    $tbl_purchase = DB::table('tbl_purchase')->where('purchase_id', $template->purchase_id)->first();
                    $tbl_purchase_deatils = DB::table('tbl_purchase_deatils')->where('id', $template->purchase_product_id)->first();
                    $nestedData['purchase_details'] = 'Purchase Date :'.$template->purchase_date.'<BR>Purchase Bill Number :<span class="badge badge-info">'.$template->p_bill_no.'</span><BR>Supplier :'.$tbl_purchase->supplier_name;
                }
                elseif($template->inward_status == 1)
                {
                    $nestedData['purchase_details'] = 'Inventory Date :'.$template->inv_date.'<BR>Inventory Reference Number :<span class="badge badge-info">'.$template->inv_ref_no.'</span>';
                }
                elseif($template->inward_status == 2)
                {
                    $nestedData['purchase_details'] = 'Inventory Date :'.$template->challan_date.'<BR>Inventory Reference Number :<span class="badge badge-info">'.$template->challan_no.'</span>';
                }
                elseif($template->inward_status == 3)
                {
                    $nestedData['purchase_details'] = 'Inventory Date :'.$template->import_date.'<BR>Inventory Reference Number :<span class="badge badge-info">'.$template->import_ref.'</span>';
                }
                elseif($template->inward_status == 4)
                {
                    $nestedData['purchase_details'] = 'Inventory Date :'.$template->recevied_date.'<BR>Inventory Reference Number :<span class="badge badge-info">'.$template->recevied_ref_no.'</span>';
                }
                
                $nestedData['product_details'] = 'Product  : <span class="badge badge-info">'.$template->product_type.'</span><BR>Product Code : '.$template->product_code.'<BR>Product ID : '.$template->product_id.'<BR>Description  :'.$template->product_details.'<BR>'.$description;
                $nestedData['price']  = 'Purchase Price : Rs '.$template->purchase_price.'<Br>Sales Price : Rs '.$template->retail_price;

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
    
    
    public function purchaseBarcode()
    {
        $setting['page_title'] = 'Barcode';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/purchase-barcode',$setting);
    }
    
    
    public function barcodePending(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $search = $request->input('search_input');
        $product_type = $request->input('product_type');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $stid = $request->input('store_id');
        $search_with = $request->input('search_with');
    
        $activeStoreId = !empty($stid) ? $stid : $store_id;
    
        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */
    
        $baseQuery = DB::table('tbl_barcode')
            ->where('tbl_barcode.barcode_status', '0')
            ->where('tbl_barcode.t_status', '0')
            ->whereNull('tbl_barcode.outward_status')
            ->whereNull('tbl_barcode.lens_box');
    
        /*
        |--------------------------------------------------------------------------
        | Total Records
        |--------------------------------------------------------------------------
        */
    
        $totalData = (clone $baseQuery)->count();
    
        /*
        |--------------------------------------------------------------------------
        | Store Filter
        |--------------------------------------------------------------------------
        */
    
        if ($activeStoreId > 0) {
            $baseQuery->where('tbl_barcode.store_id', $activeStoreId);
        } else {
            $baseQuery->whereNull('tbl_barcode.transfer_outward_status');
        }
    
        /*
        |--------------------------------------------------------------------------
        | Product Type Filter
        |--------------------------------------------------------------------------
        */
    
        if (!empty($product_type)) {
            $baseQuery->where('tbl_barcode.product_type', $product_type);
        }
    

         /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */
        
        /*if (!empty($date_from) && !empty($date_to)) {
        
            $baseQuery->where(function ($query) use ($date_from, $date_to) {
        
                // Purchase Date records
                $query->where(function ($q) use ($date_from, $date_to) {
        
                    $q->whereNotNull('tbl_barcode.purchase_date')
                      ->whereBetween(
                            DB::raw('DATE(tbl_barcode.purchase_date)'),
                            [$date_from, $date_to]
                      );
                })
        
                // Challan Date records when purchase_date NULL
                ->orWhere(function ($q) use ($date_from, $date_to) {
        
                    $q->whereNull('tbl_barcode.purchase_date')
                      ->whereNotNull('tbl_barcode.challan_date')
                      ->whereBetween(
                            DB::raw('DATE(tbl_barcode.challan_date)'),
                            [$date_from, $date_to]
                      );
                });
        
            });
        }*/
    
        /*
        |--------------------------------------------------------------------------
        | Search Filter
        |--------------------------------------------------------------------------
        */
    
        if (!empty($search_with) && !empty($search)) {
    
            $columnMap = [
                'Barcode' => 'tbl_barcode.barcode_no',
                'Description' => 'tbl_barcode.product_details',
                'Product Code' => 'tbl_barcode.product_code',
                'Purchase Bill Number' => 'tbl_barcode.p_bill_no',
                'Challan Number' => 'tbl_barcode.challan_no',
            ];
    
            if (isset($columnMap[$search_with])) {
                $baseQuery->where($columnMap[$search_with], 'like', "%{$search}%");
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | Total Filtered Records
        |--------------------------------------------------------------------------
        */
    
        $totalFiltered = (clone $baseQuery)->count();
    
        /*
        |--------------------------------------------------------------------------
        | Fetch Data
        |--------------------------------------------------------------------------
        */
    
        $templates = $baseQuery
            ->leftJoin('tbl_store as s', function ($join) 
            {
                $join->on('tbl_barcode.store_id', '=', 's.id')
                ->orOn('tbl_barcode.transfer_store_id', '=', 's.id');
            })
            ->select(
                'tbl_barcode.*',
                's.store_name'
            )
            ->offset($start)
            ->limit($limit)
            ->orderBy('tbl_barcode.id', 'DESC')
            ->get();
    
        /*
        |--------------------------------------------------------------------------
        | Prepare Data
        |--------------------------------------------------------------------------
        */
    
        $data = [];
        $i = $start + 1;
    
        foreach ($templates as $template) {
    
            /*
            |--------------------------------------------------------------------------
            | Lens Description
            |--------------------------------------------------------------------------
            */
    
            $description = '';
    
            if ($template->product_type == 'Lens') {
    
                $description =
                    '<strong style="color:red"> Box per piece: '
                    . $template->perbox .
                    '</strong><br>
    
                    <strong>Batch Number: '
                    . $template->batch_no .
                    '</strong><br>
    
                    <strong>Mfg Date: '
                    . $template->mfg_date .
                    '</strong><br>
    
                    <strong>Expiry Date: '
                    . $template->expiry_date .
                    '</strong>';
            }
    
            /*
            |--------------------------------------------------------------------------
            | Purchase / Inventory Details
            |--------------------------------------------------------------------------
            */
    
            $purchaseDetails = '';
    
            /*
            |--------------------------------------------------------------------------
            | Purchase / Inventory Details
            |--------------------------------------------------------------------------
            */
            
            $purchaseDetails = '';

            /*
            |--------------------------------------------------------------------------
            | Challan Details
            |--------------------------------------------------------------------------
            */
            
            if (!empty($template->challan_no)) {
            
                $purchaseDetails .=
                    'Challan Date: '
                    . ($template->challan_date ?? 'NULL') .
                    '<br>
            
                    Challan Number:
                    <span class="badge badge-info">'
                    . ($template->challan_no ?? 'NULL') .
                    '</span><br><br>';
            }
            
            /*
            |--------------------------------------------------------------------------
            | Purchase Details
            |--------------------------------------------------------------------------
            */
            
            if (!empty($template->purchase_id)) {
            
                $tbl_purchase = DB::table('tbl_purchase')
                    ->where('purchase_id', $template->purchase_id)
                    ->first();
            
                $purchaseDetails .=
                    'Purchase Date: '
                    . ($template->purchase_date ?? 'NULL') .
                    '<br>
            
                    Purchase Bill Number:
                    <span class="badge badge-info">'
                    . ($template->p_bill_no ?? 'NULL') .
                    '</span><br>
            
                    Supplier: '
                    . ($tbl_purchase->supplier_name ?? 'NULL');
            
            } else {
            
                // If purchase not available
                $purchaseDetails .=
                    'Purchase Date: NULL
                    <br>
            
                    Purchase Bill Number:
                    <span class="badge badge-danger">
                    NULL
                    </span><br>
            
                    Supplier: NULL';
            }
    
            /*
            |--------------------------------------------------------------------------
            | Row Data
            |--------------------------------------------------------------------------
            */
    
            $nestedData = [
    
                'sr_no' => $i++,
    
                'warehouse' => $template->store_name ?? '',
    
                'responsive_id' => '',
    
                'barcode_id' => $template->id,
    
                'barcode' => $template->barcode_no,
    
                'purchase_details' => $purchaseDetails,
    
                'product_details' =>
                    'Product:
                    <span class="badge badge-info">'
                    . $template->product_type .
                    '</span><br>
    
                    Product Code: '
                    . $template->product_code .
                    '<br>
    
                    Product ID: '
                    . $template->product_id .
                    '<br>
    
                    Description: '
                    . $template->product_details .
                    '<br>'
                    . $description,
    
                'purchase_price' => 'Rs ' . $template->purchase_price,
    
                'retail_price' => 'Rs ' . $template->retail_price,
    
                'pdeatils' => $template->product_details,
            ];
    
            $data[] = $nestedData;
        }
    
        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */
    
        return response()->json([
    
            "draw" => intval($request->input('draw')),
    
            "recordsTotal" => $totalData,
    
            "recordsFiltered" => $totalFiltered,
    
            "data" => $data,
        ]);
    }

    
    
    public function barcodeNewUpdate(Request $request)
    {
        $user = auth()->user();
        $barcode_ids = $request->ids;
        $errorIDs = $successIds = 0;
        $errorIDs = count($barcode_ids);
        $barcodedetailsCount = Barcode::whereIn('id', $barcode_ids)->where('barcode_status', 0)->get();
        if (count($barcodedetailsCount->toArray()) == count($barcode_ids)) 
        {
            foreach ($barcodedetailsCount as $barcodedetails) 
            {
                $tbl_barcode = DB::table('tbl_barcode')->where('id', $barcodedetails->id)->first();
                

                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                    'barcode_no' => $tbl_barcode->barcode_no,
                    'store_id' => $tbl_barcode->store_id,
                    'reference_type' => 'Barcode No Change',
                    'action_perform' => 'Update',
                    'added_by' => $user->id,

                ]);
                
                $barcodesData = ['barcode_no' => $request->newbarcode_no];
                Barcode::where('id', $barcodedetails->id)->update($barcodesData);
                $successIds++;
                $errorIDs--;
            }
            return response()->json([
                'status'  => true,
                'code'  => '200',
                'message' => $successIds . ' Barcode No Change',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'code'  => '201',
                'message' => count($barcode_ids) - count($barcodedetailsCount->toArray()) . ' Barcode status is different. please check selected barcode',
            ]);
        }
    }


    public function bulkBarcodeGenerate(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $barcode_setting = DB::table("tbl_barcode_setting")
            ->where('by_default_set', 1)
            ->first();
    
        if (!$barcode_setting) {
            return response()->json([
                'status' => false,
                'message' => 'Barcode setting not found for this store.'
            ], 404);
        }
    

        $barcodes = Barcode::whereIn('id', $request->ids)->get();
    
        if ($barcodes->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No barcode data found for the provided IDs.'
            ], 404);
        }
    
        // Fix UTF-8 encoding issues
        $barcode_array = $barcodes->toArray();
        foreach ($barcode_array as &$barcode) {
            foreach ($barcode as $key => $value) {
                if (is_string($value)) {
                    $barcode[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            }
        }
        
        if ($barcode_setting->paper_format == 'advance') 
        {
            $width_pt = $barcode_setting->paper_width * 2.834645669;
            $height_pt = $barcode_setting->paper_height * 2.834645669;

            $pdf = Pdf::loadView($this->view_route . '/barcode-pdf', ['barcodes' => $barcode_array,'barcode_setting' =>$barcode_setting])
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ])->setPaper([0, 0, $width_pt, $height_pt]);
    
        }
    
        
        return $pdf->stream(time() . '.pdf');
    }
    
    
    
    public function PurchaseFullbarcode($id)
    {
        $store_id = auth()->user()->store_id;
        $purchase_id = base64_decode($id);
        $barcode_setting = DB::table("tbl_barcode_setting")
            ->where('by_default_set', 1)
            ->first();
    
        if (!$barcode_setting) {
            return response()->json([
                'status' => false,
                'message' => 'Barcode setting not found for this store.'
            ], 404);
        }
    

        $barcodes = Barcode::where('purchase_id', $purchase_id)->get();
    
        if ($barcodes->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No barcode data found for the provided IDs.'
            ], 404);
        }
    
        // Fix UTF-8 encoding issues
        $barcode_array = $barcodes->toArray();
        foreach ($barcode_array as &$barcode) {
            foreach ($barcode as $key => $value) {
                if (is_string($value)) {
                    $barcode[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            }
        }
        
        if ($barcode_setting->paper_format == 'advance') 
        {
            $pdf = Pdf::loadView($this->view_route . '/barcode-pdf', ['barcodes' => $barcode_array,'barcode_setting' =>$barcode_setting])
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ])->setPaper([0, 0, 283.46, 42.52]);
    
        }
    
        
        return $pdf->stream(time() . '.pdf');
    }
    

    public function bulkConfirmBarcode(Request $request)
    {
        $user = auth()->user();
        $barcode_ids = $request->ids;
        $errorIDs = $successIds = 0;
        $errorIDs = count($barcode_ids);
        $barcodedetailsCount = Barcode::whereIn('id', $barcode_ids)->where('barcode_status', 0)->get();
        if (count($barcodedetailsCount->toArray()) == count($barcode_ids)) 
        {
            foreach ($barcodedetailsCount as $barcodedetails) 
            {
                $tbl_barcode = DB::table('tbl_barcode')->where('id', $barcodedetails->id)->first();
                
                if($tbl_barcode->product_type == 'Lens')
                {
                    $update_inventory =  DB::table('tbl_barcode')->where('lens_box', $tbl_barcode->barcode_no)->update([
                        'barcode_status'      => 1,
                    ]);
                }
                
                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                    'barcode_no' => $tbl_barcode->barcode_no,
                    'store_id' => $tbl_barcode->store_id,
                    'reference_type' => 'Barcode',
                    'action_perform' => 'Confirm',
                    'added_by' => $user->id,

                ]);
                
                $barcodesData = ['barcode_status' => 1];
                Barcode::where('id', $barcodedetails->id)->update($barcodesData);
                $successIds++;
                $errorIDs--;
            }
            return response()->json([
                'status'  => true,
                'code'  => '200',
                'message' => $successIds . ' Barcode Confirm',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'code'  => '201',
                'message' => count($barcode_ids) - count($barcodedetailsCount->toArray()) . ' Barcode status is different. please check selected barcode',
            ]);
        }
    }
    
    
    public function singleConfirmBarcode($id)
    {
        $user = auth()->user();
        $tbl_barcode = DB::table('tbl_barcode')->where('id', $id)->first();
                
        if($tbl_barcode->product_type == 'Lens')
        {
            $update_inventory =  DB::table('tbl_barcode')->where('lens_box', $tbl_barcode->barcode_no)->update([
                'barcode_status'      => 1,
            ]);
        }
        
        $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
            'barcode_no' => $tbl_barcode->barcode_no,
            'store_id' => $tbl_barcode->store_id,
            'reference_type' => 'Barcode',
            'action_perform' => 'Confirm',
            'added_by' => $user->id,

        ]);
        
        $Is_confirm = DB::table('tbl_barcode')->where('id', $id)->update(['barcode_status' => 1]);
        if (!$Is_confirm) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Barcode confirm  successfully',
        ]);
    }
    
    
    public function singleBarcodeUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode_no'        => 'required|string|max:255',
            'retail_price'        => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }
        
        

        Barcode::where('id', $request->uid)->update($request->except(['_token','_method','uid']));
        
        return response()->json([
            'status'  => 'true',
            'code'  => '200',
            'message' => 'Barcode Update successfully!',
        ]);
    }
    
    
    public function confirmBarcode()
    {
        $setting['page_title'] = 'Barcode';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/confirm-barcode',$setting);
    }
    
    
    public function barcodeConfirmDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $search = $request->input('search_input');
        $product_type = $request->input('product_type');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $stid = $request->input('store_id');
        
        // Determine which store_id to use
        $activeStoreId = !empty($stid) ? $stid : $store_id;
        
        $templates = DB::table('tbl_barcode')
            ->where('barcode_status', '1')
            ->where('t_status', '0')
            ->where('outward_status', NULL)
            ->whereNull('lens_box');
        
 
        
        // Apply store_id filter conditionally
        if (!$templates)
        {
            if ($activeStoreId > 0) {
                $templates->where('store_id', $activeStoreId);
            } else {
                // store_id = 0 → do not apply store_id filter
                // optional: include transfer conditions if needed
                $templates->where('transfer_outward_status', NULL);
            }
            
            
        }    
        
        // Total count before filtering
        $totalData = (clone $templates)->count();
        
        // Apply filters
        if (!empty($product_type)) {
            $templates->where('product_type', $product_type);
        }
        
        if (!empty($date_from) && !empty($date_to)) {
            $templates->whereBetween('purchase_date', [$date_from, $date_to]);
        }
        
        if (!empty($search)) {
            $search = trim($search);
            $searchValues = array_filter(array_map('trim', explode(',', $search)));
        
            if (count($searchValues) > 1 && count($searchValues) <= 100) {
                $templates->where(function ($query) use ($searchValues) {
                    $query->whereIn('p_bill_no', $searchValues)
                          ->orWhereIn('product_code', $searchValues)
                          ->orWhereIn('barcode_no', $searchValues);
                });
            } else {
                $templates->where(function ($query) use ($search) {
                    $query->where('p_bill_no', 'like', "%{$search}%")
                          ->orWhere('product_code', 'like', "%{$search}%")
                          ->orWhere('barcode_no', 'like', "%{$search}%");
                });
            }
        }
        
        // Count filtered results
        $totalFiltered = (clone $templates)->count();
        
        // Pagination
        $templates = $templates
            ->offset($start)
            ->limit($limit)
            ->orderBy('id', 'DESC')
            ->get();

        $data = [];
        if (! empty($templates)) {
            foreach ($templates as $template) 
            {
                if($template->inward_status == '0')
                {
                    $istatus = '<span class="badge badge-dark">Purchase</span>';
                    
                }elseif($template->inward_status == '1')
                {
                    $istatus = '<span class="badge badge-dark">Manually </span>';
                    
                }
                elseif($template->inward_status == '2')
                {
                    $istatus = '<span class="badge badge-dark">Challan</span>';
                    
                }
                elseif($template->inward_status == '3')
                {
                    $istatus = '<span class="badge badge-dark">Imported </span>';
                    
                }
                elseif($template->inward_status == '4')
                {
                    $istatus = '<span class="badge badge-dark">Recevied Store </span>';
                    
                }

                
                
                if($template->outward_status == '0')
                {
                    $ostatus = '<span class="badge badge-info">Sold</span>';
                    
                }elseif($template->outward_status == '1')
                {
                    $ostatus = '<span class="badge badge-info">Adjust</span>';
                    
                }
                elseif($template->outward_status == '2')
                {
                    $ostatus = '<span class="badge badge-info">Return</span>';
                    
                }
                elseif($template->outward_status == '3')
                {
                    $ostatus = '<span class="badge badge-info">Deleted </span>';
                    
                }
                elseif($template->outward_status == '4')
                {
                    $ostatus = '<span class="badge badge-info">Transfer Store </span>';
                    
                }
                else
                {
                    $ostatus = '<span class="badge badge-info">Avilable </span>';
                    
                }
                

                if($template->product_type == 'Lens')
                {
                    
                    
                    $description = '<strong style="color:red"> Box per peice :  '.$template->perbox.'</strong><br>
                     <strong> Batch Number :  '.$template->batch_no.'</strong><br>
                     <strong> Mfg Date  :  '.$template->mfg_date.'</strong><br>
                     <strong> Expiry  Date  :  '.$template->expiry_date.'</strong>
                    ';
                }
                else
                {
                    $description = '';
                }
                
                if($template->t_status == 0)
                {
                    $store= Store::where('id', $template->store_id)->first();
                }
                else
                {
                    $store= Store::where('id', $template->transfer_store_id)->first();
                }
                
                
                $nestedData['store_name'] = $store->store_name;
                $nestedData['responsive_id']    = '';
                $nestedData['barcode_id']       = $template->id;
                $nestedData['barcode'] = $template->barcode_no;
                /*
                |--------------------------------------------------------------------------
                | Purchase / Inventory Details
                |--------------------------------------------------------------------------
                */
                
                $purchaseDetails = '';
    
                /*
                |--------------------------------------------------------------------------
                | Challan Details
                |--------------------------------------------------------------------------
                */
                
                if (!empty($template->challan_no)) {
                
                    $purchaseDetails .=
                        'Challan Date: '
                        . ($template->challan_date ?? 'NULL') .
                        '<br>
                
                        Challan Number:
                        <span class="badge badge-info">'
                        . ($template->challan_no ?? 'NULL') .
                        '</span><br><br>';
                }
                
                /*
                |--------------------------------------------------------------------------
                | Purchase Details
                |--------------------------------------------------------------------------
                */
                
                if (!empty($template->purchase_id)) {
                
                    $tbl_purchase = DB::table('tbl_purchase')
                        ->where('purchase_id', $template->purchase_id)
                        ->first();
                
                    $purchaseDetails .=
                        'Purchase Date: '
                        . ($template->purchase_date ?? 'NULL') .
                        '<br>
                
                        Purchase Bill Number:
                        <span class="badge badge-info">'
                        . ($template->p_bill_no ?? 'NULL') .
                        '</span><br>
                
                        Supplier: '
                        . ($tbl_purchase->supplier_name ?? 'NULL');
                
                } else {
                
                    // If purchase not available
                    $purchaseDetails .=
                        'Purchase Date: NULL
                        <br>
                
                        Purchase Bill Number:
                        <span class="badge badge-danger">
                        NULL
                        </span><br>
                
                        Supplier: NULL';
                }
                
                    $nestedData['purchase_details'] = $purchaseDetails;
               
               
                $nestedData['product_details'] = 'Product  : <span class="badge badge-info">'.$template->product_type.'</span><BR>Product Code : '.$template->product_code.'<BR>Product ID : '.$template->product_id.'<BR>Description  :'.$template->product_details.'<BR>'.$description;
                $nestedData['purchase_price'] = 'Rs '.$template->purchase_price;
                $nestedData['retail_price'] = 'Rs '.$template->retail_price;
                $nestedData['pdeatils'] = $template->product_details;
                $nestedData['inward_status'] = $istatus;
                $nestedData['outward_status'] = $ostatus;
                $data[] = $nestedData;
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
    
    
    public function addChallan()
    {
        $setting['page_title'] = 'Add Challan';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/add-challan',$setting);
    }
    
    
    public function Storechallan(Request $request)
    {
        $user = auth()->user();
        $store_id = auth()->user()->store_id;
        // Validation
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255',
            'challan_no'    => 'required|string|max:255',
            'date_from'     => 'required',
            'tax_rule'      => 'required',
            'recevied_store_id'     => 'required',
            'billing_store_id'     => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        // At least one valid product
        $hasValidProduct = false;
        foreach ($request->input('product_type', []) as $i => $type) {
            if (!empty($type) && !empty($request->input("product_code.$i"))) {
                $hasValidProduct = true;
                break;
            }
        }
    
        if (!$hasValidProduct) {
            return response()->json([
                'status' => false,
                'errors' => 'Please add at least one valid product.'
            ], 422);
        }
    

    
        DB::beginTransaction();
        try {
            $data = $request->all();
    
            // Create Purchase record
            $challan = Challan::create([
                'supplier_name'       => $data['supplier_name'],
                'challan_no'           => $data['challan_no'],
                'challan_date'       => $data['date_from'],
                'tax_rule'            => $data['tax_rule'] ?? null,
                'total_qty'           => $data['total_qty'] ?? 0,
                'total_base_amount'   => $data['total_base_amount'] ?? 0,
                'total_gst_amount'    => $data['total_gst_amount'] ?? 0,
                'total_p_amount'      => $data['total_p_amount'] ?? 0,
                'added_by'            => $user->id,
                'recevied_store_id'   => $data['recevied_store_id'],
                'billing_store_id'    => $data['billing_store_id'],
            ]);
    
            // Loop through each product
            foreach ($data['product_type'] as $i => $type) 
            {
                $code = $data['product_code'][$i] ?? null;
                if (empty($type) || empty($code)) continue;
                
                $tCount = DB::table('tbl_product_code')
                         ->where('product_type', $type)
                         ->where('product_code', $code)
                         ->where('productdetails', $data['product_details'][$i])->count();
                         
                
                $PCount = DB::table('tbl_product_code')->where('product_code', $code)->count();
                    
                
                $tbl_product_code = DB::table('tbl_product_code')->where('product_code', $code)->first();
                $product_id = $tbl_product_code->product_id;
                
                $challanProduct = ChallanProduct::create([
                    'challan_id'          => $challan->id,
                    'challan_no'              => $data['challan_no'],
                    'product_type'         => $type,
                    'product_code'         => $code,
                    'product_id'           => $product_id,
                    'product_name'         => $data['product_name'][$i] ?? '',
                    'product_details'      => $data['product_description'][$i] ?? '',
                    'quality_detail'       => $data['product_quality'][$i] ?? '',
                    'company_detail'       => $data['product_company'][$i] ?? '',
                    'product_base_price'   => (float)($data['product_base_price'][$i] ?? 0),
                    'product_purchase_price'=> (float)($data['product_purchase_price'][$i] ?? 0),
                    'hsn_code'             => $data['hsn_code'][$i] ?? '',
                    'gst_amt'              => (float)($data['gst_amt'][$i] ?? 0),
                    'gst'                  => (float)($data['gst'][$i] ?? 0),
                    'qty'                  => (int)($data['product_qty'][$i] ?? 0),
                    'total_purchase_price' => (float)($data['total_purchase_price'][$i] ?? 0),
                    'product_retail_price' => (float)($data['product_retail_price'][$i] ?? 0),
                    'bb_Price'             => (float)($data['product_bb_price'][$i] ?? 0),
                    'color_details'        => $data['product_color'][$i] ?? '',
                    'material_detail'      => $data['product_material'][$i] ?? '',
                    'size_details'         => $data['product_size'][$i] ?? '',
                    'Type_details'         => $data['product_lenstype'][$i] ?? '',
                    'shape_details'        => $data['product_shape'][$i] ?? '',
                    'coating_detail'       => $data['product_coating'][$i] ?? '',
                    'design_details'       => $data['product_design'][$i] ?? '',
                    'index_detail'         => $data['product_index'][$i] ?? '',
                    'Number_detail'        => $data['product_number'][$i] ?? '',
                    'ct_detail'            => $data['product_tc'][$i] ?? '',
                    'validity_detail'      => $data['product_validity'][$i] ?? '',
                    'sph_detail'           => $data['product_sph'][$i] ?? '',
                    'cyl_details'          => $data['product_cyl'][$i] ?? '',
                    'axis_detail'          => $data['product_axis'][$i] ?? '',
                    'addiional_detail'     => $data['product_addition'][$i] ?? '',
                    'bc_detail'            => $data['product_bc'][$i] ?? '',
                    'diameter_detail'      => $data['product_diameter'][$i] ?? '',
                    'powertype_details'    => $data['product_powertype'][$i] ?? '',
                    'batchno_details'      => $data['product_batch'][$i] ?? '',
                    'modality_details'     => $data['modality_details'][$i] ?? '',
                    'box_detail'           => $data['product_noofbox'][$i] ?? '',
                    'perbox_detail'        => $data['product_perbox'][$i] ?? '',
                    'mfg_detail'           => $data['product_mfg'][$i] ?? '',
                    'expiry_detail'        => $data['product_expiry'][$i] ?? '',
                    'variant_detail'       => $data['product_variant'][$i] ?? '',
                    'description_details'  => $data['product_invoicedescription'][$i] ?? '',
                    'recevied_store_id'    => $data['recevied_store_id'],
                    'billing_store_id'     => $data['billing_store_id'],
                    'barcode_option'       => $data['barcode_option'][$i] ?? '',
                    'added_by'             => $user->id,
                ]);
                
                $this->updateChallanInventory($type, $code, $data['product_description'][$i], $request, $i, $data['recevied_store_id'],$product_id);
                
                 $add_record = DB::table('tbl_inventory_record')->insert([
                    'product_code' => $code,
                    'product_id' => $product_id,
                    'perbox' => $data['product_perbox'][$i],
                    'product_type' => $type, 
                    'product_details' => $data['product_description'][$i],
                    'store_id' =>  $data['recevied_store_id'],
                    'qty' => (int)($data['product_qty'][$i] ?? 0),
                    'added_date' => $data['date_from'],
                    'inward_status' => 2,
                    'added_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                
                if($data['barcode_option'][$i] == '1')
                {
                    $this->generateChallanBarcodes($type, $challan, $challanProduct, $data['product_description'][$i], $request, $i, $data['recevied_store_id'],$product_id);
                }
            }
    
            DB::commit();
            return response()->json(['success' => 'Challan saved successfully!']);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the challan save process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    private function updateChallanInventory($type, $code, $product_details, $request, $i, $store_id, $pid)
    {
        $qty = (int) ($request->input("product_qty.$i", 0));
        $perbox = (int) ($request->input("product_perbox.$i", 1));
        $box_detail = (int) ($request->input("product_noofbox.$i", $qty));
    
        $query = DB::table('tbl_inventory_levels')
            ->where('product_code', $code)
            ->where('product_details', $product_details)
            ->where('product_id', $pid)
            ->where('store_id', $store_id);
    
        $inventory = $query->first();
    
        if ($type === 'Lens') {
    
            if ($inventory) {
    
                $query->update([
                    'available_quantity' => $inventory->available_quantity - $box_detail,
                    'tota_lens_qty' => $inventory->tota_lens_qty + ($perbox * $box_detail),
                    'updated_at' => now()
                ]);
    
            } else {
    
                DB::table('tbl_inventory_levels')->insert([
                    'product_code' => $code,
                    'product_id' => $pid,
                    'product_type' => $type,
                    'product_details' => $product_details,
                    'perbox' => $perbox,
                    'store_id' => $store_id,
                    'available_quantity' => $box_detail,
                    'tota_lens_qty' => -($perbox * $box_detail),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
    
        } else {
    
            if ($inventory) {
    
                $query->update([
                    'available_quantity' => $inventory->available_quantity + $qty,
                    'updated_at' => now()
                ]);
    
            } else {
    
                DB::table('tbl_inventory_levels')->insert([
                    'product_code' => $code,
                    'product_id' => $pid,
                    'product_type' => $type,
                    'product_details' => $product_details,
                    'store_id' => $store_id,
                    'available_quantity' => $qty,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
    private function generateChallanBarcodes($type, $challan, $challanProduct,$product_details, $request, $i, $store_id, $pid)
    {
       
        $user = auth()->user();
        $qty = (int)($request->input("product_qty.$i", 0));
        $box_detail = (int)($request->input("product_noofbox.$i", $qty));
        $perbox = (int)($request->input("product_perbox.$i", 1));
        $purchase_price = (float)($request->input("product_purchase_price.$i", 0));
        $retail_price = (float)($request->input("product_retail_price.$i", 0));
        $code = $request->input("product_code.$i");


        if ($type === 'Lens') 
        {
            for ($b = 0; $b < $box_detail; $b++) 
            {
                $box_barcode = $this->generateUniqueRandomId(6);
    
                DB::table('tbl_barcode')->insert([
                    'purchase_date' => '',
                    'challan_date' => $request->input('challan_date') ?? now(),
                    'challan_no' => $request->input('challan_no'),
                    'product_code' => $code,
                    'product_id' => $pid,
                    'perbox' => $perbox,
                    'mfg_date' => $challanProduct->mfg_detail,
                    'expiry_date' => $challanProduct->expiry_detail,
                    'batch_no' => $challanProduct->batchno_details,
                    'product_type' => $type,
                    'barcode_no' => $box_barcode,
                    'product_details' => $product_details,
                    'purchase_price' => $purchase_price,
                    'retail_price' => $retail_price,
                    'store_id' => $store_id,
                    'inward_status' => 2,
                    'barcode_status' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
                $piece_price = round($purchase_price / max($perbox, 1), 2);
                $piece_retail = round($retail_price / max($perbox, 1), 2);
    
                for ($p = 0; $p < $perbox; $p++) 
                {
                    
                    DB::table('tbl_barcode')->insert([
                        'purchase_date' => '',
                        'challan_date' => $request->input('challan_date') ?? now(),
                        'challan_no' => $request->input('challan_no'),
                        'product_code' => $code,
                        'product_id' => $pid,
                        'product_type' => $type,
                        'barcode_no' => $this->generateUniqueRandomId1(7),
                        'lens_box' => $box_barcode,
                        'mfg_date' => $challanProduct->mfg_detail,
                        'expiry_date' => $challanProduct->expiry_detail,
                        'batch_no' => $challanProduct->batchno_details,
                        'product_details' => $product_details,
                        'purchase_price' => $piece_price,
                        'retail_price' => $piece_retail,
                        'store_id' => $store_id,
                        'inward_status' => 2,
                        'barcode_status' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                        'barcode_no' => $box_barcode,
                        'store_id' => $store_id,
                        'reference_type' => 'Challan',
                        'action_perform' => 'Add',
                        'added_by' => $user->id,
                ]);
            }
            
            
        } 
        else {
            for ($q = 0; $q < $qty; $q++) 
            {
                $barcode = $this->generateUniqueRandomId(6);
                
                DB::table('tbl_barcode')->insert([

                    'purchase_date' => '',
                    'challan_date' => $request->input('challan_date') ?? now(),
                    'challan_no' => $request->input('challan_no'),
                    'product_code' => $code,
                    'product_id' => $pid,
                    'product_type' => $type,
                    'barcode_no' => $barcode,
                    'product_details' => $product_details,
                    'purchase_price' => $purchase_price,
                    'is_pair' => $challanProduct->is_pair,
                    'retail_price' => $retail_price,
                    'store_id' => $store_id,
                    'inward_status' => 2,
                    'barcode_status' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                    'barcode_no' => $barcode,
                    'store_id' => $store_id,
                    'reference_type' => 'Challan',
                    'action_perform' => 'Add',
                    'added_by' => $user->id,

                ]);
            }
            
            
        }
        
    }
    
    
    public function pendingChallan()
    {
        $setting['page_title'] = 'Pending Challan';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/pending-challan',$setting);
    }
    

    public function pendingchallanDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $search = $request->input('search_input');
        $product_type = $request->input('product_type');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $stid = $request->input('store_id');
        
        // Determine which store_id to use

        $templates = DB::table('tbl_challan')->where('challan_status', '0');
        if($store_id > 0)
        {
            $templates->where('recevied_store_id', $store_id);
        }
        
        
        if(!empty($stid))
        {
            $templates->where('recevied_store_id', $stid);
        }
            
 
        
        // Total count before filtering
        $totalData = (clone $templates)->count();
        
        // Apply filters
        /*if (!empty($product_type)) {
            $templates->where('product_type', $product_type);
        }*/
        
        if (!empty($date_from) && !empty($date_to)) {
            $templates->whereBetween('challan_date', [$date_from, $date_to]);
        }
        
        if (!empty($search)) {
            $search = trim($search);
            $searchValues = array_filter(array_map('trim', explode(',', $search)));
        
            if (count($searchValues) > 1 && count($searchValues) <= 100) {
                $templates->where(function ($query) use ($searchValues) {
                    $query->whereIn('supplier_name', $searchValues);
                });
            } else {
                $templates->where(function ($query) use ($search) {
                    $query->where('supplier_name', 'like', "%{$search}%")
                          ->orWhere('challan_no', 'like', "%{$search}%");
                });
            }
        }
        
        // Count filtered results
        $totalFiltered = (clone $templates)->count();
        
        // Pagination
        $templates = $templates
            ->offset($start)
            ->limit($limit)
            ->orderBy('id', 'DESC')
            ->get();

        $data = [];
        if (! empty($templates)) {
            foreach ($templates as $template) 
            {
               
                
                $store= Store::where('id', $template->recevied_store_id)->first();
                $nestedData['store_name'] = $store->store_name;
                $nestedData['responsive_id']    = '';
                $nestedData['challan_id']       = $template->id;
                $nestedData['challan_date'] = 'Challan Date : '.$template->challan_date.'<Br> Added Date : '.$template->created_at;
                $nestedData['supplier_name']       = $template->supplier_name;
                $nestedData['challan_no']       = '<span class="badge badge-info">'.$template->challan_no.'</span>';
               
                $nestedData['unit_price'] = $template->total_base_amount;
                $nestedData['discount'] = $template->total_discount;
                $nestedData['base_amount'] = $template->total_base_amount;
                $nestedData['gst_amount'] = $template->total_gst_amount;
                $nestedData['qty'] = $template->total_qty;
                $nestedData['total_purchase'] = $template->total_p_amount;
                $nestedData['challan_type'] = $template->challan_type;
                $nestedData['recevied_store_id'] = $template->recevied_store_id;
                $data[] = $nestedData;
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
    
    
    public function challanDestroy(Request $request)
    {
        $user = auth()->user();
        $challan_ids = $request->ids;
        
        Challan::whereIn('id', $challan_ids)->delete();
        ChallanProduct::whereIn('challan_id', $challan_ids)->delete();
        
        return response()->json([
            'status'  => true,
            'code'  => '200',
            'message' => 'Challan Deleted',
        ]);
    }
    
    
    public function checkSameStore(Request $request)
    {
        $challan_ids = $request->ids;
    
        $storeIds = Challan::whereIn('id', $challan_ids)
                    ->pluck('recevied_store_id')
                    ->pluck('supplier_name')
                    ->unique();
    
        if ($storeIds->count() > 1) {
            return response()->json([
                'status'  => false,
                'code'  => 200,
                'challan_ids'  => $challan_ids,
                'message' => 'We found different stores or supplier in selected items. Please make sure all selected items belong to the same store or supplier.',
            ]);
        }
    
        return response()->json([
            'status'  => true,
            'code'  => 200,
            'challan_ids'  => $challan_ids,
            'store_id' => $storeIds->first(),
            'message' => 'Same store',
        ]);
    }
    
    
    public function updatePurchaseOfChallan(Request $request)
    {
        $challanIds = json_decode($request->challan_ids);
        

        $challanProduct = ChallanProduct::with('challan')
                        ->whereIn('challan_id', $challanIds)
                        ->get();
                        

        $setting['page_title'] = 'Update Purchase Details of Challan';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        
        $setting['supplier_name'] = $challanProduct->first()->challan->supplier_name;
        $setting['taxRule'] = $challanProduct->first()->challan->tax_rule;
        $setting['stid'] = $challanProduct->first()->challan->recevied_store_id;
        
        $setting['challanproduct'] = $challanProduct;
        return view($this->view_route.'/update-purchase-of-challan',$setting);
    
    }
    
    public function addChallanToPurchaseRecord(Request $request)
    {
        $user = auth()->user();
    
        // ==========================
        // VALIDATION
        // ==========================
    
        $validator = Validator::make($request->all(), [
    
            'supplier_name' => 'required|string|max:255',
    
            'p_bill_no' => 'required|string|max:255',
    
            'date_from' => 'required',
    
            'tax_rule' => 'required',
    
            'store_id' => 'required',
    
        ]);
    
        if ($validator->fails())
        {
            return response()->json([
    
                'status' => false,
    
                'errors' => $validator->errors()
    
            ], 422);
        }
    
        DB::beginTransaction();
    
        try
        {
            $data = $request->all();
    
            // ==========================
            // CREATE PURCHASE
            // ==========================
    
            $purchase = Purchase::create([
    
                'supplier_name'       => $data['supplier_name'],
    
                'p_bill_no'           => $data['p_bill_no'],
    
                'purchase_date'       => $data['date_from'],
    
                'tax_rule'            => $data['tax_rule'] ?? null,
    
                'total_qty'           => $data['total_qty'] ?? 0,
    
                'total_unit_amount'   => $data['total_unit_amount'] ?? 0,
    
                'total_base_amount'   => $data['total_base_amount'] ?? 0,
    
                'total_gst_amount'    => $data['total_gst_amount'] ?? 0,
    
                'total_p_amount'      => $data['total_p_amount'] ?? 0,
    
                'round_off'           => $data['round_off'] ?? 0,
    
                'net_purchase_amount' => $data['net_purchase_amount'] ?? 0,
    
                'added_by'            => $user->id,
    
                'store_id'            => $data['store_id'],
    
            ]);
    
            // ==========================
            // PRODUCT LOOP
            // ==========================
    
            foreach ($data['product_type'] as $i => $type)
            {
                $code = $data['product_code'][$i] ?? null;
    
                if (empty($type) || empty($code))
                {
                    continue;
                }
    
                // ==========================
                // GET CHALLAN PRODUCT
                // ==========================
    
                $ChallanProduct = ChallanProduct::where(
                    'id',
                    $data['challanproductid'][$i]
                )->first();
    
                if (!$ChallanProduct)
                {
                    continue;
                }
    
                // ==========================
                // CREATE PURCHASE PRODUCT
                // ==========================
    
                $purchaseProduct = PurchaseProduct::create([
    
                    'purchase_id' => $purchase->id,
    
                    'bill_no' => $data['p_bill_no'],
    
                    'product_type' => $type,
    
                    'product_code' => $code,
    
                    'product_id' => $ChallanProduct->product_id,
    
                    'product_name' => $ChallanProduct->product_name ?? '',
    
                    'product_details' => $ChallanProduct->product_details ?? '',
    
                    'quality_detail' => $ChallanProduct->quality_detail ?? '',
    
                    'company_detail' => $ChallanProduct->company_detail ?? '',
    
                    'product_price' =>
                        (float)($data['product_price'][$i] ?? 0),
    
                    'product_base_price' =>
                        (float)($data['product_base_price'][$i] ?? 0),
    
                    'product_purchase_price' =>
                        (float)($data['product_purchase_price'][$i] ?? 0),
    
                    'hsn_code' =>
                        $data['hsn_code'][$i] ?? '',
    
                    'gst_amt' =>
                        (float)($data['gst_amt'][$i] ?? 0),
    
                    'gst' =>
                        (float)($data['gst'][$i] ?? 0),
    
                    'qty' =>
                        (int)($data['product_qty'][$i] ?? 0),
    
                    'total_purchase_price' =>
                        (float)($data['total_purchase_price'][$i] ?? 0),
    
                    'product_retail_price' =>
                        (float)($data['product_retail_price'][$i] ?? 0),
    
                    'color_details' =>
                        $ChallanProduct->color_details ?? '',
    
                    'material_detail' =>
                        $ChallanProduct->material_detail ?? '',
    
                    'size_details' =>
                        $ChallanProduct->size_details ?? '',
    
                    'Type_details' =>
                        $ChallanProduct->Type_details ?? '',
    
                    'shape_details' =>
                        $ChallanProduct->shape_details ?? '',
    
                    'coating_detail' =>
                        $ChallanProduct->coating_detail ?? '',
    
                    'design_details' =>
                        $ChallanProduct->design_details ?? '',
    
                    'index_detail' =>
                        $ChallanProduct->index_detail ?? '',
    
                    'Number_detail' =>
                        $ChallanProduct->Number_detail ?? '',
    
                    'ct_detail' =>
                        $ChallanProduct->ct_detail ?? '',
    
                    'validity_detail' =>
                        $ChallanProduct->validity_detail ?? '',
    
                    'sph_detail' =>
                        $ChallanProduct->sph_detail ?? '',
    
                    'cyl_details' =>
                        $ChallanProduct->cyl_details ?? '',
    
                    'axis_detail' =>
                        $ChallanProduct->axis_detail ?? '',
    
                    'addiional_detail' =>
                        $ChallanProduct->addiional_detail ?? '',
    
                    'bc_detail' =>
                        $ChallanProduct->bc_detail ?? '',
    
                    'diameter_detail' =>
                        $ChallanProduct->diameter_detail ?? '',
    
                    'powertype_details' =>
                        $ChallanProduct->powertype_details ?? '',
    
                    'batchno_details' =>
                        $ChallanProduct->batchno_details ?? '',
    
                    'modality_details' =>
                        $ChallanProduct->modality_details ?? '',
    
                    'box_detail' =>
                        $ChallanProduct->box_detail ?? '',
    
                    'perbox_detail' =>
                        $ChallanProduct->perbox_detail ?? '',
    
                    'mfg_detail' =>
                        $ChallanProduct->mfg_detail ?? '',
    
                    'expiry_detail' =>
                        $ChallanProduct->expiry_detail ?? '',
    
                    'variant_detail' =>
                        $ChallanProduct->variant_detail ?? '',
    
                    'description_details' =>
                        $ChallanProduct->description_details ?? '',
    
                    'store_id' =>
                        $data['store_id'],
    
                    'added_by' =>
                        $user->id,
    
                    'is_pair' => '',
                ]);
    
                // ==========================
                // UPDATE CHALLAN STATUS
                // ==========================
    
                DB::table('tbl_challan')
                    ->where('id', $ChallanProduct->challan_id)
                    ->update([
    
                        'challan_status' => 1,
    
                        'updated_at' => now()
                    ]);
    
                // ==========================
                // UPDATE BARCODE
                // ==========================
    
                $challanbarcode = DB::table('tbl_barcode')
                    ->where('challan_no', $ChallanProduct->challan_no)
                    ->where('product_type', $ChallanProduct->product_type)
                    ->where('product_code', $ChallanProduct->product_code)
                    ->where('product_details', $ChallanProduct->product_details)
                    ->first();
    
                if ($challanbarcode)
                {
                    DB::table('tbl_barcode')
    
                        ->where('challan_no', $ChallanProduct->challan_no)
    
                        ->where('product_type', $ChallanProduct->product_type)
    
                        ->where('product_code', $ChallanProduct->product_code)
    
                        ->where('product_details', $ChallanProduct->product_details)
    
                        ->update([
    

                            'purchase_id' => $purchase->id,
    
                            'purchase_product_id' => $purchaseProduct->id,
    
                            'purchase_date' => $data['date_from'],
    
                            'p_bill_no' => $data['p_bill_no'],

                            'updated_at' => now()
    
                        ]);
                      
                }

            }
    
            DB::commit();
    
            return response()->json([
    
                'success' => 'Purchase and Products saved successfully!'
            ]);
        }
        catch (\Exception $e)
        {
            DB::rollBack();
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Something went wrong during the purchase save process.',
    
                'error' => $e->getMessage()
    
            ], 500);
        }
    }
    
    public function pendingPurchase()
    {
        $user = auth()->user();
        $setting['page_title'] = 'Pending Purchases';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/pending-purchase',$setting);
    }
    
    
    
    public function pendingPurchaseDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
    
        $search = $request->input('search_input');
        $product_type = $request->input('product_type');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $stid = $request->input('store_id');
    
        // Main Query with JOIN
        $templates = DB::table('tbl_sales_product as tsp')
            ->leftJoin('tbl_sales as ts', 'ts.order_no', '=', 'tsp.order_no')
            ->select(
                'tsp.*',
                'ts.cust_name',
                'ts.sale_date'
            )
            ->where('tsp.pending_purchase_status', 0)
            ->where('tsp.return_status', '0');
    
        // Store Filter
        if ($store_id > 0) {
            $templates->where('tsp.store_id', $store_id);
        }
    
        if (!empty($stid)) {
            $templates->where('tsp.store_id', $stid);
        }
    
        // Total count before filtering
        $totalData = (clone $templates)->count();
    
        // Product Type Filter
        if (!empty($product_type)) {
            $templates->where('tsp.product_type', $product_type);
        }
    
        // Date Filter
        if (!empty($date_from) && !empty($date_to)) {
        
            $from = date('Y-m-d 00:00:00', strtotime($date_from));
            $to   = date('Y-m-d 23:59:59', strtotime($date_to));
        
            $templates->whereBetween('ts.sale_date', [$from, $to]);
        }
    
        // Search Filter
        if (!empty($search)) {
    
            $search = trim($search);
            $searchValues = array_filter(array_map('trim', explode(',', $search)));
    
            if (count($searchValues) > 1 && count($searchValues) <= 100) {
    
                $templates->where(function ($query) use ($searchValues) {
                    $query->whereIn('tsp.product_code', $searchValues);
                });
    
            } else {
    
                $templates->where(function ($query) use ($search) {
                    $query->where('tsp.product_code', 'like', "%{$search}%")
                        ->orWhere('tsp.product_deatils', 'like', "%{$search}%");
                });
            }
        }
    
        // Filtered Count
        $totalFiltered = (clone $templates)->count();
    
        // Pagination
        $templates = $templates
            ->offset($start)
            ->limit($limit)
            ->orderBy('tsp.id', 'DESC')
            ->get();
    
        $data = [];
    
        if (!empty($templates)) {
    
            foreach ($templates as $template) 
            {
                
                if($template->product_type =='Glass')
                {
                    if($template->right_glass == NULL)
                    {
                        $product_type = $template->product_type.' <span class="badge badge-danger">Left</span>';
                        $product_deatils = $template->left_glass;
                    }
                    else
                    {
                        $product_type = $template->product_type.' <span class="badge badge-danger">Right</span>';
                        $product_deatils = $template->right_glass;
                    }
                }
                else
                {
                    $product_type = $template->product_type;
                    $product_deatils = $template->product_deatils;
                }
    
                $store = Store::where('id', $template->store_id)->first();
    
                $nestedData['store_name'] = $store->store_name ?? '';
    
                $nestedData['responsive_id'] = '';
    
                $nestedData['sid'] = $template->id;
    
                $nestedData['order_details'] =
                    'Order No : ' . $template->order_no .
                    '<br> Order Date : ' . $template->sale_date;
    
                $nestedData['cust_name'] = $template->cust_name ?? '';
    
                $nestedData['product_type'] = $product_type;
    
                $nestedData['product_code'] = $template->product_code;
    
                $nestedData['description'] = $product_deatils ?? '';
    
                $data[] = $nestedData;
            }
        }
    
        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ];
    
        return response()->json($json_data);
    }
    
    
    public function checkSameStoreOrder(Request $request)
    {
        $sales_product_ids = $request->ids;
    
        $storeIds = DB::table('tbl_sales_product')->whereIn('id', $sales_product_ids)
                    ->pluck('store_id')
                    ->unique();
    
        if ($storeIds->count() > 1) {
            return response()->json([
                'status'  => false,
                'code'  => 200,
                'sales_product_ids'  => $sales_product_ids,
                'message' => 'We found different stores . Please make sure all selected items belong to the same store.',
            ]);
        }
    
        return response()->json([
            'status'  => true,
            'code'  => 200,
            'sales_product_ids'  => $sales_product_ids,
            'store_id' => $storeIds->first(),
            'message' => 'Same store',
        ]);
    }
    
    
    public function updatePurchaseOfOrder(Request $request)
    {
        $saleproductIds = json_decode($request->sales_product_ids);
        


        $saleProduct = SaleProduct::with('sale')
                        ->whereIn('id', $saleproductIds)
                        ->get();
                        

        $setting['page_title'] = 'Update Purchase Details of Order';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        
        $setting['taxRule'] = $saleProduct->first()->sale->tax_rule;
        $setting['stid'] = $saleProduct->first()->sale->store_id;
        

        $setting['saleproduct'] = $saleProduct;
        return view($this->view_route.'/update-purchase-of-order',$setting);
    
    }
    
    
    public function addPurchaseToPendingSale(Request $request)
    {
        $user = auth()->user();
    
        // ==========================
        // VALIDATION
        // ==========================
    
        $validator = Validator::make($request->all(), [
    
            'supplier_name' => 'required|string|max:255',
            'p_bill_no'     => 'required|string|max:255',
            'date_from'     => 'required',
            'tax_rule'      => 'required',
            'store_id'      => 'required',
    
        ]);
    
        if ($validator->fails())
        {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        DB::beginTransaction();
    
        try
        {
            $data = $request->all();
    
            // ==========================
            // CREATE PURCHASE
            // ==========================
    
            $purchase = Purchase::create([
    
                'supplier_name'       => $data['supplier_name'],
                'p_bill_no'           => $data['p_bill_no'],
                'purchase_date'       => $data['date_from'],
                'tax_rule'            => $data['tax_rule'] ?? null,
                'total_qty'           => $data['total_qty'] ?? 0,
                'total_unit_amount'   => $data['total_unit_amount'] ?? 0,
                'total_base_amount'   => $data['total_base_amount'] ?? 0,
                'total_gst_amount'    => $data['total_gst_amount'] ?? 0,
                'total_p_amount'      => $data['total_p_amount'] ?? 0,
                'round_off'           => $data['round_off'] ?? 0,
                'net_purchase_amount' => $data['net_purchase_amount'] ?? 0,
                'added_by'            => $user->id,
                'store_id'            => $data['store_id'],
    
            ]);
    
            // ==========================
            // PRODUCT LOOP
            // ==========================
    
            foreach ($data['product_type'] as $i => $type)
            {
                $code = $data['product_code'][$i] ?? null;
    
                if (empty($type) || empty($code))
                {
                    continue;
                }
    
                // ==========================
                // GET SALE PRODUCT
                // ==========================
    
                $SaleProduct = SaleProduct::find($data['saleproductid'][$i]);
    
                if (!$SaleProduct)
                {
                    continue;
                }
    
                $product_id = $SaleProduct->product_id;
    
                // ==========================
                // GET ORDER NO
                // ==========================
    
                $orderNo = DB::table('tbl_sales_product')
                    ->where('id', $data['saleproductid'][$i])
                    ->value('order_no');
    
                // ==========================
                // CREATE PURCHASE PRODUCT
                // ==========================
    
                $purchaseProduct = PurchaseProduct::create([
    
                    'purchase_id' => $purchase->id,
                    'bill_no' => $data['p_bill_no'],
    
                    'product_type' => $type,
                    'product_code' => $code,
                    'product_id' => $product_id,
    
                    'product_name' => $SaleProduct->product_name ?? '',
                    'product_details' => $data['product_details'][$i] ?? '',
    
                    'quality_detail' => $SaleProduct->product_quality ?? '',
                    'company_detail' => $SaleProduct->product_company ?? '',
    
                    'product_price' =>
                        (float)($data['product_price'][$i] ?? 0),
    
                    'product_base_price' =>
                        (float)($data['product_base_price'][$i] ?? 0),
    
                    'product_purchase_price' =>
                        (float)($data['product_purchase_price'][$i] ?? 0),
    
                    'hsn_code' =>
                        $data['hsn_code'][$i] ?? '',
    
                    'gst_amt' =>
                        (float)($data['gst_amt'][$i] ?? 0),
    
                    'gst' =>
                        (float)($data['gst'][$i] ?? 0),
    
                    'qty' =>
                        (int)($data['product_qty'][$i] ?? 0),
    
                    'total_purchase_price' =>
                        (float)($data['total_purchase_price'][$i] ?? 0),
    
                    'product_retail_price' =>
                        (float)($data['product_retail_price'][$i] ?? 0),
    
                    'color_details' =>
                        $SaleProduct->product_color ?? '',
    
                    'material_detail' =>
                        $SaleProduct->product_material ?? '',
    
                    'size_details' =>
                        $SaleProduct->product_size ?? '',
    
                    'Type_details' =>
                        $SaleProduct->product_typesss ?? '',
    
                    'shape_details' =>
                        $SaleProduct->product_shape ?? '',
    
                    'coating_detail' =>
                        $SaleProduct->product_coating ?? '',
    
                    'design_details' =>
                        $SaleProduct->product_design ?? '',
    
                    'index_detail' =>
                        $SaleProduct->product_index ?? '',
    
                    'Number_detail' =>
                        $SaleProduct->product_number ?? '',
    
                    'ct_detail' =>
                        $SaleProduct->product_ct ?? '',
    
                    'validity_detail' =>
                        $SaleProduct->product_validity ?? '',
    
                    'box_detail' =>
                        $SaleProduct->box_detail ?? '',
    
                    'perbox_detail' =>
                        $SaleProduct->perbox_detail ?? '',
    
                    'store_id' =>
                        $data['store_id'],
    
                    'added_by' =>
                        $user->id,
    
                    'is_pair' => '',
    
                ]);
    
                if (!$purchaseProduct)
                {
                    continue;
                }
    
                // ==========================
                // UPDATE SALE PRODUCT STATUS
                // ==========================
    
                DB::table('tbl_sales_product')
                    ->where('id', $data['saleproductid'][$i])
                    ->update([
                        'pending_purchase_status' => 1,
                        'p_bill_no' => $data['p_bill_no'],
                        'updated_at' => now()
                    ]);
    
                // ==========================
                // UPDATE INVENTORY
                // ==========================
    
                $this->updateInventorySalePurchase(
                    $type,
                    $code,
                    $data['product_details'][$i],
                    $request,
                    $i,
                    $data['store_id'],
                    $product_id
                );
    
                // ==========================
                // GENERATE BARCODE
                // ==========================
    
                if (($data['barcode_g'][$i] ?? 0) == '1')
                {
                    $this->generatependingPurchaseBarcodes(
                        $type,
                        $purchase,
                        $purchaseProduct,
                        $data['product_details'][$i],
                        $request,
                        $i,
                        $data['store_id'],
                        $product_id,
                        $orderNo
                    );
                }
            }
    
            DB::commit();
    
            return response()->json([
                'success' => 'Purchase and Products saved successfully!'
            ]);
        }
        catch (\Exception $e)
        {
            DB::rollBack();
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Something went wrong during the purchase save process.',
    
                'error' => $e->getMessage()
    
            ], 500);
        }
    }
    
    
    private function generatependingPurchaseBarcodes(
        $type,
        $purchase,
        $purchaseProduct,
        $product_details,
        $request,
        $i,
        $store_id,
        $pid,
        $orderNo
    )
    {
        $user = auth()->user();
    
        $qty = (int)($request->input("product_qty.$i", 0));
    
        $box_detail = (int)($request->input("box_detail.$i", $qty));
    
        $perbox = (int)($request->input("perbox_detail.$i", 1));
    
        $purchase_price = (float)($request->input("total_purchase_price.$i", 0));
    
        $retail_price = (float)($request->input("product_retail_price.$i", 0));
    
        $code = $request->input("product_code.$i");
    
        if ($type === 'Lens')
        {
            for ($b = 0; $b < $box_detail; $b++)
            {
                $box_barcode = $this->generateUniqueRandomId(6);
    
                DB::table('tbl_barcode')->insert([
    
                    'purchase_id' => $purchase->id,
                    'purchase_product_id' => $purchaseProduct->id,
                    'purchase_date' => $request->input('date_from') ?? now(),
                    'p_bill_no' => $request->input('p_bill_no'),
    
                    'product_code' => $code,
                    'product_id' => $pid,
                    'perbox' => $perbox,
                    'product_type' => $type,
    
                    'barcode_no' => $box_barcode,
    
                    'product_details' => $product_details,
    
                    'mfg_date' => $purchaseProduct->mfg_detail,
                    'expiry_date' => $purchaseProduct->expiry_detail,
                    'batch_no' => $purchaseProduct->batchno_details,
    
                    'purchase_price' => $purchase_price,
                    'retail_price' => $retail_price,
    
                    'store_id' => $store_id,
    
                    'barcode_status' => 1,
                    'inward_status' => 0,
                    'outward_status' => 0,
                    'refrence_no' => $orderNo,
    
                    'created_at' => now(),
                    'updated_at' => now()
    
                ]);
    
                $piece_price = round($purchase_price / max($perbox, 1), 2);
    
                $piece_retail = round($retail_price / max($perbox, 1), 2);
    
                for ($p = 0; $p < $perbox; $p++)
                {
                    DB::table('tbl_barcode')->insert([
    
                        'purchase_id' => $purchase->id,
                        'purchase_product_id' => $purchaseProduct->id,
                        'purchase_date' => $request->input('date_from') ?? now(),
                        'p_bill_no' => $request->input('p_bill_no'),
    
                        'product_code' => $code,
                        'product_id' => $pid,
                        'product_type' => $type,
    
                        'barcode_no' => $this->generateUniqueRandomId1(7),
    
                        'lens_box' => $box_barcode,
    
                        'product_details' => $product_details,
    
                        'mfg_date' => $purchaseProduct->mfg_detail,
                        'expiry_date' => $purchaseProduct->expiry_detail,
                        'batch_no' => $purchaseProduct->batchno_details,
    
                        'purchase_price' => $piece_price,
                        'retail_price' => $piece_retail,
    
                        'store_id' => $store_id,
    
                        'barcode_status' => 1,
                        'inward_status' => 0,
                        'outward_status' => 0,
                        'refrence_no' => $orderNo,
    
                        'created_at' => now(),
                        'updated_at' => now()
    
                    ]);
                }
    
                DB::table('tbl_barcode_track_record')->insert([
    
                    'barcode_no' => $box_barcode,
                    'store_id' => $store_id,
                    'reference_type' => 'Sale',
                    'action_perform' => 'Order',
                    'added_by' => $user->id,
    
                ]);
            }
        }
        else
        {
            for ($q = 0; $q < $qty; $q++)
            {
                $barcode = $this->generateUniqueRandomId(6);
    
                DB::table('tbl_barcode')->insert([
    
                    'purchase_id' => $purchase->id,
                    'purchase_product_id' => $purchaseProduct->id,
                    'purchase_date' => $request->input('date_from') ?? now(),
                    'p_bill_no' => $request->input('p_bill_no'),
    
                    'product_code' => $code,
                    'product_id' => $pid,
                    'product_type' => $type,
    
                    'barcode_no' => $barcode,
    
                    'product_details' => $product_details,
    
                    'purchase_price' => $purchase_price,
    
                    'is_pair' => $purchaseProduct->is_pair,
    
                    'retail_price' => $retail_price,
    
                    'store_id' => $store_id,
    
                    'barcode_status' => 1,
                    'inward_status' => 0,
                    'outward_status' => 0,
                    'refrence_no' => $orderNo,
    
                    'created_at' => now(),
                    'updated_at' => now()
    
                ]);
    
                DB::table('tbl_barcode_track_record')->insert([
    
                    'barcode_no' => $barcode,
                    'store_id' => $store_id,
                    'reference_type' => 'Sale',
                    'action_perform' => 'Order',
                    'added_by' => $user->id,
    
                ]);
            }
        }
    }
    
    
    private function updateInventorySalePurchase(
        $type,
        $code,
        $product_details,
        $request,
        $i,
        $store_id,
        $pid
    )
    {
        $qty = (int)($request->input("product_qty.$i", 1));
    
        $perbox = (int)($request->input("perbox_detail.$i", 1));
    
        $box_detail = (int)($request->input("box_detail.$i", $qty));
    
        $query = DB::table('tbl_inventory_levels')
            ->where('product_code', $code)
            ->where('product_details', $product_details)
            ->where('product_id', $pid)
            ->where('store_id', $store_id);
    
        $inventory = $query->first();
    
        if ($type === 'Lens')
        {
            if ($inventory)
            {
                $query->update([
    
                    'available_quantity' =>
                        $inventory->available_quantity + $box_detail,
    
                    'tota_lens_qty' =>
                        $inventory->tota_lens_qty + ($perbox * $box_detail),
    
                    'updated_at' => now()
    
                ]);
            }
            else
            {
                DB::table('tbl_inventory_levels')->insert([
    
                    'product_code' => $code,
                    'product_id' => $pid,
                    'product_type' => $type,
                    'product_details' => $product_details,
                    'perbox' => $perbox,
                    'store_id' => $store_id,
    
                    'available_quantity' => $box_detail,
    
                    'tota_lens_qty' => ($perbox * $box_detail),
    
                    'created_at' => now(),
                    'updated_at' => now()
    
                ]);
            }
        }
        else
        {
            if ($inventory)
            {
                $query->update([
    
                    'available_quantity' =>
                        $inventory->available_quantity + $qty,
    
                    'updated_at' => now()
    
                ]);
            }
            else
            {
                DB::table('tbl_inventory_levels')->insert([
    
                    'product_code' => $code,
                    'product_id' => $pid,
                    'product_type' => $type,
                    'product_details' => $product_details,
                    'store_id' => $store_id,
    
                    'available_quantity' => $qty,
    
                    'created_at' => now(),
                    'updated_at' => now()
    
                ]);
            }
        }
    }
    
    
    public function purchaseReturn()
    {
        $user = auth()->user();
        $setting['page_title'] = 'Purchase Return';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/purchase-return',$setting);
    }
    
    
    
    
    public function purchaseReturnDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $product_type = $request->input('product_type');
        $search1 = $request->input('search1');
        $storeid = $request->input('store_id');
        
        if($store_id == '0')
        {
            $totalData = DB::table('tbl_purchase_return');
        }
        else
        {
            $totalData = DB::table('tbl_purchase_return')->where('store_id', $store_id);
        }
        
        if ($date_from != '' && $date_to != '')
        {
            $totalData->whereBetween('return_date', [$date_from, $date_to]);
        }
        if ($storeid != '')
        {
            $totalData->where('store_id', $storeid);
        }
        if ($product_type != '')
        {
            $totalData->where('product_type', [$product_type]);
        }
        if ($search1 != '') 
        {
            $totalData->where('supplier_name', 'like', '%' . $search1 . '%')
            ->orWhere('bill_no', 'like', '%' . $search1 . '%')
            ->orWhere('return_date', 'like', '%' . $search1 . '%')
            ->orWhere('barcode_no', 'like', '%' . $search1 . '%')
            ->orWhere('product_code', 'like', '%' . $search1 . '%');
        }
        $totalData = $totalData->count();
        
        if($store_id == '0')
        {
            $templates = DB::table('tbl_purchase_return');
        }
        else
        {
            $templates = DB::table('tbl_purchase_return')->where('store_id', $store_id);
        }
        if ($storeid != '')
        {
            $templates->where('store_id', $storeid);
        }
        if ($date_from != '' && $date_to != '') 
        {
           $templates->whereBetween('return_date', [$date_from,  $date_to]);
        }
        if ($product_type != '')
        {
            $templates->where('product_type', [$product_type]);
        }
        if ($search1 != '') 
        {
            $templates->where('supplier_name', 'like', '%' . $search1 . '%')
            ->orWhere('bill_no', 'like', '%' . $search1 . '%')
            ->orWhere('return_date', 'like', '%' . $search1 . '%')
            ->orWhere('barcode_no', 'like', '%' . $search1 . '%')
            ->orWhere('product_code', 'like', '%' . $search1 . '%');
        }


        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('return_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                
                $nestedData['sr_no']    = $i++;
                $nestedData['supplier_name'] = $template->supplier_name;
                $nestedData['purchase_date']  = date("d-m-Y", strtotime($template->purchase_date));
                $nestedData['bill_no'] = '<span class="badge badge-success">'.$template->bill_no.'</span>';
                $nestedData['product_type']  = $template->product_type;
                $nestedData['product_code']  = $template->product_code;
                $nestedData['description']  = $template->description;
                $nestedData['qty']  = $template->qty;
                $nestedData['total_purchase']  = 'Rs '.$template->total_purchase;
                $nestedData['barcode_no']  = $template->barcode_no;
                $nestedData['comment']  = $template->comment;
                $nestedData['return_date']  = date("d-m-Y", strtotime($template->return_date));
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
    
    public function addReturn()
    {
        $user = auth()->user();
        $setting['page_title'] = 'Purchase Return';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/add-return',$setting);
    }
    
    public function purchaseProductList(Request $request)
    {
        $product_type = $request->input('product_type');
        $search_by = $request->input('search_by');
        $search_text = $request->input('search_text');
        $store_id = $request->input('store_id');
    
        $query = DB::table('tbl_purchase as p')
            ->leftJoin('tbl_purchase_deatils as pd', 'pd.purchase_id', '=', 'p.purchase_id')
            ->leftJoin('tbl_barcode as b', 'b.purchase_id', '=', 'p.purchase_id');
    
        if ($product_type) {
            $query->where('pd.product_type', $product_type);
        }
    
        if ($search_by) 
        {
            if($search_text)
            {
                if($search_text == '1')
                {
                    $query->where('p.supplier_name', $search_text);
                }
                elseif($search_text == '2')
                {
                    $query->where('pd.product_code', $search_text);
                }
                elseif($search_text == '3')
                {
                    $query->where('b.barcode_no', $search_text);
                }
                elseif($search_text == '4')
                {
                     $query->where('p.p_bill_no', $search_text);
                }
                elseif($search_text == '5')
                {
                    $query->where('pd.company_detail', $search_text);
                }
                elseif($search_text == '6')
                {
                    $query->where('p.purchase_date', $search_text);
                }
                
            }
            
        }
    
        if ($store_id) {
            $query->where('pd.store_id', $store_id);
        }
    
        // Proper NULL condition group
        $query->where(function ($q) {
            $q->whereNull('b.outward_status')
              ->orWhereNull('b.transfer_outward_status');
        });
    
        $results = $query->select(
            'pd.*',
            'p.supplier_name',
            'p.p_bill_no',
            'p.purchase_date',
            'b.barcode_no',
            'b.outward_status',
            'b.transfer_outward_status',
            'b.id as bid'
        )->get();
    
        if ($results->isEmpty()) {
            return response()->json('<div class="alert alert-danger">No data found.</div>');
        }

    
        $data = '';
    
        $data .= '
        <div class="container">
            <table id="datatable2" class="table card-table table-vcenter text-nowrap" style="color:#000;">
                <thead>
                  <tr>
                    <th>
                        <input type="checkbox" id="select-all" onclick="toggleAll(this)">
                    </th>
                    <th>Supplier Name</th>
                    <th>Bill No</th>
                    <th>Purchase Date</th>
                    <th>Product</th>
                    <th>Product Code</th>
                    <th>Description</th>
                    <th>Total Purchase</th>
                    <th>Barcode</th>
                  </tr>
                </thead>
                <tbody>';
    
        foreach ($results as $product) {
    
            $data .= '
                <tr>
                     <td>
                        <input type="checkbox"
                               class="row-checkbox"
                               value="'.$product->bid.'">
                     </td>
    
                     <td>'.$product->supplier_name.'</td>
                     <td>'.$product->p_bill_no.'</td>
                     <td>'.$product->purchase_date.'</td>
                     <td>'.$product->product_type.'</td>
                     <td>'.$product->product_code.'</td>
                     <td>'.$product->product_details.'</td>
                     <td>'.$product->product_purchase_price.'</td>
                     <td>'.$product->barcode_no.'</td>
                </tr>
            ';
        }
    
        $data .= '
                </tbody>
            </table>
    
            <hr/>
    
            <div class="row">
                <div class="col-md-12">
                   <div class="form-group">
                        <label>Comment</label>
    
                        <textarea class="form-control input"
                                  id="return_comment"
                                  name="return_comment"></textarea>
    
                        <input type="hidden"
                               class="form-control input"
                               id="purchase_id"
                               name="purchase_id"
                               value="'.($product->purchase_id ?? '').'">
                    </div>
                </div>
            </div>
    
            <button class="btn btn-gradient"
                    id="submitreturnBtn"
                    type="button">
                Return
            </button>
        </div>
    
        <script>
            function toggleAll(source) {
                const checkboxes = document.querySelectorAll(".row-checkbox");
    
                checkboxes.forEach(cb => {
                    cb.checked = source.checked;
                });
            }
        </script>
        ';
    
        return response()->json($data);
    }
    
    public function purchaseReturenStored(Request $request)
    {
        $user = auth()->user();
        $store_id = auth()->user()->store_id;
        

        DB::beginTransaction();
    
        try 
        {
            $product_id= $request->input('product_id');
            $count_qty = is_array($product_id) ? count($product_id) : 0;
            $return_comment = $request->input('return_comment');
            

            $total_unit_amount=  0;
            $total_base_amount=  0;
            $total_gst_amount=  0;
            $total_qty=  0;
            $total_p_amount=  0;
            
            
            foreach ($product_id as $product) 
            {
                $barcode = DB::table('tbl_barcode')->where('id', $product)->first();
                $tbl_purchase = DB::table('tbl_purchase')->where('purchase_id', $barcode->purchase_id)->first();
                
                DB::table('tbl_purchase_return')->insert([
                    'supplier_name'   => $tbl_purchase->supplier_name,
                    'bill_no'         => $barcode->p_bill_no,
                    'purchase_date'   => $barcode->purchase_date,
                    'return_date'     => date("Y-m-d"),
                    'product_type'    => $barcode->product_type,
                    'product_code'    => $barcode->product_code, 
                    'description'     => $barcode->product_details,
                    'qty'             => 1,
                    'total_purchase'  => $barcode->purchase_price,
                    'barcode_no'      => $barcode->barcode_no,
                    'comment'         => $return_comment,
                    'added_by'        => $user->id,
                    'store_id'        => $user->store_id,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ]);
                
                if($barcode->t_status == '1')
                {
                    $update_barcode =  DB::table('tbl_barcode')->where('id', $product)->update([
                        'transfer_outward_status'      => 2,
                        'updated_at' => now()
                    ]);
                    
                    $store_id = $barcode->transfer_store_id;
                }
                else
                {
                    $update_barcode =  DB::table('tbl_barcode')->where('id', $product)->update([
                        'outward_status'      => 2,
                        'updated_at' => now()
                    ]);
                    
                    $store_id = $barcode->store_id;
                }
                
                 $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                        'barcode_no' => $barcode->barcode_no,
                        'store_id' => $store_id,
                        'reference_type' => 'Return',
                        'action_perform' => 'Return',
                        'added_by' => $user->id,
                ]);
                
                
                
                
                $tbl_inventory = DB::table('tbl_inventory_levels')->where('product_type', $barcode->product_type)->where('product_code', $barcode->product_code)
                ->where('product_details', $barcode->product_details)->where('product_id', $barcode->product_id)->where('store_id', $store_id)->first();
                
                $update_inventory =  DB::table('tbl_inventory_levels')->where('product_id', $barcode->product_id)->where('product_code', $barcode->product_code)
                ->where('product_details', $barcode->product_details)->where('store_id', $store_id)->update([
                    'available_quantity'      => $tbl_inventory->available_quantity-1,
                    'updated_at' => now()
                ]);
                
                // $tbl_purchase_deatils = DB::table('tbl_purchase_deatils')->where('id', $barcode->purchase_product_id)->first();
                
                // $old_qty = $tbl_purchase_deatils->qty;
                
                // $new_qty = $old_qty - 1;
                
                // if($new_qty == 0)
                // {
                //     DB::table('tbl_purchase_deatils')->where('id', $barcode->purchase_product_id)->delete();
                // }
                // else
                // {
                    
                //     $tax_rule = $tbl_purchase->tax_rule;
                //     $gst = $tbl_purchase_deatils->gst;
                    
                //     if($tax_rule == 'Exclude')
                //     {
                //         $bace_price = $tbl_purchase_deatils->product_base_price;
                //         $gstAmount = ($bace_price * $gst) / 100;
                //     }
                //     else
                //     {
                //         $bace_price = $tbl_purchase_deatils->product_base_price;
                //         $gstAmount = ($bace_price * $gst) / (100 + $gst);
                //         $bace_price = $bace_price - $gstAmount;
                //     }
                    
                    
                //     $product_price = $bace_price;
                //     $product_base_price = $bace_price;
                //     $qty = $new_qty;
                //     $product_purchase_price = $gstAmount+$bace_price;
                    
                //     $total_gst = $qty*$gstAmount;
                //     $total_product_base_price = $qty*$product_base_price;
                    
                //     $total_purchase_price = $total_gst+$total_product_base_price;
                    
                //     $total_unit_amount+=  $bace_price*$qty;
                //     $total_base_amount+=  $bace_price*$qty;
                //     $total_gst_amount+=  $gstAmount*$qty;
                //     $total_qty+=  $qty;
                //     $total_p_amount+=  $total_purchase_price;
                    
                    
                //     $update_product =  DB::table('tbl_purchase_deatils')->where('id', $barcode->purchase_product_id)->update([
                //         'product_price'         => $bace_price,
                //         'product_base_price'    => $bace_price,
                //         'gst_amt'               => $gstAmount,
                //         'product_purchase_price'=> $total_product_base_price,
                //         'qty'                   => $qty,
                //         'total_purchase_price'  => $total_purchase_price,
                //         'updated_at' => now()
                //     ]);
                    
                // }
                
                
                // $update_purchase =  DB::table('tbl_purchase')->where('purchase_id', $request->input('purchase_id'))->update([
                //         'total_qty'            => $total_qty,
                //         'total_unit_amount'    => $total_unit_amount,
                //         'total_base_amount'    => $total_base_amount,
                //         'total_gst_amount'     => $total_gst_amount,
                //         'total_p_amount'       => $total_p_amount,
                //         'net_purchase_amount'  => $total_purchase_price,
                //         'updated_at' => now()
                //     ]);
                
                
            }

            
		    DB::commit();
            return response()->json(['success' => 'Purchase Products return saved successfully!']);
		} 
		catch (\Exception $e) 
		{
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the purchase return process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function purchaseGrid()
    {
        $setting['page_title'] = 'Glass Grid Purchase';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/purchase-grid',$setting);
    }
    
    
    public function purchaseGridAdd(Request $request)
    {
        $user = auth()->user();

        
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255',
            'p_bill_no'     => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        


        DB::beginTransaction();
    
        try 
        {
            $data = $request->all();


            $purchase = Purchase::create([
                'supplier_name'       => $data['supplier_name'],
                'p_bill_no'           => $data['p_bill_no'],
                'purchase_date'       => $data['date_from'],
                'tax_rule'            => $data['tax_rule'] ?? null,
                'total_qty'           => $data['qty'] ?? 0,
                'total_unit_amount'   => $data['product_price'] ?? 0,
                'total_base_amount'   => $data['product_base_price']?? 0,
                'total_gst_amount'    => $data['gst_amt']?? 0,
                'total_p_amount'      => $data['total_unit_price'] ?? 0,
                'round_off'           => $data['round_off'] ?? 0,
                'net_purchase_amount' => $data['total_purchase_price'] ?? 0,
                'added_by'            => $user->id,
                'store_id'            => $data['store_id'],
            ]);
            
            
            $matrix = $request->input('matrix', []);

            $flatProducts = []; 
            
            foreach ($matrix as $sph => $cylRow)
            {
                foreach ($cylRow as $cyl => $qty)
                {
                    $qty = floatval($qty);
        
                    if ($qty > 0)
                    {
                        $flatProducts[] = [
                            'sph' => $sph,
                            'cyl' => $cyl,
                            'qty' => $qty,
                        ];
                        
                       $fields = [
                            trim($data['product_details'] ?? ''),
                            trim($data['company_detail'] ?? ''),
                            trim($data['color_details'] ?? ''),
                            trim($data['material_detail'] ?? ''),
                            trim($data['coating_detail'] ?? ''),
                            trim($data['design_details'] ?? ''),
                            trim($data['index_detail'] ?? ''),
                            trim($data['quality_detail'] ?? ''),
                            !empty($sph) ? 'SPH:' . trim($sph) : '',
                            !empty($cyl) ? 'CYL:' . trim($cyl) : '',
                            !empty($data['addiional_detail']) ? 'ADD:' . trim($data['addiional_detail']) : '',
                            !empty($data['axis_detail']) ? 'Axis:' . trim($data['axis_detail']) : '',
                        ];
                        
                        $filteredFields = array_filter($fields, function ($value) {
                            return !empty($value) && trim($value) !== '';
                        });
                        $product_details = implode(' - ', $filteredFields); 
                        
                        $fieldsproduct = [
                            trim($data['product_details'] ?? ''),
                            trim($data['company_detail'] ?? ''),
                            trim($data['color_details'] ?? ''),
                            trim($data['material_detail'] ?? ''),
                            trim($data['coating_detail'] ?? ''),
                            trim($data['design_details'] ?? ''),
                            trim($data['index_detail'] ?? ''),
                            trim($data['quality_detail'] ?? ''),
                        ];
                        
                        $filteredFieldsProduct = array_filter($fieldsproduct, function ($value) {
                            return !empty($value) && trim($value) !== '';
                        });
                        

                        $product_detailsP = implode(' - ', $filteredFieldsProduct); 

                        $tCount = DB::table('tbl_product_code')
                         ->where('product_type', 'Glass')
                         ->where('product_code', $data['product_code'])
                         ->where('productdetails', $product_detailsP)->count();
                     
                        $PCount = DB::table('tbl_product_code')->where('product_code', $data['product_code'])->count();
                        
                        if($tCount == 0)
                        {
                            
                            $idgenerate = $this->generateUniqueRandomIdProduct(6, 'tbl_product_code', 'product_id');
                            
                            if($PCount == 0)
                            {
                                $product_id = $idgenerate;
                            }
                            else
                            {
                                $tbl_product_code = DB::table('tbl_product_code')->where('product_code', $data['product_code'])->first();
                                $product_id = $tbl_product_code->product_id;
                            }
                            
                            $Product = Product::create([
                                'product_type'         => 'Glass',
                                'product_code'         => $data['product_code'],
                                'product_id' => $product_id,
                                'productdetails'       => $product_detailsP,
                                'product_name'         => $data['product_details'] ?? '',
                                'Company'              => $data['company_detail'] ?? '',
                                'Quality'              => $data['quality_detail'] ?? '',
                                'Color'                => $data['color_details'] ?? '',
                                'Material'             => $data['material_detail'] ?? '',
                                'Coating'              => $data['coating_detail'] ?? '',
                                'Design'               => $data['design_details'] ?? '',
                                'Index'                => $data['index_detail'] ?? '',
                                'SPH'                  => $sph ?? '',
                                'CYL'                  => $cyl ?? '',
                                'AXIS'                 => $data['axis_detail'] ?? '',
                                'ADD'                  => $data['addiional_detail'] ?? '',
                                'Track_Inventory'      => $data['Track_Inventory'] ?? '',
                                'Allow_Negative_Inventory' => $data['Negative_Inventory'] ?? '',
                                'Purchase_Base_Price'   => (float)($data['product_base_price'] ?? 0),
                                'Purchase_Price'        => (float)($data['product_base_price'] ?? 0)+(float)($data['gst_amt'] ?? 0),
                                'Retail_Price'          => (float)($data['product_retail_price'] ?? 0),
                                'store_id'              => $data['store_id'],
                                'added_by'              => $user->id,
                            ]);
                        }
                        else
                        {
                            $tbl_product_code = DB::table('tbl_product_code')->where('product_code', $data['product_code'])->first();
                            $product_id = $tbl_product_code->product_id;
                        }
                        
   
                                
                        
                        $purchaseproduct =  PurchaseProduct::create([
                            'purchase_id'         => $purchase->id,
                            'bill_no'             => $data['p_bill_no'],
                            'product_type'        => 'Glass',
                            'product_code'        => $data['product_code'],
                            'product_id'          => $product_id,
                            'product_details'     => $product_details,
                            'quality_detail'      => $data['quality_detail'] ?? '',
                            'company_detail'      => $data['company_detail'] ?? '',
                            'product_price'       => (float)($data['product_price'] ?? 0),
                            'product_base_price'  => (float)($data['product_base_price'] ?? 0),
                            'product_purchase_price'  => (float)($data['total_purchase'] ?? 0),
                            'hsn_code'            => $data['hsn_code'] ?? '',
                            'gst_amt'             => (float)($data['gst_amt'] ?? 0),
                            'gst'                 => (float)($data['gst'] ?? 0),
                            'qty'                 => (int)($qty ?? 0),
                            'total_purchase_price'=> (float)(($data['total_purchase'])*(int)($qty ?? 0)  ?? 0),
                            'product_retail_price'=> (float)(($data['product_retail_price'])?? 0),
                            'color_details'       => $data['color_details'] ?? '',
                            'material_detail'     => $data['material_detail'] ?? '',
                            'coating_detail'      => $data['coating_detail'] ?? '',
                            'design_details'      => $data['design_details'] ?? '',
                            'index_detail'        => $data['index_detail'] ?? '',
                            'addiional_detail'    => $data['addiional_detail'] ?? '',
                            'axis_detail'         => $data['axis_detail'] ?? '',
                            'sph_detail'          => $sph ?? '',
                            'cyl_details'         => $cyl ?? '',
                            'store_id'            => $data['store_id'],
                        ]);
                        
                        
                        $add_record = DB::table('tbl_inventory_record')->insert([
                            'product_code' => $data['product_code'],
                            'product_id' => $product_id,
                            'product_type' => 'Glass', 
                            'product_details' => $product_details,
                            'store_id' =>  $data['store_id'],
                            'qty' => (int)($qty ?? 0),
                            'added_date' => $data['date_from'],
                            'inward_status' => 0,
                            'added_by' => $user->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        /************* UPDATE IN INVETORY ********************/
                        
                        //dd($product_details);
                        
                        $inventory = DB::table('tbl_inventory_levels')
                            ->where('product_code', $data['product_code'])
                            ->where('product_id', $product_id)
                            ->where('product_details', $product_details)
                            ->where('store_id', $data['store_id'])
                            ->first();
                            
                        if ($inventory) {
                            DB::table('tbl_inventory_levels')
                            ->where('id', $inventory->id)
                            ->update([
                                'available_quantity'      => $inventory->available_quantity + $qty,
                                'updated_at' => now()
                            ]);
                        } 
                        else 
                        {
                            DB::table('tbl_inventory_levels')->insert([
                                'product_code' => $data['product_code'],
                                'product_id' => $product_id,
                                'product_type' => 'Glass',
                                'product_details' => $product_details,
                                'store_id'     => $data['store_id'],
                                'available_quantity'        => $qty,
                                'created_at'   => now(),
                                'updated_at'   => now()
                            ]);
                        } 
                        
                        // Product qty wise barcode generate
                        
                        if($data['barcode_option'] == '1')
                        {
                            for ($j = 0; $j < $qty; $j++) 
                            {
                                $idgeneratess = $this->generateUniqueRandomId(6, 'tbl_barcode', 'barcode_no');
                                
                                DB::table('tbl_barcode')->insert([
                                    'purchase_id'   => $purchase->id,
                                    'purchase_product_id'   => $purchaseproduct->id,
                                    'purchase_date' => $data['date_from'] ?? now(),
                                    'p_bill_no'     => $data['p_bill_no'],
                                    'product_code'  => $data['product_code'],
                                    'product_id'  => $product_id,
                                    'product_type'  => 'Glass', 
                                    'barcode_no'    => $idgeneratess,
                                    'product_details'     => $product_details,
                                    'purchase_price'=> (float)($data['total_purchase'] ?? 0),
                                    'retail_price'  => (float)($data['product_retail_price'] ?? 0),
                                    'store_id'      => $data['store_id'],
                                    'inward_status'      => 0,
                                    'created_at'    => now(),
                                    'updated_at'    => now()
                                ]);
                                
                                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                                    'barcode_no' => $idgeneratess,
                                    'store_id' => $data['store_id'],
                                    'reference_type' => 'Purchase',
                                    'action_perform' => 'Add',
                                    'added_by' => $user->id,
            
                                ]);
                            }
                        }
                    }
                    if (empty($flatProducts))
                    {
                        return response()->json([
                            'status' => false,
                            'message' => 'No valid matrix values found.',
                        ], 422);
                    }
                }
            }


            DB::commit();
    
            return response()->json(['success' => 'Purchase and Products saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the purchase save process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }        
    
    
    
    
    public function additionalDiscount()
    {
        $user = auth()->user();
        $setting['page_title'] = 'Barcode';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        $setting['suppliers'] = Supplier::where('status', '1')->get();
        return view($this->view_route.'/additional-discount',$setting);
    }
    
    public function additionalDiscountDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $supplier_company = $request->input('supplier_company');
        $search1 = $request->input('search_input');

        $totalData = DB::table('tbl_purchase')->where('store_id', $store_id)->where('is_Deleted', '0')->where('additional_dis','!=','');
        if ($search1 != '') 
        {
            $totalData->where('supplier_name', 'like', '%' . $supplier_company . '%');
        }
        if ($search1 != '') 
        {
            $totalData->where('p_bill_no',$search1);
        }
        $totalData = $totalData->count();
        
        $templates = DB::table('tbl_purchase')->where('store_id', $store_id)->where('is_Deleted', '0')->where('additional_dis','!=','');
        if ($search1 != '') 
        {
            $templates->where('supplier_name', 'like', '%' . $supplier_company . '%');
        }
        if ($search1 != '') 
        {
            $templates->where('p_bill_no',$search1);
        }


        $tem = $tem1 = $templates;
        $templates = $tem->offset($start)
        ->limit($limit)
        ->orderBy('purchase_id', 'DESC')
        ->get();
        $totalFiltered = $templates->count();
         
        $data = [];
        if (! empty($templates))
        {
            $i=1;
            foreach ($templates as $template) 
            {
                $nestedData['sr_no']    = $i++;
                $nestedData['supplier_name'] = $template->supplier_name;
                $nestedData['bill_no'] = $template->p_bill_no;
                $nestedData['p_date']  = $template->purchase_date;
                $nestedData['additional_dis']  = 'Rs ' .$template->additional_dis.' ('.$template->dis_per.'%)' ;
                $nestedData['encryptedId']  = $template->purchase_id;
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
    
    
    public function additionalDiscountAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dis_per' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        
        DB::table('tbl_purchase')->where('purchase_id', $request->purchase_id)->update([
            'dis_per'             => $request->dis_per,
            'additional_dis'      => $request->dis_amount,
            'updated_at' => now()
        ]);

        return response()->json(['success' => 'Additional discount add successfully!']);
    }
    
    public function additionaldisdestroy($id)
    {
        $user_id = auth()->user()->id;
        $Is_delted = DB::table('tbl_purchase')->where('purchase_id', $id)->update(['additional_dis' => '','dis_per' => '']);
        if (!$Is_delted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again',
            ]);
        }
        return response()->json([
            'status'  => 'success',
            'message' => 'Additional Discount  was successfully deleted',
        ]);
    }
    
    
    public function missingPurchasePrice()
    {
        $user = auth()->user();
        $setting['page_title'] = 'Missing Purchase Price';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/missing-purchase-price',$setting);
    }
    
    
    public function missingPriceBarcodeDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $product_type= $request->input('product_type');
        $search1 = $request->input('search1');
        $storeid = $request->input('store_id');
        if($store_id == '0')
        {
            $totalData = DB::table('tbl_barcode')->where('purchase_price','=', '0.00');
        }
        else
        {
            $totalData = DB::table('tbl_barcode')->where('store_id', $store_id)->where('purchase_price','=', '0.00');
        }
        
        
        if ($product_type != '')
        {
            $totalData->where('product_type', [$product_type]);
        }
        if ($storeid != '')
        {
            $totalData->where('store_id', $storeid);
        }
        if ($search1 != '') 
        {
            $totalData->where('product_details', 'like', '%' . $search1 . '%')
            ->orWhere('product_code', 'like', '%' . $search1 . '%');
        }
        $totalData = $totalData->count();
        
        if($store_id == '0')
        {
            $templates = DB::table('tbl_barcode')->where('purchase_price','=', '0.00');
        }
        else
        {
            $templates = DB::table('tbl_barcode')->where('store_id', $store_id)->where('purchase_price','=', '0.00');
        }
        if ($storeid != '')
        {
            $templates->where('store_id', $storeid);
        }
        if ($product_type != '') 
        {
           $templates->where('product_type', [$product_type]);
        }
        
        if ($search1 != '') 
        {
            $templates->where('product_details', 'like', '%' . $search1 . '%')
            ->orWhere('product_code', 'like', '%' . $search1 . '%');
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
                if($template->product_type == 'Lens')
                {

                    $description = $template->product_details.'<BR><strong style="color:red"> Box per peice :  '.$template->perbox.'</strong>';
                }
                else
                {
                    $description = $template->product_details;
                }
                $encryptedId = base64_encode($template->id);
                $receive_store = Store::find($template->store_id);
                $nestedData['sr_no']    = $i++;
                $nestedData['store_name'] = $receive_store->store_name;
                $nestedData['purchase_date'] = $template->purchase_date;
                $nestedData['product_type'] = $template->product_type;
                $nestedData['product_code']  = $template->product_code;
                $nestedData['product_details'] = $description;
                $nestedData['barcode_no']  =   '<span class="badge badge-info">'.$template->barcode_no.'</span>';
                $nestedData['purchase_price']  = $template->purchase_price;
                $nestedData['retail_price']  = $template->retail_price;
                $nestedData['description'] = $template->product_details;
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
    
    
    public function barcodePriceUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_price'        => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }
        
        $decryptedId = base64_decode($request->barcode_id);

         $update_barcode =  DB::table('tbl_barcode')->where('id', $decryptedId)->update([
                    'purchase_price'      => $request->purchase_price,
                    'updated_at' => now()
                ]);
        
        return response()->json([
            'status'  => 'true',
            'code'  => '200',
            'message' => 'Barcode purchase price update successfully!',
        ]);
    }
    
    
    public function purchaseFilter(Request $request)
    {
        $supplier = $request->input('supplier_company');
        $billNo = $request->input('bill_no');
    
        $query = DB::table('tbl_purchase');
    
        if ($supplier) {
            $query->where('supplier_name', $supplier);
        }
    
        if ($billNo) {
            $query->where('p_bill_no', $billNo);
        }
    
        $results = $query->first();
    
        if (!$results) {
            return response()->json('<div class="alert alert-danger">No data found.</div>');
        }
    
        $csrfToken = csrf_token();
        $routeUrl = route('admin.additional-discount-add');
    
        $data = <<<HTML
        <form id="additionalForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{$csrfToken}">
            <input type="hidden" name="_method" id="formMethod" value="POST">
        
            <div class="row">
                <div class="col-md-4">
                    <label>Total Purchase Amount</label>
                    <input class="form-control" value="{$results->total_p_amount}" id="total_p_amount" name="total_p_amount" readonly>
                </div>
                <div class="col-md-4">
                    <label>Total Round Off Amount</label>
                    <input class="form-control" value="{$results->round_off}" id="round_off" name="round_off" readonly>
                </div>
                <div class="col-md-4">
                    <label>Total Net Purchase Amount</label>
                    <input class="form-control" value="{$results->net_purchase_amount}" id="net_purchase_amount" name="net_purchase_amount" readonly>
                </div>
            </div>
        
            <hr/>
        
            <div class="row">
                <div class="col-md-3">Additional Discount :</div>
                <div class="col-md-2">
                    <input type="number" class="form-control" placeholder="Discount %" id="dis_per" name="dis_per">
                    <span class="error badge text-danger" id="dis_perError"></span>
                </div>
                <div class="col-md-2">
                    <input class="form-control" placeholder="Discount Amt" id="dis_amount" name="dis_amount" readonly>
                </div>
            </div>
        
            <input type="hidden" class="form-control" value="{$results->purchase_id}" id="purchase_id" name="purchase_id" readonly>
            <button class="btn btn-gradient mt-3" type="submit" title="Next">Submit</button>
        </form>
    
    <script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $("input[name='_token']").val()
            }
        });
    
        $("#dis_per").on("keyup", function() {
            let totalAmount = parseFloat($("#net_purchase_amount").val()) || 0;
            let discountPercent = parseFloat($(this).val()) || 0;
    
            if (discountPercent >= 0 && discountPercent <= 100) {
                let discountAmount = (totalAmount * discountPercent) / 100;
                $("#dis_amount").val(discountAmount.toFixed(2));
            } else {
                $("#dis_amount").val("0.00");
            }
        });
    
        $("#additionalForm").submit(function(e) {
            e.preventDefault();
    
            let isValid = true;
            $(".error").text("");
            $(".is-invalid").removeClass("is-invalid");
    
            let dis_per = $("#dis_per").val().trim();
    
            if (dis_per === "") {
                $("#dis_perError").text("Discount % required.");
                $("#dis_per").addClass("is-invalid");
                isValid = false;
            }
    
            if (!isValid) {
                return;
            }
    
            let form = $("#additionalForm")[0];
            let formData = new FormData(form);
    
            $.ajax({
                type: "POST",
                url: "{$routeUrl}",
                data: formData,
                dataType: "JSON",
                processData: false,
                contentType: false,
                success: function(response) {
                    if ($.isEmptyObject(response.error)) {
                        $.toaster({
                            priority: "success",
                            title: response.success,
                            message: ""
                        });
                        location.reload();
                    } else {
                        $(".error").text("");
                        $(".is-invalid").removeClass("is-invalid");
    
                        $.each(response.error, function(index, value) {
                            if (index === "dis_per") {
                                $("#dis_perError").text(value);
                                $("#dis_per").addClass("is-invalid");
                            }
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error: " + textStatus + " - " + errorThrown);
                }
            });
        });
    });
    </script>
    HTML;
    
        return response()->json($data);
    }
    


/*********************************************************************************************************************************************/
    
}