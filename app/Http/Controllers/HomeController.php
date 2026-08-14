<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\b2c\B2cOrder;
use App\Models\b2c\B2cOrderItem;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        // 1. Executive KPIs
        $kpis = [
            'orders_today'       => B2cOrder::whereDate('created_at', $today)->count(),
            'revenue_today'      => (float) B2cOrder::whereDate('created_at', $today)->where('payment_status', 'paid')->sum('grand_total'),
            'orders_this_month'  => B2cOrder::where('created_at', '>=', $startOfMonth)->count(),
            'revenue_this_month' => (float) B2cOrder::where('created_at', '>=', $startOfMonth)->where('payment_status', 'paid')->sum('grand_total'),
            'pending_rx'         => B2cOrder::where('rx_verification_status', 'pending_review')->count(),
            'in_lab'             => B2cOrder::whereIn('lab_status', ['assigned', 'cutting', 'fitting'])->count(),
            'ready_to_ship'      => B2cOrder::where(function ($q) {
                $q->where('lab_status', 'completed')
                  ->orWhere('order_status', 'ready_to_ship');
            })->whereNull('tracking_number')->count(),
            'payment_issues'     => B2cOrder::whereIn('payment_status', ['failed', 'cod_pending'])->count(),
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

        // 3. Live Recent Orders (8 most recent)
        $recentOrders = B2cOrder::with(['items.lensPackage', 'user', 'payments'])
            ->latest('created_at')
            ->take(8)
            ->get();

        // 4. Last 7 Days Daily Performance
        $dailyTrend = collect(range(6, 0))->map(function ($daysAgo) {
            $d = Carbon::today()->subDays($daysAgo);
            return [
                'day'     => $d->format('D, d M'),
                'orders'  => B2cOrder::whereDate('created_at', $d)->count(),
                'revenue' => (float) B2cOrder::whereDate('created_at', $d)->where('payment_status', 'paid')->sum('grand_total'),
            ];
        });

        // 5. Product Category Summary from Order Items
        $lensVsFrame = [
            'frames'   => B2cOrderItem::where('product_type', 'frame')->count(),
            'lenses'   => B2cOrderItem::whereNotNull('lens_package_id')->count(),
            'goggles'  => B2cOrderItem::where('product_type', 'goggles')->count(),
        ];

        return view('layouts.index', compact('page_title', 'breadcrumbs', 'kpis', 'pipeline', 'recentOrders', 'dailyTrend', 'lensVsFrame'));
    }
}
