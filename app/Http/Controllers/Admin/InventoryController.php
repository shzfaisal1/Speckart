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
use App\Models\purchases\Purchase;
use App\Models\purchases\PurchaseProduct;
use App\Models\product\Product;
use PDF;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\Jobs\ProcessAudit;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\ProcessStockTransfer;
use App\Models\inventory\BarcodeTrack;
use App\Models\inventory\InventoryAudit;
use App\Models\inventory\InventoryAuditBarcode;
use App\Models\inventory\InventoryLevels;
use App\Models\inventory\InventoryRecords;
use App\Models\inventory\TransferStock;
use App\Models\sale\Sale;
use App\Models\sale\SaleProduct;



class InventoryController extends Controller
{
    public $view_route = 'inventory';
    
    public function inventoryLevel()
    {
        $setting['page_title'] = 'Inventory level';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/inventory-level',$setting);
    }
    
    public function inventoryDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
    
        $product_type = $request->input('product_type');
        $search1 = $request->input('search1');
        $storeid = $request->input('store_id');
        $inv_status = $request->input('inv_status');

        $query = DB::table('tbl_inventory_levels')->where('status', '0');
        if ($store_id != '0') {
            $query->where('store_id', $store_id);
        }
    
        if ($inv_status == '2') {
            $query->where('available_quantity', '>', 0);
        } elseif ($inv_status == '3') {
            $query->where('available_quantity', '<', 0);
        }
    
        if ($product_type != '') {
            $query->where('product_type', $product_type);
        }
    
        if ($storeid != '') {
            $query->where('store_id', $storeid);
        }
    
        if ($search1 != '') 
        {
            $query->where(function($q) use ($search1) {
                $q->where('product_details', 'like', "%$search1%")
                  ->orWhere('product_code', 'like', "%$search1%");
            });
        }
    
        $totalFiltered = $query->count();
        $totalData = DB::table('tbl_inventory_levels')
            ->where('status', '0')
            ->count();

        $templates = $query->offset($start)
                   ->limit($limit)
                   ->orderBy('available_quantity', 'ASC')
                   ->get();
    
        $data = [];
        $i = $start + 1;
    
