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
use App\Models\product\Product;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventorySummaryExport;
use App\Exports\TransferSummaryExport;
use App\Exports\PurchaseSummaryExport;
use App\Exports\PurchaseReturnSummaryExport;
use App\Exports\LossSummaryExport;
use App\Exports\PendingOrderSummaryExport;
use App\Exports\SalesSummaryExport;
use App\Exports\SalesReturnSummaryExport;
use App\Exports\GSTinputSummaryExport;


class ReportController extends Controller
{
    function __construct()
    {
        
    }
    
    public $view_route = 'report';
    

    public function generateReports()
    {
        $setting['page_title'] = 'Report List';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/report-list',$setting);
    }
    
    
    public function inventoryReport()
    {
        $setting['page_title'] = 'Inventory Report';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/inventory-report',$setting);
    }
    
    public function inventorydataReport(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $product_type = $request->input('product_type');
        $search = $request->input('search');
        $inv_status = $request->input('inv_status');
        $stid = $request->input('stid');
        
        if(!empty($stid))
        {
            $query = DB::table('tbl_inventory_levels')->where('store_id', $stid);
        }
        else
        {
            if($store_id == 0)
            {
                $query = DB::table('tbl_inventory_levels');
            }
            else
            {
                $query = DB::table('tbl_inventory_levels')
                ->where('store_id', $store_id);
            }
        }
        
        
        
       
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('product_details', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }
    
        if (!empty($product_type)) {
            $query->where('product_type', $product_type);
        }
    
        if ($inv_status == '2') {
            $query->where('available_quantity', '>', 0);
        } elseif ($inv_status == '3') {
            $query->where('available_quantity', '<', 0);
        }
    
        $types = ['Frame', 'Glass', 'Goggles', 'Lens', 'Solution', 'Other', 'Non Chargeable'];
    
        $results = $query->select('product_type', DB::raw('SUM(available_quantity) as total'))
            ->groupBy('product_type')
            ->pluck('total', 'product_type')
            ->toArray();
    
        $sums = [];
        foreach ($types as $type) {
            $sums[$type] = [
                'inv' => (float)($results[$type] ?? 0)
            ];
        }
    
        $totalAvailableQty = array_sum(array_column($sums, 'inv'));
    
        $rows = '';
        foreach ($sums as $type => $data) {
            $rows .= "<tr><td>{$type}</td><td>{$data['inv']}</td></tr>";
        }
    
        $chartLabels = json_encode(array_keys($sums));
        $chartSeries = json_encode(array_column($sums, 'inv'));
        $colors = json_encode([
            "#4a32d4", "#f7592d", "#f7be2d", "#3abc1d", "#f72d66", "#2dcbf7", "#888888"
        ]);
    
        $legendRows = '';
        $colorList = ['bg-primary','bg-orange','bg-warning','bg-teal','bg-danger','bg-info','bg-secondary'];
        $i = 0;
        foreach ($sums as $type => $data) {
            $legendRows .= "
                <tr class='border-bottom'>
                    <td class='p-2'><div class='w-3 h-3 {$colorList[$i]} mr-2 mt-1 brround'></div></td>
                    <td class='p-2'>{$type}</td>
                    <td class='p-2'>{$data['inv']}</td>
                </tr>";
            $i++;
        }
    
        $data = '
            <div class="row">
                <div class="col-lg-6">
                    <table border="1" cellspacing="0" cellpadding="8" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
                        <thead style="background-color: #00484a; color: #fff;">
                            <tr><th>Product</th><th>Total Inventory</th></tr>
                        </thead>
                        <tbody>'.$rows.'</tbody>
                        <tfoot style="background-color: #00484a; color: #fff; font-weight: bold;">
                            <tr><td>Total</td><td>'.$totalAvailableQty.'</td></tr>
                        </tfoot>
                    </table>
                </div>
    
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-xl-7 col-lg-6 col-md-6">
                            <div id="chart"></div>
                        </div>
                        <div class="col-xl-5 col-lg-6 col-md-6">
                            <table class="table table-hover mb-0">
                                <tbody>'.$legendRows.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
            <script>
            var options = {
                chart: { width: 300, height: 330, type: "donut" },
                dataLabels: { enabled: false },
                series: '.$chartSeries.',
                colors: '.$colors.',
                labels: '.$chartLabels.',
                legend: { show: false }
            };
            new ApexCharts(document.querySelector("#chart"), options).render();
    
            var barOptions = {
                chart: { type: "bar", height: 400 },
                series: [{ name: "Inventory", data: '.$chartSeries.' }],
                xaxis: { categories: '.$chartLabels.' },
                colors: ["#4a32d4"],
                dataLabels: { enabled: true },
                tooltip: {
                    y: { formatter: function (val) { return val.toLocaleString(); } }
                }
            };
            new ApexCharts(document.querySelector("#barChart"), barOptions).render();
            </script>
    
            <div class="row"><div class="col-xl-12"><div id="barChart"></div></div></div>
        ';
    
        return response()->json([
            'status' => 'success',
            'inventorydata_section' => $data
        ]);
    }

    
    public function inventoryExcelDownload(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $product_type = $request->input('product_type');
        $search = $request->input('search');
        $inv_status = $request->input('inv_status');
        $storeid = $request->input('store_id');

        $export = new InventorySummaryExport($store_id, $product_type, $search, $inv_status, $storeid);

        $fileName = 'inventory_report_' . time() . '.xlsx';

        return Excel::download(new InventorySummaryExport($store_id, $product_type, $search, $inv_status, $storeid), $fileName);
    }
    
