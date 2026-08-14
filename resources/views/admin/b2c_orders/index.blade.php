@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap');

.b2c-wrap {
    font-family: 'Inter', sans-serif;
    padding: 0 4px;
}
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.kpi-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
    text-decoration: none !important;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.kpi-title {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.kpi-value {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    margin-top: 4px;
}
.kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.kpi-icon.primary { background: #e0f2fe; color: #0284c7; }
.kpi-icon.success { background: #ecfdf5; color: #059669; }
.kpi-icon.warning { background: #fffbeb; color: #d97706; }
.kpi-icon.purple  { background: #f3e8ff; color: #9333ea; }
.kpi-icon.danger  { background: #fef2f2; color: #dc2626; }

/* Filter & Search Bar */
.filter-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 18px 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.filter-grid-row1 {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
    gap: 12px;
    margin-bottom: 12px;
}
.filter-grid-row2 {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding-top: 12px;
    border-top: 1px dashed #e2e8f0;
}
.date-inputs-group {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    flex-wrap: wrap;
}
.date-presets-group {
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-preset {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #475569;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
}
.btn-preset:hover {
    background: #07484A;
    color: #ffffff;
    border-color: #07484A;
}
.form-label-custom {
    font-size: 11.5px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 4px;
    display: block;
}
.form-control-custom {
    width: 100%;
    height: 38px;
    padding: 6px 12px;
    font-size: 12.5px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background-color: #ffffff;
    color: #1e293b;
    outline: none;
    transition: border-color 0.15s;
}
.form-control-custom:focus {
    border-color: #07484A;
    box-shadow: 0 0 0 2px rgba(7,72,74,0.15);
}

/* Orders Table */
.orders-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.table-responsive {
    overflow-x: auto;
}
.orders-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    text-align: left;
}
.orders-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 16px;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}
.orders-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.orders-table tr:hover {
    background-color: #f8fafc;
}
.order-num {
    font-family: 'JetBrains Mono', monospace;
    font-weight: 700;
    color: #07484A;
    text-decoration: none;
}
.order-num:hover {
    text-decoration: underline;
}
.customer-name {
    font-weight: 600;
    color: #0f172a;
}
.customer-meta {
    font-size: 11.5px;
    color: #64748b;
}

/* Badges */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.badge-rx-approved     { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.badge-rx-pending      { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.badge-rx-clarification{ background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.badge-rx-none         { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

.badge-order-placed      { background: #e0f2fe; color: #0284c7; }
.badge-order-processing  { background: #f3e8ff; color: #9333ea; }
.badge-order-shipped     { background: #ede9fe; color: #6366f1; }
.badge-order-delivered   { background: #ecfdf5; color: #059669; }
.badge-order-cancelled   { background: #fef2f2; color: #dc2626; }
.badge-order-returned    { background: #fff1f2; color: #e11d48; }

.badge-pay-paid   { background: #ecfdf5; color: #059669; font-weight: 600; }
.badge-pay-pending{ background: #fffbeb; color: #d97706; font-weight: 600; }
.badge-pay-failed { background: #fef2f2; color: #dc2626; font-weight: 600; }

.btn-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #475569;
    text-decoration: none;
    transition: all 0.15s;
}
.btn-action-icon:hover {
    background: #07484A;
    color: #ffffff;
    border-color: #07484A;
}
</style>

<div class="b2c-wrap">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0;">
                <i class="fa fa-shopping-bag" style="color: #07484A; margin-right: 8px;"></i>B2C Order Management
            </h2>
            <p style="font-size: 13px; color: #64748b; margin: 2px 0 0 0;">Manage online customer orders, prescription verification, optical lab handover, and shipments</p>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="kpi-grid">
        <a href="{{ route('admin.b2c-orders.index') }}" class="kpi-card">
            <div>
                <div class="kpi-title">Orders Today</div>
                <div class="kpi-value">{{ $kpis['orders_today'] }}</div>
            </div>
            <div class="kpi-icon primary"><i class="fa fa-shopping-cart"></i></div>
        </a>

        <div class="kpi-card">
            <div>
                <div class="kpi-title">Today's Revenue</div>
                <div class="kpi-value">₹{{ number_format($kpis['revenue_today'], 0) }}</div>
            </div>
            <div class="kpi-icon success"><i class="fa fa-inr"></i></div>
        </div>

        <a href="{{ route('admin.b2c-orders.index', ['rx_status' => 'pending_review']) }}" class="kpi-card">
            <div>
                <div class="kpi-title">Pending Rx Check</div>
                <div class="kpi-value" style="color: {{ $kpis['pending_rx'] > 0 ? '#d97706' : '#0f172a' }};">
                    {{ $kpis['pending_rx'] }}
                </div>
            </div>
            <div class="kpi-icon warning"><i class="fa fa-eye"></i></div>
        </a>

        <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'processing']) }}" class="kpi-card">
            <div>
                <div class="kpi-title">In Lab Cutting</div>
                <div class="kpi-value">{{ $kpis['in_lab'] }}</div>
            </div>
            <div class="kpi-icon purple"><i class="fa fa-cogs"></i></div>
        </a>

        <a href="{{ route('admin.b2c-orders.index', ['payment_status' => 'failed']) }}" class="kpi-card">
            <div>
                <div class="kpi-title">Payment Issues</div>
                <div class="kpi-value" style="color: {{ $kpis['payment_issues'] > 0 ? '#dc2626' : '#0f172a' }};">
                    {{ $kpis['payment_issues'] }}
                </div>
            </div>
            <div class="kpi-icon danger"><i class="fa fa-exclamation-triangle"></i></div>
        </a>

        <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'ready_to_ship']) }}" class="kpi-card">
            <div>
                <div class="kpi-title">Ready to Ship</div>
                <div class="kpi-value" style="color: {{ $kpis['ready_to_ship'] > 0 ? '#059669' : '#0f172a' }};">
                    {{ $kpis['ready_to_ship'] }}
                </div>
            </div>
            <div class="kpi-icon success"><i class="fa fa-truck"></i></div>
        </a>
    </div>

    <!-- Multi-Filter & Search Bar -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.b2c-orders.index') }}">
            <!-- Row 1: Search & Categorical Filters -->
            <div class="filter-grid-row1">
                <div>
                    <label class="form-label-custom"><i class="fa fa-search" style="margin-right: 4px; color: #07484A;"></i> Omni Search</label>
                    <input type="text" name="search" class="form-control-custom" placeholder="Search Order #, Name, Phone, Tracking..." value="{{ request('search') }}">
                </div>

                <div>
                    <label class="form-label-custom">Order Status</label>
                    <select name="order_status" class="form-control-custom">
                        <option value="all">All Statuses</option>
                        <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Placed</option>
                        <option value="confirmed" {{ request('order_status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ request('order_status') == 'processing' ? 'selected' : '' }}>In Lab / Processing</option>
                        <option value="ready_to_ship" {{ request('order_status') == 'ready_to_ship' ? 'selected' : '' }}>Ready to Ship</option>
                        <option value="shipped" {{ request('order_status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('order_status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="returned" {{ request('order_status') == 'returned' ? 'selected' : '' }}>Returned / Remake</option>
                    </select>
                </div>

                <div>
                    <label class="form-label-custom">Prescription (Rx)</label>
                    <select name="rx_status" class="form-control-custom">
                        <option value="all">All Rx States</option>
                        <option value="pending_review" {{ request('rx_status') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                        <option value="approved" {{ request('rx_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="clarification_needed" {{ request('rx_status') == 'clarification_needed' ? 'selected' : '' }}>Clarification Needed</option>
                        <option value="not_required" {{ request('rx_status') == 'not_required' ? 'selected' : '' }}>Not Required / Zero Power</option>
                    </select>
                </div>

                <div>
                    <label class="form-label-custom">Payment</label>
                    <select name="payment_status" class="form-control-custom">
                        <option value="all">All Payments</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cod_pending" {{ request('payment_status') == 'cod_pending' ? 'selected' : '' }}>COD Pending</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <div>
                    <label class="form-label-custom">Delivery Mode</label>
                    <select name="delivery_method" class="form-control-custom">
                        <option value="all">All Modes</option>
                        <option value="standard" {{ request('delivery_method') == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="express" {{ request('delivery_method') == 'express' ? 'selected' : '' }}>Express (24-48H)</option>
                        <option value="store_pickup" {{ request('delivery_method') == 'store_pickup' ? 'selected' : '' }}>Store Pickup</option>
                    </select>
                </div>
            </div>

            <!-- Row 2: Date Range Filter & Actions -->
            <div class="filter-grid-row2">
                <div class="date-inputs-group">
                    <div>
                        <label class="form-label-custom"><i class="fa fa-calendar" style="margin-right: 3px; color: #07484A;"></i> From Date</label>
                        <input type="date" name="date_from" id="filter_date_from" class="form-control-custom" style="width: 160px;" value="{{ request('date_from') }}">
                    </div>

                    <div>
                        <label class="form-label-custom"><i class="fa fa-calendar" style="margin-right: 3px; color: #07484A;"></i> To Date</label>
                        <input type="date" name="date_to" id="filter_date_to" class="form-control-custom" style="width: 160px;" value="{{ request('date_to') }}">
                    </div>

                    <div style="margin-bottom: 2px;">
                        <label class="form-label-custom" style="visibility: hidden;">Presets</label>
                        <div class="date-presets-group">
                            <button type="button" class="btn-preset" onclick="setDatePreset('today')">Today</button>
                            <button type="button" class="btn-preset" onclick="setDatePreset('yesterday')">Yesterday</button>
                            <button type="button" class="btn-preset" onclick="setDatePreset('7days')">Last 7 Days</button>
                            <button type="button" class="btn-preset" onclick="setDatePreset('month')">This Month</button>
                            @if(request('date_from') || request('date_to'))
                            <button type="button" class="btn-preset" style="color: #dc2626; border-color: #fca5a5;" onclick="clearDates()">Clear Dates ✕</button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2" style="margin-bottom: 2px;">
                    <button type="submit" class="btn" style="background: #07484A; color: #fff; border-radius: 8px; height: 38px; padding: 0 18px; font-weight: 600; font-size: 13px;">
                        <i class="fa fa-filter mr-1"></i> Apply Filter
                    </button>
                    <a href="{{ route('admin.b2c-orders.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px; height: 38px; padding: 0 14px; font-size: 13px; display: flex; align-items: center;">
                        Reset All
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Orders Table Card -->
    <div class="orders-card">
        <div class="table-responsive">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order # & Date</th>
                        <th>Customer</th>
                        <th>Products & Specs</th>
                        <th>Prescription</th>
                        <th>Order Total</th>
                        <th>Status</th>
                        <th>Delivery & Courier</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <!-- Order # & Date -->
                        <td>
                            <a href="{{ route('admin.b2c-orders.show', $order->id) }}" class="order-num">{{ $order->order_number }}</a>
                            <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">
                                {{ $order->created_at->format('d M Y, h:i A') }}
                            </div>
                            <span style="font-size: 10px; background: #f1f5f9; color: #475569; padding: 1px 5px; border-radius: 4px; font-weight: 600;">
                                {{ strtoupper($order->device_type ?? 'WEB') }}
                            </span>
                        </td>

                        <!-- Customer -->
                        <td>
                            <div class="customer-name">{{ $order->customer_name }}</div>
                            <div class="customer-meta"><i class="fa fa-phone" style="margin-right: 3px;"></i> {{ $order->customer_phone ?? 'N/A' }}</div>
                        </td>

                        <!-- Products & Specs -->
                        <td>
                            <div style="font-weight: 500; color: #1e293b;">
                                {{ $order->items->first()->product_name ?? 'Eyewear Product' }}
                                @if($order->items->count() > 1)
                                <span style="font-size: 11px; color: #0284c7; font-weight: 600;">+{{ $order->items->count() - 1 }} more</span>
                                @endif
                            </div>
                            <div style="font-size: 11.5px; color: #64748b;">
                                @if($order->items->first() && $order->items->first()->lensPackage)
                                <i class="fa fa-eye" style="margin-right: 3px;"></i> {{ $order->items->first()->lensPackage->name }}
                                @else
                                <i class="fa fa-eye" style="margin-right: 3px;"></i> Single Vision / Standard
                                @endif
                            </div>
                        </td>

                        <!-- Prescription Badge -->
                        <td>
                            @if($order->rx_verification_status === 'approved')
                                <span class="badge-status badge-rx-approved"><i class="fa fa-check-circle"></i> Verified</span>
                            @elseif($order->rx_verification_status === 'pending_review')
                                <span class="badge-status badge-rx-pending"><i class="fa fa-clock-o"></i> Review Req</span>
                            @elseif($order->rx_verification_status === 'clarification_needed')
                                <span class="badge-status badge-rx-clarification"><i class="fa fa-phone"></i> Call Cust</span>
                            @else
                                <span class="badge-status badge-rx-none">Zero / No Rx</span>
                            @endif
                        </td>

                        <!-- Order Total & Payment -->
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">₹{{ number_format($order->grand_total, 2) }}</div>
                            <span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-pay-paid' : ($order->payment_status == 'failed' ? 'badge-pay-failed' : 'badge-pay-pending') }}">
                                {{ strtoupper($order->payment_status) }}
                            </span>
                        </td>

                        <!-- Order Status -->
                        <td>
                            @php
                                $statusClass = 'badge-order-placed';
                                if($order->order_status == 'processing') $statusClass = 'badge-order-processing';
                                elseif($order->order_status == 'shipped') $statusClass = 'badge-order-shipped';
                                elseif($order->order_status == 'delivered') $statusClass = 'badge-order-delivered';
                                elseif($order->order_status == 'cancelled') $statusClass = 'badge-order-cancelled';
                                elseif($order->order_status == 'returned') $statusClass = 'badge-order-returned';
                            @endphp
                            <span class="badge-status {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                            </span>
                        </td>

                        <!-- Delivery & Courier -->
                        <td>
                            <div style="font-size: 12px; font-weight: 500; color: #334155;">
                                {{ ucfirst($order->delivery_method ?? 'Standard') }}
                            </div>
                            @if(!empty($order->tracking_number))
                            <div style="font-size: 11px; color: #0284c7; font-family: 'JetBrains Mono', monospace;">
                                {{ $order->courier_partner ?? 'Courier' }}: {{ $order->tracking_number }}
                            </div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td style="text-align: right;">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.b2c-orders.show', $order->id) }}" class="btn-action-icon" title="View Order 360°">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.b2c-orders.invoice', $order->id) }}" target="_blank" class="btn-action-icon" title="Print Tax Invoice">
                                    <i class="fa fa-file-text-o"></i>
                                </a>
                                <a href="{{ route('admin.b2c-orders.lab-work-order', $order->id) }}" target="_blank" class="btn-action-icon" title="Print Optical Lab Job Sheet">
                                    <i class="fa fa-wrench"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 48px 24px; color: #64748b;">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; color: #94a3b8; font-size: 20px;">
                                <i class="fa fa-inbox"></i>
                            </div>
                            <div style="font-weight: 600; font-size: 14px; color: #334155; margin-bottom: 4px;">No B2C orders found</div>
                            <div style="font-size: 12px; color: #94a3b8;">Try adjusting your search criteria or filter options.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 12px; color: #64748b;">
                Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
            </div>
            <div>
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
function setDatePreset(type) {
    const fromEl = document.getElementById('filter_date_from');
    const toEl = document.getElementById('filter_date_to');
    const now = new Date();
    
    function fmt(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    if (type === 'today') {
        const todayStr = fmt(now);
        fromEl.value = todayStr;
        toEl.value = todayStr;
    } else if (type === 'yesterday') {
        const y = new Date(now);
        y.setDate(y.getDate() - 1);
        const yStr = fmt(y);
        fromEl.value = yStr;
        toEl.value = yStr;
    } else if (type === '7days') {
        const past = new Date(now);
        past.setDate(past.getDate() - 6);
        fromEl.value = fmt(past);
        toEl.value = fmt(now);
    } else if (type === 'month') {
        const start = new Date(now.getFullYear(), now.getMonth(), 1);
        fromEl.value = fmt(start);
        toEl.value = fmt(now);
    }
}

function clearDates() {
    document.getElementById('filter_date_from').value = '';
    document.getElementById('filter_date_to').value = '';
}
</script>
@endsection

@endsection
