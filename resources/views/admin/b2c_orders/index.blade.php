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
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}
.kpi-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
    text-decoration: none !important;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
}
.kpi-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.kpi-value {
    font-size: 24px;
    font-weight: 800;
    color: #0f172a;
    margin-top: 4px;
    line-height: 1.2;
}
.kpi-sub {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 2px;
}
.kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.kpi-icon.primary { background: #e0f2fe; color: #0284c7; }
.kpi-icon.success { background: #ecfdf5; color: #059669; }
.kpi-icon.warning { background: #fffbeb; color: #d97706; }
.kpi-icon.teal    { background: #ccfbf1; color: #0d9488; }
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

.status-select-badge {
    display: inline-block !important;
    padding: 4px 28px 4px 12px !important;
    border-radius: 20px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.4px !important;
    border: 1px solid transparent !important;
    cursor: pointer !important;
    outline: none !important;
    box-shadow: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background-repeat: no-repeat !important;
    background-position: right 8px center !important;
    background-size: 10px 10px !important;
    line-height: 1.5 !important;
    height: 28px !important;
}
.status-select-badge option {
    background-color: #ffffff !important;
    color: #0f172a !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    padding: 6px 12px !important;
}
.status-select-pending      { background-color: #e0f2fe !important; color: #0284c7 !important; border-color: #bae6fd !important; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%230284c7'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E") !important; }
.status-select-confirmed    { background-color: #e0e7ff !important; color: #4338ca !important; border-color: #c7d2fe !important; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%234338ca'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E") !important; }
.status-select-processing   { background-color: #f3e8ff !important; color: #9333ea !important; border-color: #e9d5ff !important; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%239333ea'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E") !important; }
.status-select-ready_to_ship{ background-color: #dcfce7 !important; color: #15803d !important; border-color: #bbf7d0 !important; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%2315803d'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E") !important; }
.status-select-shipped     { background-color: #ede9fe !important; color: #6366f1 !important; border-color: #ddd6fe !important; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%236366f1'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E") !important; }
.status-select-delivered   { background-color: #ecfdf5 !important; color: #059669 !important; border-color: #a7f3d0 !important; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23059669'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E") !important; }
.status-select-cancelled   { background-color: #fef2f2 !important; color: #dc2626 !important; border-color: #fecaca !important; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23dc2626'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E") !important; }
.status-select-returned    { background-color: #fff1f2 !important; color: #e11d48 !important; border-color: #fecdd3 !important; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23e11d48'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E") !important; }

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

    <!-- KPI Summary Grid (6 Unified Core Metrics) -->
    <div class="kpi-grid">
        {{-- Card 1: Orders Today --}}
        <a href="{{ route('admin.b2c-orders.index') }}" class="kpi-card">
            <div>
                <div class="kpi-title">Orders Today</div>
                <div class="kpi-value">{{ $kpis['orders_today'] ?? 0 }}</div>
                <div class="kpi-sub">Month: {{ $kpis['orders_this_month'] ?? 0 }} total</div>
            </div>
            <div class="kpi-icon primary"><i class="fa fa-shopping-cart"></i></div>
        </a>

        {{-- Card 2: Revenue Today --}}
        <div class="kpi-card">
            <div>
                <div class="kpi-title">Revenue Today</div>
                <div class="kpi-value">₹{{ number_format($kpis['revenue_today'] ?? 0, 0) }}</div>
                <div class="kpi-sub">Month: ₹{{ number_format($kpis['revenue_this_month'] ?? 0, 0) }}</div>
            </div>
            <div class="kpi-icon success"><i class="fa fa-inr"></i></div>
        </div>

        {{-- Card 3: Pending Orders --}}
        <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'pending']) }}" class="kpi-card">
            <div>
                <div class="kpi-title">Pending Orders</div>
                <div class="kpi-value" style="color: {{ ($kpis['pending_orders'] ?? 0) > 0 ? '#d97706' : '#0f172a' }};">
                    {{ $kpis['pending_orders'] ?? 0 }}
                </div>
                <div class="kpi-sub">Awaiting Processing</div>
            </div>
            <div class="kpi-icon warning"><i class="fa fa-clock-o"></i></div>
        </a>

        {{-- Card 4: Ready To Ship --}}
        <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'ready_to_ship']) }}" class="kpi-card">
            <div>
                <div class="kpi-title">Ready To Ship</div>
                <div class="kpi-value" style="color: {{ ($kpis['ready_to_ship'] ?? 0) > 0 ? '#059669' : '#0f172a' }};">
                    {{ $kpis['ready_to_ship'] ?? 0 }}
                </div>
                <div class="kpi-sub">Awaiting Courier AWB</div>
            </div>
            <div class="kpi-icon teal"><i class="fa fa-truck"></i></div>
        </a>

        {{-- Card 5: Cancelled Orders --}}
        <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'cancelled']) }}" class="kpi-card">
            <div>
                <div class="kpi-title">Cancelled Orders</div>
                <div class="kpi-value" style="color: {{ ($kpis['cancelled_orders'] ?? 0) > 0 ? '#dc2626' : '#0f172a' }};">
                    {{ $kpis['cancelled_orders'] ?? 0 }}
                </div>
                <div class="kpi-sub">Month: {{ $kpis['cancelled_this_month'] ?? 0 }} total</div>
            </div>
            <div class="kpi-icon danger"><i class="fa fa-times-circle"></i></div>
        </a>

        {{-- Card 6: Returns / RTO --}}
        <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'returned']) }}" class="kpi-card">
            <div>
                <div class="kpi-title">Returns / RTO</div>
                <div class="kpi-value" style="color: {{ ($kpis['returns_count'] ?? 0) > 0 ? '#9333ea' : '#0f172a' }};">
                    {{ $kpis['returns_count'] ?? 0 }}
                </div>
                <div class="kpi-sub">Reverse Logistics</div>
            </div>
            <div class="kpi-icon purple"><i class="fa fa-undo"></i></div>
        </a>
    </div>

    <!-- Multi-Filter & Search Bar -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.b2c-orders.index') }}">
            <!-- Row 1: Search & Categorical Filters -->
            <div class="filter-grid-row1" style="grid-template-columns: 2fr 1fr 1fr 1fr;">
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

                {{-- Prescription (Rx) Filter Dropdown (Temporarily Hidden) --}}
                {{--
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
                --}}

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
                            @if(!empty($order->membership_type))
                                <div style="margin-top: 3px;">
                                    <span style="font-size: 10.5px; font-weight: 700; background: linear-gradient(135deg, #07484A, #00B9B9); color: #ffffff; padding: 2px 8px; border-radius: 12px; display: inline-flex; align-items: center; gap: 3px;">
                                        👑 {{ $order->membership_type }}
                                    </span>
                                </div>
                            @endif
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
                            @php $firstItem = $order->items->first(); @endphp
                            @if($order->rx_verification_status === 'approved')
                                <button type="button" class="btn p-0 border-0 bg-transparent text-start" onclick="openRxWorkbenchModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ $order->rx_verification_status }}', '{{ addslashes($order->optometrist_notes ?? '') }}', '{{ addslashes(json_encode($firstItem)) }}')" title="Click to view photo & edit prescription power">
                                    <span class="badge-status badge-rx-approved" style="cursor: pointer;"><i class="fa fa-check-circle"></i> Verified <i class="fa fa-pencil" style="font-size: 10px; margin-left: 2px;"></i></span>
                                </button>
                            @elseif($order->rx_verification_status === 'pending_review')
                                <button type="button" class="btn p-0 border-0 bg-transparent text-start" onclick="openRxWorkbenchModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ $order->rx_verification_status }}', '{{ addslashes($order->optometrist_notes ?? '') }}', '{{ addslashes(json_encode($firstItem)) }}')" title="Click to view photo & edit prescription power">
                                    <span class="badge-status badge-rx-pending" style="cursor: pointer;"><i class="fa fa-clock-o"></i> Review Req <i class="fa fa-pencil" style="font-size: 10px; margin-left: 2px;"></i></span>
                                </button>
                            @elseif($order->rx_verification_status === 'clarification_needed')
                                <button type="button" class="btn p-0 border-0 bg-transparent text-start" onclick="openRxWorkbenchModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ $order->rx_verification_status }}', '{{ addslashes($order->optometrist_notes ?? '') }}', '{{ addslashes(json_encode($firstItem)) }}')" title="Click to view photo & edit prescription power">
                                    <span class="badge-status badge-rx-clarification" style="cursor: pointer;"><i class="fa fa-phone"></i> Call Cust <i class="fa fa-pencil" style="font-size: 10px; margin-left: 2px;"></i></span>
                                </button>
                            @else
                                <button type="button" class="btn p-0 border-0 bg-transparent text-start" onclick="openRxWorkbenchModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ $order->rx_verification_status }}', '{{ addslashes($order->optometrist_notes ?? '') }}', '{{ addslashes(json_encode($firstItem)) }}')" title="Click to view photo & edit prescription power">
                                    <span class="badge-status badge-rx-none" style="cursor: pointer;">Zero / No Rx <i class="fa fa-pencil" style="font-size: 10px; margin-left: 2px;"></i></span>
                                </button>
                            @endif
                        </td>

                        <!-- Order Total & Payment -->
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">₹{{ number_format($order->grand_total, 2) }}</div>
                            <span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-pay-paid' : ($order->payment_status == 'failed' ? 'badge-pay-failed' : 'badge-pay-pending') }}">
                                {{ strtoupper($order->payment_status) }}
                            </span>
                        </td>

                        <!-- Order Status (Interactive Dropdown) -->
                        <td>
                            <form method="POST" action="{{ route('admin.b2c-orders.update-status', $order->id) }}" style="margin: 0;">
                                @csrf
                                <select name="order_status" onchange="this.form.submit()" class="status-select-badge status-select-{{ $order->order_status }}" title="Click to change order status">
                                    <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Placed</option>
                                    <option value="confirmed" {{ $order->order_status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>In Lab</option>
                                    <option value="ready_to_ship" {{ $order->order_status == 'ready_to_ship' ? 'selected' : '' }}>Ready to Ship</option>
                                    <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="returned" {{ $order->order_status == 'returned' ? 'selected' : '' }}>Returned</option>
                                </select>
                            </form>
                        </td>

                        <!-- Actions -->
                        <td style="text-align: right;">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.b2c-orders.show', $order->id) }}" class="btn-action-icon" title="View Order 360° Profile">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.b2c-orders.invoice', $order->id) }}" target="_blank" class="btn-action-icon" title="Print Customer Tax Invoice">
                                    <i class="fa fa-file-text-o"></i>
                                </a>
                                <a href="{{ route('admin.b2c-orders.lab-work-order', $order->id) }}" target="_blank" class="btn-action-icon" title="Print Optical Lab Job Sheet">
                                    <i class="fa fa-glasses" style="color: #07484A;"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px 24px; color: #64748b;">
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

