<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\b2c\B2cOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class B2cCustomerController extends Controller
{
    /**
     * Display a listing of registered B2C online customers.
     */
    public function index(Request $request)
    {
        $page_title  = 'Registered B2C Customers';
        $breadcrumbs = [
            ['link' => route('index'), 'name' => 'Home'],
            ['name' => 'B2C Customers'],
        ];

        // Base query for registered online users or users with B2C orders
        $query = User::where(function ($q) {
            $q->where('user_type', 'B2C')
              ->orWhereHas('b2cOrders');
        })->withCount('b2cOrders')
          ->withSum('b2cOrders as total_spent', 'grand_total')
          ->with(['b2cOrders' => function ($q) {
              $q->latest('created_at')->limit(1);
          }, 'addresses']);

        // Omni Search (Name, Email, Phone, Staff ID)
        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('staff_id', 'like', "%{$term}%");
            });
        }

        // Filter by Order Activity (with orders / no orders)
        if ($request->filled('order_activity')) {
            $activity = $request->input('order_activity');
            if ($activity === 'has_orders') {
                $query->has('b2cOrders');
            } elseif ($activity === 'no_orders') {
                $query->doesntHave('b2cOrders');
            }
        }

        // Filter by Status (Active / Inactive)
        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        // Filter by Registration Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // KPI Counts
        $kpis = [
            'total_customers' => User::where(function ($q) {
                $q->where('user_type', 'B2C')->orWhereHas('b2cOrders');
            })->count(),
            'active_customers' => User::where(function ($q) {
                $q->where('user_type', 'B2C')->orWhereHas('b2cOrders');
            })->where('status', 1)->count(),
            'customers_with_orders' => User::where(function ($q) {
                $q->where('user_type', 'B2C')->orWhereHas('b2cOrders');
            })->has('b2cOrders')->count(),
            'total_online_orders' => B2cOrder::count(),
            'total_online_revenue' => B2cOrder::sum('grand_total') ?? 0,
        ];

        // Sort by newest registered first
        $customers = $query->latest('created_at')->paginate(20)->withQueryString();

        return view('admin.b2c_customers.index', compact('customers', 'kpis', 'page_title', 'breadcrumbs'));
    }

    /**
     * Display 360° details of a registered B2C customer.
     */
    public function show($id)
    {
        $customer = User::with(['b2cOrders.items', 'addresses'])
            ->withCount('b2cOrders')
            ->withSum('b2cOrders as total_spent', 'grand_total')
            ->findOrFail($id);

        $page_title  = 'Customer 360°: ' . $customer->name;
        $breadcrumbs = [
            ['link' => route('index'), 'name' => 'Home'],
            ['link' => route('admin.b2c-customers.index'), 'name' => 'B2C Customers'],
            ['name' => $customer->name],
        ];

        // Fetch customer's order history
        $orders = B2cOrder::with(['items', 'payments'])
            ->where('user_id', $customer->id)
            ->orWhere('guest_email', $customer->email)
            ->orWhere('guest_phone', $customer->phone)
            ->latest('created_at')
            ->get();

        return view('admin.b2c_customers.show', compact('customer', 'orders', 'page_title', 'breadcrumbs'));
    }

    /**
     * Toggle active/inactive status of a customer account.
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->status = ($user->status == 1) ? 0 : 1;
        $user->save();

        $state = ($user->status == 1) ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Customer account has been {$state}.");
    }
}