    public function stockTransferReport()
    {
        $setting['page_title'] = 'Transfer Stock Report';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/stock-transfer-report',$setting);
    }
    
    
    public function transferdataReport(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $from_store = $request->input('from_store') ?? ($store_id ?: '0');
        $to_store = $request->input('to_store');
        $product_type = $request->input('product_type');
        $search = $request->input('search');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
    
        $query = DB::table('tbl_transfer_stock')
            ->when($from_store, fn($q) => $q->where('from_store', $from_store))
            ->when($to_store, fn($q) => $q->where('to_store', $to_store))
            ->when($search, fn($q) => $q->where(function($q) use ($search) {
                $q->where('product_details', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            }))
            ->when($date_from && $date_to, fn($q) => $q->whereBetween('created_at', [
                Carbon::parse($date_from)->startOfDay(),
                Carbon::parse($date_to)->endOfDay()
            ]))
            ->when($product_type, fn($q) => $q->where('product_type', $product_type));
    
        // Product types
        $types = ['Frame', 'Glass', 'Goggles', 'Lens', 'Solution', 'Other', 'Non Chargeable'];
    
        // Aggregate results
        $results = $query->select(
            'product_type',
            DB::raw('SUM(transfer_stock) as total_transfer'),
            DB::raw('SUM(purchase_price) as total_purchase'),
            DB::raw('SUM(retail_price) as total_retail')
        )
        ->groupBy('product_type')
        ->get()
        ->keyBy('product_type')
        ->toArray();
    
        // Prepare sums for each type
        $sums = [];
        foreach ($types as $type) {
            $sums[$type] = [
                'transfer' => $results[$type]->total_transfer ?? 0,
                'purchase' => $results[$type]->total_purchase ?? 0,
                'retail' => $results[$type]->total_retail ?? 0,
            ];
        }
    
        // Totals for footer
        $totalTransfer = array_sum(array_column($sums, 'transfer'));
        $totalPurchase = array_sum(array_column($sums, 'purchase'));
        $totalRetail = array_sum(array_column($sums, 'retail'));
    
        // Build HTML table rows
        $rows = '';
        foreach ($sums as $type => $values) {
            $rows .= "<tr>
                <td>{$type}</td>
                <td>{$values['transfer']}</td>
                <td>Rs " . round($values['purchase'], 2) . "</td>
                <td>Rs " . round($values['retail'], 2) . "</td>
            </tr>";
        }
    
        // Chart data (ApexCharts)
        $chartLabels = json_encode(array_keys($sums)); // ["Frame","Glass",...]
        $chartSeries = json_encode(array_values(array_map(fn($v) => (float)$v['transfer'], $sums)));
    
        $colors = json_encode(["#4a32d4", "#f7592d", "#f7be2d", "#3abc1d", "#f72d66", "#2dcbf7", "#888888"]);
    

    
        // Legend table
        $legendRows = '';
        $colorList = ['bg-primary','bg-orange','bg-warning','bg-teal','bg-danger','bg-info','bg-secondary'];
        $i = 0;
        foreach ($sums as $type => $values) {
            $legendRows .= "
            <tr class='border-bottom'>
                <td class='p-2'><div class='w-3 h-3 {$colorList[$i]} mr-2 mt-1 brround'></div></td>
                <td class='p-2'>{$type}</td>
                <td class='p-2'>{$values['transfer']}</td>
            </tr>";
            $i++;
        }
    
        // Main HTML
        $data = '
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-table">
                        <div id="processingLoader" class="processing-loader" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <strong class="text-success">Please wait...</strong>
                                        <div class="spinner-border ms-auto text-success spinner-grow" role="status" aria-hidden="true"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="col-lg-6">
                    <table border="1" cellspacing="0" cellpadding="8"
                           style="border-collapse: collapse; width: 100%; margin: auto; font-family: Arial, sans-serif;">
                        <thead style="background-color: #00484a; color: #fff;">
                            <tr>
                                <th>Product</th>
                                <th>Total Transfer</th>
                                <th>Total Purchase Value</th>
                                <th>Total Sale Value</th>
                            </tr>
                        </thead>
                        <tbody>'.$rows.'</tbody>
                        <tfoot style="background-color: #00484a; color: #fff; font-weight: bold;">
                            <tr>
                                <td>Total</td>
                                <td>'.$totalTransfer.'</td>
                                <td>'. round($totalPurchase, 2).'</td>
                                <td>'. round($totalRetail, 2).'</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
    
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-xl-7 col-lg-6 col-md-6">
                            <div class="overflow-hidden justify-content-center mx-auto text-center align-items-center">
                                <div id="chart"></div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-6 col-md-6">
                            <table class="table table-hover mb-0">
                                <tbody>'.$legendRows.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    
            <script>
                var options = {
                    chart: {
                        width: 300,
                        height: 330,
                        type: "donut",
                    },
                    dataLabels: { enabled: false },
                    series: '.$chartSeries.',
                    colors: '.$colors.',
                    labels: '.$chartLabels.',
                    legend: { show: false },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: { width: 250 },
                            legend: { show: false }
                        }
                    }]
                };
    
                var chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();
            </script>
    
            
    
              <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <div class="row">
              <div class="col-xl-12 col-lg-12 col-md-12">
                <div id="barChart"></div>
            </div>
            </div>
            
            <script>
                var barOptions = {
                chart: {
                    type: "bar",
                    height: 400
                },
                series: [{
                    name: "Transfer Stock",
                    data: ' . json_encode(array_column($sums, "transfer")) . '
                }],
                xaxis: {
                    categories: ' . json_encode(array_keys($sums)) . '
                },
                colors: ["#4a32d4"],
                dataLabels: {
                    enabled: true
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString();
                        }
                    }
                }
            };
            new ApexCharts(document.querySelector("#barChart"), barOptions).render();
            </script>
        ';
    
        $response['status'] = 'success';
        $response['inventorydata_section'] = $data;
    
        echo json_encode($response);
    }

    
    
    public function transferstockReportDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $from_store = $request->input('from_store') ?? $store_id;
        $to_store = $request->input('to_store');
        $product_type = $request->input('product_type');
        $search = $request->input('search.value');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $start = $request->input('start', 0);
        $limit = $request->input('length', 10);
    
        $query = DB::table('tbl_transfer_stock as t')
            ->leftJoin('users as u', 'u.id', '=', 't.transfer_by')
            ->leftJoin('tbl_store as fs', 'fs.id', '=', 't.from_store')
            ->leftJoin('tbl_store as ts', 'ts.id', '=', 't.to_store')
            ->select(
                't.*',
                'u.name as transfer_by_name',
                'fs.store_name as from_store_name',
                'ts.store_name as to_store_name'
            )
            ->when($from_store, fn($q) => $q->where('t.from_store', $from_store))
            ->when($to_store, fn($q) => $q->where('t.to_store', $to_store))
            ->when($search, fn($q) => $q->where(function($q) use ($search) {
                $q->where('t.product_details', 'like', "%{$search}%")
                  ->orWhere('t.product_code', 'like', "%{$search}%");
            }))
            ->when($date_from && $date_to, fn($q) => $q->whereBetween('t.created_at', [
                Carbon::parse($date_from)->startOfDay(),
                Carbon::parse($date_to)->endOfDay()
            ]))
            ->when($product_type, fn($q) => $q->where('t.product_type', $product_type));
    
        $totalData = DB::table('tbl_transfer_stock')->count();
        $totalFiltered = (clone $query)->count();
    
        $templates = $query
            ->orderBy('t.transfer_id', 'DESC')
            ->offset($start)
            ->limit($limit)
            ->get();
    
        $data = [];
        $i = $start + 1;
        foreach ($templates as $template) {
            $data[] = [
                'sr_no' => $i++,
                'from_store' => $template->from_store_name,
                'to_store' => $template->to_store_name,
                'refrence_no' => $template->refrence_no,
                'product_type' => $template->product_type,
                'product_code' => $template->product_code,
                'product_details' => $template->product_details,
                'purchase_price' => 'Rs ' . number_format($template->purchase_price, 2),
                'retail_price' => 'Rs ' . number_format($template->retail_price, 2),
                'transfer_by' => $template->transfer_by_name,
                'transfer_date' => date('d M, Y', strtotime($template->created_at)),
            ];
        }
    
        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ]);
    }
    
    
    public function transferExcelDownload(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $product_type = $request->input('product_type');
        $search = $request->input('search');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $from_store = $request->input('from_store') ?? $store_id;
        $to_store = $request->input('to_store');

        $export = new TransferSummaryExport($product_type, $search, $date_from, $date_to, $from_store, $to_store);

        $fileName = 'transfer_report_' . time() . '.xlsx';

        return Excel::download(new TransferSummaryExport($product_type, $search, $date_from, $date_to, $from_store, $to_store), $fileName);
    }
    
    
    public function purchaseReport()
    {
        $setting['page_title'] = 'Purchase Report';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/purchase-report',$setting);
    }
    
    
    
    public function purchasedataReport(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        $storeId       = $request->input('store_id');
        $productType   = $request->input('product_type');
        $search        = $request->input('search');
        $supplierName  = $request->input('supplier_name');
        $dateFrom      = $request->input('date_from');
        $dateTo        = $request->input('date_to');
        $dateFrom = date('Y-m-d', strtotime($dateFrom));
        $dateTo   = date('Y-m-d', strtotime($dateTo));
        

        $finalStoreId = $storeId ?: $authStoreId;
    
        $query = DB::table('tbl_purchase_deatils as pd')
            ->leftJoin('tbl_purchase as p', 'p.purchase_id', '=', 'pd.purchase_id')
            ->select(
                'pd.product_type','p.purchase_date','p.supplier_name',
                DB::raw('SUM(pd.total_purchase_price) as total_purchase_price'),
                DB::raw('SUM(pd.qty) as total_qty'),
                DB::raw('SUM(pd.box_detail) as total_box_detail')
            );
    
        if ($finalStoreId != 0) {
            $query->where('pd.store_id', $finalStoreId);
        }
    
        if (!empty($productType)) {
            $query->where('pd.product_type', $productType);
        }
    
        if (!empty($supplierName)) {
            $query->where('p.supplier_name', $supplierName);
        }
    
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pd.product_details', 'like', "%{$search}%")
                  ->orWhere('pd.product_code', 'like', "%{$search}%");
            });
        }
    
        if (!empty($dateFrom) && !empty($dateTo)) {
             $query->whereBetween('p.purchase_date', [$dateFrom,  $dateTo]);
        }

        $results = $query->groupBy('pd.product_type','p.purchase_date','p.supplier_name')->get();
    
        $grouped = $results->groupBy('product_type');
    
        $types = ['Frame', 'Glass', 'Goggles', 'Lens', 'Solution', 'Other', 'Non Chargeable'];
    
        $sums = [];
        foreach ($types as $type) 
        {
            if($type == "Lens")
            {
                $qty      = isset($grouped[$type]) ? $grouped[$type]->sum('total_box_detail') : 0;
            }
            else
            {
                $qty      = isset($grouped[$type]) ? $grouped[$type]->sum('total_qty') : 0;
            }
            
            $purchase = isset($grouped[$type]) ? $grouped[$type]->sum('total_purchase_price') : 0;
            
    
            $sums[$type] = [
                'purchase'  => round($purchase, 2) ,
                'inventory' => (float) $qty
            ];
        }
    
        $totalPurchase  = array_sum(array_column($sums, 'purchase'));
        $totalInventory = array_sum(array_column($sums, 'inventory'));
    
        $colors = [
            "#4a32d4", "#f7592d", "#f7be2d", "#3abc1d", "#f72d66", "#2dcbf7", "#888888"
        ];
    
        $purchaseChartColumns = [];
        $inventoryChartColumns = [];
        $chartColors = [];
    
        $i = 0;
        foreach ($sums as $type => $data) 
        {
            
            $purchaseChartColumns[] = '["' . $type . '", ' . $data['purchase'] . ']';
            $inventoryChartColumns[] = '["' . $type . '", ' . $data['inventory'] . ']';
            $chartColors[$type] = $colors[$i] ?? '#ccc';
            $i++;
        }
    
        $rows = '';
        foreach ($sums as $type => $values) 
        {
            $rows .= "
                <tr>
                    <td>{$type}</td>
                    <td>Rs " . number_format($values['purchase'], 2) . "</td>
                    <td>" . number_format($values['inventory'], 0) . "</td>
                </tr>";
        }
    
        $data = '
            <div class="row">
                <div class="col-lg-12">
                    <table border="1" cellspacing="0" cellpadding="8"
                        style="border-collapse: collapse; width: 100%; margin: auto; font-family: Arial, sans-serif;">
                        <thead style="background-color: #00484a; color: #fff;">
                            <tr>
                                <th>Product</th>
                                <th>Total Purchase Value</th>
                                <th>Total Qty</th>
                            </tr>
                        </thead>
                        <tbody>' . $rows . '</tbody>
                        <tfoot style="background-color: #00484a; color: #fff; font-weight: bold;">
                            <tr>
                                <td>Total</td>
                                <td>Rs ' . number_format($totalPurchase, 2) . '</td>
                                <td>' . number_format($totalInventory, 0) . '</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>    
            <div class="row">    
    
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div id="chart-pie2" class="chartsh"></div>
                        </div>
                        <h3 style="text-align: center;font-size: 16px;font-weight: 600;">With Purchase Price</h3>
                    </div>
                </div>
    
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div id="chart-pie3" class="chartsh"></div>
                        </div>
                        <h3 style="text-align: center;font-size: 16px;font-weight: 600;">With Inventory (Qty)</h3>
                    </div>
                </div>
            </div>
    
            <script>
                var purchaseChart = c3.generate({
                    bindto: "#chart-pie2",
                    data: {
                        columns: [' . implode(',', $purchaseChartColumns) . '],
                        type: "pie",
                        colors: ' . json_encode($chartColors) . ',
                    },
                    tooltip: {
                        format: {
                            value: function (value, ratio, id) {
                                return d3.format(",.2f")(value);
                            }
                        }
                    },
                    pie: {
                        label: {
                            format: function (value, ratio, id) {
                                return d3.format(",.2f")(value);
                            }
                        }
                    },
                     legend: { show: true },
                     padding: { bottom: 0, top: 0 },
                    size: {
                        height: 350,   
                        width: 350     
                    }
                });
    
                var inventoryChart = c3.generate({
                    bindto: "#chart-pie3",
                    data: {
                        columns: [' . implode(',', $inventoryChartColumns) . '],
                        type: "pie",
                        colors: ' . json_encode($chartColors) . ',
                    },
                    tooltip: {
                        format: {
                            value: function (value, ratio, id) {
                                return value;
                            }
                        }
                    },
                    pie: {
                        label: {
                            format: function (value, ratio, id) {
                                return value;
                            }
                        }
                    },
                    legend: { show: true },
                    padding: { bottom: 0, top: 0 },
                    size: {
                            height: 350,   
                            width: 350     
                        }
                    });
            </script>
             <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <div class="row">
              <div class="col-xl-12 col-lg-12 col-md-12">
                <div id="barChart"></div>
            </div>
            </div>
            
            <script>
                var barOptions = {
                chart: {
                    type: "bar",
                    height: 400
                },
                series: [{
                    name: "Purchase Price",
                    data: ' . json_encode(array_column($sums, "purchase")) . '
                }],
                xaxis: {
                    categories: ' . json_encode(array_keys($sums)) . '
                },
                colors: ["#4a32d4"],
                dataLabels: {
                    enabled: true
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString();
                        }
                    }
                }
            };
            new ApexCharts(document.querySelector("#barChart"), barOptions).render();
            </script>
        ';
    
        return response()->json([
            'status' => 'success',
            'purchasedata_section' => $data,
        ]);
    }
    
    
    public function purchaseReportDatatable(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        $storeId      = $request->input('store_id');
        $productType  = $request->input('product_type');
        $search       = $request->input('search1');
        $supplierName = $request->input('supplier_name');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');
    
        if (!empty($dateFrom)) $dateFrom = date('Y-m-d', strtotime($dateFrom));
        if (!empty($dateTo))   $dateTo   = date('Y-m-d', strtotime($dateTo));
    
        $finalStoreId = $storeId ?: $authStoreId;
    
        $start = $request->input('start', 0);
        $limit = $request->input('length', 10);
    
        $query = DB::table('tbl_purchase_deatils as pd')
            ->leftJoin('tbl_purchase as p', 'p.purchase_id', '=', 'pd.purchase_id')
            ->select(
                'pd.*',
                'p.*'
            );
    
        if ($finalStoreId != 0) {
            $query->where('pd.store_id', $finalStoreId);
        }
    
        if (!empty($productType)) {
            $query->where('pd.product_type', $productType);
        }
    
        if (!empty($supplierName)) {
            $query->where('p.supplier_name', $supplierName);
        }
    
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pd.product_details', 'like', "%{$search}%")
                  ->orWhere('pd.product_code', 'like', "%{$search}%");
            });
        }
    
        if (!empty($dateFrom) && !empty($dateTo)) {
            $query->whereBetween('p.purchase_date', [$dateFrom, $dateTo]);
        }
    
        $totalData = DB::table('tbl_purchase_deatils')->count();
        $totalFiltered = (clone $query)->count();
    
        $templates = $query
            ->orderBy('pd.id', 'DESC')
            ->offset($start)
            ->limit($limit)
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
            
            if($template->product_type == 'Lens')
            {
               $qty =  $template->box_detail;
            }
            else
            {
               $qty =  $template->qty;
            }
            
            if($template->product_type == 'Lens')
            {

                $description = $template->product_details.'<BR><strong style="color:red"> Box per peice :  '.$template->perbox_detail.'</strong>';
            }
            else
            {
                $description = $template->product_details;
            }
                
            $data[] = [
                'sr_no'           => $i++,
                'store_name'      => $store_name,
                'supplier'        => $template->supplier_name,
                'purchase_details' => 'Bill No : <span class="badge badge-success">'.$template->bill_no.'</span><br> Purchase Date : '.date("d-m-Y", strtotime($template->purchase_date)),
                'product_details' => 'Product :'.$template->product_type.'<br> Product Code :'.$template->product_code.'<br>Product id :'.$template->product_id,
                'description'     => $description ?? '',
                'basic_price'     => $template->product_base_price ?? '',
                'gst'             => 'HSN Code :'.$template->hsn_code.'<Br>GST :'.$template->gst.' % <Br>GST Amount :'.$template->gst_amt,
                'purchase_price'  => $template->product_purchase_price ?? '',
                'qty'             => $qty ?? '',
                'total_amount'    => $template->total_purchase_price ?? '',
                'sale_price'      => $template->product_retail_price ?? '',
            ];
        }
    
        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ]);
    }
    
    
    public function purchaseExcelDownload(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        $storeId       = $request->input('store_id');
        $productType   = $request->input('product_type');
        $search        = $request->input('search');
        $supplierName  = $request->input('supplier_name');
        $dateFrom      = $request->input('date_from');
        $dateTo        = $request->input('date_to');
        $dateFrom = date('Y-m-d', strtotime($dateFrom));
        $dateTo   = date('Y-m-d', strtotime($dateTo));

        $export = new PurchaseSummaryExport($productType, $search, $supplierName, $dateFrom, $dateTo, $storeId);

        $fileName = 'transfer_report_' . time() . '.xlsx';

        return Excel::download(new PurchaseSummaryExport($productType, $search, $supplierName, $dateFrom, $dateTo, $storeId), $fileName);
    }
    
    
    public function purchasereturnReport()
    {
        $setting['page_title'] = 'Purchase Return Report';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/purchase-return-report',$setting);
    }
    
    
    
    public function purchasereturndataReport(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        $storeId       = $request->input('store_id');
        $productType   = $request->input('product_type');
        $search        = $request->input('search');
        $supplierName  = $request->input('supplier_name');
        $dateFrom      = $request->input('date_from');
        $dateTo        = $request->input('date_to');
        $dateFrom = date('Y-m-d', strtotime($dateFrom));
        $dateTo   = date('Y-m-d', strtotime($dateTo));
        

        $finalStoreId = $storeId ?: $authStoreId;
    
        $query = DB::table('tbl_purchase_deatils as pd')
            ->leftJoin('tbl_purchase as p', 'p.purchase_id', '=', 'pd.purchase_id')
            ->select(
                'pd.product_type','p.purchase_date','p.supplier_name',
                DB::raw('SUM(pd.total_purchase_price) as total_purchase_price'),
                DB::raw('SUM(pd.qty) as total_qty'),
                DB::raw('SUM(pd.box_detail) as total_box_detail')
            );
    
        if ($finalStoreId != 0) {
            $query->where('pd.store_id', $finalStoreId);
        }
    
        if (!empty($productType)) {
            $query->where('pd.product_type', $productType);
        }
    
        if (!empty($supplierName)) {
            $query->where('p.supplier_name', $supplierName);
        }
    
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pd.product_details', 'like', "%{$search}%")
                  ->orWhere('pd.product_code', 'like', "%{$search}%");
            });
        }
    
        if (!empty($dateFrom) && !empty($dateTo)) {
             $query->whereBetween('pd.return_date', [$dateFrom,  $dateTo]);
        }
        $query->where('pd.p_status', '1');

        $results = $query->groupBy('pd.product_type','p.purchase_date','p.supplier_name')->get();
    
        $grouped = $results->groupBy('product_type');
    
        $types = ['Frame', 'Glass', 'Goggles', 'Lens', 'Solution', 'Other', 'Non Chargeable'];
    
        $sums = [];
        foreach ($types as $type) 
        {
            if($type == "Lens")
            {
                $qty      = isset($grouped[$type]) ? $grouped[$type]->sum('total_box_detail') : 0;
            }
            else
            {
                $qty      = isset($grouped[$type]) ? $grouped[$type]->sum('total_qty') : 0;
            }
            
            $purchase = isset($grouped[$type]) ? $grouped[$type]->sum('total_purchase_price') : 0;
            
    
            $sums[$type] = [
                'purchase'  => round($purchase, 2) ,
                'inventory' => (float) $qty
            ];
        }
    
        $totalPurchase  = array_sum(array_column($sums, 'purchase'));
        $totalInventory = array_sum(array_column($sums, 'inventory'));
    
        $colors = [
            "#4a32d4", "#f7592d", "#f7be2d", "#3abc1d", "#f72d66", "#2dcbf7", "#888888"
        ];
    
        $purchaseChartColumns = [];
        $inventoryChartColumns = [];
        $chartColors = [];
    
        $i = 0;
        foreach ($sums as $type => $data) 
        {
            
            $purchaseChartColumns[] = '["' . $type . '", ' . $data['purchase'] . ']';
            $inventoryChartColumns[] = '["' . $type . '", ' . $data['inventory'] . ']';
            $chartColors[$type] = $colors[$i] ?? '#ccc';
            $i++;
        }
    
        $rows = '';
        foreach ($sums as $type => $values) 
        {
            $rows .= "
                <tr>
                    <td>{$type}</td>
                    <td>Rs " . number_format($values['purchase'], 2) . "</td>
                    <td>" . number_format($values['inventory'], 0) . "</td>
                </tr>";
        }
    
        $data = '
            <div class="row">
                <div class="col-lg-12">
                    <table border="1" cellspacing="0" cellpadding="8"
                        style="border-collapse: collapse; width: 100%; margin: auto; font-family: Arial, sans-serif;">
                        <thead style="background-color: #00484a; color: #fff;">
                            <tr>
                                <th>Product</th>
                                <th>Total Purchase Return Value</th>
                                <th>Total Qty</th>
                            </tr>
                        </thead>
                        <tbody>'. $rows .'</tbody>
                        <tfoot style="background-color: #00484a; color: #fff; font-weight: bold;">
                            <tr>
                                <td>Total</td>
                                <td>Rs '. number_format($totalPurchase, 2) .'</td>
                                <td>'. number_format($totalInventory, 0) .'</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>    
            <div class="row">    
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div id="chart-pie2" class="chartsh"></div>
                        </div>
                        <h3 style="text-align: center;font-size: 16px;font-weight: 600;">With Purchase Price</h3>
                    </div>
                </div>
    
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div id="chart-pie3" class="chartsh"></div>
                        </div>
                        <h3 style="text-align: center;font-size: 16px;font-weight: 600;">With Inventory (Qty)</h3>
                    </div>
                </div>
            </div>
    
            <script>
                var purchaseChart = c3.generate({
                    bindto: "#chart-pie2",
                    data: {
                        columns: [' . implode(',', $purchaseChartColumns) . '],
                        type: "pie",
                        colors: ' . json_encode($chartColors) . ',
                    },
                    tooltip: {
                        format: {
                            value: function (value, ratio, id) {
                                return d3.format(",.2f")(value);
                            }
                        }
                    },
                    pie: {
                        label: {
                            format: function (value, ratio, id) {
                                return d3.format(",.2f")(value);
                            }
                        }
                    },
                     legend: { show: true },
                     padding: { bottom: 0, top: 0 },
                    size: {
                        height: 350,   
                        width: 350     
                    }
                });
    
                var inventoryChart = c3.generate({
                    bindto: "#chart-pie3",
                    data: {
                        columns: [' . implode(',', $inventoryChartColumns) . '],
                        type: "pie",
                        colors: ' . json_encode($chartColors) . ',
                    },
                    tooltip: {
                        format: {
                            value: function (value, ratio, id) 
                            {
                                return value;
                            }
                        }
                    },
                    pie: {
                        label: {
                            format: function (value, ratio, id) 
                            {
                                return value;
                            }
                        }
                    },
                    legend: { show: true },
                    padding: { bottom: 0, top: 0 },
                    size: {
                            height: 350,   
                            width: 350     
                        }
                    });
            </script>
             <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <div class="row">
              <div class="col-xl-12 col-lg-12 col-md-12">
                <div id="barChart"></div>
            </div>
            </div>
            
            <script>
                var barOptions = {
                chart: {
                    type: "bar",
                    height: 400
                },
                series: [{
                    name: "Purchase Price",
                    data: ' . json_encode(array_column($sums, "purchase")) . '
                }],
                xaxis: {
                    categories: ' . json_encode(array_keys($sums)) . '
                },
                colors: ["#4a32d4"],
                dataLabels: {
                    enabled: true
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString();
                        }
                    }
                }
            };
            new ApexCharts(document.querySelector("#barChart"), barOptions).render();
            </script>
        ';
    
        return response()->json([
            'status' => 'success',
            'purchasedata_section' => $data,
        ]);
    }
    
    
    
    public function purchasereturnReportDatatable(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        $storeId      = $request->input('store_id');
        $productType  = $request->input('product_type');
        $search       = $request->input('search1');
        $supplierName = $request->input('supplier_name');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');
    
        if (!empty($dateFrom)) $dateFrom = date('Y-m-d', strtotime($dateFrom));
        if (!empty($dateTo))   $dateTo   = date('Y-m-d', strtotime($dateTo));
    
        $finalStoreId = $storeId ?: $authStoreId;
    
        $start = $request->input('start', 0);
        $limit = $request->input('length', 10);
    
        $query = DB::table('tbl_purchase_deatils as pd')
            ->leftJoin('tbl_purchase as p', 'p.purchase_id', '=', 'pd.purchase_id')
            ->select(
                'pd.*',
                'p.*'
            )
            ->where('pd.p_status', '1');
    
        if ($finalStoreId != 0) {
            $query->where('pd.store_id', $finalStoreId);
        }
    
        if (!empty($productType)) {
            $query->where('pd.product_type', $productType);
        }
    
        if (!empty($supplierName)) {
            $query->where('p.supplier_name', $supplierName);
        }
    
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pd.product_details', 'like', "%{$search}%")
                  ->orWhere('pd.product_code', 'like', "%{$search}%");
            });
        }
    
        if (!empty($dateFrom) && !empty($dateTo)) {
            $query->whereBetween('p.purchase_date', [$dateFrom, $dateTo]);
        }
    
        $totalData = DB::table('tbl_purchase_deatils')->where('p_status', '1')->count();
        $totalFiltered = (clone $query)->count();
    
        $templates = $query
            ->orderBy('pd.id', 'DESC')
            ->offset($start)
            ->limit($limit)
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
            
            if($template->product_type == 'Lens')
            {
               $qty =  $template->box_detail;
            }
            else
            {
               $qty =  $template->qty;
            }
            
            if($template->product_type == 'Lens')
            {
                $description = $template->product_details.'<BR><strong style="color:red"> Box per peice :  '.$template->perbox_detail.'</strong>';
            }
            else
            {
                $description = $template->product_details;
            }
                
            $data[] = [
                'sr_no'           => $i++,
                'store_name'      => $store_name,
                'supplier'        => $template->supplier_name,
                'purchase_details' => 'Bill No : <span class="badge badge-success">'.$template->bill_no.'</span><br> Purchase Date : '.date("d-m-Y", strtotime($template->purchase_date)),
                'product_details' => 'Product :'.$template->product_type.'<br> Product Code :'.$template->product_code.'<br>Product id :'.$template->product_id,
                'description'     => $description ?? '',
                'basic_price'     => $template->product_base_price ?? '',
                'gst'             => 'HSN Code :'.$template->hsn_code.'<Br>GST :'.$template->gst.' % <Br>GST Amount :'.$template->gst_amt,
                'purchase_price'  => $template->product_purchase_price ?? '',
                'qty'             => $qty ?? '',
                'total_amount'    => $template->total_purchase_price ?? '',
                'sale_price'      => $template->product_retail_price ?? '',
            ];
        }
    
        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ]);
    }
    
    
    public function purchasereturnExcelDownload(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        $storeId       = $request->input('store_id');
        $productType   = $request->input('product_type');
        $search        = $request->input('search');
        $supplierName  = $request->input('supplier_name');
        $dateFrom      = $request->input('date_from');
        $dateTo        = $request->input('date_to');
        $dateFrom      = date('Y-m-d', strtotime($dateFrom));
        $dateTo        = date('Y-m-d', strtotime($dateTo));

        $export = new PurchaseReturnSummaryExport($productType, $search, $supplierName, $dateFrom, $dateTo, $storeId);

        $fileName = 'transfer_report_' . time() . '.xlsx';

        return Excel::download(new PurchaseReturnSummaryExport($productType, $search, $supplierName, $dateFrom, $dateTo, $storeId), $fileName);
    }
    
    
    public function lossReport()
    {
        $setting['page_title'] = 'Loss or Damage  Report';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/loss-report',$setting);
    }
    
    
    
    public function lossdataReport(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $product_type = $request->input('product_type');
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $stid = $request->input('store_id');
        
        $activeStoreId = !empty($stid) ? $stid : $store_id;
    
        $query = DB::table('tbl_barcode')
            ->where('store_id', $activeStoreId)
            ->where('t_status', '0')
            ->where('loss_damage', '1')
            ->whereNull('lens_box');
        
        if (!$query)
        {
            if ($activeStoreId > 0) {
                
                $query->where('transfer_store_id', $activeStoreId)->where('loss_damage', '1')->whereNull('lens_box');
            }
        }
    
        if (!empty($dateFrom) && !empty($dateTo)) {
            $query->whereBetween('adj_date', [$dateFrom, $dateTo]);
        }
    
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('product_details', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }
    
        if (!empty($product_type)) {
            $query->where('product_type', $product_type);
        }
    
        // NEW UPDATED QUERY — GROUP BY AND COUNT ONLY
        $groupedResults = $query->select(
                'product_code',
                'product_type',
                'product_details',
                DB::raw('COUNT(*) as total_count')
            )
            ->groupBy('product_code', 'product_type', 'product_details')
            ->get();
    
        // Predefined types
        $types = ['Frame', 'Glass', 'Goggles', 'Lens', 'Solution', 'Other', 'Non Chargeable'];
    
        // Prepare count totals per product type
        $typeTotals = [];
        foreach ($types as $type) {
            $typeTotals[$type] = 0;
        }
    
        foreach ($groupedResults as $row) {
            if (isset($typeTotals[$row->product_type])) {
                $typeTotals[$row->product_type] += $row->total_count;
            }
        }
    
        // Prepare table rows
        $rows = '';
        foreach ($types as $type) {
            $rows .= "<tr><td>{$type}</td><td>{$typeTotals[$type]}</td></tr>";
        }
    
        $totalCount = array_sum($typeTotals);
    
        // Apex Charts Data
        $chartLabels = json_encode(array_keys($typeTotals));
        $chartSeries = json_encode(array_values($typeTotals));
    
        $colors = json_encode([
            "#4a32d4", "#f7592d", "#f7be2d", "#3abc1d", "#f72d66", "#2dcbf7", "#888888"
        ]);
    
        // Legend rows
        $legendRows = '';
        $colorList = ['bg-primary','bg-orange','bg-warning','bg-teal','bg-danger','bg-info','bg-secondary'];
        $i = 0;
    
        foreach ($types as $type) {
            $legendRows .= "
                <tr class='border-bottom'>
                    <td class='p-2'><div class='w-3 h-3 {$colorList[$i]} mr-2 mt-1 brround'></div></td>
                    <td class='p-2'>{$type}</td>
                    <td class='p-2'>{$typeTotals[$type]}</td>
                </tr>";
            $i++;
        }
    
        // Final HTML + JS
        $data = '
            <div class="row">
                <div class="col-lg-6">
                    <table border="1" cellspacing="0" cellpadding="8" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
                        <thead style="background-color: #00484a; color: #fff;">
                            <tr><th>Product</th><th>Total Loss or Damage Stock</th></tr>
                        </thead>
                        <tbody>'.$rows.'</tbody>
                        <tfoot style="background-color: #00484a; color: #fff; font-weight: bold;">
                            <tr><td>Total</td><td>'.$totalCount.'</td></tr>
                        </tfoot>
                    </table>
                </div>
    
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-xl-7 col-lg-6 col-md-6">
                            <div id="chart"></div>
                        </div>
                        <div class="col-xl-5 col-lg-6 col-md-6">
                            <table class="table table-hover mb-0">
                                <tbody>'.$legendRows.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
            <script>
            var options = {
                chart: { width: 300, height: 330, type: "donut" },
                dataLabels: { enabled: false },
                series: '.$chartSeries.',
                colors: '.$colors.',
                labels: '.$chartLabels.',
                legend: { show: false }
            };
            new ApexCharts(document.querySelector("#chart"), options).render();
    
            var barOptions = {
                chart: { type: "bar", height: 400 },
                series: [{ name: "Count", data: '.$chartSeries.' }],
                xaxis: { categories: '.$chartLabels.' },
                colors: ["#4a32d4"],
                dataLabels: { enabled: true },
                tooltip: {
                    y: { formatter: function (val) { return val.toLocaleString(); } }
                }
            };
            new ApexCharts(document.querySelector("#barChart"), barOptions).render();
            </script>
    
            <div class="row"><div class="col-xl-12"><div id="barChart"></div></div></div>
        ';
    
        return response()->json([
            'status' => 'success',
            'lossdata_section' => $data
        ]);
    }
    
    
    public function lossDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
    
        $productType = $request->input('product_type');
        $search      = $request->input('search');
        $dateFrom    = $request->input('date_from');
        $dateTo      = $request->input('date_to');
    
        if (!empty($dateFrom)) $dateFrom = date('Y-m-d', strtotime($dateFrom));
        if (!empty($dateTo))   $dateTo   = date('Y-m-d', strtotime($dateTo));
    
        $stid = $request->input('store_id');
        $activeStoreId = !empty($stid) ? $stid : $store_id;
    
        $start = $request->input('start', 0);
        $limit = $request->input('length', 10);
    
        // ------------------------- BASE QUERY -------------------------
        $query = DB::table('tbl_barcode')
            ->where('store_id', $activeStoreId)
            ->where('t_status', '0')
            ->where('loss_damage', '1')
            ->whereNull('lens_box');
        
        if (!$query)
        {
            if ($activeStoreId > 0) {
                $query->where('transfer_store_id', $activeStoreId)->where('loss_damage', '1')->whereNull('lens_box');
            }
        }
    
        if (!empty($productType)) {
            $query->where('product_type', $productType);
        }
    
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('product_details', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }
    
        if (!empty($dateFrom) && !empty($dateTo)) {
            $query->whereBetween('adj_date', [$dateFrom, $dateTo]);
        }
    
        // ------------------------- GROUP QUERY -------------------------
        $groupQuery = $query->select(
                'product_code',
                'product_type',
                DB::raw('MAX(product_details) AS product_details'),
                DB::raw('COUNT(*) AS total_count'),
                DB::raw('SUM(purchase_price) AS total_purchase'),
                DB::raw('MAX(store_id) AS store_id'),                
                DB::raw('MAX(perbox) AS perbox_detail'),       
                DB::raw('MAX(t_status) AS t_status')
            )
            ->groupBy('product_code', 'product_type', 'product_details');
    
        $totalData = DB::table('tbl_barcode')->count();
    
        $totalFiltered = $groupQuery->get()->count();
    
        $templates = $groupQuery
            ->offset($start)
            ->limit($limit)
            ->get();
    
        $data = [];
        $i = $start + 1;
    
        foreach ($templates as $template) {
    
            if($template->t_status == 0)
            {
                $store_name= Store::where('id', $template->store_id)->first();
            }
            else
            {
                $store_name= Store::where('id', $template->transfer_store_id)->first();
            }
    
            if ($template->product_type == 'Lens') {
                $description = $template->product_details .
                               '<br><strong style="color:red">Box per piece: ' .
                               $template->perbox_detail .
                               '</strong>';
            } else {
                $description = $template->product_details;
            }
    
            $data[] = [
                'sr_no'        => $i++,
                'store_name'   => $store_name->store_name,
                'product_code' => $template->product_code,
                'product_type' => $template->product_type,
                'description'  => $description,
                'qty'          => $template->total_count,
                'total_price'  => $template->total_purchase,
            ];
        }
    
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }

    
    
    public function lossExcelDownload(Request $request)
    {
        $store_id = auth()->user()->store_id;
        
        
    
        $stid = $request->input('store_id');
        $activeStoreId = !empty($stid) ? $stid : $store_id;
        $productType   = $request->input('product_type');
        $search        = $request->input('search');
        $dateFrom      = $request->input('date_from');
        $dateTo        = $request->input('date_to');
        $dateFrom      = date('Y-m-d', strtotime($dateFrom));
        $dateTo        = date('Y-m-d', strtotime($dateTo));
        

        $export = new LossSummaryExport($productType, $search, $dateFrom, $dateTo, $activeStoreId);

        $fileName = 'loss_report_' . time() . '.xlsx';

        return Excel::download(new LossSummaryExport($productType, $search, $dateFrom, $dateTo, $activeStoreId), $fileName);
    }
    
    
    
    public function salesReport()
    {
        $setting['page_title'] = 'Sales Report';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/sales-report',$setting);
    }
    
    
    public function saledataReport(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        /* ================= REQUEST DATA ================= */
    
        $storeId     = $request->store_id ?: $authStoreId;
        $fromDate    = $request->from_date;
        $toDate      = $request->to_date;
    
        /* ================= QUERY ================= */
    
        $query = DB::table('tbl_sales_product as sd')
            ->join('tbl_sales as s', 's.sale_id', '=', 'sd.sale_id')
            ->select(
                'sd.product_type',
                DB::raw('SUM(sd.sale_price) as total_sale_price'),
                DB::raw('SUM(sd.qty) as total_qty'),
                DB::raw('SUM(sd.no_of_glass) as total_glass_qty')
            );
    
        /* ================= FILTERS ================= */
    
        if ($storeId != 0) {
            $query->where('sd.store_id', $storeId);
        }
    
        if ($request->product_type) {
            $query->where('sd.product_type', $request->product_type);
        }
    
        if ($request->sale_person) {
            $query->where('s.sale_person', $request->sale_person);
        }
    
        if ($request->sale_status) {
            $query->where('s.sale_status', $request->sale_status);
        }
    
        if ($fromDate && $toDate) {
            $query->whereBetween('s.sale_date', [$fromDate, $toDate]);
        }
    
        if ($request->price_from && $request->price_to) {
            $query->whereBetween('sd.sale_price', [$request->price_from, $request->price_to]);
        }
    
        if ($request->gst_no) {
            $query->where('s.gst_no', $request->gst_no);
        }
    
        /* ================= EXECUTE ================= */
    
        $results = $query
            ->groupBy('sd.product_type')
            ->get()
            ->keyBy('product_type');
    
        /* ================= PRODUCT TYPES ================= */
    
        $types = [
            'Frame',
            'Glass',
            'Goggles',
            'Lens',
            'Solution',
            'Other',
            'Repair',
            'Non Chargeable'
        ];
    
        /* ================= CALCULATIONS ================= */
    
        $sums = [];
    
        foreach ($types as $type) {
            $row = $results[$type] ?? null;
    
            $sales = $row ? (float) $row->total_sale_price : 0;
    
            $qty = 0;
            if ($row) {
                $qty = ($type === 'Glass')
                    ? (int) $row->total_glass_qty
                    : (int) $row->total_qty;
            }
    
            $sums[$type] = [
                'sales'     => round($sales, 2),
                'inventory' => $qty
            ];
        }
    
        $totalSales     = array_sum(array_column($sums, 'sales'));
        $totalInventory = array_sum(array_column($sums, 'inventory'));
    
        /* ================= CHART DATA ================= */
    
        $colors = [
            "#4a32d4", "#f7592d", "#f7be2d", "#3abc1d",
            "#f72d66", "#2dcbf7", "#ff8c00", "#888888"
        ];
    
        $saleChartColumns = [];
        $inventoryChartColumns = [];
        $chartColors = [];
    
        $barCategories = [];
        $barSalesData = [];
    
        $i = 0;
        foreach ($sums as $type => $data) {
            $saleChartColumns[] = '["' . $type . '", ' . $data['sales'] . ']';
            $inventoryChartColumns[] = '["' . $type . '", ' . $data['inventory'] . ']';
            $chartColors[$type] = $colors[$i] ?? '#ccc';
    
            // Bar chart data
            $barCategories[] = '"' . $type . '"';
            $barSalesData[] = $data['sales'];
    
            $i++;
        }
    
        /* ================= TABLE ================= */
    
        $rows = '';
        foreach ($sums as $type => $values) {
            $rows .= "
                <tr>
                    <td>{$type}</td>
                    <td>Rs " . number_format($values['sales'], 2) . "</td>
                    <td>" . number_format($values['inventory']) . "</td>
                </tr>";
        }
    
        /* ================= HTML + JS ================= */
    
        $data = '
        <div class="row">
            <div class="col-lg-12">
                <table border="1" cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse">
                    <thead style="background:#00484a;color:#fff">
                        <tr>
                            <th>Product</th>
                            <th>Total Sales Value</th>
                            <th>Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>' . $rows . '</tbody>
                    <tfoot style="background:#00484a;color:#fff;font-weight:bold">
                        <tr>
                            <td>Total</td>
                            <td>Rs ' . number_format($totalSales, 2) . '</td>
                            <td>' . number_format($totalInventory) . '</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    
        <div class="row mt-4">
            <div class="col-lg-6">
                <div id="chart-pie2"></div>
                <h4 class="text-center">Sales (Actual Amount)</h4>
            </div>
            <div class="col-lg-6">
                <div id="chart-pie3"></div>
                <h4 class="text-center">Inventory (Actual Qty)</h4>
            </div>
        </div>
    
        <div class="row mt-4">
            <div class="col-lg-12">
                <div id="chart-bar-sales"></div>
                <h4 class="text-center">Product Wise Sales</h4>
            </div>
        </div>
    
        <script>
            /* ================= PIE CHART - SALES ================= */
            c3.generate({
                bindto: "#chart-pie2",
                data: {
                    columns: [' . implode(',', $saleChartColumns) . '],
                    type: "pie",
                    colors: ' . json_encode($chartColors) . '
                },
                tooltip: {
                    format: {
                        value: function(value){ return "Rs " + value.toLocaleString(); }
                    }
                },
                pie: {
                    label: {
                        format: function(value){ return "Rs " + value.toLocaleString(); }
                    }
                }
            });
    
            /* ================= PIE CHART - INVENTORY ================= */
            c3.generate({
                bindto: "#chart-pie3",
                data: {
                    columns: [' . implode(',', $inventoryChartColumns) . '],
                    type: "pie",
                    colors: ' . json_encode($chartColors) . '
                },
                tooltip: {
                    format: {
                        value: function(value){ return value; }
                    }
                },
                pie: {
                    label: {
                        format: function(value){ return value; }
                    }
                }
            });
    
            /* ================= BAR CHART - SALES ================= */
            c3.generate({
                bindto: "#chart-bar-sales",
                data: {
                    columns: [
                        ["Sales", ' . implode(',', $barSalesData) . ']
                    ],
                    type: "bar",
                    colors: {
                        Sales: "#4a32d4"
                    }
                },
                axis: {
                    x: {
                        type: "category",
                        categories: [' . implode(',', $barCategories) . ']
                    },
                    y: {
                        tick: {
                            format: function (d) {
                                return "Rs " + d.toLocaleString();
                            }
                        }
                    }
                },
                bar: {
                    width: {
                        ratio: 0.6
                    }
                },
                tooltip: {
                    format: {
                        value: function(value){
                            return "Rs " + value.toLocaleString();
                        }
                    }
                }
            });
        </script>
        ';
    
        return response()->json([
            'status' => 'success',
            'saledata_section' => $data
        ]);
    }
    
    
    public function SaleExcelDownload(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        $storeId       = $request->input('store_id');
        $productType   = $request->input('product_type');
        $sale_person        = $request->input('sale_person');
        $sale_type  = $request->input('sale_type');
        $search_by      = $request->input('search_by');
        $search_text        = $request->input('search_text');
        $dateFrom      = date('Y-m-d', strtotime($request->input('from_date')));
        $dateTo        = date('Y-m-d', strtotime($request->input('to_date')));
		$price_from        = $request->input('price_from');
		$price_to        = $request->input('price_to');
		$gst_no        = $request->input('gst_no');
		$sort_by        = $request->input('sort_by');

        $export = new SalesSummaryExport($productType, $sale_person, $sale_type, $search_by, $search_text, $storeId
		,$dateFrom,$dateTo,$price_from,$price_to,$gst_no,$sort_by);

        $fileName = 'pending_order_report_' . time() . '.xlsx';

        return Excel::download(new SalesSummaryExport($productType, $sale_person, $sale_type, $search_by, $search_text, $storeId
		,$dateFrom,$dateTo,$price_from,$price_to,$gst_no,$sort_by), $fileName);
		
    }

   

    public function pendingOrderReport()
    {
        $setting['page_title'] = 'Pending Order Report';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/pending-order-report',$setting);
    }
    
    
    
    public function pendingorderdataReport(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        /* ================= REQUEST DATA ================= */
    
        $storeId     = $request->store_id ?: $authStoreId;
        $fromDate    = $request->from_date;
        $toDate      = $request->to_date;
    
        /* ================= QUERY ================= */
    
        $query = DB::table('tbl_sales_product as sd')
            ->join('tbl_sales as s', 's.sale_id', '=', 'sd.sale_id')
            ->select(
                'sd.product_type',
                DB::raw('SUM(sd.sale_price) as total_sale_price'),
                DB::raw('SUM(sd.qty) as total_qty'),
                DB::raw('SUM(sd.no_of_glass) as total_glass_qty')
            );
    
        /* ================= FILTERS ================= */
    
        if ($storeId != 0) {
            $query->where('sd.store_id', $storeId);
        }
    
        if ($request->product_type) {
            $query->where('sd.product_type', $request->product_type);
        }
    
        if ($request->sale_person) {
            $query->where('s.sale_person', $request->sale_person);
        }

    
        if ($fromDate && $toDate) {
            $query->whereBetween('s.sale_date', [$fromDate, $toDate]);
        }
    
        if ($request->price_from && $request->price_to) {
            $query->whereBetween('sd.sale_price', [$request->price_from, $request->price_to]);
        }
    
        if ($request->gst_no) {
            $query->where('s.gst_no', $request->gst_no);
        }
        
        $query->where('s.sales_status', 0);
    
        /* ================= EXECUTE ================= */
    
        $results = $query
            ->groupBy('sd.product_type')
            ->get()
            ->keyBy('product_type');
    
        /* ================= PRODUCT TYPES ================= */
    
        $types = [
            'Frame',
            'Glass',
            'Goggles',
            'Lens',
            'Solution',
            'Other',
            'Repair',
            'Non Chargeable'
        ];
    
        /* ================= CALCULATIONS ================= */
    
        $sums = [];
    
        foreach ($types as $type) {
            $row = $results[$type] ?? null;
    
            $sales = $row ? (float) $row->total_sale_price : 0;
    
            $qty = 0;
            if ($row) {
                $qty = ($type === 'Glass')
                    ? (int) $row->total_glass_qty
                    : (int) $row->total_qty;
            }
    
            $sums[$type] = [
                'sales'     => round($sales, 2),
                'inventory' => $qty
            ];
        }
    
        $totalSales     = array_sum(array_column($sums, 'sales'));
        $totalInventory = array_sum(array_column($sums, 'inventory'));
    
        /* ================= CHART DATA ================= */
    
        $colors = [
            "#4a32d4", "#f7592d", "#f7be2d", "#3abc1d",
            "#f72d66", "#2dcbf7", "#ff8c00", "#888888"
        ];
    
        $saleChartColumns = [];
        $inventoryChartColumns = [];
        $chartColors = [];
    
        $barCategories = [];
        $barSalesData = [];
    
        $i = 0;
        foreach ($sums as $type => $data) {
            $saleChartColumns[] = '["' . $type . '", ' . $data['sales'] . ']';
            $inventoryChartColumns[] = '["' . $type . '", ' . $data['inventory'] . ']';
            $chartColors[$type] = $colors[$i] ?? '#ccc';
    
            // Bar chart data
            $barCategories[] = '"' . $type . '"';
            $barSalesData[] = $data['sales'];
    
            $i++;
        }
    
        /* ================= TABLE ================= */
    
        $rows = '';
        foreach ($sums as $type => $values) {
            $rows .= "
                <tr>
                    <td>{$type}</td>
                    <td>Rs " . number_format($values['sales'], 2) . "</td>
                    <td>" . number_format($values['inventory']) . "</td>
                </tr>";
        }
    
        /* ================= HTML + JS ================= */
    
        $data = '
        <div class="row">
            <div class="col-lg-12">
                <table border="1" cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse">
                    <thead style="background:#00484a;color:#fff">
                        <tr>
                            <th>Product</th>
                            <th>Total Pending Order Value</th>
                            <th>Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>' . $rows . '</tbody>
                    <tfoot style="background:#00484a;color:#fff;font-weight:bold">
                        <tr>
                            <td>Total</td>
                            <td>Rs ' . number_format($totalSales, 2) . '</td>
                            <td>' . number_format($totalInventory) . '</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    
        <div class="row mt-4">
            <div class="col-lg-6">
                <div id="chart-pie2"></div>
                <h4 class="text-center">Pending Order (Actual Amount)</h4>
            </div>
            <div class="col-lg-6">
                <div id="chart-pie3"></div>
                <h4 class="text-center">Inventory (Actual Qty)</h4>
            </div>
        </div>
    
        <div class="row mt-4">
            <div class="col-lg-12">
                <div id="chart-bar-sales"></div>
                <h4 class="text-center">Product Wise Pending Order</h4>
            </div>
        </div>
    
        <script>
            /* ================= PIE CHART - SALES ================= */
            c3.generate({
                bindto: "#chart-pie2",
                data: {
                    columns: [' . implode(',', $saleChartColumns) . '],
                    type: "pie",
                    colors: ' . json_encode($chartColors) . '
                },
                tooltip: {
                    format: {
                        value: function(value){ return "Rs " + value.toLocaleString(); }
                    }
                },
                pie: {
                    label: {
                        format: function(value){ return "Rs " + value.toLocaleString(); }
                    }
                }
            });
    
            /* ================= PIE CHART - INVENTORY ================= */
            c3.generate({
                bindto: "#chart-pie3",
                data: {
                    columns: [' . implode(',', $inventoryChartColumns) . '],
                    type: "pie",
                    colors: ' . json_encode($chartColors) . '
                },
                tooltip: {
                    format: {
                        value: function(value){ return value; }
                    }
                },
                pie: {
                    label: {
                        format: function(value){ return value; }
                    }
                }
            });
    
            /* ================= BAR CHART - SALES ================= */
            c3.generate({
                bindto: "#chart-bar-sales",
                data: {
                    columns: [
                        ["Sales", ' . implode(',', $barSalesData) . ']
                    ],
                    type: "bar",
                    colors: {
                        Sales: "#4a32d4"
                    }
                },
                axis: {
                    x: {
                        type: "category",
                        categories: [' . implode(',', $barCategories) . ']
                    },
                    y: {
                        tick: {
                            format: function (d) {
                                return "Rs " + d.toLocaleString();
                            }
                        }
                    }
                },
                bar: {
                    width: {
                        ratio: 0.6
                    }
                },
                tooltip: {
                    format: {
                        value: function(value){
                            return "Rs " + value.toLocaleString();
                        }
                    }
                }
            });
        </script>
        ';
    
        return response()->json([
            'status' => 'success',
            'saledata_section' => $data
        ]);
    }
    
    
    public function pendingorderExcelDownload(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        $storeId       = $request->input('store_id');
        $productType   = $request->input('product_type');
        $sale_person   = $request->input('sale_person');
        $sale_type     = $request->input('sale_type');
        $search_by     = $request->input('search_by');
        $search_text   = $request->input('search_text');
        $dateFrom      = date('Y-m-d', strtotime($request->input('from_date')));
        $dateTo        = date('Y-m-d', strtotime($request->input('to_date')));
		$price_from    = $request->input('price_from');
		$price_to      = $request->input('price_to');
		$gst_no        = $request->input('gst_no');
		$sort_by       = $request->input('sort_by');

        $export = new PendingOrderSummaryExport($productType, $sale_person, $sale_type, $search_by, $search_text, $storeId
		,$dateFrom,$dateTo,$price_from,$price_to,$gst_no,$sort_by);

        $fileName = 'pending_order_report_' . time() . '.xlsx';

        return Excel::download(new PendingOrderSummaryExport($productType, $sale_person, $sale_type, $search_by, $search_text, $storeId
		,$dateFrom,$dateTo,$price_from,$price_to,$gst_no,$sort_by), $fileName);
		
    }
    
    
    
    public function salesReturnReport()
    {
        $setting['page_title'] = 'Sales Return Report';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/sales-return-report',$setting);
    }
    
    
    public function salereturndataReport(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        /* ================= REQUEST DATA ================= */
    
        $storeId     = $request->store_id ?: $authStoreId;
        $fromDate    = $request->from_date;
        $toDate      = $request->to_date;
    
        /* ================= QUERY ================= */
    
        $query = DB::table('tbl_sales_product as sd')
            ->join('tbl_sales as s', 's.sale_id', '=', 'sd.sale_id')
            ->select(
                'sd.product_type',
                DB::raw('SUM(sd.sale_price) as total_sale_price'),
                DB::raw('SUM(sd.qty) as total_qty'),
                DB::raw('SUM(sd.no_of_glass) as total_glass_qty')
            );
    
        /* ================= FILTERS ================= */
    
        if ($storeId != 0) {
            $query->where('sd.store_id', $storeId);
        }
    
        if ($request->product_type) {
            $query->where('sd.product_type', $request->product_type);
        }
    
        if ($request->sale_person) {
            $query->where('s.sale_person', $request->sale_person);
        }
    

        if ($fromDate && $toDate) {
            $query->whereBetween('s.return_date', [$fromDate, $toDate]);
        }
    
        if ($request->price_from && $request->price_to) {
            $query->whereBetween('sd.sale_price', [$request->price_from, $request->price_to]);
        }
    
        if ($request->gst_no) {
            $query->where('s.gst_no', $request->gst_no);
        }
        
        $query->where('sd.return_status', 3);
    
        /* ================= EXECUTE ================= */
    
        $results = $query
            ->groupBy('sd.product_type')
            ->get()
            ->keyBy('product_type');
    
        /* ================= PRODUCT TYPES ================= */
    
        $types = [
            'Frame',
            'Glass',
            'Goggles',
            'Lens',
            'Solution',
            'Other',
            'Repair',
            'Non Chargeable'
        ];
    
        /* ================= CALCULATIONS ================= */
    
        $sums = [];
    
        foreach ($types as $type) {
            $row = $results[$type] ?? null;
    
            $sales = $row ? (float) $row->total_sale_price : 0;
    
            $qty = 0;
            if ($row) {
                $qty = ($type === 'Glass')
                    ? (int) $row->total_glass_qty
                    : (int) $row->total_qty;
            }
    
            $sums[$type] = [
                'sales'     => round($sales, 2),
                'inventory' => $qty
            ];
        }
    
        $totalSales     = array_sum(array_column($sums, 'sales'));
        $totalInventory = array_sum(array_column($sums, 'inventory'));
    
        /* ================= CHART DATA ================= */
    
        $colors = [
            "#4a32d4", "#f7592d", "#f7be2d", "#3abc1d",
            "#f72d66", "#2dcbf7", "#ff8c00", "#888888"
        ];
    
        $saleChartColumns = [];
        $inventoryChartColumns = [];
        $chartColors = [];
    
        $barCategories = [];
        $barSalesData = [];
    
        $i = 0;
        foreach ($sums as $type => $data) {
            $saleChartColumns[] = '["' . $type . '", ' . $data['sales'] . ']';
            $inventoryChartColumns[] = '["' . $type . '", ' . $data['inventory'] . ']';
            $chartColors[$type] = $colors[$i] ?? '#ccc';
    
            // Bar chart data
            $barCategories[] = '"' . $type . '"';
            $barSalesData[] = $data['sales'];
    
            $i++;
        }
    
        /* ================= TABLE ================= */
    
        $rows = '';
        foreach ($sums as $type => $values) {
            $rows .= "
                <tr>
                    <td>{$type}</td>
                    <td>Rs " . number_format($values['sales'], 2) . "</td>
                    <td>" . number_format($values['inventory']) . "</td>
                </tr>";
        }
    
        /* ================= HTML + JS ================= */
    
        $data = '
        <div class="row">
            <div class="col-lg-12">
                <table border="1" cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse">
                    <thead style="background:#00484a;color:#fff">
                        <tr>
                            <th>Product</th>
                            <th>Total Sales Return Value</th>
                            <th>Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>' . $rows . '</tbody>
                    <tfoot style="background:#00484a;color:#fff;font-weight:bold">
                        <tr>
                            <td>Total</td>
                            <td>Rs ' . number_format($totalSales, 2) . '</td>
                            <td>' . number_format($totalInventory) . '</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    
        <div class="row mt-4">
            <div class="col-lg-6">
                <div id="chart-pie2"></div>
                <h4 class="text-center">Sales Return (Actual Amount)</h4>
            </div>
            <div class="col-lg-6">
                <div id="chart-pie3"></div>
                <h4 class="text-center">Inventory (Actual Qty)</h4>
            </div>
        </div>
    
        <div class="row mt-4">
            <div class="col-lg-12">
                <div id="chart-bar-sales"></div>
                <h4 class="text-center">Product Wise Sales Return</h4>
            </div>
        </div>
    
        <script>
            /* ================= PIE CHART - SALES ================= */
            c3.generate({
                bindto: "#chart-pie2",
                data: {
                    columns: [' . implode(',', $saleChartColumns) . '],
                    type: "pie",
                    colors: ' . json_encode($chartColors) . '
                },
                tooltip: {
                    format: {
                        value: function(value){ return "Rs " + value.toLocaleString(); }
                    }
                },
                pie: {
                    label: {
                        format: function(value){ return "Rs " + value.toLocaleString(); }
                    }
                }
            });
    
            /* ================= PIE CHART - INVENTORY ================= */
            c3.generate({
                bindto: "#chart-pie3",
                data: {
                    columns: [' . implode(',', $inventoryChartColumns) . '],
                    type: "pie",
                    colors: ' . json_encode($chartColors) . '
                },
                tooltip: {
                    format: {
                        value: function(value){ return value; }
                    }
                },
                pie: {
                    label: {
                        format: function(value){ return value; }
                    }
                }
            });
    
            /* ================= BAR CHART - SALES ================= */
            c3.generate({
                bindto: "#chart-bar-sales",
                data: {
                    columns: [
                        ["Sales", ' . implode(',', $barSalesData) . ']
                    ],
                    type: "bar",
                    colors: {
                        Sales: "#4a32d4"
                    }
                },
                axis: {
                    x: {
                        type: "category",
                        categories: [' . implode(',', $barCategories) . ']
                    },
                    y: {
                        tick: {
                            format: function (d) {
                                return "Rs " + d.toLocaleString();
                            }
                        }
                    }
                },
                bar: {
                    width: {
                        ratio: 0.6
                    }
                },
                tooltip: {
                    format: {
                        value: function(value){
                            return "Rs " + value.toLocaleString();
                        }
                    }
                }
            });
        </script>
        ';
    
        return response()->json([
            'status' => 'success',
            'saledata_section' => $data
        ]);
    }
    
    
    
    public function SaleReturnExcelDownload(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        $storeId       = $request->input('store_id');
        $productType   = $request->input('product_type');
        $sale_person   = $request->input('sale_person');
        $sale_type     = $request->input('sale_type');
        $search_by     = $request->input('search_by');
        $search_text   = $request->input('search_text');
        $dateFrom      = date('Y-m-d', strtotime($request->input('from_date')));
        $dateTo        = date('Y-m-d', strtotime($request->input('to_date')));
		$price_from    = $request->input('price_from');
		$price_to      = $request->input('price_to');
		$gst_no        = $request->input('gst_no');
		$sort_by       = $request->input('sort_by');

        $export = new SalesReturnSummaryExport($productType, $sale_person, $sale_type, $search_by, $search_text, $storeId
		,$dateFrom,$dateTo,$price_from,$price_to,$gst_no,$sort_by);

        $fileName = 'sale-return_report_' . time() . '.xlsx';

        return Excel::download(new SalesReturnSummaryExport($productType, $sale_person, $sale_type, $search_by, $search_text, $storeId
		,$dateFrom,$dateTo,$price_from,$price_to,$gst_no,$sort_by), $fileName);
    }
    
    public function GSTinputReport()
    {
        $setting['page_title'] = 'GST Input Report';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/gst-input-report',$setting);
    }
    
    
    public function gstinputDatatable(Request $request)
    {
        $store_id = auth()->user()->store_id;
        $limit = $request->input('length');
        $start = $request->input('start');
    
        $date_from   = $request->input('date_from');
        $date_to     = $request->input('date_to');
        $storeid = $request->input('store_id');
        $date_type = $request->input('date_type');
        $sort_by = $request->input('sort_by');
        

        // Base query
       $query = DB::table('tbl_purchase as p')
            ->join('tbl_purchase_deatils as pd', 'pd.purchase_id', '=', 'p.purchase_id')
            ->where('p.is_Deleted', '0');
        
        if ($store_id != '0') 
        {
            $query->where('p.store_id', $store_id);
        }
        
        if ($date_type != '') 
        {
            if ($date_type == 0)
            {
                $query->whereBetween('p.purchase_date', [$date_from, $date_to]);
            }
            else
            {
                $query->whereBetween('p.created_at', [$date_from, $date_to . ' 23:59:59']);
            }
        
        }
        else
        {
            $query->whereBetween('p.purchase_date', [$date_from, $date_to]);
        }
        
        if ($storeid != '') 
        {
            $query->where('p.store_id', $storeid);
        }
        
        // Total records before pagination
        $totalFiltered = $query->count();
        
        $totalData = DB::table('tbl_purchase')
            ->where('is_Deleted', '0')
            ->count();
        
        // Apply pagination
        $templates = $query
            ->select(
                'p.*',
                'pd.id as pid',
                'pd.hsn_code',
                'pd.gst_amt',
                'pd.gst',
                'pd.product_base_price',
                'pd.product_purchase_price',
                'pd.qty',
                'pd.total_purchase_price'
            )
            ->offset($start)
            ->limit($limit)
            ->get();
    
        $data = [];
        $i = $start + 1;
    
        foreach ($templates as $template)
        {
            $tbl_suppliers =  DB::table("tbl_suppliers")->where('supplier_company', $template->supplier_name)->first();

            $nestedData = [
                'sr_no' => $i++,
                'bill_no' => $template->p_bill_no,
                'created_at' => date('d-m-Y h:i A', strtotime($template->created_at)),
                'purchase_date' => date('d-m-Y', strtotime($template->purchase_date)),
                'supplier_name' => $template->supplier_name,
                'gst_no' => $tbl_suppliers->gst_no,
                'state' => $tbl_suppliers->state,
                'hsn_code' => $template->hsn_code ?? '',
                'base_value' => $template->product_base_price,
                'gst' => $template->gst,
                'qty' => $template->qty,
                'gst_amount' => number_format($template->gst_amt / 2, 2) . ' / ' . number_format($template->gst_amt / 2, 2),
                'igst_amount' => '',
                'total_gst' => $template->gst_amt,
                'total_purchase' => $template->total_purchase_price,
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
    
    
    public function gstinputExcelDownload(Request $request)
    {
        $authStoreId = auth()->user()->store_id;
    
        $storeId       = $request->input('store_id');
        $date_type  = $request->input('date_type');
        $dateFrom      = date('Y-m-d', strtotime($request->input('date_from')));
        $dateTo        = date('Y-m-d', strtotime($request->input('date_to')));
		$sort_by        = $request->input('sort_by');
		

        $export = new GSTinputSummaryExport($storeId, $date_type, $dateFrom, $dateTo, $sort_by);

        $fileName = 'gst_input_report_' . time() . '.xlsx';

        return Excel::download(new GSTinputSummaryExport($storeId, $date_type, $dateFrom, $dateTo, $sort_by), $fileName);
		
    }
    
    
    public function GSToutReport()
    {
        $setting['page_title'] = 'GST Out Report';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/gst-out-report',$setting);
    }
    
    public function paymentReport()
    {
        $setting['page_title'] = 'Payment Report';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/payment-report',$setting);
    }




}    