<!-- Side-by-Side Prescription Workbench Modal -->
<div class="modal fade" id="rxWorkbenchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: #07484A; color: #ffffff; padding: 14px 20px;">
                <h5 class="modal-title" style="font-weight: 700; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-eye"></i> Eye Prescription Workbench — <span id="rxModalOrderNum">WEB69897</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rxWorkbenchForm" method="POST" action="">
                @csrf
                <div class="modal-body" style="padding: 20px; background: #f8fafc;">
                    <div class="row g-3">
                        <!-- LEFT COLUMN: Uploaded Photo Viewer (50%) -->
                        <div class="col-md-6" style="border-right: 1px solid #e2e8f0; padding-right: 20px;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong style="font-size: 13px; color: #0f172a;"><i class="fa fa-picture-o" style="color: #07484A;"></i> Customer Uploaded Doctor Photo</strong>
                                <a id="rxDownloadLink" href="#" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size: 11px; padding: 2px 8px; border-radius: 4px;">
                                    <i class="fa fa-download"></i> Download Image
                                </a>
                            </div>
                            <div style="background: #0f172a; border-radius: 8px; min-height: 360px; max-height: 420px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;" id="rxImageContainer">
                                <img id="rxModalImage" src="" alt="Doctor Prescription Photo" style="max-width: 100%; max-height: 400px; object-fit: contain; transition: transform 0.2s ease;">
                                <div id="rxNoImageFallback" style="display: none; color: #94a3b8; text-align: center; padding: 40px 20px;">
                                    <i class="fa fa-file-text-o" style="font-size: 48px; margin-bottom: 12px; display: block; color: #64748b;"></i>
                                    <strong style="font-size: 14px; color: #e2e8f0; display: block;">No Prescription Photo Uploaded</strong>
                                    <span style="font-size: 12px;">Customer selected manual power entry or phone verification.</span>
                                </div>
                            </div>
                            <!-- Image Zoom & Rotate Controls -->
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-light border" onclick="zoomRxImage(1.2)" title="Zoom In"><i class="fa fa-search-plus"></i> Zoom In</button>
                                <button type="button" class="btn btn-sm btn-light border" onclick="zoomRxImage(0.8)" title="Zoom Out"><i class="fa fa-search-minus"></i> Zoom Out</button>
                                <button type="button" class="btn btn-sm btn-light border" onclick="rotateRxImage()" title="Rotate 90°"><i class="fa fa-repeat"></i> Rotate</button>
                                <button type="button" class="btn btn-sm btn-light border" onclick="resetRxImage()" title="Reset"><i class="fa fa-refresh"></i> Reset</button>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Power Matrix Input Form (50%) -->
                        <div class="col-md-6" style="padding-left: 20px;">
                            <strong style="font-size: 13px; color: #0f172a; display: block; margin-bottom: 10px;">
                                <i class="fa fa-pencil-square-o" style="color: #07484A;"></i> Enter / Edit Eye Power Matrix
                            </strong>
                            <input type="hidden" name="item_id" id="rxModalItemId" value="">
                            
                            <!-- Matrix Table -->
                            <div class="table-responsive mb-3" style="border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; background: #ffffff;">
                                <table class="table table-bordered mb-0" style="font-size: 12px; text-align: center; vertical-align: middle;">
                                    <thead style="background: #f1f5f9; color: #334155; font-size: 11px; font-weight: 700;">
                                        <tr>
                                            <th>EYE</th>
                                            <th>SPH</th>
                                            <th>CYL</th>
                                            <th>AXIS (°)</th>
                                            <th>ADD</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="font-weight: 700; background: #f8fafc; color: #0284c7;">OD (RIGHT)</td>
                                            <td><input type="text" name="items_power[GL_EYE_RS_D]" id="rx_GL_EYE_RS_D" class="form-control form-control-sm text-center" placeholder="+0.00"></td>
                                            <td><input type="text" name="items_power[GL_EYE_RC_D]" id="rx_GL_EYE_RC_D" class="form-control form-control-sm text-center" placeholder="-0.00"></td>
                                            <td><input type="text" name="items_power[GL_EYE_RA_D]" id="rx_GL_EYE_RA_D" class="form-control form-control-sm text-center" placeholder="180"></td>
                                            <td><input type="text" name="items_power[GL_EYE_RADD]" id="rx_GL_EYE_RADD" class="form-control form-control-sm text-center" placeholder="+2.00"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: 700; background: #f8fafc; color: #0284c7;">OS (LEFT)</td>
                                            <td><input type="text" name="items_power[GL_EYE_LS_D]" id="rx_GL_EYE_LS_D" class="form-control form-control-sm text-center" placeholder="+0.00"></td>
                                            <td><input type="text" name="items_power[GL_EYE_LC_D]" id="rx_GL_EYE_LC_D" class="form-control form-control-sm text-center" placeholder="-0.00"></td>
                                            <td><input type="text" name="items_power[GL_EYE_LA_D]" id="rx_GL_EYE_LA_D" class="form-control form-control-sm text-center" placeholder="180"></td>
                                            <td><input type="text" name="items_power[GL_EYE_LADD]" id="rx_GL_EYE_LADD" class="form-control form-control-sm text-center" placeholder="+2.00"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- PD Input -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label style="font-size: 11px; font-weight: 700; color: #475569;">TOTAL BINOCULAR PD (mm)</label>
                                    <input type="text" name="items_power[GL_EYE_totalPD]" id="rx_GL_EYE_totalPD" class="form-control form-control-sm" placeholder="e.g. 63">
                                </div>
                                <div class="col-6">
                                    <label style="font-size: 11px; font-weight: 700; color: #475569;">VERIFICATION STATUS *</label>
                                    <select name="rx_status" id="rxModalStatus" class="form-control form-control-sm" required style="font-weight: 600;">
                                        <option value="approved">✓ Approve (Ready for Lab Job Sheet)</option>
                                        <option value="clarification_needed">🔴 Flag Clarification (Call Customer)</option>
                                        <option value="rejected">❌ Reject Prescription</option>
                                        <option value="pending_review">⏳ Pending Review</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="mb-3">
                                <label style="font-size: 11px; font-weight: 700; color: #475569;">OPTOMETRIST / STAFF REMARKS</label>
                                <textarea name="optometrist_notes" id="rxModalNotes" class="form-control form-control-sm" rows="3" placeholder="Enter notes or confirmation with customer over phone..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #ffffff; border-top: 1px solid #e2e8f0; padding: 12px 20px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background: #07484A; color: #ffffff; font-weight: 700; padding: 6px 20px; border-radius: 6px;">
                        <i class="fa fa-check"></i> Save Power & Sync Lab Job Sheet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
