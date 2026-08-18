<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\sale\Sale;
use App\Models\sale\SaleProduct;
use App\Models\sale\SalePayment;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class B2cOrderController extends Controller
{
    /**
     * Display a listing of B2C orders with KPI summary cards & filters.
     */
    public function index(Request $request)
    {
        $page_title = 'B2C Orders';
        $breadcrumbs = [
            ['name' => 'Dashboard', 'link' => route('index')],
            ['name' => 'B2C Online Orders', 'link' => 'javascript:void(0)'],
        ];

        // ── 1. Calculate KPI Metrics ──────────────────────────────────────
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $kpis = [
            'orders_today'         => Sale::b2c()->whereDate('created_at', $today)->count(),
            'revenue_today'        => (float) Sale::b2c()->whereDate('created_at', $today)->where('payment_status', 'paid')->sum('total_payable'),
            'orders_this_month'    => Sale::b2c()->where('created_at', '>=', $startOfMonth)->count(),
            'revenue_this_month'   => (float) Sale::b2c()->where('created_at', '>=', $startOfMonth)->where('payment_status', 'paid')->sum('total_payable'),
            'pending_orders'       => Sale::b2c()->where('order_status', 'pending')->count(),
            'ready_to_ship'        => Sale::b2c()->where('order_status', 'ready_to_ship')->count(),
            'cancelled_orders'     => Sale::b2c()->where('order_status', 'cancelled')->count(),
            'cancelled_this_month' => Sale::b2c()->where('order_status', 'cancelled')->where('created_at', '>=', $startOfMonth)->count(),
            'returns_count'        => Sale::b2c()->where('order_status', 'returned')->count(),
            'payment_issues'       => Sale::b2c()->whereIn('payment_status', ['failed', 'cod_pending'])->count(),
            'pending_rx'           => Sale::b2c()->where('rx_verification_status', 'pending_review')->count(),
            'in_lab'               => Sale::b2c()->whereIn('lab_status', ['assigned', 'cutting', 'fitting'])->count(),
        ];

        // ── 2. Build Query with Filters ───────────────────────────────────
        $query = Sale::b2c()->with(['products', 'user', 'payments'])
            ->latest('created_at');

        // Omni-Search: Order ID, Name, Phone, Email, Tracking
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('order_no', 'LIKE', "%{$search}%")
                  ->orWhere('cust_name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_no', 'LIKE', "%{$search}%")
                  ->orWhere('email_id', 'LIKE', "%{$search}%")
                  ->orWhere('tracking_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter: Order Status
        if ($request->filled('order_status') && $request->input('order_status') !== 'all') {
            $query->where('order_status', $request->input('order_status'));
        }

        // Filter: Payment Status
        if ($request->filled('payment_status') && $request->input('payment_status') !== 'all') {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // Filter: Prescription Verification Status
        if ($request->filled('rx_status') && $request->input('rx_status') !== 'all') {
            $query->where('rx_verification_status', $request->input('rx_status'));
        }

        // Filter: Delivery Method
        if ($request->filled('delivery_method') && $request->input('delivery_method') !== 'all') {
            $query->where('delivery_method', $request->input('delivery_method'));
        }

        // Filter: Product Type (via products)
        if ($request->filled('product_type') && $request->input('product_type') !== 'all') {
            $pType = $request->input('product_type');
            $query->whereHas('products', function ($iq) use ($pType) {
                $iq->where('product_type', $pType);
            });
        }

        // Filter: Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $orders = $query->paginate(15)->withQueryString();

        // Attach Membership Type to each order
        foreach ($orders as $ord) {
            $ord->membership_type = null;
            if (\Illuminate\Support\Facades\Schema::hasTable('tbl_customer')) {
                $c = DB::table('tbl_customer')
                    ->where(function($q) use ($ord) {
                        if ($ord->user_id) $q->where('customer_id', $ord->user_id);
                        if ($ord->contact_no) $q->orWhere('contact_no', $ord->contact_no);
                        if ($ord->email_id) $q->orWhere('email_id', $ord->email_id);
                    })
                    ->first();

                if ($c && !empty($c->membership_card_id) && !empty($c->membership_expiry) && Carbon::parse($c->membership_expiry)->isFuture()) {
                    $card = DB::table('tbl_membership_card')->where('card_id', $c->membership_card_id)->first();
                    $ord->membership_type = $card->card_name ?? 'VIP Member';
                }
            }
        }

        return view('admin.b2c_orders.index', compact('orders', 'kpis', 'page_title', 'breadcrumbs'));
    }

    /**
     * Display full 360° Order Detail View.
     */
    public function show($id)
    {
        $order = Sale::b2c()->with([
            'products.product',
            'products.lensPackage',
            'payments',
            'user',
            'offer',
        ])->findOrFail($id);

        $order->membership_type = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_customer')) {
            $c = DB::table('tbl_customer')
                ->where(function($q) use ($order) {
                    if ($order->user_id) $q->where('customer_id', $order->user_id);
                    if ($order->contact_no) $q->orWhere('contact_no', $order->contact_no);
                    if ($order->email_id) $q->orWhere('email_id', $order->email_id);
                })
                ->first();

            if ($c && !empty($c->membership_card_id) && !empty($c->membership_expiry) && Carbon::parse($c->membership_expiry)->isFuture()) {
                $card = DB::table('tbl_membership_card')->where('card_id', $c->membership_card_id)->first();
                $order->membership_type = $card->card_name ?? 'VIP Member';
            }
        }

        $page_title = 'Order ' . $order->order_no;
        $breadcrumbs = [
            ['name' => 'Dashboard', 'link' => route('index')],
            ['name' => 'B2C Orders', 'link' => route('admin.b2c-orders.index')],
            ['name' => $order->order_no, 'link' => 'javascript:void(0)'],
        ];

        // Available stores / lab centers
        $stores = Store::all();

        return view('admin.b2c_orders.show', compact('order', 'stores', 'page_title', 'breadcrumbs'));
    }

    /**
     * Update primary Order Status & log history.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|string',
            'note'         => 'nullable|string|max:500',
        ]);

        $order = Sale::findOrFail($id);
        $fromStatus = $order->order_status;
        $toStatus   = $request->input('order_status');

        $order->order_status = $toStatus;
        if ($request->filled('admin_note')) {
            $order->admin_note = $request->input('admin_note');
        } elseif ($request->filled('note')) {
            $order->admin_note = ($order->admin_note ? $order->admin_note . "\n" : "") . $request->input('note');
        }
        $order->save();

        // Auto-credit earned loyalty points if delivered
        if (in_array($toStatus, ['delivered', 'completed']) && $fromStatus !== $toStatus) {
            $this->creditLoyaltyPointsOnDelivery($order);
        }

        return redirect()->back()->with('success', "Order status successfully updated to " . ucfirst(str_replace('_', ' ', $toStatus)));
    }

    /**
     * Verify prescription (Approve / Flag Clarification / Reject & update power matrix).
     */
    public function verifyPrescription(Request $request, $id)
    {
        $request->validate([
            'rx_status'         => 'required|in:approved,clarification_needed,rejected,pending_review',
            'optometrist_notes' => 'nullable|string|max:1000',
            'items'             => 'nullable|array',
        ]);

        $order = Sale::findOrFail($id);
        $fromRxStatus = $order->rx_verification_status;
        $newRxStatus  = $request->input('rx_status');

        $order->rx_verification_status = $newRxStatus;
        $order->verified_by            = Auth::id();
        $order->verified_at            = Carbon::now();
        $order->optometrist_notes      = $request->input('optometrist_notes');

        // Save item prescription power matrix (supports items array or item_id + items_power)
        if ($request->has('item_id') && $request->has('items_power')) {
            $itemId = $request->input('item_id');
            $powers = $request->input('items_power');
            $item = SaleProduct::where('sale_id', $order->sale_id)->where('id', $itemId)->first();
            if (!$item) {
                $item = SaleProduct::where('sale_id', $order->sale_id)->first();
            }
            if ($item && is_array($powers)) {
                $item->update([
                    'GL_EYE_RS_D'    => (isset($powers['GL_EYE_RS_D']) && $powers['GL_EYE_RS_D'] !== '') ? $powers['GL_EYE_RS_D'] : $item->GL_EYE_RS_D,
                    'GL_EYE_RC_D'    => (isset($powers['GL_EYE_RC_D']) && $powers['GL_EYE_RC_D'] !== '') ? $powers['GL_EYE_RC_D'] : $item->GL_EYE_RC_D,
                    'GL_EYE_RA_D'    => (isset($powers['GL_EYE_RA_D']) && $powers['GL_EYE_RA_D'] !== '') ? $powers['GL_EYE_RA_D'] : $item->GL_EYE_RA_D,
                    'GL_EYE_RADD'    => (isset($powers['GL_EYE_RADD']) && $powers['GL_EYE_RADD'] !== '') ? $powers['GL_EYE_RADD'] : $item->GL_EYE_RADD,
                    'GL_EYE_RPD'     => (isset($powers['GL_EYE_RPD']) && $powers['GL_EYE_RPD'] !== '') ? $powers['GL_EYE_RPD'] : $item->GL_EYE_RPD,
                    'GL_EYE_LS_D'    => (isset($powers['GL_EYE_LS_D']) && $powers['GL_EYE_LS_D'] !== '') ? $powers['GL_EYE_LS_D'] : $item->GL_EYE_LS_D,
                    'GL_EYE_LC_D'    => (isset($powers['GL_EYE_LC_D']) && $powers['GL_EYE_LC_D'] !== '') ? $powers['GL_EYE_LC_D'] : $item->GL_EYE_LC_D,
                    'GL_EYE_LA_D'    => (isset($powers['GL_EYE_LA_D']) && $powers['GL_EYE_LA_D'] !== '') ? $powers['GL_EYE_LA_D'] : $item->GL_EYE_LA_D,
                    'GL_EYE_LADD'    => (isset($powers['GL_EYE_LADD']) && $powers['GL_EYE_LADD'] !== '') ? $powers['GL_EYE_LADD'] : $item->GL_EYE_LADD,
                    'GL_EYE_LPD'     => (isset($powers['GL_EYE_LPD']) && $powers['GL_EYE_LPD'] !== '') ? $powers['GL_EYE_LPD'] : $item->GL_EYE_LPD,
                    'GL_EYE_totalPD' => (isset($powers['GL_EYE_totalPD']) && $powers['GL_EYE_totalPD'] !== '') ? $powers['GL_EYE_totalPD'] : $item->GL_EYE_totalPD,
                ]);
            }
        } elseif ($request->has('items') && is_array($request->input('items'))) {
            foreach ($request->input('items') as $itemId => $powers) {
                $item = SaleProduct::where('sale_id', $order->sale_id)->where('id', $itemId)->first();
                if ($item) {
                    $item->update([
                        'GL_EYE_RS_D'    => (isset($powers['GL_EYE_RS_D']) && $powers['GL_EYE_RS_D'] !== '') ? $powers['GL_EYE_RS_D'] : $item->GL_EYE_RS_D,
                        'GL_EYE_RC_D'    => (isset($powers['GL_EYE_RC_D']) && $powers['GL_EYE_RC_D'] !== '') ? $powers['GL_EYE_RC_D'] : $item->GL_EYE_RC_D,
                        'GL_EYE_RA_D'    => (isset($powers['GL_EYE_RA_D']) && $powers['GL_EYE_RA_D'] !== '') ? $powers['GL_EYE_RA_D'] : $item->GL_EYE_RA_D,
                        'GL_EYE_RADD'    => (isset($powers['GL_EYE_RADD']) && $powers['GL_EYE_RADD'] !== '') ? $powers['GL_EYE_RADD'] : $item->GL_EYE_RADD,
                        'GL_EYE_RPD'     => (isset($powers['GL_EYE_RPD']) && $powers['GL_EYE_RPD'] !== '') ? $powers['GL_EYE_RPD'] : $item->GL_EYE_RPD,
                        'GL_EYE_LS_D'    => (isset($powers['GL_EYE_LS_D']) && $powers['GL_EYE_LS_D'] !== '') ? $powers['GL_EYE_LS_D'] : $item->GL_EYE_LS_D,
                        'GL_EYE_LC_D'    => (isset($powers['GL_EYE_LC_D']) && $powers['GL_EYE_LC_D'] !== '') ? $powers['GL_EYE_LC_D'] : $item->GL_EYE_LC_D,
                        'GL_EYE_LA_D'    => (isset($powers['GL_EYE_LA_D']) && $powers['GL_EYE_LA_D'] !== '') ? $powers['GL_EYE_LA_D'] : $item->GL_EYE_LA_D,
                        'GL_EYE_LADD'    => (isset($powers['GL_EYE_LADD']) && $powers['GL_EYE_LADD'] !== '') ? $powers['GL_EYE_LADD'] : $item->GL_EYE_LADD,
                        'GL_EYE_LPD'     => (isset($powers['GL_EYE_LPD']) && $powers['GL_EYE_LPD'] !== '') ? $powers['GL_EYE_LPD'] : $item->GL_EYE_LPD,
                        'GL_EYE_totalPD' => (isset($powers['GL_EYE_totalPD']) && $powers['GL_EYE_totalPD'] !== '') ? $powers['GL_EYE_totalPD'] : $item->GL_EYE_totalPD,
                    ]);
                }
            }
        }

        // If approved and order was in pending/confirmed, advance to processing
        if ($newRxStatus === 'approved' && in_array($order->order_status, ['pending', 'confirmed'])) {
            $order->order_status = 'processing';
        }

        $order->save();

        return redirect()->back()->with('success', "Prescription power saved & synced with Optical Lab Job Sheet!");
    }

    /**
     * Update Optical Lab / Fulfillment Center status.
     */
    public function updateLabStatus(Request $request, $id)
    {
        $request->validate([
            'lab_status'      => 'required|string',
            'assigned_lab_id' => 'nullable|integer',
            'lab_job_number'  => 'nullable|string|max:50',
            'lab_notes'       => 'nullable|string|max:500',
        ]);

        $order = Sale::findOrFail($id);
        $toLabStatus   = $request->input('lab_status');

        $order->lab_status = $toLabStatus;
        if ($request->filled('assigned_lab_id')) {
            $order->assigned_lab_id = $request->input('assigned_lab_id');
            if (empty($order->lab_assigned_at)) {
                $order->lab_assigned_at = Carbon::now();
            }
        }
        if ($request->filled('lab_job_number')) {
            $order->lab_job_number = $request->input('lab_job_number');
        }
        if ($request->filled('lab_notes')) {
            $order->lab_notes = $request->input('lab_notes');
        }
        if ($toLabStatus === 'qc_passed' || $toLabStatus === 'completed') {
            $order->lab_completed_at = Carbon::now();
            if ($order->order_status === 'processing') {
                $order->order_status = 'ready_to_ship';
            }
        }

        $order->save();

        return redirect()->back()->with('success', "Lab status updated to " . ucfirst(str_replace('_', ' ', $toLabStatus)));
    }

    /**
     * Update Courier & Shipment Tracking information.
     */
    public function updateTracking(Request $request, $id)
    {
        $request->validate([
            'courier_partner'        => 'required|string|max:100',
            'tracking_number'        => 'required|string|max:100',
            'tracking_url'           => 'nullable|url|max:255',
            'expected_delivery_date' => 'nullable|date',
            'delivery_method'        => 'nullable|string',
        ]);

        $order = Sale::findOrFail($id);

        $order->courier_partner = $request->input('courier_partner');
        $order->tracking_number = $request->input('tracking_number');
        $order->tracking_url    = $request->input('tracking_url');
        if ($request->filled('expected_delivery_date')) {
            $order->expected_delivery_date = $request->input('expected_delivery_date');
        }
        if ($request->filled('delivery_method')) {
            $order->delivery_method = $request->input('delivery_method');
        }

        // Advance to shipped if was ready to ship
        if (in_array($order->order_status, ['processing', 'ready_to_ship', 'confirmed'])) {
            $order->order_status = 'shipped';
        }

        $order->save();

        return redirect()->back()->with('success', "Tracking information successfully updated and order marked as Shipped.");
    }

    /**
     * Add an internal admin staff note.
     */
    public function addNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        $order = Sale::findOrFail($id);
        
        $currentNote = $order->admin_note;
        $order->admin_note = ($currentNote ? $currentNote . "\n" : "") . $request->input('note');
        $order->save();

        return redirect()->back()->with('success', "Internal staff note recorded.");
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $order = Sale::findOrFail($id);
        $order->order_status = 'cancelled';
        $order->admin_note   = ($order->admin_note ? $order->admin_note . " | " : "") . "Cancelled: " . $request->input('cancellation_reason');
        $order->save();

        return redirect()->back()->with('success', "Order has been marked as Cancelled.");
    }

    /**
     * Process return, exchange, or optical lens remake.
     */
    public function processReturn(Request $request, $id)
    {
        $request->validate([
            'return_type'   => 'required|in:refund,replacement,lens_remake',
            'reason'        => 'required|in:power_mismatch,frame_damage,fit_issue,changed_mind,other',
            'exchange_type' => 'required|in:same_product,different_power,different_frame,none',
            'admin_notes'   => 'nullable|string|max:1000',
        ]);

        $order = Sale::findOrFail($id);

        $order->return_type = $request->input('return_type');
        $order->return_reason = $request->input('reason');
        $order->return_exchange_type = $request->input('exchange_type');
        $order->return_stage = 'requested';
        $order->return_admin_notes = $request->input('admin_notes');

        if ($request->input('return_type') === 'lens_remake') {
            $order->lab_status = 'assigned'; // Re-open lab cutting ticket
            $order->lab_notes  = "FREE LENS REMAKE: " . ($request->input('admin_notes') ?? 'Optical power adjustment');
        } else {
            $order->order_status = 'returned';
        }
        $order->save();

        return redirect()->back()->with('success', "Return / Optical Remake action registered successfully.");
    }

    /**
     * Bulk status update for selected orders.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'order_ids'   => 'required|array',
            'order_ids.*' => 'integer',
            'bulk_status' => 'required|string',
        ]);

        $ids    = $request->input('order_ids');
        $status = $request->input('bulk_status');

        Sale::whereIn('id', $ids)->where('sales_type', 0)->update(['order_status' => $status]);

        return redirect()->back()->with('success', count($ids) . " orders updated to " . ucfirst(str_replace('_', ' ', $status)));
    }

    /**
     * Printable Tax Invoice.
     */
    public function invoice($id)
    {
        $order = Sale::b2c()->with(['products.product', 'products.lensPackage', 'payments', 'user'])->findOrFail($id);
        $store = Store::first();

        return view('admin.b2c_orders.invoice', compact('order', 'store'));
    }

    /**
     * Printable Optical Lab Work Order / Job Sheet.
     */
    public function labWorkOrder($id)
    {
        $order = Sale::b2c()->with(['products.product', 'products.lensPackage', 'user'])->findOrFail($id);
        $store = Store::first();

        return view('admin.b2c_orders.lab_work_order', compact('order', 'store'));
    }

    /**
     * Credit earned loyalty points to customer ledger once order is delivered.
     */
    protected function creditLoyaltyPointsOnDelivery(Sale $order)
    {
        try {
            $pointsToCredit = (int) ($order->earnedPoints ?? 0);
            if ($pointsToCredit <= 0) {
                return;
            }

            if (!\Illuminate\Support\Facades\Schema::hasTable('tbl_loyaltyrogram_histroy')) {
                return;
            }

            // Check if already credited for this order to prevent duplicate points
            $alreadyCredited = DB::table('tbl_loyaltyrogram_histroy')
                ->where('description', 'LIKE', '%' . $order->order_no . '%')
                ->where('add_remove', 1) // 1 = Added / Earned
                ->exists();

            if ($alreadyCredited) {
                return;
            }

            // Find customer record
            $customer = null;
            if ($order->user_id && \Illuminate\Support\Facades\Schema::hasTable('users')) {
                $user = DB::table('users')->where('id', $order->user_id)->first();
                if ($user && \Illuminate\Support\Facades\Schema::hasTable('tbl_customer')) {
                    $customer = DB::table('tbl_customer')
                        ->where(function ($q) use ($user, $order) {
                            if (!empty($user->phone)) $q->orWhere('contact_no', $user->phone);
                            if (!empty($user->email)) $q->orWhere('email_id', $user->email);
                            if (!empty($order->contact_no)) $q->orWhere('contact_no', $order->contact_no);
                            if (!empty($order->email_id)) $q->orWhere('email_id', $order->email_id);
                            $q->orWhere('customer_id', $user->id);
                        })
                        ->orderByDesc('customer_id')
                        ->first();
                }
            }

            if (!$customer && \Illuminate\Support\Facades\Schema::hasTable('tbl_customer') && (!empty($order->contact_no) || !empty($order->email_id))) {
                $customer = DB::table('tbl_customer')
                    ->where(function ($q) use ($order) {
                        if (!empty($order->contact_no)) $q->orWhere('contact_no', $order->contact_no);
                        if (!empty($order->email_id)) $q->orWhere('email_id', $order->email_id);
                    })
                    ->orderByDesc('customer_id')
                    ->first();
            }

            if ($customer) {
                $openingBal = (float) ($customer->Loyalty_Points_Bal ?? 0);
                $closingBal = $openingBal + $pointsToCredit;

                // Log in Loyalty Passbook History
                DB::table('tbl_loyaltyrogram_histroy')->insert([
                    'customer_id'    => $customer->customer_id,
                    'opening_points' => $openingBal,
                    'redeem'         => $pointsToCredit,
                    'bal_point'      => $closingBal,
                    'description'    => 'Earned on Delivery of Order ' . $order->order_no,
                    'add_remove'     => 1, // 1 = Added/Earned
                    'store_id'       => $order->store_id ?? 1,
                    'added_by'       => Auth::id() ?? 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ]);

                // Update Customer Balance in tbl_customer
                DB::table('tbl_customer')
                    ->where('customer_id', $customer->customer_id)
                    ->update([
                        'Loyalty_Points'     => ($customer->Loyalty_Points ?? 0) + $pointsToCredit,
                        'Loyalty_Points_Bal' => $closingBal,
                        'updated_at'         => Carbon::now(),
                    ]);

                // Also update users table if loyalty_points column exists
                if ($order->user_id && \Illuminate\Support\Facades\Schema::hasTable('users') && \Illuminate\Support\Facades\Schema::hasColumn('users', 'loyalty_points')) {
                    DB::table('users')->where('id', $order->user_id)->increment('loyalty_points', $pointsToCredit);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error crediting loyalty points on delivery: ' . $e->getMessage());
        }
    }
}
