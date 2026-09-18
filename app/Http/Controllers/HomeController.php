<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sale\Sale;
use App\Models\sale\SaleProduct;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the admin order operations dashboard.
     */
    public function index()
    {
        $page_title = 'Dashboard';
        $breadcrumbs = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => 'Dashboard'],
        ];

        $today        = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Executive KPIs (6 Core Business & Operations Metrics)
        $returnsCount = Sale::b2c()->where('order_status', 'returned')->count();

        $kpis = [
            'orders_today'         => Sale::b2c()->whereDate('created_at', $today)->count(),
            'revenue_today'        => (float) Sale::b2c()->whereDate('created_at', $today)->where('payment_status', 'paid')->sum('total_payable'),
            'orders_this_month'    => Sale::b2c()->where('created_at', '>=', $startOfMonth)->count(),
            'revenue_this_month'   => (float) Sale::b2c()->where('created_at', '>=', $startOfMonth)->where('payment_status', 'paid')->sum('total_payable'),
            'pending_orders'       => Sale::b2c()->where('order_status', 'pending')->count(),
            'ready_to_ship'        => Sale::b2c()->where('order_status', 'ready_to_ship')->count(),
            'cancelled_orders'     => Sale::b2c()->where('order_status', 'cancelled')->count(),
            'cancelled_this_month' => Sale::b2c()->where('order_status', 'cancelled')->where('created_at', '>=', $startOfMonth)->count(),
            'returns_count'        => $returnsCount,
            'payment_issues'       => Sale::b2c()->whereIn('payment_status', ['failed', 'cod_pending'])->count(),
            'pending_rx'           => Sale::b2c()->where('rx_verification_status', 'pending_review')->count(),
            'in_lab'               => Sale::b2c()->whereIn('lab_status', ['assigned', 'cutting', 'fitting'])->count(),
        ];

        // 2. Order Funnel / Pipeline Breakdown
        $pipeline = [
            'pending'       => Sale::b2c()->where('order_status', 'pending')->count(),
            'rx_review'     => $kpis['pending_rx'],
            'in_lab'        => $kpis['in_lab'],
            'ready_to_ship' => $kpis['ready_to_ship'],
            'shipped'       => Sale::b2c()->where('order_status', 'shipped')->count(),
            'delivered'     => Sale::b2c()->where('order_status', 'delivered')->count(),
        ];

        // Pipeline Array for Horizontal Bar Chart
        $pipelineChartData = [
            ['stage' => '1. Placed', 'count' => (int) $pipeline['pending']],
            ['stage' => '2. Rx Review', 'count' => (int) $pipeline['rx_review']],
            ['stage' => '3. Optical Lab', 'count' => (int) $pipeline['in_lab']],
            ['stage' => '4. Ready to Ship', 'count' => (int) $pipeline['ready_to_ship']],
            ['stage' => '5. In Transit', 'count' => (int) $pipeline['shipped']],
            ['stage' => '6. Delivered', 'count' => (int) $pipeline['delivered']],
        ];

        // 3. Live Recent Orders (8 most recent)
        $recentOrders = Sale::b2c()->with(['products.lensPackage', 'user', 'payments'])
            ->latest('created_at')
            ->take(8)
            ->get();

        // 4. Product Category Summary from Database
        $framesCount = SaleProduct::where('product_type', 'frame')->orWhere('product_type', 'Frame')->count();
        $lensesCount = SaleProduct::where(function($q) {
            $q->whereNotNull('package_id')->orWhereIn('product_type', ['lens', 'Lens', 'Glass']);
        })->count();
        $gogglesCount = SaleProduct::whereIn('product_type', ['goggles', 'Goggles', 'sunglasses', 'Sunglasses'])->count();

        $productMixData = [
            ['category' => 'Optical Frames', 'units' => max(0, $framesCount), 'color' => '#0d5c56'],
            ['category' => 'Rx Lenses Fitted', 'units' => max(0, $lensesCount), 'color' => '#059669'],
            ['category' => 'Sunglasses & Goggles', 'units' => max(0, $gogglesCount), 'color' => '#0284c7'],
        ];

        // 5. Dynamic Multi-Period Historical Performance Dataset
        $minDate = Sale::b2c()->min('created_at');
        $earliest = $minDate ? Carbon::parse($minDate) : Carbon::now()->startOfYear();
        $startDate = $earliest->copy()->startOfYear();
        $endDate   = Carbon::today();

        // Query real aggregated daily orders
        $dbDaily = Sale::selectRaw("DATE(created_at) as order_date, COUNT(*) as order_count, SUM(total_payable) as total_revenue")
            ->where('created_at', '>=', $startDate)
            ->groupBy('order_date')
            ->get()
            ->keyBy('order_date');

        // Build continuous dynamic daily timeline
        $performanceData = [];
        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $dStr = $cursor->toDateString();
            $rec = $dbDaily->get($dStr);

            $dayOrders = 0;
            $dayRevenue = 0.0;

            if ($rec) {
                $dayOrders += (int) $rec->order_count;
                $dayRevenue += (float) $rec->total_revenue;
            }

            $performanceData[] = [
                'date'    => $dStr,
                'year'    => (int) $cursor->format('Y'),
                'month'   => (int) $cursor->format('n'),
                'day'     => (int) $cursor->format('j'),
                'orders'  => $dayOrders,
                'revenue' => round($dayRevenue, 2),
            ];

            $cursor->addDay();
        }

        $availableYears = Sale::selectRaw('DISTINCT YEAR(created_at) as yr')->pluck('yr')->filter()->toArray();
        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }
        $minDateStr = $startDate->toDateString();
        $maxDateStr = $endDate->toDateString();
        $stores     = Store::all();

         // ── NEW: Order Dashboard Analytics (selected date range) ───────────
        $dateFrom = request('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo   = request('date_to', Carbon::now()->toDateString()); 
        if (empty($dateFrom) && empty($dateTo)) {
            $dateFrom = Carbon::now()->startOfMonth()->toDateString();
            $dateTo   = Carbon::now()->toDateString();
        }
        $baseSales = Sale::b2c()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);
    
        // ATV
        $paidSales   = (clone $baseSales)->where('payment_status', 'paid');
        $totalRevenue = (float) $paidSales->sum('total_payable');
        $orderCount   = $paidSales->count();
        $atv = $orderCount > 0 ? round($totalRevenue / $orderCount, 2) : 0;
    
        $dashboardKpis = [
            'total_orders'  => (clone $baseSales)->count(),
            'paid_orders'   => $orderCount,
            'total_revenue' => $totalRevenue,
            'atv'           => $atv,
            'delivered'     => (clone $baseSales)->where('order_status', 'delivered')->count(),
            'cancelled'     => (clone $baseSales)->where('order_status', 'cancelled')->count(),
        ];
    
     
        // ── Brand-wise Sales ───────────────────────────────────────────────
        $brandWise = DB::query()
            ->fromSub(
                DB::table('tbl_sales_product as sp')
                    ->join('tbl_sales as s', 's.sale_id', '=', 'sp.sale_id')
                    ->leftJoin('tbl_product_code as pc', 'pc.id', '=', 'sp.product_id')
                    ->where('s.sales_type', 0)
                    ->whereDate('s.created_at', '>=', $dateFrom)
                    ->whereDate('s.created_at', '<=', $dateTo)
                    ->where('s.order_status', '!=', 'cancelled')
                    ->select(
                        DB::raw("
                            COALESCE(
                                NULLIF(pc.Company, ''),
                                NULLIF(sp.product_company, ''),
                                'Unknown Brand'
                            ) AS brand_name
                        "),
                        'sp.qty',
                        'sp.sale_price',
                        's.sale_id'
                    ),
                'brand_sales'
            )
            ->select(
                'brand_name',
                DB::raw('SUM(qty) AS total_qty'),
                DB::raw('SUM(sale_price * qty) AS total_revenue'),
                DB::raw('COUNT(DISTINCT sale_id) AS order_count')
            )
            ->groupBy('brand_name')
            ->orderByDesc('total_revenue')
            ->limit(12)
            ->get();



            // ── Lens Package Sales (FIXED) ─────────────────────────────────────────
            $lensPackageWise = DB::table('tbl_sales_product as sp')
                ->join('tbl_sales as s', 's.sale_id', '=', 'sp.sale_id')
                ->leftJoin('lens_packages as lp', 'lp.id', '=', 'sp.package_id')
                ->where('s.sales_type', 0)
                ->whereDate('s.created_at', '>=', $dateFrom)
                ->whereDate('s.created_at', '<=', $dateTo)
                ->where('s.order_status', '!=', 'cancelled')
                ->whereNotNull('sp.package_id')
                ->where('sp.package_id', '!=', '')
                ->where('sp.package_id', '!=', '0')
                ->select(
                    DB::raw("COALESCE(lp.name, CONCAT('Package #', sp.package_id)) as package_name"),
                    DB::raw('SUM(sp.qty) as total_qty'),
                    DB::raw('SUM(sp.lens_package_price * sp.qty) as package_revenue'),
                    DB::raw('COUNT(DISTINCT s.sale_id) as order_count')
                )
                ->groupBy(DB::raw("COALESCE(lp.name, CONCAT('Package #', sp.package_id))"))
                ->orderByDesc('package_revenue')
                ->get();
        
     
            // ── Contact Lens Sales ────────────────────────────────────────────────
            $contactLens = DB::query()
                ->fromSub(
                    DB::table('tbl_sales_product as sp')
                        ->join('tbl_sales as s', 's.sale_id', '=', 'sp.sale_id')
                        ->leftJoin('tbl_product_code as pc', 'pc.id', '=', 'sp.product_id')
                        ->where('s.sales_type', 0)
                        ->whereDate('s.created_at', '>=', $dateFrom)
                        ->whereDate('s.created_at', '<=', $dateTo)
                        ->where('s.order_status', '!=', 'cancelled')
                        ->where(function ($q) {
                            $q->where('sp.product_type', 'like', '%contact%')
                                ->orWhere('pc.product_type', 'like', '%contact%')
                                ->orWhere('pc.product_type', 'like', '%Contact Lens%');
                        })
                        ->select(
                            DB::raw("
                                COALESCE(
                                    NULLIF(pc.product_name, ''),
                                    NULLIF(sp.product_code, ''),
                                    'Contact Lens'
                                ) AS product_name
                            "),
                            'sp.qty',
                            'sp.sale_price',
                            's.sale_id'
                        ),
                    'contact_lens_sales'
                )
                ->select(
                    'product_name',
                    DB::raw('SUM(qty) AS total_qty'),
                    DB::raw('SUM(sale_price * qty) AS total_revenue'),
                    DB::raw('COUNT(DISTINCT sale_id) AS order_count')
                )
                ->groupBy('product_name')
                ->orderByDesc('total_revenue')
                ->get();

        // ── Best Selling Products ─────────────────────────────────────────────
        $bestSelling = DB::query()
            ->fromSub(
                DB::table('tbl_sales_product as sp')
                    ->join('tbl_sales as s', 's.sale_id', '=', 'sp.sale_id')
                    ->leftJoin('tbl_product_code as pc', 'pc.id', '=', 'sp.product_id')
                    ->where('s.sales_type', 0)
                    ->whereDate('s.created_at', '>=', $dateFrom)
                    ->whereDate('s.created_at', '<=', $dateTo)
                    ->where('s.order_status', '!=', 'cancelled')
                    ->select(
                        DB::raw("
                            COALESCE(
                                NULLIF(pc.product_name, ''),
                                NULLIF(sp.product_code, ''),
                                'Unknown Product'
                            ) AS product_name
                        "),
                        DB::raw("
                            COALESCE(
                                NULLIF(pc.product_code, ''),
                                sp.product_code
                            ) AS sku
                        "),
                        DB::raw("
                            COALESCE(
                                NULLIF(pc.product_type, ''),
                                NULLIF(sp.product_type, ''),
                                'other'
                            ) AS product_type
                        "),
                        'sp.qty',
                        'sp.sale_price',
                        's.sale_id'
                    ),
                'best_selling_products'
            )
            ->select(
                'product_name',
                'sku',
                'product_type',
                DB::raw('SUM(qty) AS total_qty'),
                DB::raw('SUM(sale_price * qty) AS total_revenue'),
                DB::raw('COUNT(DISTINCT sale_id) AS order_count')
            )
            ->groupBy(
                'product_name',
                'sku',
                'product_type'
            )
            ->orderByDesc('total_qty')
            ->limit(15)
            ->get();


        // ── Salesperson-wise (already correct, just for completeness) ─────────
        $salespersonWise = DB::table('tbl_sales as s')
            ->leftJoin('users as u', 'u.id', '=', 's.sale_person')
            ->where('s.sales_type', 0)
            ->whereDate('s.created_at', '>=', $dateFrom)
            ->whereDate('s.created_at', '<=', $dateTo)
            ->where('s.order_status', '!=', 'cancelled')
            ->select(
                DB::raw("COALESCE(u.name, CONCAT('Staff #', s.sale_person), 'Online / System') as salesperson"),
                DB::raw('COUNT(s.sale_id) as order_count'),
                DB::raw('SUM(CASE WHEN s.payment_status = "paid" THEN s.total_payable ELSE 0 END) as revenue'),
                DB::raw('ROUND(AVG(CASE WHEN s.payment_status = "paid" THEN s.total_payable ELSE NULL END), 2) as avg_ticket')
            )
            ->groupBy(DB::raw("COALESCE(u.name, CONCAT('Staff #', s.sale_person), 'Online / System')"))
            ->orderByDesc('revenue')
            ->get();
            
        // ── Category-wise Sales ────────────────────────────────────────
            $categoryWise = DB::query()
                ->fromSub(
                    DB::table('tbl_sales_product as sp')
                        ->join('tbl_sales as s', 's.sale_id', '=', 'sp.sale_id')
                        ->leftJoin('tbl_product_code as pc', 'pc.id', '=', 'sp.product_id')
                        ->where('s.sales_type', 0)
                        ->whereDate('s.created_at', '>=', $dateFrom)
                        ->whereDate('s.created_at', '<=', $dateTo)
                        ->where('s.order_status', '!=', 'cancelled')
                        ->select(
                            DB::raw("
                                COALESCE(
                                    NULLIF(pc.product_type, ''),
                                    'Other'
                                ) AS category
                            "),
                            'sp.qty',
                            'sp.sale_price',
                            's.sale_id'
                        ),
                    'category_sales'
                )
                ->select(
                    'category',
                    DB::raw('SUM(qty) AS total_qty'),
                    DB::raw('SUM(sale_price * qty) AS total_revenue'),
                    DB::raw('COUNT(DISTINCT sale_id) AS order_count')
                )
                ->groupBy('category')
                ->orderByDesc('total_revenue')
                ->get();
    
        
       

        return view('layouts.index', compact(
            'page_title',
            'breadcrumbs',
            'kpis',
            'pipeline',
            'pipelineChartData',
            'recentOrders',
            'productMixData',
            'performanceData',
            'availableYears',
            'minDateStr',
            'maxDateStr',
            'stores',
            //
            'dateFrom',
            'dateTo',
            'dashboardKpis',
            'brandWise',
            'lensPackageWise',
            'contactLens',
            'bestSelling',
            'salespersonWise',
            'categoryWise'
            
        ));
    }
}