let currentScale = 1;
let currentRotation = 0;

function openRxWorkbenchModal(orderId, orderNum, rxStatus, notes, itemJsonStr) {
    const form = document.getElementById('rxWorkbenchForm');
    form.action = "{{ url('admin/b2c-orders') }}/" + orderId + "/verify-prescription";
    document.getElementById('rxModalOrderNum').innerText = orderNum;
    document.getElementById('rxModalStatus').value = rxStatus || 'approved';
    document.getElementById('rxModalNotes').value = notes || '';
    
    resetRxImage();
    
    try {
        const item = typeof itemJsonStr === 'object' ? itemJsonStr : JSON.parse(itemJsonStr);
        document.getElementById('rxModalItemId').value = item.id || '';
        
        const imgEl = document.getElementById('rxModalImage');
        const fallbackEl = document.getElementById('rxNoImageFallback');
        const downloadEl = document.getElementById('rxDownloadLink');
        
        if (item.prescription_file_url && item.prescription_file_url.trim() !== '') {
            const imgUrl = item.prescription_file_url.startsWith('http') ? item.prescription_file_url : ("{{ asset('') }}" + item.prescription_file_url.replace(/^\//, ''));
            imgEl.src = imgUrl;
            imgEl.style.display = 'block';
            fallbackEl.style.display = 'none';
            downloadEl.href = imgUrl;
            downloadEl.style.display = 'inline-block';
        } else {
            imgEl.style.display = 'none';
            fallbackEl.style.display = 'block';
            downloadEl.style.display = 'none';
        }
        
        document.getElementById('rx_GL_EYE_RS_D').value = item.GL_EYE_RS_D !== null ? item.GL_EYE_RS_D : '';
        document.getElementById('rx_GL_EYE_RC_D').value = item.GL_EYE_RC_D !== null ? item.GL_EYE_RC_D : '';
        document.getElementById('rx_GL_EYE_RA_D').value = item.GL_EYE_RA_D || '';
        document.getElementById('rx_GL_EYE_RADD').value = item.GL_EYE_RADD !== null ? item.GL_EYE_RADD : '';
        
        document.getElementById('rx_GL_EYE_LS_D').value = item.GL_EYE_LS_D !== null ? item.GL_EYE_LS_D : '';
        document.getElementById('rx_GL_EYE_LC_D').value = item.GL_EYE_LC_D !== null ? item.GL_EYE_LC_D : '';
        document.getElementById('rx_GL_EYE_LA_D').value = item.GL_EYE_LA_D || '';
        document.getElementById('rx_GL_EYE_LADD').value = item.GL_EYE_LADD !== null ? item.GL_EYE_LADD : '';
        
        document.getElementById('rx_GL_EYE_totalPD').value = item.GL_EYE_totalPD || '';
    } catch(e) {
        console.error('Error parsing item rx data', e);
    }

    const modal = new bootstrap.Modal(document.getElementById('rxWorkbenchModal'));
    modal.show();
}

function zoomRxImage(factor) {
    currentScale *= factor;
    applyRxTransform();
}
function rotateRxImage() {
    currentRotation = (currentRotation + 90) % 360;
    applyRxTransform();
}
function resetRxImage() {
    currentScale = 1;
    currentRotation = 0;
    applyRxTransform();
}
function applyRxTransform() {
    const imgEl = document.getElementById('rxModalImage');
    if (imgEl) {
        imgEl.style.transform = `scale(${currentScale}) rotate(${currentRotation}deg)`;
    }
}

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
