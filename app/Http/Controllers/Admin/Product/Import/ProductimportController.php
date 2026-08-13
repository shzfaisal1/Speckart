<?php

namespace App\Http\Controllers\Admin\product\import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductimportController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:Product-Import', ['only' => ['index']]);
    }

    public $view_route = 'products';
    public $view_frame = 'import';

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $setting['page_title'] = 'Product Import';

        return view(
            $this->view_route . '/' . $this->view_frame . '/index',
            $setting
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    public function search(Request $request)
    {
        $totalData = DB::table('tbl_product_import')->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');

        $start = $request->input('start');

        if (empty($request->input('search1')))
        {
            $templates = DB::table('tbl_product_import')
                ->offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();
        }
        else
        {
            $search = $request->input('search1');

            $templates = DB::table('tbl_product_import')
                ->where('product_type', 'like', '%' . $search . '%')
                ->offset($start)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();

            $totalFiltered = DB::table('tbl_product_import')
                ->where('product_type', 'like', '%' . $search . '%')
                ->count();
        }

        $data = [];

        if (!empty($templates))
        {
            $i = 1;

            foreach ($templates as $template)
            {
                $created_by = User::find($template->added_by);

                $users_count = DB::table('tbl_product_code')
                    ->where('bulkproduct_id', $template->id)
                    ->count();

                $nestedData['sr_no'] = $i++;

                $nestedData['refrence_no'] =
                    $template->refrence_no;

                $nestedData['product_type'] =
                    $template->product_type;

                $nestedData['total_records_upload'] =
                    $template->total_records_upload;

                $nestedData['created_at'] =
                    date('d M,Y', strtotime($template->created_at))
                    . ' (' . ($created_by->name ?? '') . ')';

                $invalidCount =
                    $template->total_records_upload - $users_count;
                    
                if(!empty($template->invalid_file))
                {
                   $ivfile =  $template->invalid_file;
                }
                else
                {
                    $ivfile = 'text.txt';
                }

                if (
                    $invalidCount > 0
                )
                {
                    $nestedData['total_invalid_records'] =
                        '<a href="' .
                        route(
                            'admin.download-invalid-file',
                            $ivfile
                        ) .
                        '" class="btn btn-danger btn-sm">
                            Download (' . $invalidCount . ')
                        </a>';
                }
                else
                {
                    $nestedData['total_invalid_records'] = 0;
                }

                $nestedData['total_records'] =
                    $users_count;

                $data[] = $nestedData;
            }
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BULK PRODUCT IMPORT
    |--------------------------------------------------------------------------
    */

    public function bulkProductAdd(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | FILE CHECK
        |--------------------------------------------------------------------------
        */

        if (!$request->hasFile('myFile'))
        {
            return back()->with(
                'error',
                'No file uploaded.'
            );
        }

        $file = $request->file('myFile');

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        if ($extension !== 'csv')
        {
            return back()->with(
                'error',
                'Only CSV files are allowed.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | READ CSV
        |--------------------------------------------------------------------------
        */

        $rows = Excel::toArray(null, $file)[0];

        if (count($rows) <= 1)
        {
            return back()->with(
                'error',
                'CSV file is empty.'
            );
        }

        $header = $rows[0];

        $dataRows = array_slice($rows, 1);

        $dataRows = array_filter($dataRows, function ($row) {
            return !empty(array_filter($row));
        });

        /*
        |--------------------------------------------------------------------------
        | VARIABLES
        |--------------------------------------------------------------------------
        */

        $validProducts = [];

        $invalidProducts = [];

        $lastid = DB::table('tbl_product_import')
            ->insertGetId([
                'added_by' => $user->id,
                'product_type' => $request->product_type,
                'total_records_upload' => count($dataRows),
                'refrence_no' => rand(11111111, 99999999),
            ]);

        /*
        |--------------------------------------------------------------------------
        | PRODUCT TYPE INDEX
        |--------------------------------------------------------------------------
        */

        $productTypeIndexes = [
            'Glass' => ['track' => 25, 'neg' => 26],
            'Frame' => ['track' => 23, 'neg' => 24],
            'Goggles' => ['track' => 23, 'neg' => 24],
            'Lens' => ['track' => 35, 'neg' => 36],
            'Solution' => ['track' => 18, 'neg' => 19],
            'Other' => ['track' => 19, 'neg' => 20],
        ];

        $type = $request->product_type;

        $indexes = $productTypeIndexes[$type] ?? [];

        /*
        |--------------------------------------------------------------------------
        | ROW LOOP
        |--------------------------------------------------------------------------
        */

        foreach ($dataRows as $rowIndex => $data)
        {
            $productErrors = [];

            $productData = [];

            $productcodeVal =
                trim((string)($data[0] ?? ''));

            $nameVal =
                trim((string)($data[1] ?? ''));

            $trackInvVal = isset($indexes['track'])
                ? trim((string)($data[$indexes['track']] ?? 'No'))
                : 'No';

            $allowNegVal = isset($indexes['neg'])
                ? trim((string)($data[$indexes['neg']] ?? 'No'))
                : 'No';

            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            if (empty($productcodeVal))
            {
                $productErrors[] =
                    'Product Code is required.';
            }

            if (empty($nameVal))
            {
                $productErrors[] =
                    $type . ' Name is required.';
            }

            if (!in_array($trackInvVal, ['Yes', 'No']))
            {
                $productErrors[] =
                    'Track Inventory must be Yes or No.';
            }

            if (!in_array($allowNegVal, ['Yes', 'No']))
            {
                $productErrors[] =
                    'Allow Negative Inventory must be Yes or No.';
            }

            /*
            |--------------------------------------------------------------------------
            | INVENTORY FLAGS
            |--------------------------------------------------------------------------
            */

            $Track_Inventory =
                $trackInvVal === 'Yes' ? 1 : 0;

            $Allow_Negative =
                $allowNegVal === 'Yes' ? 1 : 0;

            /*
            |--------------------------------------------------------------------------
            | PRODUCT ID
            |--------------------------------------------------------------------------
            */

            $idgenerate = $this->generateUniqueRandomId(
                6,
                'tbl_product_code',
                'product_id'
            );

            /*
            |--------------------------------------------------------------------------
            | FRAME PRODUCT
            |--------------------------------------------------------------------------
            */

            if ($type === 'Frame')
            {
                $fields = [
                    $data[1] ?? '',
                    $data[2] ?? '',
                    $data[11] ?? '',
                ];

                $productdetails = implode(
                    ' - ',
                    array_filter($fields)
                );

                $productData = [

                    'product_id' => $idgenerate,

                    'product_code' => $data[0] ?? '',

                    'product_type' => 'Frame',

                    'product_name' => $data[1] ?? '',

                    'productdetails' => $productdetails,

                    'Company' => $data[2] ?? '',

                    'Gender' => $data[3] ?? '',

                    'Color' => $data[4] ?? '',

                    'Size' => $data[5] ?? '',

                    'Type' => $data[6] ?? '',

                    'Shape' => $data[7] ?? '',

                    'Material' => $data[8] ?? '',

                    'Temple_Detail' => $data[9] ?? '',

                    'Bridge_Size' => $data[10] ?? '',

                    'Quality' => $data[11] ?? '',

                    'Qty' => $data[12] ?? 0,

                    'Purchase_Base_Price' => $data[13] ?? 0,

                    'Purchase_Price' => $data[13] ?? 0,

                    'Retail_Price' => $data[14] ?? 0,

                    'discount_price' => $data[15] ?? 0,

                    'BB_Price' => $data[16] ?? 0,

                    'hsn_code' => $data[17] ?? '',

                    'tax_per' => $data[18] ?? 0,

                    'add_tax_per' => $data[19] ?? 0,

                    'tax_rule' => $data[20] ?? '',

                    'barcode' => $data[21] ?? '',

                    'invoice_description' => $data[22] ?? '',

                    'Track_Inventory' => $Track_Inventory,

                    'Allow_Negative_Inventory' =>
                        $Allow_Negative,

                    'tray_no' => $data[25] ?? '',

                    'added_by' => $user->id,

                    'bulkproduct_id' => $lastid,
                ];
            }
            elseif ($type === 'Goggles') 
            {
                $fields = [
                    $data[1] ?? null,
                    $data[2] ?? null,
                    $data[11] ?? null,
                ];
                
                $productdetails = implode(' - ', array_filter($fields));
                
                $productData = [
                    'product_id' => $idgenerate,
                    'productdetails' => $productdetails,
                    'product_code' => $data[0],
                    'product_type' => 'Goggles',
                    'product_name' => $data[1],
                    'Company' => $data[2],
                    'Gender' => $data[3],
                    'Color' => $data[4],
                    'Size' => $data[5],
                    'Type' => $data[6],
                    'Shape' => $data[7],
                    'Material' => $data[8],
                    'Temple_Detail' => $data[9],
                    'Bridge_Size' => $data[10],
                    'Quality' => $data[11],
                    'Qty' => $data[12],
                    'Purchase_Base_Price' => $data[13],
                    'Purchase_Price' => $data[13],
                    'Retail_Price' => $data[14],
                    'discount_price' => $data[15],
                    'BB_Price' => $data[16],
                    'hsn_code' => $data[17],
                    'tax_per' => $data[18],
                    'add_tax_per' => $data[19],
                    'tax_rule' => $data[20],
                    'barcode' => $data[21],
                    'invoice_description' => $data[22],
                    'Track_Inventory' => $Track_Inventory,
                    'Allow_Negative_Inventory' => $Allow_Negative,
                    'tray_no' => $data[25],
                    'added_by' => $user->id,
                    'bulkproduct_id' => $lastid,
                ];
            }
            elseif ($type === 'Glass') 
            {
                $isPairVal = trim((string) ($data[14] ?? ''));
                if (!empty($isPairVal) && !in_array($isPairVal, ['Yes', 'No']))
                {
                    $productErrors[] = 'IS Pair must be Yes or No.';
                }
                $is_pair = ($isPairVal === 'Yes') ? 1 : 0;
                
                $fields = [
                    $data[1] ?? null,
                    $data[2] ?? null,
                    $data[3] ?? null,
                    $data[4] ?? null,
                    $data[5] ?? null,
                    $data[6] ?? null,
                    $data[7] ?? null,
                    $data[8] ?? null,
                    !empty($data[9]) ? 'SPH:' . $data[9] : null,
                    !empty($data[10]) ? 'CYL:' . $data[10] : null,
                    !empty($data[12]) ? 'Additional:' . $data[12] : null,
                    !empty($data[11]) ? 'Axis:' . $data[11] : null,
                ];
                $productdetails = implode(' - ', array_filter($fields));
    
                $productData = [
                    'product_id' => $idgenerate,
                    'productdetails' => $productdetails,
                    'product_code' => $data[0],
                    'product_type' => 'Glass',
                    'product_name' => $data[1],
                    'Company' => $data[2],
                    'Color' => $data[3],
                    'Material' => $data[4],
                    'Coating' => $data[5],
                    'Design' => $data[6],
                    'Index' => $data[7],
                    'Quality' => $data[8],
                    'SPH' => $data[9],
                    'CYL' => $data[10],
                    'AXIS' => $data[11],
                    'ADD' => $data[12],
                    'Qty' => $data[13],
                    'is_pair' => $is_pair,
                    'Purchase_Base_Price' => $data[15],
                    'Purchase_Price' => $data[15],
                    'Retail_Price' => $data[16],
                    'discount_price' => $data[17],
                    'BB_Price' => $data[18],
                    'hsn_code' => $data[19],
                    'tax_per' => $data[20],
                    'add_tax_per' => $data[21],
                    'tax_rule' => $data[22],
                    'barcode' => $data[23],
                    'invoice_description' => $data[24],
                    'Track_Inventory' => $Track_Inventory,
                    'Allow_Negative_Inventory' => $Allow_Negative,
                    'tray_no' => $data[27],
                    'added_by' => $user->id,
                    'bulkproduct_id' => $lastid,
                ];
            }
            elseif ($type === 'Lens') 
            {
                $fields = [
                    $data[1] ?? null,
                    $data[2]?? null,
                    $data[12] ?? null,
                    $data[6] ?? null,
                    $data[3] ?? null,
                    $data[4] ?? null,
                    $data[5] ?? null,
                    $data[7] ?? null,
                    $data[9] ?? null,
                    !empty($data[13]) ? 'SPH:' . $data[13] : null,
                    !empty($data[14]) ? 'CYL:' .  $data[14] : null,
                    !empty($data[16]) ? 'Additional:' . $data[16] : null,
                    !empty($data[15]) ? 'Axis:' . $data[15] : null,
                    $data[17] ?? null,
                    $data[18] ?? null,
                    $data[19] ?? null
                ];
                $productdetails = implode(' - ', array_filter($fields));
                
                $productData = [
                    'product_id' => $idgenerate,
                    'productdetails' => $productdetails,
                    'product_code' => $data[0],
                    'product_type' => 'Goggles',
                    'product_name' => $data[1],
                    'Company' => $data[2],
                    'Color' => $data[3],
                    'Number' => $data[4],
                    'CT' => $data[5],
                    'Type' => $data[6],
                    'Material' => $data[7],
                    'Modality' => $data[8],
                    'Validity' => $data[9],
                    'WC' => $data[10],
                    'Dk_t' => $data[11],
                    'Quality' => $data[12],
                    'SPH' => $data[13],
                    'CYL' => $data[14],
                    'AXIS' => $data[15],
                    'ADD' => $data[16],
                    'base_carve' => $data[17],
                    'Diameter' => $data[18],
                    'Power_Type' => $data[19],
                    'No_Of_Boxes' => $data[20],
                    'Pieces_Per_Box' => $data[21],
                    'Batch_Number' => $data[22],
                    'Mfg_Date' => $data[23],
                    'Expiry_Date' => $data[24],
                    'Purchase_Base_Price' => $data[25],
                    'Purchase_Price' => $data[25],
                    'Retail_Price' => $data[26],
                    'discount_price' => $data[27],
                    'BB_Price' => $data[28],
                    'hsn_code' => $data[29],
                    'tax_per' => $data[30],
                    'add_tax_per' => $data[31],
                    'tax_rule' => $data[32],
                    'barcode' => $data[33],
                    'invoice_description' => $data[34],
                    'Track_Inventory' => $Track_Inventory,
                    'Allow_Negative_Inventory' => $Allow_Negative,
                    'tray_no' => $data[37],
                    'added_by' => $user->id,
                    'bulkproduct_id' => $lastid,
                ];
            }
            elseif ($type === 'Solution') 
            {
                $fields = [
                    $data[1] ?? null,
                    $data[2] ?? null,
                    $data[6] ?? null,
                    $data[3] ?? null,
                    $data[4] ?? null,
                    $data[5] ?? null
                ];
                $productdetails = implode(' - ', array_filter($fields));
                $productData = [
                    'product_id' => $idgenerate,
                    'productdetails' => $productdetails,
                    'product_code' => $data[0],
                    'product_type' => 'Solution',
                    'product_name' => $data[1],
                    'Company' => $data[2],
                    'Variant' => $data[3],
                    'Type' => $data[4],
                    'Color' => $data[5],
                    'Quality' => $data[6],
                    'Qty' => $data[7],
                    'Purchase_Base_Price' => $data[8],
                    'Purchase_Price' => $data[8],
                    'Retail_Price' => $data[9],
                    'discount_price' => $data[10],
                    'BB_Price' => $data[11],
                    'hsn_code' => $data[12],
                    'tax_per' => $data[13],
                    'add_tax_per' => $data[14],
                    'tax_rule' => $data[15],
                    'barcode' => $data[16],
                    'invoice_description' => $data[17],
                    'Track_Inventory' => $Track_Inventory,
                    'Allow_Negative_Inventory' => $Allow_Negative,
                    'tray_no' => $data[20],
                    'added_by' => $user->id,
                    'bulkproduct_id' => $lastid,
                ];
            } elseif ($type === 'Other') {
                
                $fields = [
                    $data[1] ?? null,
                    $data[2] ?? null,
                    $data[4] ?? null,
                    $data[3] ?? null,
                    $data[5] ?? null,
					$data[6] ?? null,
					$data[7] ?? null
                ];
                $productdetails = implode(' - ', array_filter($fields));
                
                $productData = [
                    'product_id' => $idgenerate,
                    'productdetails' => $productdetails,
                    'product_code' => $data[0],
                    'product_type' => 'Other',
                    'product_name' => $data[1],
                    'Company' => $data[2],
                    'Type' => $data[3],
                    'Color' => $data[4],
                    'Shape' => $data[5],
                    'Size' => $data[6],
                    'Quality' => $data[7],
                    'Qty' => $data[8],
                    'Purchase_Base_Price' => $data[9],
                    'Purchase_Price' => $data[9],
                    'Retail_Price' => $data[10],
                    'discount_price' => $data[11],
                    'BB_Price' => $data[12],
                    'hsn_code' => $data[13],
                    'tax_per' => $data[14],
                    'add_tax_per' => $data[15],
                    'tax_rule' => $data[16],
                    'barcode' => $data[17],
                    'invoice_description' => $data[18],
                    'Track_Inventory' => $Track_Inventory,
                    'Allow_Negative_Inventory' => $Allow_Negative,
                    'tray_no' => $data[21],
                    'added_by' => $user->id,
                    'bulkproduct_id' => $lastid,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | VALID / INVALID
            |--------------------------------------------------------------------------
            */

            if (empty($productErrors))
            {
                $validProducts[] = [
                    'data' => $productData,
                ];
            }
            else
            {
                $rowData = [];

                foreach ($header as $key => $head)
                {
                    $rowData[$head] = $data[$key] ?? '';
                }

                $rowData['Error'] =
                    implode(', ', $productErrors);

                $invalidProducts[] = $rowData;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT VALID PRODUCTS
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try
        {
            foreach ($validProducts as $productWrap)
            {
                $product = $productWrap['data'];

                $exists = DB::table('tbl_product_code')
                    ->where('product_type', $product['product_type'])
                    ->where('product_code', $product['product_code'])
                    ->where('productdetails', $product['productdetails'])
                    ->count();

                if ($exists == 0)
                {
                    DB::table('tbl_product_code')
                        ->insert($product);
                }
            }

            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollback();

            return back()->with(
                'error',
                'Database insert failed : '
                . $e->getMessage()
            );
        }


        
        /*
        |--------------------------------------------------------------------------
        | INVALID ROW EXPORT
        |--------------------------------------------------------------------------
        */
        
        if (!empty($invalidProducts))
        {
            $errorFileName =
                'invalid_product_' . time() . '.csv';
        
            /*
            |--------------------------------------------------------------------------
            | CREATE DIRECTORY
            |--------------------------------------------------------------------------
            */
        
            $directory =
                storage_path('app/public');
        
            if (!file_exists($directory))
            {
                mkdir($directory, 0777, true);
            }
        
            /*
            |--------------------------------------------------------------------------
            | FULL FILE PATH
            |--------------------------------------------------------------------------
            */
        
            $filePath =
                $directory . '/' . $errorFileName;
        
            /*
            |--------------------------------------------------------------------------
            | OPEN FILE
            |--------------------------------------------------------------------------
            */
        
            $file = fopen($filePath, 'w');
        
            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */
        
            fputcsv(
                $file,
                array_keys($invalidProducts[0])
            );
        
            /*
            |--------------------------------------------------------------------------
            | DATA
            |--------------------------------------------------------------------------
            */
        
            foreach ($invalidProducts as $row)
            {
                fputcsv($file, $row);
            }
        
            fclose($file);
        
            /*
            |--------------------------------------------------------------------------
            | CHECK FILE EXISTS
            |--------------------------------------------------------------------------
            */
        
            if (!file_exists($filePath))
            {
                return back()->with(
                    'error',
                    'Invalid file not generated.'
                );
            }
        
            /*
            |--------------------------------------------------------------------------
            | UPDATE DATABASE
            |--------------------------------------------------------------------------
            */
        
            DB::table('tbl_product_import')
                ->where('id', $lastid)
                ->update([
                    'invalid_file' => $errorFileName
                ]);
        
            /*
            |--------------------------------------------------------------------------
            | STOP IMPORT
            |--------------------------------------------------------------------------
            */
        
            return back()->with(
                'error',
                count($invalidProducts) .
                ' invalid rows found. Download invalid file from table.'
            );
        }
        
        /*
        |--------------------------------------------------------------------------
        | INSERT ALL VALID PRODUCTS
        |--------------------------------------------------------------------------
        */
        
        DB::beginTransaction();
        
        try
        {
            $insertCount = 0;
        
            foreach ($validProducts as $productWrap)
            {
                $product = $productWrap['data'];
        
                /*
                |--------------------------------------------------------------------------
                | DUPLICATE CHECK
                |--------------------------------------------------------------------------
                */
        
                $exists = DB::table('tbl_product_code')
                    ->where('product_type', $product['product_type'])
                    ->where('product_code', $product['product_code'])
                    ->where('productdetails', $product['productdetails'])
                    ->count();
        
                if ($exists == 0)
                {
                    DB::table('tbl_product_code')
                        ->insert($product);
        
                    $insertCount++;
                }
                else
                {
                    /*
                    |--------------------------------------------------------------------------
                    | DUPLICATE RECORD AS INVALID
                    |--------------------------------------------------------------------------
                    */
        
                    $duplicateRow = $product;
        
                    $duplicateRow['Error'] =
                        'Duplicate Product Already Exists';
        
                    $invalidProducts[] = $duplicateRow;
                }
            }
        
            /*
            |--------------------------------------------------------------------------
            | FINAL INSERT COUNT CHECK
            |--------------------------------------------------------------------------
            */
        
            if ($insertCount != count($validProducts))
            {
                DB::rollback();
        
                return back()->with(
                    'error',
                    'Some records failed during insert process.'
                );
            }
        
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollback();
        
            return back()->with(
                'error',
                'Database insert failed : ' .
                $e->getMessage()
            );
        }
        
        /*
        |--------------------------------------------------------------------------
        | SUCCESS MESSAGE
        |--------------------------------------------------------------------------
        */
        
        return back()->with(
            'success',
            count($validProducts) .
            ' products uploaded successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD INVALID FILE
    |--------------------------------------------------------------------------
    */

    public function downloadInvalidFile($file)
    {
        $filePath =
            storage_path('app/public/' . $file);

        if (!file_exists($filePath))
        {
            return back()->with(
                'error',
                'Invalid file not found.'
            );
        }

        return response()->download($filePath);
    }

    /*
    |--------------------------------------------------------------------------
    | UNIQUE RANDOM ID
    |--------------------------------------------------------------------------
    */

    public function generateUniqueRandomId(
        $length = 6,
        $table = 'tbl_product_code',
        $column = 'product_id',
        $min = 100000,
        $max = 999999
    )
    {
        do
        {
            $id = random_int($min, $max);

        } while (
            DB::table($table)
                ->where($column, $id)
                ->exists()
        );

        return $id;
    }
}