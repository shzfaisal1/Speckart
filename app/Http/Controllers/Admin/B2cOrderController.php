<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\b2c\B2cOrder;
use App\Models\b2c\B2cOrderItem;
use App\Models\b2c\B2cOrderLog;
use App\Models\b2c\B2cOrderNote;
use App\Models\b2c\B2cOrderPayment;
use App\Models\b2c\B2cOrderReturn;
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
        // Sync legacy sales at most once per hour (not every page load)
        if (!cache()->has('b2c_legacy_sync_done')) {
            $this->syncFromLegacySales();
            cache()->put('b2c_legacy_sync_done', true, now()->addHour());
        }

        $page_title = 'B2C Orders';
        $breadcrumbs = [
            ['name' => 'Dashboard', 'link' => route('index')],
            ['name' => 'B2C Online Orders', 'link' => 'javascript:void(0)'],
        ];

        // ── 1. Calculate KPI Metrics ──────────────────────────────────────
        $today = Carbon::today();

        $startOfMonth = Carbon::now()->startOfMonth();
        $returnsCount = B2cOrder::where('order_status', 'returned')->count();
        if (\Illuminate\Support\Facades\Schema::hasTable('b2c_order_returns')) {
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

        // ── 2. Build Query with Filters ───────────────────────────────────
        $query = B2cOrder::with(['items', 'user', 'payments'])
            ->latest('created_at');

        // Omni-Search: Order ID, Name, Phone, Email, Tracking
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhere('guest_name', 'LIKE', "%{$search}%")
                  ->orWhere('guest_phone', 'LIKE', "%{$search}%")
                  ->orWhere('guest_email', 'LIKE', "%{$search}%")
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

        // Filter: Product Type (via items)
        if ($request->filled('product_type') && $request->input('product_type') !== 'all') {
            $pType = $request->input('product_type');
            $query->whereHas('items', function ($iq) use ($pType) {
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
                        if ($ord->customer_phone) $q->orWhere('contact_no', $ord->customer_phone);
                        if ($ord->customer_email) $q->orWhere('email_id', $ord->customer_email);
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
        $order = B2cOrder::with([
            'items.product',
            'items.lensPackage',
            'payments',
            'logs.user',
            'notes.author',
            'returns.item',
            'returns.user',
            'user',
            'optometrist',
            'offer',
        ])->findOrFail($id);

        $order->membership_type = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('tbl_customer')) {
            $c = DB::table('tbl_customer')
                ->where(function($q) use ($order) {
                    if ($order->user_id) $q->where('customer_id', $order->user_id);
                    if ($order->customer_phone) $q->orWhere('contact_no', $order->customer_phone);
                    if ($order->customer_email) $q->orWhere('email_id', $order->customer_email);
                })
                ->first();

            if ($c && !empty($c->membership_card_id) && !empty($c->membership_expiry) && Carbon::parse($c->membership_expiry)->isFuture()) {
                $card = DB::table('tbl_membership_card')->where('card_id', $c->membership_card_id)->first();
                $order->membership_type = $card->card_name ?? 'VIP Member';
            }
        }

        $page_title = 'Order ' . $order->order_number;
        $breadcrumbs = [
            ['name' => 'Dashboard', 'link' => route('index')],
            ['name' => 'B2C Orders', 'link' => route('admin.b2c-orders.index')],
            ['name' => $order->order_number, 'link' => 'javascript:void(0)'],
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

        $order = B2cOrder::findOrFail($id);
        $fromStatus = $order->order_status;
        $toStatus   = $request->input('order_status');

        $order->order_status = $toStatus;
        if ($request->filled('admin_note')) {
            $order->admin_note = $request->input('admin_note');
        }
        $order->save();

        // Sync with legacy tbl_sales
        $this->syncToLegacySale($order, $toStatus);

        // Auto-credit earned loyalty points if delivered
        if (in_array($toStatus, ['delivered', 'completed']) && $fromStatus !== $toStatus) {
            $this->creditLoyaltyPointsOnDelivery($order);
        }

        // Record in Activity Log
        B2cOrderLog::create([
            'order_id'    => $order->id,
            'user_id'     => Auth::id(),
            'action'      => 'order_status_updated',
            'from_status' => $fromStatus,
            'to_status'   => $toStatus,
            'notes'       => $request->input('note') ?? "Order status changed from {$fromStatus} to {$toStatus}",
            'created_at'  => Carbon::now(),
        ]);

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

        $order = B2cOrder::findOrFail($id);
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
            $item = B2cOrderItem::where('order_id', $order->id)->where('id', $itemId)->first();
            if (!$item) {
                $item = B2cOrderItem::where('order_id', $order->id)->first();
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
                $item = B2cOrderItem::where('order_id', $order->id)->where('id', $itemId)->first();
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

        // Sync with legacy tbl_sales
        $this->syncToLegacySale($order, $order->order_status);

        // Record in Activity Log
        $user = Auth::user();
        $userName = $user ? $user->name : 'Optometrist';
        B2cOrderLog::create([
            'order_id'    => $order->id,
            'user_id'     => Auth::id(),
            'action'      => 'prescription_verified',
            'from_status' => $fromRxStatus,
            'to_status'   => $newRxStatus,
            'notes'       => "Prescription saved & verified as '{$newRxStatus}' by {$userName}. Note: " . ($request->input('optometrist_notes') ?? 'None'),
            'created_at'  => Carbon::now(),
        ]);

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

        $order = B2cOrder::findOrFail($id);
        $fromLabStatus = $order->lab_status;
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

        // Sync with legacy tbl_sales
        $this->syncToLegacySale($order, $order->order_status);

        // Record in Activity Log
        B2cOrderLog::create([
            'order_id'    => $order->id,
            'user_id'     => Auth::id(),
            'action'      => 'lab_status_updated',
            'from_status' => $fromLabStatus,
            'to_status'   => $toLabStatus,
            'notes'       => "Lab status updated to '{$toLabStatus}'. Job #: " . ($order->lab_job_number ?? 'N/A'),
            'created_at'  => Carbon::now(),
        ]);

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

        $order = B2cOrder::findOrFail($id);

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

        // Sync with legacy tbl_sales
        $this->syncToLegacySale($order, $order->order_status);

        // Record in Activity Log
        B2cOrderLog::create([
            'order_id'    => $order->id,
            'user_id'     => Auth::id(),
            'action'      => 'tracking_updated',
            'from_status' => null,
            'to_status'   => 'shipped',
            'notes'       => "Courier: {$order->courier_partner} | Tracking: {$order->tracking_number}",
            'created_at'  => Carbon::now(),
        ]);

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

        $order = B2cOrder::findOrFail($id);

        $note = B2cOrderNote::create([
            'order_id'            => $order->id,
            'user_id'             => Auth::id(),
            'note'                => $request->input('note'),
            'is_customer_visible' => false,
        ]);

        B2cOrderLog::create([
            'order_id'    => $order->id,
            'user_id'     => Auth::id(),
            'action'      => 'admin_note_added',
            'from_status' => null,
            'to_status'   => null,
            'notes'       => "Note added: " . substr($request->input('note'), 0, 80) . "...",
            'created_at'  => Carbon::now(),
        ]);

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

        $order = B2cOrder::findOrFail($id);
        $fromStatus = $order->order_status;
        $order->order_status = 'cancelled';
        $order->admin_note   = ($order->admin_note ? $order->admin_note . " | " : "") . "Cancelled: " . $request->input('cancellation_reason');
        $order->save();

        // Sync with legacy tbl_sales
        $this->syncToLegacySale($order, 'cancelled');

        B2cOrderLog::create([
            'order_id'    => $order->id,
            'user_id'     => Auth::id(),
            'action'      => 'order_cancelled',
            'from_status' => $fromStatus,
            'to_status'   => 'cancelled',
            'notes'       => "Order cancelled. Reason: " . $request->input('cancellation_reason'),
            'created_at'  => Carbon::now(),
        ]);

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

        $order = B2cOrder::findOrFail($id);

        $returnRecord = B2cOrderReturn::create([
            'order_id'      => $order->id,
            'user_id'       => Auth::id(),
            'return_type'   => $request->input('return_type'),
            'reason'        => $request->input('reason'),
            'exchange_type' => $request->input('exchange_type'),
            'status'        => 'requested',
            'admin_notes'   => $request->input('admin_notes'),
        ]);

        $order->return_reason = $request->input('reason');
        $order->exchange_type = $request->input('exchange_type');
        if ($request->input('return_type') === 'lens_remake') {
            $order->lab_status = 'assigned'; // Re-open lab cutting ticket
            $order->lab_notes  = "FREE LENS REMAKE: " . ($request->input('admin_notes') ?? 'Optical power adjustment');
        } else {
            $order->order_status = 'returned';
        }
        $order->save();

        // Sync with legacy tbl_sales
        $this->syncToLegacySale($order, $order->order_status);

        B2cOrderLog::create([
            'order_id'    => $order->id,
            'user_id'     => Auth::id(),
            'action'      => 'return_remake_initiated',
            'from_status' => $order->order_status,
            'to_status'   => $request->input('return_type'),
            'notes'       => "Initiated " . ucfirst(str_replace('_', ' ', $request->input('return_type'))) . " due to " . ucfirst(str_replace('_', ' ', $request->input('reason'))),
            'created_at'  => Carbon::now(),
        ]);

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

        B2cOrder::whereIn('id', $ids)->update(['order_status' => $status]);

        $updatedOrders = B2cOrder::whereIn('id', $ids)->get();
        foreach ($updatedOrders as $ord) {
            $this->syncToLegacySale($ord, $status);

            B2cOrderLog::create([
                'order_id'    => $ord->id,
                'user_id'     => Auth::id(),
                'action'      => 'bulk_status_update',
                'from_status' => null,
                'to_status'   => $status,
                'notes'       => "Bulk updated to {$status}",
                'created_at'  => Carbon::now(),
            ]);
        }

        return redirect()->back()->with('success', count($ids) . " orders updated to " . ucfirst(str_replace('_', ' ', $status)));
    }

    /**
     * Printable Tax Invoice.
     */
    public function invoice($id)
    {
        $order = B2cOrder::with(['items.product', 'items.lensPackage', 'payments', 'user'])->findOrFail($id);
        $store = Store::first();

        return view('admin.b2c_orders.invoice', compact('order', 'store'));
    }

    /**
     * Printable Optical Lab Work Order / Job Sheet.
     */
    public function labWorkOrder($id)
    {
        $order = B2cOrder::with(['items.product', 'items.lensPackage', 'optometrist'])->findOrFail($id);
        $store = Store::first();

        return view('admin.b2c_orders.lab_work_order', compact('order', 'store'));
    }

    /**
     * Automatically sync any existing orders from tbl_sales into b2c_orders if missing.
     */
    protected function syncFromLegacySales()
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('tbl_sales') || !\Illuminate\Support\Facades\Schema::hasTable('b2c_orders')) {
                return;
            }

            $legacySales = DB::table('tbl_sales')
                ->where(function($q) {
                    $q->where('sales_type', 0)
                      ->orWhere('order_no', 'LIKE', 'WEB%')
                      ->orWhere('order_no', 'LIKE', 'B2C%');
                })
                ->where(function($q) {
                    $q->where('is_deleted', 0)->orWhereNull('is_deleted');
                })
                ->get();

            foreach ($legacySales as $sale) {
                $orderNo = $sale->order_no ?? ('WEB' . $sale->sale_id);
                $exists = B2cOrder::where('order_number', $orderNo)->exists();
                if ($exists) {
                    continue;
                }

                $products = DB::table('tbl_sales_product')->where('sale_id', $sale->sale_id ?? $sale->id)->get();
                $hasRx = false;
                foreach ($products as $p) {
                    if (!empty($p->GL_EYE_RS_D) || !empty($p->GL_EYE_LS_D) || !empty($p->prescription_notes)) {
                        $hasRx = true;
                        break;
                    }
                }

                $addressSnapshot = [
                    'full_name'    => $sale->cust_name,
                    'phone'        => $sale->contact_no,
                    'email'        => $sale->email_id,
                    'full_address' => $sale->cust_address,
                    'pincode'      => $sale->pincode,
                ];

                $order = B2cOrder::create([
                    'order_number'               => $orderNo,
                    'user_id'                    => $sale->added_by ?? $sale->cust_id,
                    'guest_name'                 => $sale->cust_name,
                    'guest_email'                => $sale->email_id,
                    'guest_phone'                => $sale->contact_no,
                    'shipping_address_snapshot'  => $addressSnapshot,
                    'subtotal'                   => (float)($sale->total_item_price ?? $sale->total_basic_amount ?? 0),
                    'discount_amount'            => (float)($sale->total_discount ?? 0),
                    'tax_amount'                 => (float)($sale->total_gst_amount ?? 0),
                    'shipping_fee'               => 0,
                    'grand_total'                => (float)($sale->total_payable ?? $sale->pay_amount ?? 0),
                    'coupon_code'                => $sale->earncoupon ?? null,
                    'coupon_discount'            => (float)($sale->coupon_amount ?? 0),
                    'loyalty_points_used'        => (float)($sale->loyalty_point_amount ?? 0),
                    'bogo_discount'              => (float)($sale->bogo_discount ?? 0),
                    'order_status'               => 'pending',
                    'rx_verification_status'     => $hasRx ? 'pending_review' : 'not_required',
                    'is_rx_required'             => $hasRx,
                    'payment_status'             => ($sale->pay_amount >= $sale->total_payable && $sale->total_payable > 0) ? 'paid' : 'pending',
                    'delivery_method'            => 'standard',
                    'created_at'                 => $sale->created_at ?? Carbon::now(),
                    'updated_at'                 => $sale->updated_at ?? Carbon::now(),
                ]);

                foreach ($products as $prod) {
                    $rx = null;
                    if (!empty($prod->prescription_notes)) {
                        $rx = json_decode($prod->prescription_notes, true);
                    }

                    B2cOrderItem::create([
                        'order_id'              => $order->id,
                        'product_id'            => $prod->product_id ?? null,
                        'product_code'          => $prod->product_code ?? null,
                        'product_name'          => $prod->product_deatils ?? 'Product',
                        'product_type'          => $prod->product_type ?? 'frame',
                        'frame_color'           => $prod->product_color ?? null,
                        'frame_size'            => $prod->product_size ?? null,
                        'qty'                   => (int)($prod->qty ?? 1),
                        'base_price'            => (float)($prod->retail_price ?? $prod->base_price ?? 0),
                        'sale_price'            => (float)($prod->sale_price ?? 0),
                        'discount_amt'          => (float)($prod->discount_amt ?? $prod->product_discount ?? 0),
                        'total_price'           => (float)(($prod->sale_price ?? 0) * ($prod->qty ?? 1)),
                        'lens_package_id'       => $prod->package_id ?? null,
                        'prescription_source'   => is_array($rx) ? ($rx['source'] ?? 'manual_entry') : 'manual_entry',
                        'prescription_file_url' => is_array($rx) ? ($rx['file_url'] ?? null) : null,
                        'GL_EYE_RS_D'           => $prod->GL_EYE_RS_D ?? (is_array($rx) ? ($rx['GL_EYE_RS_D'] ?? null) : null),
                        'GL_EYE_RC_D'           => $prod->GL_EYE_RC_D ?? (is_array($rx) ? ($rx['GL_EYE_RC_D'] ?? null) : null),
                        'GL_EYE_RA_D'           => $prod->GL_EYE_RA_D ?? (is_array($rx) ? ($rx['GL_EYE_RA_D'] ?? null) : null),
                        'GL_EYE_RADD'           => $prod->GL_EYE_RADD ?? (is_array($rx) ? ($rx['GL_EYE_RADD'] ?? null) : null),
                        'GL_EYE_LS_D'           => $prod->GL_EYE_LS_D ?? (is_array($rx) ? ($rx['GL_EYE_LS_D'] ?? null) : null),
                        'GL_EYE_LC_D'           => $prod->GL_EYE_LC_D ?? (is_array($rx) ? ($rx['GL_EYE_LC_D'] ?? null) : null),
                        'GL_EYE_LA_D'           => $prod->GL_EYE_LA_D ?? (is_array($rx) ? ($rx['GL_EYE_LA_D'] ?? null) : null),
                        'GL_EYE_LADD'           => $prod->GL_EYE_LADD ?? (is_array($rx) ? ($rx['GL_EYE_LADD'] ?? null) : null),
                        'GL_EYE_totalPD'        => $prod->GL_EYE_totalPD ?? (is_array($rx) ? ($rx['GL_EYE_totalPD'] ?? null) : null),
                        'prescription_notes'    => $prod->prescription_notes ?? null,
                        'item_status'           => 'pending',
                        'created_at'            => $prod->created_at ?? $order->created_at,
                        'updated_at'            => $prod->updated_at ?? $order->updated_at,
                    ]);
                }

                B2cOrderPayment::create([
                    'order_id'        => $order->id,
                    'payment_gateway' => strtolower($sale->pay_method ?? 'COD'),
                    'amount'          => (float)($sale->total_payable ?? $sale->pay_amount ?? 0),
                    'payment_method'  => strtoupper($sale->pay_method ?? 'COD'),
                    'status'          => ($sale->pay_amount >= $sale->total_payable && $sale->total_payable > 0) ? 'success' : 'pending',
                    'paid_at'         => ($sale->pay_amount >= $sale->total_payable && $sale->total_payable > 0) ? ($sale->created_at ?? Carbon::now()) : null,
                    'created_at'      => $sale->created_at ?? Carbon::now(),
                    'updated_at'      => $sale->updated_at ?? Carbon::now(),
                ]);

                B2cOrderLog::create([
                    'order_id'   => $order->id,
                    'user_id'    => $sale->added_by ?? null,
                    'action'     => 'order_placed',
                    'notes'      => 'Order created via checkout.',
                    'created_at' => $sale->created_at ?? Carbon::now(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('B2C Order Sync error: ' . $e->getMessage());
        }
    }

    /**
     * Keep legacy tbl_sales in sync with B2C Order status updates.
     */
    protected function syncToLegacySale(B2cOrder $order, ?string $orderStatus = null)
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('tbl_sales')) {
                return;
            }

            $status = $orderStatus ?? $order->order_status;
            $legacySalesStatus = 0;

            if (in_array($status, ['delivered', 'completed'])) {
                $legacySalesStatus = 2;
            } elseif (in_array($status, ['shipped', 'ready_to_ship', 'processing'])) {
                $legacySalesStatus = 1;
            } elseif (in_array($status, ['cancelled', 'returned'])) {
                $legacySalesStatus = 3;
            } else {
                $legacySalesStatus = 0;
            }

            $updateData = [
                'sales_status' => $legacySalesStatus,
                'updated_at'   => Carbon::now(),
            ];

            if ($status === 'delivered') {
                $updateData['delivered_date'] = Carbon::now()->toDateString();
            }

            if (!empty($order->tracking_number)) {
                $updateData['tracking_no'] = $order->tracking_number;
            }

            DB::table('tbl_sales')
                ->where('order_no', $order->order_number)
                ->orWhere('id', $order->id)
                ->update($updateData);

        } catch (\Exception $e) {
            \Log::error('Error syncing status to legacy sales: ' . $e->getMessage());
        }
    }

    /**
     * Credit earned loyalty points to customer ledger once order is delivered.
     */
    protected function creditLoyaltyPointsOnDelivery(B2cOrder $order)
    {
        try {
            $pointsToCredit = (int) ($order->loyalty_points_earned ?? 0);
            if ($pointsToCredit <= 0) {
                return;
            }

            if (!\Illuminate\Support\Facades\Schema::hasTable('tbl_loyaltyrogram_histroy')) {
                return;
            }

            // Check if already credited for this order to prevent duplicate points
            $alreadyCredited = DB::table('tbl_loyaltyrogram_histroy')
                ->where('description', 'LIKE', '%' . $order->order_number . '%')
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
                            if (!empty($order->guest_phone)) $q->orWhere('contact_no', $order->guest_phone);
                            if (!empty($order->guest_email)) $q->orWhere('email_id', $order->guest_email);
                            $q->orWhere('customer_id', $user->id);
                        })
                        ->orderByDesc('customer_id')
                        ->first();
                }
            }

            if (!$customer && \Illuminate\Support\Facades\Schema::hasTable('tbl_customer') && (!empty($order->guest_phone) || !empty($order->guest_email))) {
                $customer = DB::table('tbl_customer')
                    ->where(function ($q) use ($order) {
                        if (!empty($order->guest_phone)) $q->orWhere('contact_no', $order->guest_phone);
                        if (!empty($order->guest_email)) $q->orWhere('email_id', $order->guest_email);
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
                    'description'    => 'Earned on Delivery of Order ' . $order->order_number,
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
