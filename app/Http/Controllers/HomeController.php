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
            'stores'
        ));
    }
}
