<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\b2c\B2cOrder;
use App\Models\b2c\B2cOrderItem;
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
        $returnsCount = B2cOrder::where('order_status', 'returned')->count();
        if (Schema::hasTable('b2c_order_returns')) {
            $returnsCount = max($returnsCount, DB::table('b2c_order_returns')->count());
        }

        $kpis = [
            'orders_today'         => B2cOrder::whereDate('created_at', $today)->count(),
            'revenue_today'        => (float) B2cOrder::whereDate('created_at', $today)->where('payment_status', 'paid')->sum('grand_total'),
            'orders_this_month'    => B2cOrder::where('created_at', '>=', $startOfMonth)->count(),
            'revenue_this_month'   => (float) B2cOrder::where('created_at', '>=', $startOfMonth)->where('payment_status', 'paid')->sum('grand_total'),
            'pending_orders'       => B2cOrder::where('order_status', 'pending')->count(),
            'ready_to_ship'        => B2cOrder::where('order_status', 'ready_to_ship')->count(),
            'cancelled_orders'     => B2cOrder::where('order_status', 'cancelled')->count(),
            'cancelled_this_month' => B2cOrder::where('order_status', 'cancelled')->where('created_at', '>=', $startOfMonth)->count(),
            'returns_count'        => $returnsCount,
            'payment_issues'       => B2cOrder::whereIn('payment_status', ['failed', 'cod_pending'])->count(),
            'pending_rx'           => B2cOrder::where('rx_verification_status', 'pending_review')->count(),
            'in_lab'               => B2cOrder::whereIn('lab_status', ['assigned', 'cutting', 'fitting'])->count(),
        ];

        // 2. Order Funnel / Pipeline Breakdown
        $pipeline = [
            'pending'       => B2cOrder::where('order_status', 'pending')->count(),
            'rx_review'     => $kpis['pending_rx'],
            'in_lab'        => $kpis['in_lab'],
            'ready_to_ship' => $kpis['ready_to_ship'],
            'shipped'       => B2cOrder::where('order_status', 'shipped')->count(),
            'delivered'     => B2cOrder::where('order_status', 'delivered')->count(),
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
        $recentOrders = B2cOrder::with(['items.lensPackage', 'user', 'payments'])
            ->latest('created_at')
            ->take(8)
            ->get();

        // 4. Product Category Summary from Database
        $framesCount = B2cOrderItem::where('product_type', 'frame')->count();
        $lensesCount = B2cOrderItem::where(function($q) {
            $q->whereNotNull('lens_package_id')->orWhere('product_type', 'lens');
        })->count();
        $gogglesCount = B2cOrderItem::where('product_type', 'goggles')->count();

        // Check legacy sales product table if B2C items are fresh/low
        if (($framesCount + $lensesCount + $gogglesCount) === 0 && Schema::hasTable('tbl_sales_product')) {
            $framesCount = DB::table('tbl_sales_product')->where('product_type', 'Frame')->count();
            $lensesCount = DB::table('tbl_sales_product')->whereIn('product_type', ['Lens', 'Glass'])->count();
            $gogglesCount = DB::table('tbl_sales_product')->where('product_type', 'Goggles')->count();
        }

        $productMixData = [
            ['category' => 'Optical Frames', 'units' => max(0, $framesCount), 'color' => '#0d5c56'],
            ['category' => 'Rx Lenses Fitted', 'units' => max(0, $lensesCount), 'color' => '#059669'],
            ['category' => 'Sunglasses & Goggles', 'units' => max(0, $gogglesCount), 'color' => '#0284c7'],
        ];

        // 5. Dynamic Multi-Period Historical Performance Dataset
        $minDateB2c = B2cOrder::min('created_at');
        $minDateLegacy = Schema::hasTable('tbl_sales') ? DB::table('tbl_sales')->min('sale_date') : null;
        $earliest = $minDateB2c ? Carbon::parse($minDateB2c) : ($minDateLegacy ? Carbon::parse($minDateLegacy) : Carbon::now()->startOfYear());
        $startDate = $earliest->copy()->startOfYear();
        $endDate   = Carbon::today();

        // Query real aggregated B2C daily orders
        $dbDailyB2c = B2cOrder::selectRaw("DATE(created_at) as order_date, COUNT(*) as order_count, SUM(grand_total) as total_revenue")
            ->where('created_at', '>=', $startDate)
            ->groupBy('order_date')
            ->get()
            ->keyBy('order_date');

        // Query legacy daily sales
        $dbDailyLegacy = collect();
        if (Schema::hasTable('tbl_sales')) {
            $dbDailyLegacy = DB::table('tbl_sales')
                ->selectRaw("DATE(sale_date) as order_date, COUNT(*) as order_count, SUM(total_payable) as total_revenue")
                ->where('sale_date', '>=', $startDate->toDateString())
                ->where(function($q) {
                    $q->where('is_deleted', 0)->orWhereNull('is_deleted');
                })
                ->groupBy('order_date')
                ->get()
                ->keyBy('order_date');
        }

        // Build continuous dynamic daily timeline
        $performanceData = [];
        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $dStr = $cursor->toDateString();
            $b2c = $dbDailyB2c->get($dStr);
            $legacy = $dbDailyLegacy->get($dStr);

            $dayOrders = 0;
            $dayRevenue = 0.0;

            if ($b2c) {
                $dayOrders += (int) $b2c->order_count;
                $dayRevenue += (float) $b2c->total_revenue;
            } elseif ($legacy) {
                $dayOrders += (int) $legacy->order_count;
                $dayRevenue += (float) $legacy->total_revenue;
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

        // Available distinct years for filter
        $availableYears = collect($performanceData)->pluck('year')->unique()->sort()->values()->toArray();
        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        $minDateStr = $startDate->toDateString();
        $maxDateStr = $endDate->toDateString();

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
            'maxDateStr'
        ));
    }
}