        foreach ($templates as $template)
        {
            $description = $template->product_details;
            $available_quantity = $template->available_quantity;
    
            if ($template->product_type == 'Lens') {
                $available_quantity .= ' ('.$template->tota_lens_qty.')';
                $description .= '<br><strong style="color:red"> Box per piece: '.$template->perbox.'</strong>';
            }
    
            $encryptedId = base64_encode($template->id);
            $receive_store = Store::find($template->store_id);
    
            $nestedData = [
                'sr_no' => $i++,
                'product_id' => $template->product_id,
                'product_type' => $template->product_type,
                'product_code' => $template->product_code,
                'description' => $description,
                'qty' => $available_quantity < 0 
                            ? '<span class="badge badge-danger">'.$available_quantity.'</span>'
                            : '<span class="badge badge-info">'.$available_quantity.'</span>',
                'perbox' => $template->perbox ?? 0,
                'encryptedId' => $encryptedId,
                'store_name' => $receive_store->store_name ?? '',
                'qty_av' => $template->available_quantity,
                'store_id' => $template->store_id,
                'product_details' => $template->product_details,
            ];
    
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ]);
    }

    
    public function stockTransfer()
    {
        $setting['page_title'] = 'Transfer Stock';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/stock-transfer',$setting);
    }
    
    
    public function transferstockDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $product_type = $request->input('product_type');
        $from_store = $request->input('from_store');
        $to_store = $request->input('to_store');
        $search1  = $request->input('search1');
    
        // Base query
        $baseQuery = DB::table('tbl_transfer_stock')->where('from_store', $store_id);
    
        if ($product_type) 
        {
            $baseQuery->where('product_type', $product_type);
        }
        
        if ($from_store)
        {
            $baseQuery->where('from_store', $from_store);
        }
        
        if ($to_store) 
        {
            $baseQuery->where('to_store', $to_store);
        }
    
        if ($search1)
        {
            $baseQuery->where(function($q) use ($search1) {
                $q->where('product_details', 'like', "%{$search1}%")
                  ->orWhere('product_code', 'like', "%{$search1}%");
            });
        }
    
        // Count total records before grouping
        $totalData = $baseQuery->count();
    
        // Group by refrence_no with aggregation
        $query = clone $baseQuery;
        $query->selectRaw('
            MAX(transfer_id) as transfer_id,
            refrence_no,
            MAX(transfer_stock) as transfer_stock,
            MAX(transfer_by) as transfer_by,
            MAX(from_store) as from_store,
            MAX(to_store) as to_store,
            MAX(created_at) as created_at,
            SUM(purchase_price) as total_purchase
        ')
        ->groupBy('refrence_no')
        ->orderBy('transfer_id', 'DESC');
    
        // Count total filtered records
        $totalFiltered = $query->get()->count();
    
        
        $templates = $query->offset($start)
                   ->limit($limit)
                   ->get();
    
        $data = [];
        $i = 1;
        foreach ($templates as $template)
        {
            $created_by = User::find($template->transfer_by);
            $from_store = Store::find($template->from_store);
            $to_store   = Store::find($template->to_store);
    
            $encryptedId = base64_encode($template->refrence_no);
            $tCount = DB::table('tbl_transfer_stock')->where('refrence_no', $template->refrence_no)->count();
            $nestedData['sr_no']          = $i++;
            $nestedData['from_store']     = $from_store->store_name ?? '';
            $nestedData['to_store']       = $to_store->store_name ?? '';
            $nestedData['refrence_no']    = $template->refrence_no;
            $nestedData['transfer_stock'] = $tCount;
            $nestedData['total_amount']   =   'Rs ' .number_format($template->total_purchase, 2);
            $nestedData['transfer_by']    = $created_by->name ?? '';
            $nestedData['transfer_date']  = date('d M,Y', strtotime($template->created_at));
            $nestedData['encryptedId']    = $encryptedId;
            $nestedData['action'] = '
                <a href="' . route('admin.stocktransfer-print', ["id" => $encryptedId]) . '" target="_blank">
                    <button type="button" class="btn btn-success btn-sm mb-1">Print</button>
                </a>';
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


    
    public function stocktransferPrint($id)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Stock Transfer invoice';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        
        $refrence_no = DB::table('tbl_transfer_stock')->where('refrence_no', $decryptedId)->first();
        $fromstore= Store::where('id', $refrence_no->from_store)->first();
        $tostore= Store::where('id', $refrence_no->to_store)->first();
        
        $setting['fstate'] = State::find($fromstore->state_id);
        $setting['fcity'] = City::find($fromstore->city_id);
        $setting['tstate'] = State::find($tostore->state_id);
        $setting['tcity'] = City::find($tostore->city_id);
        $setting['refrence_no'] = $refrence_no;
        $setting['fromstore'] = $fromstore;
        $setting['refid'] = $id;
        $setting['tostore'] = $tostore;
        
        return view($this->view_route.'/stock-transfer-invoice',$setting);

        
    }
    
    
    public function stocktransferPdf($id)
    {
        $decryptedId = base64_decode($id);
        $setting['page_title'] = 'Stock Transfer';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        
        $refrence_no = DB::table('tbl_transfer_stock')->where('refrence_no', $decryptedId)->first();
        $fromstore= Store::where('id', $refrence_no->from_store)->first();
        $tostore= Store::where('id', $refrence_no->to_store)->first();
        
        $setting['fstate'] = State::find($fromstore->state_id);
        $setting['fcity'] = City::find($fromstore->city_id);
        $setting['tstate'] = State::find($tostore->state_id);
        $setting['tcity'] = City::find($tostore->city_id);
        $setting['refrence_no'] = $refrence_no;
        $setting['fromstore'] = $fromstore;
        $setting['refid'] = $id;
        $setting['tostore'] = $tostore;
        
        $pdf = Pdf::loadView($this->view_route . '/stock-transfer-pdf',$setting)
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);

        return $pdf->stream($decryptedId . '.pdf');
        

        
    }
    
    
    public function createTransferstock()
    {
        $setting['page_title'] = 'Transfer Stock';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/create-stock-transfer',$setting);
    }
    
    
    public function stockProductList(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $from_store = $request->input('from_store');
        $to_store = $request->input('to_store');
        $product_type = $request->input('product_type');
        $product_code = $request->input('product_code');
        $product_details = $request->input('product_details');
    
        $query = DB::table('tbl_inventory_levels')
            ->where('store_id', $from_store)
            ->where('available_quantity', '>', '0');
    
        if ($product_type) {
            $query->where('product_type', $product_type);
        }
        if ($product_code) {
            $query->where('product_code', $product_code);
        }
        if ($product_details) {
            $query->where('product_details', $product_details);
        }
    
        $results = $query->get();
    
        if ($results->isEmpty()) 
        {
            return response()->json('<div class="alert alert-danger">No data found.</div>');
        }
    
        $data = '';
        $data .= '
        <div class="container">
            <table id="datatable2" class="table card-table table-vcenter text-nowrap" style="color: #000;">
                <thead>
                  <tr>
                    <th style="color: #6b6f80;">Sr.No</th>
                    <th style="color: #6b6f80;">Product Type</th>
                    <th style="color: #6b6f80;">Product Code</th>
                    <th style="color: #6b6f80;">Description</th>
                    <th style="color: #6b6f80;">Available Qty</th>
                    <th style="color: #6b6f80;">Transfer Qty</th>
                    <th style="color: #6b6f80;">Remaining Qty</th>
                    <th style="color: #6b6f80;">Retail Price</th>
                  </tr>
                </thead>
                <tbody>';
        $i = 1;
        foreach ($results as $product) {
            $summary = DB::table('tbl_barcode')
                ->where('product_type', $product->product_type)
                ->where('product_code', $product->product_code)
                ->where('product_details', $product->product_details)
                ->where('store_id', $store_id)
                ->orderBy('id', 'DESC')
                ->first();
    
            $retail_price = $summary->retail_price ?? '0.00';
    
            $data .= '
                <tr> 
                    <td>' . $i++ . '</td>
                    <td>' . e($product->product_type) . '</td>
                    <td>' . e($product->product_code) . '</td>
                    <td>' . e($product->product_details) . '</td>
                    <td><input type="text" class="form-control input" value="' . e($product->available_quantity) . '" name="available_quantity[]" readonly></td>
                    <td><input type="number" class="form-control input transfer-input" value="0" name="transfer_quantity[]"></td>
                    <td><input type="text" class="form-control input" value="' . e($product->available_quantity) . '" name="rem_quantity[]" readonly></td>
                    <td><input type="text" class="form-control input" name="retail_price[]" value="' . e($retail_price) . '" ></td>
                    <input type="hidden" class="form-control input" name="perbox[]" value="' . e($product->perbox) . '" readonly>
                </tr>';
        }
    
        $lastProductId = e($product->id);
    
        $data .= '
                </tbody>
            </table>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                   <div class="form-group">
                        <label>Comment</label>
                        <textarea class="form-control input" id="transfer_comment" name="transfer_comment"></textarea>
                        <input type="hidden" class="form-control input" id="stock_id" name="stock_id" value="' . $lastProductId . '">
                    </div>
                </div>
            </div>
            <button class="btn btn-gradient" id="submittransferBtn" type="button">Transfer</button>
        </div>
    
        <script>
        document.addEventListener("input", function (e) {
            if (e.target && e.target.name === "transfer_quantity[]") {
                let row = e.target.closest("tr");
    
                let availableInput = row.querySelector(\'input[name="available_quantity[]"]\');
                let transferInput = row.querySelector(\'input[name="transfer_quantity[]"]\');
                let remInput = row.querySelector(\'input[name="rem_quantity[]"]\');
    
                let availableQty = parseFloat(availableInput.value) || 0;
                let transferQty = parseFloat(transferInput.value) || 0;
    
                if (transferQty < 0) {
                    alert("Transfer quantity cannot be negative.");
                    transferInput.value = 0;
                    remInput.value = availableQty.toFixed(2);
                    return;
                }
    
                if (transferQty > availableQty) {
                    alert("Transfer quantity cannot exceed available quantity.");
                    transferInput.value = 0;
                    remInput.value = availableQty.toFixed(2);
                    return;
                }
    
                remInput.value = (availableQty - transferQty).toFixed(2);
            }
        });
        </script>';
    
        return response()->json($data);
    }
    
    
    public function stockTransferUpdate(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $user = auth()->user();
        
        DB::beginTransaction();
    
        try 
        {
            $products= $request->input('products');
            $transfer_comment = $request->input('transfer_comment');
            $stock_id= $request->input('stock_id');
            $from_store= $request->input('from_store');
            $to_store= $request->input('to_store');
            $idgenerate = $this->generateUniqueRandomId(8, 'tbl_transfer_stock', 'refrence_no');
            
            foreach ($products as $product) 
            {
                $product = (object) $product;
                // Insert Transfer Stock Details
               
               $transfer_id = DB::table('tbl_transfer_stock')->insertGetId([
                    'product_type'      => $product->product_type,
                    'product_code'      => $product->product_code,
                    'product_details'   => $product->product_details,
                    'retail_price'      => $product->retail_price,
                    'refrence_no'       => $idgenerate,
                    'perbox'            => $product->perbox,
                    'from_store'        => $from_store,
                    'to_store'          => $to_store,
                    'transfer_stock'    => $product->transfer_quantity,
                    'transfer_by'       => $user->id,
                    'transfer_comment'  => $transfer_comment,
                    'store_id'          => $user->store_id,
                    'created_at'        => now(),
                    'updated_at'        => now()
                ]);
            
            
                // Update Stock Inventory
                
                $update_inventory =  DB::table('tbl_inventory_levels')->where('id', $stock_id)->update([
                    'available_quantity'      => $product->rem_quantity,
                    'updated_at' => now()
                ]);
                

                $inventory = DB::table('tbl_inventory_levels')
                    ->where('product_type', $product->product_type)
                    ->where('product_code', $product->product_code)
                    ->where('product_details', $product->product_details)
                    ->where('perbox', $product->perbox)
                    ->where('store_id', $to_store)
                    ->first();
    
                $qty = (int)($product->transfer_quantity ?? 0);
    
                if ($inventory) {
                    DB::table('tbl_inventory_levels')
                        ->where('id', $inventory->id)
                        ->update([
                            'available_quantity'      => $inventory->available_quantity + $qty,
                            'updated_at' => now()
                        ]);
                } else {
                    DB::table('tbl_inventory_levels')->insert([
                        'product_code' => $product->product_code,
                        'product_type' => $product->product_type,
                        'product_details' => $product->product_details,
                        'perbox' => $product->perbox,
                        'store_id'     => $to_store,
                        'available_quantity'        => $qty,
                        'created_at'   => now(),
                        'updated_at'   => now()
                    ]);
                }
                
                
                // Transfer Barcode
                for ($j = 0; $j < $product->transfer_quantity; $j++) 
                {
                    $tbarcode = DB::table('tbl_barcode')
                    ->where('product_type', $product->product_type)
                    ->where('product_code', $product->product_code)
                    ->where('product_details', $product->product_details)
                    ->where('store_id', $from_store)
                    ->where('refrence_type', 1)
                    ->first();
                    
                    if(empty($tbarcode))
                    {
                        return response()->json(['success' => 'Barcode Not Found!']);
                    }
                    
                   $transfer_barcode =  DB::table('tbl_barcode')->where('id', $tbarcode->id)->update([
                        'retail_price'      => $product->retail_price,
                        'store_id'      => $to_store,
                        'refrence_no'      => $idgenerate,
                        'updated_at' => now()
                    ]); 
                }
            }    
        
            DB::commit();
            return response()->json(['success' => 'Stock transfer saved successfully!']);
		} 
		catch (\Exception $e) 
		{
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the stock transfer process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function barcodeTransfer()
    {
        $setting['page_title'] = 'Transfer Stock Using Barcode';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/barcode-transfer',$setting);
    }
    
    public function generateUniqueRandomId($length = 8, $table = 'tbl_transfer_stock', $column = 'refrence_no', $min = 100000, $max = 999999)
    {
        do {
            $id = 'ST'.random_int($min, $max);
        } while (
            DB::table($table)->where($column, $id)->exists()
        );
    
        return $id;
    }
    
    
    public function checkBarcode(Request $request)
    {
        $barcode = trim($request->barcode);
        $fromStore = $request->from_store;
        $audit = $request->audit;
        
        if(!empty($audit))
        {
            $product_type = $request->product_type;
            $store_id = auth()->user()->store_id;
            
            $exists = DB::table('tbl_barcode')
            ->where('barcode_no', $barcode)
            ->where('product_type', $product_type)
            ->where('store_id', $store_id)
            ->first();
            return response()->json(['valid' => $exists ? true : false]);
        }
        else
        {
            $exists = DB::table('tbl_barcode')
                ->where('barcode_no', $barcode)
                ->where('store_id', $fromStore)
                ->where('t_status','0')
                ->whereNull('outward_status')
                ->first();
            
            if (!$exists) 
            {
                $exists = DB::table('tbl_barcode')
                ->where('barcode_no', $barcode)
                ->where('transfer_store', $fromStore)
                ->whereNull('transfer_outward_status')
                ->first();
            }
            return response()->json(['valid' => $exists ? true : false]);
        }
    }
    
    
    public function getBarcodeDetails(Request $request)
    {
        $barcodes = $request->barcodes ?? [];

        if (empty($barcodes)) {
            return response()->json(['success' => false, 'data' => []]);
        }
    
        $results = DB::table('tbl_barcode')
            ->whereIn('barcode_no', $barcodes)
            ->get();
    
        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
    
    
    public function confirmTransfer(Request $request)
    {
        $user = auth()->user();
        $from_store = $request->from_store;
        $to_store = $request->to_store;
        $comment = $request->comment;
        $items = $request->items;
    
        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'No items.']);
        }
    
        // Dispatch job to queue
        ProcessStockTransfer::dispatch($user, $from_store, $to_store, $comment, $items);
    
        return response()->json([
            'success' => true,
            'message' => 'Stock transfer is being processed in the background.'
        ]);
    }

    

    public function stockReceivedStore()
    {
        $setting['page_title'] = 'Recevied Stock From Store';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/stock-recevied-store',$setting);
    }
    
    
    
    public function receviedstockstoreDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit    = $request->input('length');
        $start    = $request->input('start');
        $product_type = $request->input('product_type');
        $from_store = $request->input('from_store');
        $to_store = $request->input('to_store');
        $search1  = $request->input('search1');
    
        // Base query
        $baseQuery = DB::table('tbl_transfer_stock')
            ->where('to_store', $store_id);
    
        if ($product_type) {
            $baseQuery->where('product_type', $product_type);
        }
        
        if ($from_store) {
            $baseQuery->where('from_store', $from_store);
        }
        
        if ($to_store) {
            $baseQuery->where('to_store', $to_store);
        }
    
        if ($search1) {
            $baseQuery->where(function($q) use ($search1) {
                $q->where('product_details', 'like', "%{$search1}%")
                  ->orWhere('product_code', 'like', "%{$search1}%");
            });
        }
    
        // Count total records before grouping
        $totalData = $baseQuery->count();
    
        // Group by refrence_no with aggregation
        $query = clone $baseQuery;
        $query->selectRaw('
            MAX(transfer_id) as transfer_id,
            refrence_no,
            MAX(transfer_stock) as transfer_stock,
            MAX(transfer_by) as transfer_by,
            MAX(from_store) as from_store,
            MAX(to_store) as to_store,
            MAX(created_at) as created_at,
            SUM(purchase_price) as total_purchase
        ')
        ->groupBy('refrence_no')
        ->orderBy('transfer_id', 'DESC');
    
        // Count total filtered records
        $totalFiltered = $query->get()->count();
    
        // Apply pagination
        $templates = $query->offset($start)
                           ->limit($limit)
                           ->get();
    
        $data = [];
        $i = 1;
        foreach ($templates as $template)
        {
            $created_by = User::find($template->transfer_by);
            $from_store = Store::find($template->from_store);
            $to_store   = Store::find($template->to_store);
    
            $encryptedId = base64_encode($template->refrence_no);
            $tCount = DB::table('tbl_transfer_stock')->where('refrence_no', $template->refrence_no)->count();
            $nestedData['sr_no']          = $i++;
            $nestedData['from_store']     = $from_store->store_name ?? '';
            $nestedData['to_store']       = $to_store->store_name ?? '';
            $nestedData['refrence_no']    = $template->refrence_no;
            $nestedData['transfer_stock'] = $tCount;
            $nestedData['total_amount'] =   'Rs ' .number_format($template->total_purchase, 2);
            $nestedData['transfer_by']    = $created_by->name ?? '';
            $nestedData['transfer_date']  = date('d M,Y', strtotime($template->created_at));
            $nestedData['encryptedId']    = $encryptedId;
            $nestedData['action'] = '
                <a href="' . route('admin.stocktransfer-print', ["id" => $encryptedId]) . '" target="_blank">
                    <button type="button" class="btn btn-success btn-sm mb-1">Print</button>
                </a>';
            $data[] = $nestedData;
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }
    
    public function inventoryAudit()
    {
        $setting['page_title'] = 'Inventory Audit';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/inventory-audit',$setting);
    }
    
    
    public function auditDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $product_type= $request->input('product_type');
        $storeid = $request->input('store_id');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        
        if($store_id == '0')
        {
            $totalData = DB::table('tbl_inventory_audit');
        }
        else
        {
            $totalData = DB::table('tbl_inventory_audit')->where('store_id', $store_id);
        }
        
        if ($product_type != '')
        {
            $totalData->where('product_type', [$product_type]);
        }
        if ($storeid != '')
        {
            $totalData->where('store_id', $storeid);
        }
        if ($date_from && $date_to) 
        {
            $totalData->whereBetween('created_at', [
                Carbon::parse($date_from)->startOfDay(), 
                Carbon::parse($date_to)->endOfDay(),     
            ]);
        }

        $totalData = $totalData->count();
        
        if($store_id == '0')
        {
            $templates = DB::table('tbl_inventory_audit');
        }
        else
        {
            $templates = DB::table('tbl_inventory_audit')->where('store_id', $store_id);
        }
        if ($storeid != '')
        {
            $templates->where('store_id', $storeid);
        }
        if ($product_type != '') 
        {
           $templates->where('product_type', [$product_type]);
        }
        
        if ($date_from && $date_to) 
        {
            $templates->whereBetween('created_at', [
                Carbon::parse($date_from)->startOfDay(), 
                Carbon::parse($date_to)->endOfDay(),     
            ]);
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
                if($template->audit_status == '0')
                {
                    $bstatus = '<span class="badge badge-info"> In Process</span>';
                }elseif($template->audit_status == '1')
                {
                    $bstatus = '<span class="badge badge-success">Completed </span>';
                }
                
                $encryptedId = base64_encode($template->id);
                $receive_store = Store::find($template->store_id);
                $nestedData['store_name'] = $receive_store->store_name;
                $nestedData['product_type'] = $template->product_type;
                $nestedData['company'] = $template->company;
                $nestedData['total_upload']     = '<a href="' . route('admin.audit-excel', ["audit_id" => $template->id, 'status' => 'UPLOAD']) . '">'.$template->total_upload.'</a>';
                $nestedData['match_barcode']    = '<a href="' . route('admin.audit-excel', ["audit_id" => $template->id, 'status' => 'MATCH']) . '">'.$template->match_barcode.'</a>';
                $nestedData['invalid_barcode']  = '<a href="' . route('admin.audit-excel', ["audit_id" => $template->id, 'status' => 'INALID']) . '">'.$template->invalid_barcode.'</a>';
                $nestedData['missing_barcode']  = '<a href="' . route('admin.audit-excel', ["audit_id" => $template->id, 'status' => 'MISSING']) . '">'.$template->missing_barcode.'</a>';
                $nestedData['created_at']    = $template->created_at;
                $nestedData['audit_status']  = $bstatus;
                $nestedData['encryptedId']   = $encryptedId;
                
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
    
    public function addAuditRecord(Request $request)
    {
        try {
            $request->validate([
                'auditbarcodes' => 'required|string',
                'product'       => 'required|string',
                'company'       => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        }

        $user = auth()->user();
        $store_id = $user->store_id;

        if (!$store_id) {
            return response()->json([
                'success' => false,
                'message' => 'User does not have a store assigned.'
            ]);
        }

        $product_type = $request->product;
        $company = $request->company;

        $barcodeArray = json_decode($request->auditbarcodes, true);
        $total_barcode = count($barcodeArray ?? []);

        if ($total_barcode === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No valid barcodes provided.'
            ]);
        }

        $audit_id = DB::table('tbl_inventory_audit')->insertGetId([
            'store_id'        => $store_id,
            'product_type'    => $product_type,
            'company'         => $company,
            'total_upload'    => $total_barcode,
            'match_barcode'   => 0,
            'invalid_barcode' => 0,
            'missing_barcode' => 0,
            'audit_status'    => 0, // 0 = processing
            'added_by'        => $user->id,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        ProcessAudit::dispatch($user, $store_id, $product_type, $company, $barcodeArray, $audit_id);

        return response()->json([
            'success' => true,
            'message' => 'Audit is being processed. You will be notified when completed.',
            'audit_id' => $audit_id
        ]);
    }
    


    public function auditExcel($audit_id, $status = "")
    {
        if (!$audit_id || $status === '') 
        {
            return back()->with('error', 'Invalid request');
        }
    
        try 
        {
            ini_set('memory_limit', '2048M');
            set_time_limit(600);
    
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
    
            $headings = ["S.No.", "Barcode", "Product Type", "Company", "Product Code", "Description",
                         "Purchase Price", "Sales Price", "B2B Price", "Store Name", "Status", "Audit Datetime"];
            $sheet->fromArray($headings, null, 'A1');
    
            $row = 2;
            $counter = 1;
    
            $bstatus = match (strtoupper($status)) 
            {
                'MATCH' => [0],
                'INVALID' => [1],
                'MISSING' => [2],
                'UPLOAD' => [0, 1, 2],
                default => [0, 1, 2],
            };
    
            $audittable = DB::table('tbl_inventory_audit')->where('id', $audit_id)->first();
            $receive_store = Store::find($audittable->store_id);
    
            DB::table('tbl_inventory_audit_barcode')
                ->where('audit_id', $audit_id)
                ->whereIn('status', $bstatus)
                ->orderBy('id', 'ASC')
                ->chunk(1000, function ($barcodeaudits) use (&$row, $sheet, &$counter, $audittable, $receive_store, $status) {
    
                    $barcodes = $barcodeaudits->pluck('barcode')->toArray();
                    $barcodeData = DB::table('tbl_barcode')
                        ->where('store_id', $audittable->store_id)
                        ->where('product_type', $audittable->product_type)
                        ->whereIn('barcode_no', $barcodes)
                        ->get()
                        ->keyBy('barcode_no');
    
                    foreach ($barcodeaudits as $barcodeaudit) 
                    {
                        $tbl_barcode = $barcodeData[$barcodeaudit->barcode] ?? null;
    
                        $sheet->fromArray([
                            $counter++,
                            $barcodeaudit->barcode,
                            $audittable->product_type,
                            $audittable->company ?? '',
                            $tbl_barcode->product_code ?? '',
                            $tbl_barcode->product_details ?? '',
                            $tbl_barcode->purchase_price ?? '',
                            $tbl_barcode->retail_price ?? '',
                            '',
                            $receive_store->store_name ?? '',
                            $status,
                            $audittable->created_at
                        ], null, 'A' . $row++);
                    }
                });
    
            foreach (range('A', 'L') as $columnID)
            {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
    
            $headerStyle = $sheet->getStyle('A1:L1');
            $headerStyle->getFont()->setBold(true);
            $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFEFEFEF');
            $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
            $fileName = 'AUDIT_REPORT_' . now()->format('Ymd_His') . '.xlsx';
            $writer = new Xlsx($spreadsheet);
    
            return response()->streamDownload(function () use ($writer)
            {
                $writer->save('php://output');
            }, $fileName);
    
        } catch (\Exception $e) {
            \Log::error('Audit Excel download failed', [
                'audit_id' => $audit_id,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Export failed. Please try again.');
        }
    }



    public function stockMovement()
    {
        $setting['page_title'] = 'Stock Movement';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/stock-movement',$setting);
    }
    
    
    
    public function stockmovementDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $product_type= $request->input('product_type');
        $storeid = $request->input('store_id');
        $search1 = $request->input('search1');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        if($store_id == '0')
        {
            $totalData = DB::table('tbl_inventory_levels');
        }
        else
        {
            $totalData = DB::table('tbl_inventory_levels')->where('store_id', $store_id);
        }
        if ($product_type != '')
        {
            $totalData->where('product_type', [$product_type]);
        }
        if ($storeid != '')
        {
            $totalData->where('store_id', [$store_id]);
        }
        if ($search1 != '') 
        {
            $totalData->where('productdetails', 'like', '%' . $search1 . '%')
            ->orWhere('product_code', 'like', '%' . $search1 . '%');
        }
        $totalData = $totalData->count();
        
        if($store_id == '0')
        {
            $templates = DB::table('tbl_inventory_levels');
        }
        else
        {
            $templates = DB::table('tbl_inventory_levels')->where('store_id', $store_id);
        }
        if ($storeid != '')
        {
            $templates->where('store_id', [$store_id]);
        }

        if ($product_type != '') 
        {
           $templates->where('product_type', [$product_type]);
        }
        if ($search1 != '') 
        {
            $templates->where('productdetails', 'like', '%' . $search1 . '%')
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
                // Calculate inward quantities
               
                
                $total_purchase =  DB::table('tbl_inventory_record')
                    ->where('product_code', $template->product_code)
                    ->where('product_type', $template->product_type)
                    ->where('product_details', $template->product_details)
                    ->where('store_id', $template->store_id)
                    ->when(!empty($template->perbox), function ($query) use ($template) {
                        $query->where('perbox', $template->perbox);
                    })
                    ->whereBetween('added_date', [$date_from, $date_to])
                    ->where('inward_status', 0)
                    ->sum('qty');
                
                
                $total_manually = DB::table('tbl_inventory_record')
                    ->where('product_code', $template->product_code)
                    ->where('product_type', $template->product_type)
                    ->where('product_details', $template->product_details)
                    ->where('store_id', $template->store_id)
                    ->when(!empty($template->perbox), function ($query) use ($template) {
                        $query->where('perbox', $template->perbox);
                    })
                    ->whereBetween('added_date', [$date_from, $date_to])
                    ->where('inward_status', 1)
                    ->sum('qty');
                    
                $total_challan = DB::table('tbl_inventory_record')
                    ->where('product_code', $template->product_code)
                    ->where('product_type', $template->product_type)
                    ->where('product_details', $template->product_details)
                    ->where('store_id', $template->store_id)
                    ->when(!empty($template->perbox), function ($query) use ($template) {
                        $query->where('perbox', $template->perbox);
                    })
                    ->whereBetween('added_date', [$date_from, $date_to])
                    ->where('inward_status', 2)
                    ->sum('qty');    
                
                
                $total_import = DB::table('tbl_inventory_record')
                    ->where('product_code', $template->product_code)
                    ->where('product_type', $template->product_type)
                    ->where('product_details', $template->product_details)
                    ->where('store_id', $template->store_id)
                    ->when(!empty($template->perbox), function ($query) use ($template) {
                        $query->where('perbox', $template->perbox);
                    })
                    ->whereBetween('added_date', [$date_from, $date_to])
                    ->where('inward_status', 3)
                    ->sum('qty');
                
                
                $total_transfer_in = DB::table('tbl_inventory_record')
                    ->where('product_code', $template->product_code)
                    ->where('product_type', $template->product_type)
                    ->where('product_details', $template->product_details)
                    ->where('store_id', $template->store_id)
                    ->when(!empty($template->perbox), function ($query) use ($template) {
                        $query->where('perbox', $template->perbox);
                    })
                    ->whereBetween('added_date', [$date_from, $date_to])
                    ->where('inward_status', 4)
                    ->sum('qty');
   
                
                // Build inward HTML
                $inward = '<strong>Imported : </strong> '.$total_import.' <br>
                           <strong>Manually : </strong> '.$total_manually.' <br>
                           <strong>Challan : </strong> '.$total_challan.' <br>
                           <strong>Purchase : </strong> '.$total_purchase.' <br>
                           <strong>Transfer Stock : </strong>'.$total_transfer_in;
                
                // Calculate outward quantities
                $total_sold= DB::table('tbl_inventory_record')
                    ->where('product_code', $template->product_code)
                    ->where('product_type', $template->product_type)
                    ->where('product_details', $template->product_details)
                    ->where('store_id', $template->store_id)
                    ->when(!empty($template->perbox), function ($query) use ($template) {
                        $query->where('perbox', $template->perbox);
                    })
                    ->whereBetween('added_date', [$date_from, $date_to])
                    ->where('outward_status', 0)
                    ->sum('qty');
                
                $total_adjust= DB::table('tbl_inventory_record')
                    ->where('product_code', $template->product_code)
                    ->where('product_type', $template->product_type)
                    ->where('product_details', $template->product_details)
                    ->where('store_id', $template->store_id)
                    ->when(!empty($template->perbox), function ($query) use ($template) {
                        $query->where('perbox', $template->perbox);
                    })
                    ->whereBetween('added_date', [$date_from, $date_to])
                    ->where('outward_status', 1)
                    ->sum('qty');
                
                $total_return= DB::table('tbl_inventory_record')
                    ->where('product_code', $template->product_code)
                    ->where('product_type', $template->product_type)
                    ->where('product_details', $template->product_details)
                    ->where('store_id', $template->store_id)
                    ->when(!empty($template->perbox), function ($query) use ($template) {
                        $query->where('perbox', $template->perbox);
                    })
                    ->whereBetween('added_date', [$date_from, $date_to])
                    ->where('outward_status', 2)
                    ->sum('qty');
                    
                 $total_delete= DB::table('tbl_inventory_record')
                    ->where('product_code', $template->product_code)
                    ->where('product_type', $template->product_type)
                    ->where('product_details', $template->product_details)
                    ->where('store_id', $template->store_id)
                    ->when(!empty($template->perbox), function ($query) use ($template) {
                        $query->where('perbox', $template->perbox);
                    })
                    ->whereBetween('added_date', [$date_from, $date_to])
                    ->where('outward_status', 3)
                    ->sum('qty'); 
                    
                $total_transfer_out= DB::table('tbl_inventory_record')
                    ->where('product_code', $template->product_code)
                    ->where('product_type', $template->product_type)
                    ->where('product_details', $template->product_details)
                    ->where('store_id', $template->store_id)
                    ->when(!empty($template->perbox), function ($query) use ($template) {
                        $query->where('perbox', $template->perbox);
                    })
                    ->whereBetween('added_date', [$date_from, $date_to])
                    ->where('outward_status', 4)
                    ->sum('qty');     
                
                // Build outward HTML
                $outward = '<strong>Sold : </strong> '.$total_sold.' <br>
                            <strong>Adjust : </strong> '.$total_adjust.' <br>
                            <strong>Purchase Return : </strong> '.$total_return.' <br>
                            <strong>Deleted : </strong> '.$total_delete.' <br>
                            <strong>Transfer Stock : </strong> '.$total_transfer_out.' <br>';
                
                // Calculate totals
                $total_inward =  $total_purchase+$total_manually+$total_import+$total_transfer_in+$total_challan; // add other inward if needed
                $total_outward = $total_sold + $total_adjust + $total_return+ $total_delete+ $total_transfer_out; // add other outward if needed
                
                $balance = $total_inward - $total_outward;
                
                $encryptedId = base64_encode($template->id);
                $receive_store = Store::find($template->store_id);
                $nestedData['store_name'] = $receive_store->store_name;
                $nestedData['description'] = 'Product :'.$template->product_type.'<Br> Product Code : '.$template->product_code.'<Br> Description : '.$template->product_details;
                $nestedData['inward'] = $inward;
                $nestedData['outward'] = $outward;
                $nestedData['total_inward'] = $total_inward;
                $nestedData['total_outward'] = $total_outward;
                $nestedData['balance'] = $balance;
                $nestedData['encryptedId'] = $encryptedId;
                $nestedData['product_type_f'] = $product_type;
                $nestedData['store_id_f'] = $storeid;
                $nestedData['search_f'] = $search1;
                $nestedData['date_from_f'] = $date_from;
                $nestedData['date_to_f'] = $date_to;
                $nestedData['product_code'] = $template->product_code;
                $nestedData['product_type'] = $template->product_type;
                $nestedData['product_details'] = $template->product_details;
                $nestedData['store_id'] = $template->store_id;
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
    
    
    public function getStockdetails(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $store_id = $request->input('store_id');
        $product_type = $request->input('product_type');
        $product_code = $request->input('product_code');
        $product_details = $request->input('product_details');
    
        $query = DB::table('tbl_barcode')
            ->where('store_id', $store_id)
            ->where('product_type', $product_type)
            ->where('product_code', $product_code)
            ->where('product_details', $product_details)
            ->whereBetween('purchase_date', [$date_from, $date_to])
            ->orderBy('id', 'desc');
    
        $products = $query->get(['barcode_no', 'inward_status', 'outward_status']);
    
        $inwardStatusMap = [
            '0' => '<span class="badge badge-dark">Purchase</span>',
            '1' => '<span class="badge badge-dark">Manually</span>',
            '2' => '<span class="badge badge-dark">Challan</span>',
            '3' => '<span class="badge badge-dark">Imported</span>',
            '4' => '<span class="badge badge-dark">Received Store</span>',
        ];
    
        $outwardStatusMap = [
            '0' => '<span class="badge badge-info">Sold</span>',
            '1' => '<span class="badge badge-info">Adjust</span>',
            '2' => '<span class="badge badge-info">Return</span>',
            '3' => '<span class="badge badge-info">Deleted</span>',
            '4' => '<span class="badge badge-info">Transfer Store</span>',
        ];
    
        return response()->json([
            'data' => $products->map(function ($p) use ($inwardStatusMap, $outwardStatusMap) {
                return [
                    'barcode_no' => $p->barcode_no,
                    'inward_status' => $inwardStatusMap[$p->inward_status] ?? '<span class="badge badge-secondary">Unknown</span>',
                    'outward_status' => $outwardStatusMap[$p->outward_status] ?? '<span class="badge badge-secondary">Available</span>',
                ];
            }),
        ]);
    }


    public function addInventory()
    {
        $setting['page_title'] = 'Inventory Add';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/add-inventory',$setting);
    }
    
    
    
    public function addInventoryRecord(Request $request)
    {
        $user = auth()->user();

        
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|string|max:255',
            'product_type'     => 'required|string|max:255',
            'tax_rule'     => 'required|string|max:255',
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
            

            if($data['product_type'] == 'Frame' || $data['product_type'] == 'Goggles')
            {
                $fields = [
                    $data['modal_frame_name'] ?? null,
                    $data['modal_frame_company'] ?? null,
                    $data['modal_frame_quality'] ?? null,
                ];
                
                $filteredFields = array_filter($fields, function ($value) {
                    return !empty($value); 
                });
                
                $product_details = implode(' - ', $filteredFields);
                $product_qty =  $data['modal_frame_qty'];
                
                $product_name =  $data['modal_frame_name'];
                $product_company =  $data['modal_frame_company'];
                $product_quality =  $data['modal_frame_quality'];
                
                $product_color =  '';
                $product_material =  '';
                $product_coating =  '';
                $product_design =  '';
                $product_index =  '';
                $product_sph =  '';
                $product_cyl =  '';
                $product_addition =  '';
                $product_axis =  '';
                $product_bc =  '';
                $product_diameter =  '';
                $product_type =  '';
                $product_batch =  '';
                $product_mfg =  '';
                $product_expiry =  '';
                $product_noofbox =  '';
                $product_perbox =  '';
                $product_invoice =  '';
                $product_validity= '';
                $product_variant =  '';
                $product_ct =  '';
                $product_number = '';
                
                
            }
            else if($data['product_type'] == 'Glass')
            {
                $fields = [
                    trim($data['modal_glass_details'] ?? ''),
                    trim($data['modal_glass_company'] ?? ''),
                    trim($data['modal_glass_color'] ?? ''),
                    trim($data['modal_glass_Material'] ?? ''),
                    trim($data['modal_glass_Coating'] ?? ''),
                    trim($data['modal_glass_Design'] ?? ''),
                    trim($data['modal_glass_Index'] ?? ''),
                    trim($data['modal_glass_quality'] ?? ''),
                    !empty($data['modal_glass_SPH']) ? 'SPH:' . trim($data['modal_glass_SPH']) : '',
                    !empty($data['modal_glass_CYL']) ? 'CYL:' . trim($data['modal_glass_CYL']) : '',
                    !empty($data['modal_glass_Addition']) ? 'ADD:' . trim($data['modal_glass_Addition']) : '',
                    !empty($data['modal_glass_Axis']) ? 'Axis:' . trim($data['modal_glass_Axis']) : '',
                ];
                
                $filteredFields = array_filter($fields, function ($value) {
                    return !empty($value) && trim($value) !== '';
                });
                
                $combined = implode(' - ', $filteredFields);
                
                $product_details = implode(' - ', $filteredFields); 

                $product_qty =  $data['modal_glass_qty'];
                
                $product_name =  $data['modal_glass_details'];
                $product_company =  $data['modal_glass_company'];
                $product_quality =  $data['modal_glass_quality'];
                
                $product_color =  $data['modal_glass_color'];
                $product_material =  $data['modal_glass_Material'];
                $product_coating =  $data['modal_glass_Coating'];
                $product_design =  $data['modal_glass_Design'];
                $product_index =  $data['modal_glass_Index'];
                $product_sph =  $data['modal_glass_SPH'];
                $product_cyl =  $data['modal_glass_CYL'];
                $product_addition =  $data['modal_glass_Addition'];
                $product_axis =  $data['modal_glass_Axis'];
                
                $product_bc =  '';
                $product_diameter =  '';
                $product_type =  '';
                $product_batch =  '';
                $product_mfg =  '';
                $product_expiry =  '';
                $product_noofbox =  '';
                $product_perbox =  '';
                $product_invoice =  '';
                $product_variant =  '';
                $product_validity = '';
                $product_ct =  '';
                $product_number = '';
            }
            else if($data['product_type'] == 'Lens')
            {
                $fields = [
                    trim($data['modal_lens_product_name'] ?? ''),
                    trim($data['modal_lens_company'] ?? ''),
                    trim($data['modal_lens_quality'] ?? ''),
                    trim($data['modal_lens_type'] ?? ''),
                    trim($data['modal_lens_color'] ?? ''),
                    trim($data['modal_lens_number'] ?? ''),
                    trim($data['modal_lens_tc'] ?? ''),
                    trim($data['modal_lens_Materials'] ?? ''),
                    trim($data['modal_lens_validity'] ?? ''),
                    !empty($data['modal_lens_sph']) ? 'SPH:' . trim($data['modal_lens_sph']) : '',
                    !empty($data['modal_lens_cyl']) ? 'CYL:' . trim($data['modal_lens_cyl']) : '',
                    !empty($data['modal_lens_addition']) ? 'ADD:' . trim($data['modal_lens_addition']) : '',
                    !empty($data['modal_lens_axis']) ? 'Axis:' . trim($data['modal_lens_axis']) : '',
                    trim($data['modal_lens_bc'] ?? ''),
                    trim($data['modal_lens_diameter'] ?? ''),
                    trim($data['modal_lens_powertype'] ?? ''),
                ];
                
                $filteredFields = array_filter($fields, function ($value) {
                    return !empty($value) && trim($value) !== '';
                });
                
                $combined = implode(' - ', $filteredFields);
                
                $product_details = implode(' - ', $filteredFields); 

                $product_qty =  $data['modal_noofbox'];
                
                $product_name =  $data['modal_lens_product_name'];
                $product_company =  $data['modal_lens_company'];
                $product_quality =  $data['modal_lens_quality'];
                
                $product_color =  $data['modal_lens_color'];
                $product_material =  $data['modal_lens_Materials'];
                $product_coating =  '';
                $product_design =  '';
                $product_index =  '';
                $product_sph =  $data['modal_lens_sph'];
                $product_cyl =  $data['modal_lens_cyl'];
                $product_addition =  $data['modal_lens_addition'];
                $product_axis =  $data['modal_lens_axis'];
                
                $product_bc =  $data['modal_lens_bc'];
                $product_diameter =  $data['#modal_lens_diameter'];
                $product_type =  $data['modal_lens_powertype'];
                $product_batch =  $data['modal_lens_batch'];
                $product_mfg =  $data['modal_lens_mfg'];
                $product_expiry =  $data['modal_lens_expiry'];
                $product_noofbox =  $data['modal_noofbox'];
                $product_perbox =  $data['modal_perbox'];
                $product_invoice =  $data['modal_lens_invoicedescription'];
                $product_validity =  $data['modal_lens_validity'];
                $product_ct =   $data['modal_lens_tc'];
                $product_number =   $data['modal_lens_number'];
                $product_variant =  '';
                
            }
            else if($data['product_type'] == 'Solution')
            {
                $fields = [
                    $data['modal_solution_product_name'] ?? null,
                    $data['modal_solution_company'] ?? null,
                    $data['modal_solution_quality'] ?? null,
                    $data['modal_solution_Variant'] ?? null,
                    $data['modal_solution_packingtype"'] ?? null,
                    $data['modal_solution_color"'] ?? null,
                ];
                
                $filteredFields = array_filter($fields, function ($value) {
                    return !empty($value); 
                });
                
                $product_details = implode(' - ', $filteredFields);
                $product_qty =  $data['modal_solution_quantity'];
                
                $product_name =  $data['modal_solution_product_name'];
                $product_company =  $data['modal_solution_company'];
                $product_quality =  $data['modal_solution_quality'];
                
                $product_color =  $data['modal_solution_color'];
                $product_material =  '';
                $product_coating =  '';
                $product_design =  '';
                $product_index =  '';
                $product_sph =  '';
                $product_cyl =  '';
                $product_addition =  '';
                $product_axis =  '';
                $product_bc =  '';
                $product_diameter =  '';
                $product_type =  $data['modal_solution_packingtype'];
                $product_batch =  '';
                $product_mfg =  '';
                $product_expiry =  '';
                $product_noofbox =  '';
                $product_perbox =  '';
                $product_invoice =  $data['modal_solution_invoicedescription'];
                $product_variant =  $data['modal_solution_Variant'];
                $product_validity = '';
                 $product_ct =  '';
                 $product_number = '';
            }
            else if($data['product_type'] == 'Other')
            {
                $fields = [
                    $data['modal_other_product_name'] ?? null,
                    $data['modal_other_company'] ?? null,
                    $data['modal_other_color'] ?? null,
                    $data['modal_other_type'] ?? null,
                    $data['modal_other_shape"'] ?? null,
                    $data['modal_other_size"'] ?? null,
                    $data['modal_other_quality"'] ?? null,
                ];
                
                $filteredFields = array_filter($fields, function ($value) {
                    return !empty($value); 
                });
                
                $product_details = implode(' - ', $filteredFields);
                $product_qty =  $data['modal_other_quantity'];
                
                $product_name =  $data['modal_other_product_name'];
                $product_company =  $data['modal_other_company'];
                $product_quality =  $data['modal_other_quality'];
                
                $product_color =  $data['modal_other_color'];
                $product_material =  '';
                $product_coating =  '';
                $product_design =  '';
                $product_index =  '';
                $product_sph =  '';
                $product_cyl =  '';
                $product_addition =  '';
                $product_axis =  '';
                $product_bc =  '';
                $product_diameter =  '';
                $product_type =  $data['modal_other_type'];
                $product_batch =  '';
                $product_mfg =  '';
                $product_expiry =  '';
                $product_noofbox =  '';
                $product_perbox =  '';
                $product_invoice =  $data['modal_other_invoicedescription'];
                $product_variant =  '';
                $product_shape =  $data['modal_other_shape'];
                $product_size =  $data['modal_other_size'];
                $product_validity = '';
                $product_ct =  '';
                $product_number = '';
            }
            

            
            $tCount = DB::table('tbl_product_code')
                         ->where('product_type', $data['product_type'])
                         ->where('product_code', $data['modal_product_code'])
                         ->where('productdetails', $product_details)->count();
           
            if($tCount == 0)
            {
                
                $idgenerate = $this->generateUniqueRandomIdProduct(6, 'tbl_product_code', 'product_id');
                if($PCount == 0)
                {
                    $product_id = $idgenerate;
                }
                else
                {
                    $tbl_product_code = DB::table('tbl_product_code')->where('product_code', $data['modal_product_code'])->first();
                    $product_id = $tbl_product_code->product_id;
                }
                $Product = Product::create([
                    'product_type'         => $data['product_type'],
                    'product_code'         => $data['modal_product_code'],
                    'product_id'           => $product_id,
                    'productdetails'       => $product_details,
                    'product_name'         => $product_name ?? '',
                    'Company'              => $product_company ?? '',
                    'Quality'              => $product_quality ?? '',
                    'Color'                => $product_color ?? '',
                    'Type'                 => $product_type ?? '',
                    'Material'             => $product_material ?? '',
                    'Coating'              => $product_coating ?? '',
                    'Design'               => $product_design ?? '',
                    'Index'                => $product_index ?? '',
                    'SPH'                  => $product_sph ?? '',
                    'CYL'                  => $product_cyl ?? '',
                    'AXIS'                 => $product_axis ?? '',
                    'ADD'                  => $product_addition ?? '',
                    'Number'               => $product_number?? '',
                    'CT'                   => $product_ct ?? '',
                    'Validity'             => $product_validity ?? '',
                    'Variant'              => $product_variant ?? '',
                    'Shape'                => $product_shape ?? '',
                    'Size'                 => $product_size ?? '',
                    'base_carve'           => $product_bc ?? '',
                    'Diameter'             => $product_diameter ?? '',
                    'No_Of_Boxes'          => $product_noofbox ?? '',
                    'Pieces_Per_Box'       => $product_perbox ?? '',
                    'Batch_Number'         => $product_batch ?? '',
                    'Mfg_Date'             => $product_mfg ?? '',
                    'Expiry_Date'          => $product_expiry ?? '',
                    'Track_Inventory'      => $data['modal_Track_Inventory'] ?? '',
                    'Allow_Negative_Inventory'        => $data['modal_Negative_Inventory'] ?? '',
                    'Purchase_Base_Price'   => (float)($data['modal_basic_price'] ?? 0),
                    'Purchase_Price'        => (float)($data['modal_total_price'] ?? 0),
                    'Retail_Price'          => (float)($data['modal_retail_price'] ?? 0),
                    'store_id'              => $data['store_id'],
                    'added_by'              => $user->id,
                    'hsn_code'              => $data['modal_hsn_code'] ?? '',
                    'tax_per'               => $data['modal_gst'] ?? '',
                    'add_tax_per'           => $data['modal_gst_amount'] ?? '',
                    'tax_rule'              => $data['tax_rule'] ?? '',
                ]);
                
                $PCount = DB::table('tbl_product_code')
                ->where('product_code', $data['modal_product_code'])->count();
                
                
        
            }
            else
            {
                $PCount = DB::table('tbl_product_code')
                ->where('product_code', $data['modal_product_code'])->count();
                
                if($PCount == 0)
                {
                    $product_id = $data['modal_product_id'];
                }
                else
                {
                    $tbl_product_code = DB::table('tbl_product_code')->where('product_code', $data['modal_product_code'])->first();
                    $product_id = $tbl_product_code->product_id;
                }
            }
            
            
            $add_record = DB::table('tbl_inventory_record')->insert([
                    'product_code' => $data['modal_product_code'],
                    'product_id' => $product_id,
                    'perbox' => $product_perbox,
                    'product_type' => $data['product_type'], 
                    'product_details' => $product_details,
                    'store_id' => $data['store_id'],
                    'qty' => $product_qty,
                    'inward_status' => 1,
                    'added_date' => date('Y-m-d'),
                    'added_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
            
            $this->updateInventory($data['modal_product_code'], $data['product_type'],$product_details, $product_id, $product_qty, $data['store_id'],
            $data['modal_noofbox'],$data['modal_perbox']);
             
            if($data['modal_barcode_option'] == '1')
            {
                $this->generateBarcodes($data['modal_product_code'], $data['product_type'], $product_id, $product_details, $product_qty, $data['modal_total_price'], 
                $data['store_id'],$data['modal_retail_price'],$data['modal_noofbox'],$data['modal_perbox']);
            }      
            


            DB::commit();
    
            return response()->json(['success' => 'Inventory  saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the Inventory save process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    
    
    public function addinventoryProductWise(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'qty'     => 'required|string|max:255',
            'tax_rule'     => 'required|string|max:255',
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
            
            $decryptedId = base64_decode($data['uid']);

            $this->updateInventory1($decryptedId,$data['qty']);
             
            if($data['modal_barcode_option'] == '1')
            {
                $this->generateBarcodes1($data['pcode'], $data['ptype'], $data['pid'], $data['qty'], $data['modal_total_price'], 
                $data['stid'],$data['modal_retail_price'],$decryptedId);
            } 
            

            DB::commit();
    
            return response()->json(['success' => 'Inventory  saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the Inventory save process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    private function updateInventory1($decryptedId,$qty)
    {
        $user = auth()->user();
        $qty = (int)($qty);

        $query = DB::table('tbl_inventory_levels')
            ->where('id', $decryptedId);

        $inventory = $query->first();
        
        
        $add_record = DB::table('tbl_inventory_record')->insert([
                    'product_code' => $inventory->product_code,
                    'product_id' => $inventory->product_id,
                    'perbox' => $inventory->perbox,
                    'product_type' => $inventory->product_type, 
                    'product_details' => $inventory->product_details,
                    'store_id' => $inventory->store_id,
                    'qty' => $qty,
                    'inward_status' => 1,
                    'added_date' => date('Y-m-d'),
                    'added_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
        if($inventory->product_type === 'Lens') 
        {
            
            $query->update([
                'available_quantity' => $inventory->available_quantity + $qty,
                'tota_lens_qty' => $inventory->tota_lens_qty + ($inventory->perbox*$qty),
                'updated_at' => now()
            ]);
  
        }
        else 
        {
            $query->update([
                'available_quantity' => $inventory->available_quantity + $qty,
                'updated_at' => now()
            ]);
            
        }
    }
    
    
    private function generateBarcodes1($product_code, $product_type, $pid, $product_qty, $pprice, $store_id, $rprice,$decryptedId)
    {
        $user = auth()->user();
        $query = DB::table('tbl_inventory_levels')
            ->where('id', $decryptedId);

        $inventory = $query->first();
        
        
        $refrence_no = $this->generateUniqueRandomIdrefrence(6);
    
        if ($product_type === 'Lens') {
            for ($b = 0; $b < $product_qty; $b++)
            {
                $box_barcode = $this->generateUniqueRandomIdss(6);
    
                DB::table('tbl_barcode')->insert([
                    'product_code' => $product_code,
                    'product_id' => $pid,
                    'perbox' => $inventory->perbox,
                    'product_type' => $product_type, 
                    'barcode_no' => $box_barcode,
                    'product_details' => $inventory->product_details,
                    'purchase_price' => $pprice,
                    'retail_price' => $rprice,
                    'store_id' => $store_id,
                    'inv_date' => date('Y-m-d'),
                    'purchase_date' => date('Y-m-d'),
                    'inv_ref_no' => $refrence_no,
                    'inward_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
                $piece_price = round($pprice / max($inventory->perbox, 1), 2);
                $piece_retail = round($rprice / max($inventory->perbox, 1), 2);
    
                for ($p = 0; $p < $inventory->perbox; $p++) {
                    DB::table('tbl_barcode')->insert([
                        'product_code' => $product_code,
                        'product_id' => $pid,
                        'product_type' => $product_type, 
                        'barcode_no' => $this->generateUniqueRandomId1(7),
                        'lens_box' => $box_barcode,
                        'product_details' => $inventory->product_details,
                        'purchase_price' => $piece_price,
                        'retail_price' => $piece_retail,
                        'store_id' => $store_id,
                        'inv_date' => date('Y-m-d'),
                        'purchase_date' => date('Y-m-d'),
                        'inv_ref_no' => $refrence_no,
                        'inward_status' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                        'barcode_no' => $box_barcode,
                        'store_id' => $store_id,
                        'reference_type' => 'Manually',
                        'action_perform' => 'Add',
                        'added_by' => $user->id,
                ]);
            }
            
            
        } else {
            for ($q = 0; $q < $product_qty; $q++)
            {
                $barcode = $this->generateUniqueRandomIdss(6);
                DB::table('tbl_barcode')->insert([
                    'product_code' => $product_code,
                    'product_id' => $pid,
                    'product_type' => $product_type, 
                    'barcode_no' => $barcode,
                    'product_details' => $inventory->product_details,
                    'purchase_price' => $pprice,
                    'retail_price' => $rprice,
                    'store_id' => $store_id,
                    'inv_date' => date('Y-m-d'),
                    'purchase_date' => date('Y-m-d'),
                    'inv_ref_no' => $refrence_no,
                    'inward_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                        'barcode_no' => $barcode,
                        'store_id' => $store_id,
                        'reference_type' => 'Manually',
                        'action_perform' => 'Add',
                        'added_by' => $user->id,
                ]);
            }
        }
    }
    
    private function updateInventory($code, $type, $product_details, $pid, $product_qty, $store_id,$perbox,$box_detail)
    {
        $qty = (int)($product_qty);
        $perbox = (int)($perbox);
        $box_detail = (int)($box_detail);
    
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
    
    
    private function generateBarcodes($product_code, $product_type, $pid, $product_details, $product_qty, $pprice, $store_id, $rprice, $box_detail, $perbox)
    {
        $user = auth()->user();
        
        $qty = (int)($box_detail);
        $box_detail = (int)($box_detail);
        $perbox = (int)($perbox);
        $purchase_price = (float)($pprice);
        $retail_price = (float)($rprice);
        $code = $product_code;
        
        $refrence_no = $this->generateUniqueRandomIdrefrence(6);
    
        if ($product_type === 'Lens')
        {
            for ($b = 0; $b < $box_detail; $b++)
            {
                $box_barcode = $this->generateUniqueRandomIdss(6);
    
                DB::table('tbl_barcode')->insert([
                    'product_code' => $code,
                    'product_id' => $pid,
                    'perbox' => $perbox,
                    'product_type' => $product_type, 
                    'barcode_no' => $box_barcode,
                    'product_details' => $product_details,
                    'purchase_price' => $purchase_price,
                    'retail_price' => $retail_price,
                    'store_id' => $store_id,
                    'inv_date' => date('Y-m-d'),
                    'purchase_date' => date('Y-m-d'),
                    'inv_ref_no' => $refrence_no,
                    'inward_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
                $piece_price = round($purchase_price / max($perbox, 1), 2);
                $piece_retail = round($retail_price / max($perbox, 1), 2);
    
                for ($p = 0; $p < $perbox; $p++) {
                    DB::table('tbl_barcode')->insert([
                        'product_code' => $code,
                        'product_id' => $pid,
                        'product_type' => $product_type, 
                        'barcode_no' => $this->generateUniqueRandomId1(7),
                        'lens_box' => $box_barcode,
                        'product_details' => $product_details,
                        'purchase_price' => $piece_price,
                        'retail_price' => $piece_retail,
                        'store_id' => $store_id,
                        'inv_date' => date('Y-m-d'),
                        'purchase_date' => date('Y-m-d'),
                        'inv_ref_no' => $refrence_no,
                        'inward_status' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                        'barcode_no' => $box_barcode,
                        'store_id' => $store_id,
                        'reference_type' => 'Manually',
                        'action_perform' => 'Add',
                        'added_by' => $user->id,
                ]);
            }
            
            
        } else {
            for ($q = 0; $q < $product_qty; $q++) 
            {
                $barcode = $this->generateUniqueRandomIdss(6);
                DB::table('tbl_barcode')->insert([
                    'product_code' => $code,
                    'product_id' => $pid,
                    'product_type' => $product_type, 
                    'barcode_no' => $barcode,
                    'product_details' => $product_details,
                    'purchase_price' => $purchase_price,
                    'retail_price' => $retail_price,
                    'store_id' => $store_id,
                    'inv_date' => date('Y-m-d'),
                    'purchase_date' => date('Y-m-d'),
                    'inv_ref_no' => $refrence_no,
                    'inward_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                        'barcode_no' => $barcode,
                        'store_id' => $store_id,
                        'reference_type' => 'Manually',
                        'action_perform' => 'Add',
                        'added_by' => $user->id,
                ]);
            }
        }
    }
    
    
    private function generateUniqueRandomIdss($length = 6, $table = 'tbl_barcode', $column = 'barcode_no', $min = 100000, $max = 999999)
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
    
    private function generateUniqueRandomIdrefrence($length = 6, $table = 'tbl_barcode', $column = 'inv_ref_no', $min = 1000000, $max = 9999999)
    {
        do 
        {
          $id = random_int($min, $max);
        } while (DB::table($table)->where($column, $id)->exists());
        return $id;
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
    
    
    
    public function purchaseProductAdd(Request $request)
    {
        $user = auth()->user();

        
        $validator = Validator::make($request->all(), [
            'supplier_name'     => 'required|string|max:255',
            'p_bill_no'     => 'required|string|max:255',
            'purchase_date'     => 'required|string|max:255',
            'tax_rule_p'     => 'required|string|max:255',
            'qty_p'     => 'required|string|max:255',
            'product_price'     => 'required|string|max:255',
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
            
            $decryptedId = base64_decode($data['inventory_id']);
            
            $query = DB::table('tbl_inventory_levels')
            ->where('id', $decryptedId);

            $inventory = $query->first();
        
            $purchase = Purchase::create([
                'supplier_name'       => $data['supplier_name'],
                'p_bill_no'           => $data['p_bill_no'],
                'purchase_date'       => $data['purchase_date'],
                'tax_rule'            => $data['tax_rule_p'] ?? null,
                'total_qty'           => $data['qty_p'] ?? 0,
                'total_unit_amount'   => $data['total_unit_price'] ?? 0,
                'total_base_amount'   => $data['total_basic_price'] ?? 0,
                'total_gst_amount'    => $data['total_gst_amount'] ?? 0,
                'total_p_amount'      => $data['total_purchase_price'] ?? 0,
                'round_off'           => $data['round_off'] ?? 0,
                'net_purchase_amount' => $data['total_net_purchase_price'] ?? 0,
                'added_by'            => $user->id,
                'store_id'            => $data['storeid_p'],
            ]);
            
            if($data['producttype_p'] == 'Lens')
            {
                $product_noofbox = $data['qty_p'];
            }
            else
            {
                $product_noofbox = '';
            }
            
            $purchaseProduct = PurchaseProduct::create([
                    'purchase_id'          => $purchase->id,
                    'bill_no'              => $data['p_bill_no'],
                    'product_type'         => $data['producttype_p'],
                    'product_code'         => $data['productcode_p'],
                    'product_id'           => $data['productid_p'],
                    'product_details'      => $inventory->product_details,
                    'product_price'        => (float)($data['product_price'] ?? 0),
                    'product_base_price'   => (float)($data['product_base_price'] ?? 0),
                    'product_purchase_price'=> (float)($data['total_purchase'] ?? 0),
                    'hsn_code'             => $data['hsn_code'] ?? '',
                    'gst_amt'              => (float)($data['gst_amt'] ?? 0),
                    'gst'                  => (float)($data['gst'] ?? 0),
                    'qty'                  => (int)($data['qty_p'] ?? 0),
                    'total_purchase_price' => (float)($data['total_purchase_price'] ?? 0),
                    'product_retail_price' => (float)($data['product_retail_price'] ?? 0),
                    'box_detail'           => $product_noofbox,
                    'perbox_detail'        => $inventory->perbox ?? '',
                    'store_id'             => $data['storeid_p'],
                    'added_by'             => $user->id,
                ]);
                
            $add_record = DB::table('tbl_inventory_record')->insert([
                    'product_code' => $data['productcode_p'],
                    'product_id' => $data['productid_p'],
                    'perbox' => $inventory->perbox,
                    'product_type' => $data['producttype_p'], 
                    'product_details' => $inventory->product_details,
                    'store_id' =>  $data['storeid_p'],
                    'qty' => $qty,
                    'added_date' => $data['purchase_date'],
                    'inward_status' => 0,
                    'added_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);    
    
            

            $this->updateInventory1($decryptedId,$data['qty_p']);
             
            if($data['barcode_option'] == '1')
            {
                $this->generatePurchaseBarcodesProduct($purchase, $purchaseProduct,$inventory->product_details,$data['producttype_p'],$data['productcode_p'],$data['productid_p']
                ,$data['total_purchase'],$data['product_retail_price'],$inventory->perbox,$data['qty_p'],$data['storeid_p'],$data['purchase_date'],$data['p_bill_no']);
            }      

            DB::commit();
    
            return response()->json(['success' => 'Purchase  saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the Purchase save process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    
    private function generatePurchaseBarcodesProduct($purchase, $purchaseProduct, $product_details,$type, $code, $pid, $purchase_price,$retail_price,$perbox,$qty,$storeid
    ,$purchase_date,$p_bill_no)
    {
        $user = auth()->user();

        if ($type === 'Lens') 
        {
            for ($b = 0; $b < $qty; $b++) {
                $box_barcode = $this->generateUniqueRandomIdss(6);
    
                DB::table('tbl_barcode')->insert([
                    'purchase_id' => $purchase->id,
                    'purchase_product_id' => $purchaseProduct->id,
                    'purchase_date' => $purchase_date ?? now(),
                    'p_bill_no' => $p_bill_no,
                    'product_code' => $code,
                    'product_id' => $pid,
                    'perbox' => $qty,
                    'product_type' => $type,
                    'barcode_no' => $box_barcode,
                    'product_details' => $product_details,
                    'purchase_price' => $purchase_price,
                    'retail_price' => $retail_price,
                    'store_id' => $storeid,
                    'inward_status' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
    
                $piece_price = round($purchase_price / max($qty, 1), 2);
                $piece_retail = round($retail_price / max($qty, 1), 2);
    
                for ($p = 0; $p < $perbox; $p++) {
                    DB::table('tbl_barcode')->insert([
                        'purchase_id' => $purchase->id,
                        'purchase_product_id' => $purchaseProduct->id,
                        'purchase_date' => $purchase_date ?? now(),
                        'p_bill_no' => $p_bill_no,
                        'product_code' => $code,
                        'product_id' => $pid,
                        'product_type' => $type,
                        'barcode_no' => $this->generateUniqueRandomId1(7),
                        'lens_box' => $box_barcode,
                        'product_details' => $product_details,
                        'purchase_price' => $piece_price,
                        'retail_price' => $piece_retail,
                        'store_id' => $storeid,
                        'inward_status' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                        'barcode_no' => $box_barcode,
                        'store_id' => $storeid,
                        'reference_type' => 'Purchase',
                        'action_perform' => 'Add',
                        'added_by' => $user->id,
                ]);
            }
            
            
        } else {
            for ($q = 0; $q < $qty; $q++) {
                $barcode = $this->generateUniqueRandomId(6);
                DB::table('tbl_barcode')->insert([
                    'purchase_id' => $purchase->id,
                    'purchase_product_id' => $purchaseProduct->id,
                    'purchase_date' => $purchase_date ?? now(),
                    'p_bill_no' => $p_bill_no,
                    'product_code' => $code,
                    'product_id' => $pid,
                    'product_type' => $type,
                    'barcode_no' => $barcode,
                    'product_details' => $product_details,
                    'purchase_price' => $purchase_price,
                    'retail_price' => $retail_price,
                    'store_id' => $storeid,
                    'inward_status' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $barcode_activity = DB::table('tbl_barcode_track_record')->insert([
                        'barcode_no' => $barcode,
                        'store_id' => $storeid,
                        'reference_type' => 'Purchase',
                        'action_perform' => 'Add',
                        'added_by' => $user->id,

                    ]);
            }
        }
    }
    
    
    public function challanProductAdd(Request $request)
    {
        $user = auth()->user();

        
        $validator = Validator::make($request->all(), [
            'supplier_name_challan'     => 'required|string|max:255',
            'challan_no'     => 'required|string|max:255',
            'challan_date'     => 'required|string|max:255',
            'tax_rule_c'     => 'required|string|max:255',
            'qty_c'     => 'required|string|max:255',
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
            
            $decryptedId = base64_decode($data['inventory_ids']);
            
            $query = DB::table('tbl_inventory_levels')
            ->where('id', $decryptedId);

            $inventory = $query->first();
        
            $purchase = Challan::create([
                'challan_date'       => $data['challan_date'],
                'supplier_name'      => $data['supplier_name_challan'],
                'challan_no'         => $data['challan_no'],
                'total_qty'          => $data['qty_c'] ?? null,
                'total_base_amount'  => $data['total_basic_price_challan'] ?? 0,
                'total_discount'     =>  0,
                'total_gst_amount'   => $data['total_gst_amount_challan']*$data['qty_p'] ?? 0,
                'total_p_amount'     => $data['total_net_purchase_challan']*$data['qty_p'] ?? 0,
                'tax_rule'           => $data['tax_rule_c'],
                'added_by'           => $user->id,
                'recevied_store_id'  => $data['storeid_c'],
                'billing_store_id'   => $data['storeid_c'],
                'challan_status'     => 0,
            ]);
            
            if($data['producttype_c'] == 'Lens')
            {
                $product_noofbox = $data['qty_c'];
            }
            else
            {
                $product_noofbox = '';
            }
            
            $purchaseProduct = ChallanProduct::create([
                    'challan_id'          => $purchase->id,
                    'challan_no'              => $data['challan_no'],
                    'product_type'         => $data['producttype_c'],
                    'product_code'         => $data['productcode_c'],
                    'product_id'           => $data['productid_c'],
                    'product_details'      => $inventory->product_details,
                    'product_price'        => (float)($data['product_price_challan'] ?? 0),
                    'product_base_price'   => (float)($data['product_base_price_challan'] ?? 0),
                    'product_purchase_price'=> (float)($data['total_purchase_challan'] ?? 0),
                    'hsn_code'             => $data['hsn_code'] ?? '',
                    'gst_amt'              => (float)($data['gst_amt'] ?? 0),
                    'gst'                  => (float)($data['gst'] ?? 0),
                    'qty'                  => (int)($data['qty_p'] ?? 0),
                    'total_purchase_price' => (float)($data['total_net_purchase_challan'] ?? 0),
                    'product_retail_price' => (float)($data['product_retail_price_challan'] ?? 0),
                    'product_bb_price' => (float)($data['bb_price_challan'] ?? 0),
                    'box_detail'           => $product_noofbox,
                    'perbox_detail'        => $inventory->perbox ?? '',
                    'batch_no_challan'             => $data['batch_no_challan'],
                    'mfg_date_challan'             => $data['mfg_date_challan'],
                    'exp_date_challan'             => $data['exp_date_challan'],
                    'ispair'             => $data['ispair'],
                    'recevied_store_id'  => $data['storeid_c'],
                    'billing_store_id'   => $data['storeid_c'],
                    'barcode_option'     => $data['barcode_option_challan'],
                ]);
                
            DB::commit();
    
            return response()->json(['success' => 'Challan  saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the Challan save process.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    
    
    public function inventoryDelete($id)
    {
        $decryptedId = base64_decode($id);
    
        $setting['page_title'] = 'Delete Inventory';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
    
        $inventory = DB::table('tbl_inventory_levels')->where('id', $decryptedId)->first();
    
        if (!$inventory) {
            abort(404, 'Inventory not found');
        }
    
        $store = Store::find($inventory->store_id);
    
        $setting['inventory'] = $inventory;
        $setting['store_name'] = $store ? $store->store_name : 'Unknown Store';
    
        return view($this->view_route.'/delete-inventory', $setting);
    }
    
    
    public function inventorybarcodeDatatable(Request $request)
    {

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir   = $request->input('order.0.dir');
        
        $product_type = $request->input('product_type');
        $product_code = $request->input('product_code');
        $product_details = $request->input('product_details');
        $perbox = $request->input('perbox');
        $stid = $request->input('store_id');
        
        $templates = DB::table('tbl_barcode')
        ->whereNull('lens_box')
        ->where('t_status', 0);

        if ($product_code) {
            $templates->where('product_code', $product_code);
        }
        
        if ($product_type) {
            $templates->where('product_type', $product_type);
        }
        
        if ($product_details) {
            $templates->where('product_details', $product_details);
        }
        
        if ($stid) 
        {
            $templates->where(function($query) use ($stid) {
                $query->where('store_id', $stid)
                      ->orWhere(function($q) use ($stid) {
                          $q->where('transfer_store_id', $stid); 
                      });
            });
        }
        
        if ($perbox) {
            if ($perbox === '') {
                $templates->where('perbox', 0);
            } else {
                $templates->where('perbox', $perbox);
            }
        }

        $totalData = (clone $templates)->count();

        $totalFiltered = (clone $templates)->count();
        
        $templates = $templates
            ->offset($start)
            ->limit($limit)
            ->orderBy('id', 'DESC')
            ->get();


        $data = [];
        if (! empty($templates)) 
        {
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
                
                if($template->transfer_store_id == NULL)
                {
                    $in_status = $template->inward_status;
                }
                
                else
                {
                    $in_status = $template->transfer_inward_status;
                }
                $nestedData['store_name'] = $store->store_name;
                $nestedData['responsive_id']    = '';
                $nestedData['barcode_id']       = $template->id;
                $nestedData['barcode'] = $template->barcode_no;
                if($in_status == 0)
                {
                    $tbl_purchase = DB::table('tbl_purchase')->where('purchase_id', $template->purchase_id)->first();
                    $tbl_purchase_deatils = DB::table('tbl_purchase_deatils')->where('id', $template->purchase_product_id)->first();
                    $nestedData['purchase_details'] = 'Purchase Date :'.$template->purchase_date.'<BR>Purchase Bill Number :<span class="badge badge-info">'.$template->p_bill_no.'</span><BR>Supplier :'.$tbl_purchase->supplier_name;
                }
                elseif($in_status == 1)
                {
                    $nestedData['purchase_details'] = 'Inventory Date :'.$template->inv_date.'<BR>Inventory Reference Number :<span class="badge badge-info">'.$template->inv_ref_no.'</span>';
                }
                elseif($in_status == 2)
                {
                    $nestedData['purchase_details'] = 'Inventory Date :'.$template->challan_date.'<BR>Inventory Reference Number :<span class="badge badge-info">'.$template->challan_no.'</span>';
                }
                elseif($in_status == 3)
                {
                    $nestedData['purchase_details'] = 'Inventory Date :'.$template->import_date.'<BR>Inventory Reference Number :<span class="badge badge-info">'.$template->import_ref.'</span>';
                }
                elseif($in_status == 4)
                {
                    $nestedData['purchase_details'] = 'Inventory Date :'.$template->recevied_date.'<BR>Inventory Reference Number :<span class="badge badge-info">'.$template->recevied_ref_no.'</span>';
                }
                
                $nestedData['product_details'] = 'Product  : <span class="badge badge-info">'.$template->product_type.'</span><BR>Product Code : '.$template->product_code.'<BR>Product ID : '.$template->product_id.'<BR>Description  :'.$template->product_details.'<BR>'.$description;
                $nestedData['purchase_price']  = 'Rs '.$template->purchase_price;
                $nestedData['retail_price']    = 'Rs '.$template->retail_price;
                $nestedData['pdeatils']        = $template->product_details;
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
    
    
    public function inventorydestroy(Request $request)
    {
        DB::beginTransaction();
    
        try {
            $user = auth()->user();
            $sender_ids = json_decode($request->sender_ids, true);
    
            foreach ($sender_ids as $id) {
    
                // Fetch barcode details safely
                $tbl_barcode = DB::table('tbl_barcode')->where('id', $id)->first();
    
                if (!$tbl_barcode) {
                    continue; // Skip if barcode not found
                }
    
                // Determine store based on transfer status
                if ($tbl_barcode->t_status == '0') {
                    $store_id = $tbl_barcode->store_id;
    
                    DB::table('tbl_barcode')
                        ->where('id', $id)
                        ->update([
                            'outward_status' => 3,
                            'adj_date'       => now()->toDateString(),
                            'adj_comment'    => $request->delete_comment,
                            'loss_damage'    => $request->loos_damage,
                            'updated_at'     => now(),
                        ]);
                } else {
                    $store_id = $tbl_barcode->transfer_store_id;
    
                    DB::table('tbl_barcode')
                        ->where('id', $id)
                        ->update([
                            'transfer_outward_status' => 3,
                            'adj_date'                => now()->toDateString(),
                            'adj_comment'             => $request->delete_comment,
                            'loss_damage'             => $request->loos_damage,
                            'updated_at'              => now(),
                        ]);
                }
    
                // Adjust inventory quantities
                $toInventory = DB::table('tbl_inventory_levels')
                    ->where('product_type', $tbl_barcode->product_type)
                    ->where('product_code', $tbl_barcode->product_code)
                    ->where('product_id', $tbl_barcode->product_id)
                    ->where('product_details', $tbl_barcode->product_details)
                    ->where('store_id', $store_id)
                    ->first();
    
                if ($toInventory) {
                    $updateToData = [
                        'available_quantity' => max(0, $toInventory->available_quantity - 1),
                        'updated_at'          => now()
                    ];
    
                    if ($tbl_barcode->product_type === 'Lens' && isset($tbl_barcode->perbox)) 
                    {
                        $updateToData['tota_lens_qty'] = max(0, $toInventory->tota_lens_qty - $tbl_barcode->perbox);
                    }
    
                    DB::table('tbl_inventory_levels')->where('id', $toInventory->id)->update($updateToData);
                }
    
                // Record deletion in inventory record table
                DB::table('tbl_inventory_record')->insert([
                    'product_code'    => $tbl_barcode->product_code,
                    'product_id'      => $tbl_barcode->product_id,
                    'product_type'    => $tbl_barcode->product_type,
                    'product_details' => $tbl_barcode->product_details,
                    'store_id'        => $store_id,
                    'qty'             => 1,
                    'added_date'      => now()->toDateString(),
                    'outward_status'  => 3,
                    'added_by'        => $user->id,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
    
                // Log barcode activity
                DB::table('tbl_barcode_track_record')->insert([
                    'barcode_no'     => $tbl_barcode->barcode_no,
                    'store_id'       => $store_id,
                    'reference_type' => 'Delete',
                    'action_perform' => 'Delete',
                    'added_by'       => $user->id,
                    'created_at'     => now(),
                ]);
            }
    
            DB::commit();
    
            return response()->json(['success' => 'Inventory was successfully deleted!']);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong during the inventory deletion process.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    
    
    public function trackBarcode()
    {
        $setting['page_title'] = 'Track barcode';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/track-barcode',$setting);
    }
    
    
    public function barcodeActivityList(Request $request)
    {

        $store_id = $request->input('store_id');
        $barcode_no = $request->input('barcode_no');
    
        $query = DB::table('tbl_barcode_track_record');
    
        if ($store_id) {
            $query->where('store_id', $store_id);
        }
    
        if ($barcode_no) {
            $query->where('barcode_no', $barcode_no);
        }
    
        $results = $query->get();
    
        if (!$results) {
            return response()->json('<div class="alert alert-danger">No data found.</div>');
        }
        
       
        
        $data = '';
        
        $data .= '
 
        <div class="container" style="overflow-x:auto;">
            <table id="datatable2" class="table card-table table-vcenter text-nowrap" style="color: #000;">
                <thead>
                  <tr>
                    <th style="color: #6b6f80;">#</th>
                    <th style="color: #6b6f80;">Date Time</th>
                    <th style="color: #6b6f80;">Store Name</th>
                    <th style="color: #6b6f80;">Product</th>
                    <th style="color: #6b6f80;">Product Code</th>
                    <th style="color: #6b6f80;">Product Details</th>
                    <th style="color: #6b6f80;">Barcode</th>
                    <th style="color: #6b6f80;">Refrence Type</th>
                    <th style="color: #6b6f80;">Action</th>
                    <th style="color: #6b6f80;">Status</th>
                  </tr>
                </thead>
                <tbody>';
                $i=1;
                foreach ($results as $product)
                {
                     $summary = DB::table('tbl_barcode')->where('barcode_no', $barcode_no)->where('store_id', $store_id);
                     $summary = $summary->first();
                     $to_store   = Store::find($product->store_id);
                     
                    if($summary->barcode_status == '0')
                    {
                        $bstatus = '<span class="badge badge-success">Pending</span>';
                        
                    }elseif($summary->barcode_status == '1')
                    {
                        $bstatus = '<span class="badge badge-info">Confirm </span>';
                        
                    }
                   $data .= '
                        <tr> 
                             <td>'.$i++.'</td>
                             <td>'.$product->created_at.'</td>
                             <td>' . $to_store->store_name . '</td>
                             <td>' . $summary->product_type . '</td>
                             <td>' . $summary->product_code . '</td>
                             <td>' . $summary->product_details . '</td>
                             <td>' . $product->barcode_no . '</td>
                             <td>' . $product->reference_type . '</td>
                             <td>' . $product->action_perform . '</td>
                             <td>'.$bstatus.'</td>
                        </tr>
                    ';
                }
                $data .= '</tbody>
            </table>

        </div>';

        return response()->json($data);
        
    }
    
    
    public function inventoryAdjustmentHistory()
    {
        $setting['page_title'] = 'Inventory Adjustment History';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/inventory-adjustment-history',$setting);
    }
    
    
    public function adjustmentDatatable(Request $request)
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
        ->where('t_status', '0')
        ->whereIn('outward_status', ['1', '3'])
        ->whereNull('lens_box');
        
        if (!$templates)
        {
            if ($activeStoreId > 0) 
            {
                $templates = DB::table('tbl_barcode')
                ->where('t_status', '0')
                ->whereIn('transfer_store_id', ['1', '3'])
                ->whereNull('lens_box');
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
        
        $totalFiltered = (clone $templates)->count();
        
        $templates = $templates
            ->offset($start)
            ->limit($limit)
            ->orderBy('id', 'DESC')
            ->get();


        $data = [];
        if (! empty($templates))
        {
            $i=1;
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
                    
                    if($template->outward_status == '3')
                    {
                        $type_status = 'Delete';
                    }
                    else
                    {
                        $type_status = 'Adjust';
                    }
                }
                else
                {
                    $store= Store::where('id', $template->transfer_store_id)->first();
                    
                    if($template->transfer_outward_status == '3')
                    {
                        $type_status = 'Delete';
                    }
                    else
                    {
                        $type_status = 'Adjust';
                    }
                }
                $nestedData['sr_no']    = $i++;
                $nestedData['store_name'] = $store->store_name;
                $nestedData['barcode_id']       = $template->id;
                $nestedData['barcode'] = $template->barcode_no;
                $nestedData['type'] = $type_status;
                $nestedData['purchase_type'] = $template->product_type;
                $nestedData['purchase_code'] = $template->product_code;
                $nestedData['product_details']        = $template->product_details.'<BR>'.$description;
                $nestedData['purchase_price']  = 'Rs '.$template->purchase_price;
                $nestedData['retail_price']    = 'Rs '.$template->retail_price;
                $nestedData['adj_date'] = $template->adj_date;
                $nestedData['comment'] = $template->adj_comment;
                $data[]     = $nestedData;
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
    
    
    
    public function getglasssels(Request $request)
    {
        $product_type = $request->product_type;
        $product_code = $request->product_code;
        $product_id   = $request->product_id;
        $store_id     = $request->store_id;
    
        $sales = DB::table('tbl_sales_product')
            ->where('product_type', $product_type)
            ->where('product_code', $product_code)
            ->where('store_id', $store_id)
            ->where('adjust_status', 0)
            ->orderBy('id', 'ASC')
            ->get([
                'id',
                'order_no',
                'purchase_price',
                'product_deatils',
                'right_purchase',
                'left_purchase',
                'store_id'
            ]);
    
        return response()->json([
            'data' => $sales->map(function ($p) use ($store_id) {
    
                $summary = DB::table('tbl_sales')
                    ->where('order_no', $p->order_no)
                    ->where('store_id', $store_id)
                    ->first();
    
                $rightleft_glass = $p->right_purchase == 1 ? 'Right' : 'Left';
    
                return [
                    'id' => $p->id,
                    'order_no' => $p->order_no,
                    'purchase_price' => $p->purchase_price,
                    'product_deatils' => $p->product_deatils,
                    'right_purchase' => $p->right_purchase,
                    'left_purchase' => $p->left_purchase,
                    'store_id' => $p->store_id,
                    'rightleft_glass' => $rightleft_glass,
                    'sale_date' => $summary ? $summary->sale_date : null,
                ];
            })
        ]);
    }
    
    
    
    public function getsalesproductwisedetails(Request $request)
    {
        $salesProductId = $request->salesProductId;
        
        // Get sales product
        $sales = SaleProduct::find($salesProductId);
    
        if (!$sales) {
            return response()->json([
                'status' => false,
                'message' => 'Sales product not found'
            ]);
        }
    
        // Get sales summary
        $summary = DB::table('tbl_sales')
            ->where('order_no', $sales->order_no)
            ->where('store_id', $sales->store_id)
            ->first();
    
        // Right / Left
        $rightleft_glass = $sales->right_purchase == 1 ? 'Right' : 'Left';
        
        $store= Store::where('id', $sales->store_id)->first();
    
        return response()->json([
            'status' => true,
            'sales'  => $sales,
            'store_name'  => $store->store_name,
            'summary' => $summary,
            'position' => $rightleft_glass
        ]);
    }


    
    
    

    
}