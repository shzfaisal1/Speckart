@extends('layouts.master')
@php
     $usr = Auth::guard()->user();
 @endphp
@section('content')
<style>
    .domestic-orders-date {
        padding-top: 0px;
    }
    
    .section-title{
        font-weight:600;
        border-left:4px solid #0d6efd;
        padding-left:10px;
        color:#2c3e50;
    }
    
    .table thead tr th {
        font-size: 12px;
        font-weight: 500 !important;
        color: #000;
    }
    
    .dashboard-nav{
        background:#f8f9fa;
        padding:8px;
        border-radius:10px;
    }
    
    .dashboard-nav .nav-item{
        margin-right:8px;
    }
    
    .dashboard-nav .nav-link{
        color:#2c3e50;
        font-weight:600;
        border-radius:8px;
        padding:10px 18px;
        background:#ffffff;
        border:1px solid #e3e6f0;
        transition:all .3s ease;
        font-size: 12px;
    }
    
    .dashboard-nav .nav-link i{
        margin-right:6px;
    }
    
    .dashboard-nav .nav-link:hover{
        background:#eef4ff;
        color:#00484a;
        transform:translateY(-2px);
    }
    
    .dashboard-nav .nav-link.active{
        background:#00484a;
        color:#fff;
        box-shadow:0 4px 12px rgba(0,0,0,0.1);
        font-size: 12px;
    }
    
    .nav-link i {
        color: #000;
    }
    
    .nav-link.active i {
        color: #fff;
    }
    
        .col-md-3
{
    margin-bottom: 10px;
}
.staff-performance-dashboard-card{
    border: 1px solid #d9dde7;
    height: 365px;
    overflow: auto;
}
.store-performance-dashboard{
    border: 1px solid #d9dde7;
    width: 100%;
    height: 250px;
    overflow: auto;
}
.staff-performance-dashboard-card .table-responsive,
.store-performance-dashboard .table-responsive{
    height: 100%;
}
.walk-in-entry-history{
    /*border: 1px solid #d9dde7;*/
    width: 100%;
    height: 500px;
    overflow: auto;
}
.walk-in-entry-history .table-responsive{height: 100%;}
</style>
    
    
<div id="ajaxLoader" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999; text-align:center;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading, please wait...</p>
    </div>
</div>
<div class="dashboard-tabs mb-4">

    <ul class="nav nav-pills dashboard-nav">
        <li class="nav-item">
            <a class="nav-link active"  href="{{route('index')}}">
                <i class="fa fa-sign-in"></i>
                Home
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('admin.walkin-dashboard')}}">
                <i class="fa fa-sign-in"></i>
                Walk-In Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.audit-dashboard')}}">
                <i class="fa fa-search"></i>
                Mystery Audit
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.nps-dashboard')}}">
                <i class="fa fa-smile-o"></i>
                NPS Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.sale-dashboard')}}">
                <i class="fa fa-line-chart"></i>
                Sales Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.account-dashboard')}}">
                <i class="fa fa-money"></i>
                Account Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"  href="{{route('admin.counting-dashboard')}}">
                <i class="fa fa-google-wallet"></i>
                Product Counting Dashboard
            </a>
        </li>

    </ul>

</div>

<!-- ══════════════════════════════════════════════════════════════════════════════
     B2C ORDER OPERATIONS COMMAND DASHBOARD
══════════════════════════════════════════════════════════════════════════════ -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap');

.home-dash-wrap {
    font-family: 'Inter', sans-serif;
    color: #1e293b;
}

/* KPI Summary Cards */
.dash-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.dash-kpi-card {
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
.dash-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
}
.dash-kpi-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.dash-kpi-val {
    font-size: 24px;
    font-weight: 800;
    color: #0f172a;
    margin-top: 4px;
    line-height: 1.2;
}
.dash-kpi-sub {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 2px;
}
.dash-kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.dash-kpi-icon.primary { background: #e0f2fe; color: #0284c7; }
.dash-kpi-icon.success { background: #ecfdf5; color: #059669; }
.dash-kpi-icon.warning { background: #fffbeb; color: #d97706; }
.dash-kpi-icon.purple  { background: #f3e8ff; color: #9333ea; }
.dash-kpi-icon.teal    { background: #ccfbf1; color: #0d9488; }
.dash-kpi-icon.danger  { background: #fef2f2; color: #dc2626; }

/* Order Pipeline Funnel Bar */
.pipeline-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 16px 20px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.pipeline-title {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.pipeline-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 8px;
}
.pipeline-step {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 12px;
    text-decoration: none !important;
    transition: all 0.15s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}
.pipeline-step:hover {
    background: #07484A;
    border-color: #07484A;
    transform: translateY(-2px);
}
.pipeline-step:hover .p-count,
.pipeline-step:hover .p-label {
    color: #ffffff !important;
}
.pipeline-step .p-count {
    font-family: 'JetBrains Mono', monospace;
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
}
.pipeline-step .p-label {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    margin-top: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* Recent Orders Command Center */
.command-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.command-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
}
.command-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.command-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
}
.command-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 16px;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}
.command-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.command-table tr:hover {
    background-color: #f8fafc;
}
.order-code {
    font-family: 'JetBrains Mono', monospace;
    font-weight: 700;
    color: #07484A;
    text-decoration: none;
}
.order-code:hover {
    text-decoration: underline;
}

/* Badges */
.badge-rx {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10.5px;
    font-weight: 600;
    text-transform: uppercase;
}
.badge-rx-ok   { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.badge-rx-req  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.badge-rx-none { background: #f1f5f9; color: #64748b; }

.badge-pay-ok  { background: #ecfdf5; color: #059669; font-weight: 600; }
.badge-pay-cod { background: #fffbeb; color: #d97706; font-weight: 600; }
.badge-pay-bad { background: #fef2f2; color: #dc2626; font-weight: 600; }

.btn-quick-act {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #475569;
    text-decoration: none;
    transition: all 0.15s;
    font-size: 12px;
}
.btn-quick-act:hover {
    background: #07484A;
    color: #ffffff;
    border-color: #07484A;
}

/* Bottom Grid Analytics */
.dash-bottom-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}
@media (max-width: 991px) {
    .dash-bottom-grid {
        grid-template-columns: 1fr;
    }
}
.trend-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 16px 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
</style>

<div class="home-dash-wrap">

    <!-- 1. Executive Operational KPI Summary Cards -->
    <div class="dash-kpi-grid">
        <a href="{{ route('admin.b2c-orders.index') }}" class="dash-kpi-card">
            <div>
                <div class="dash-kpi-title">Today's Orders</div>
                <div class="dash-kpi-val">{{ $kpis['orders_today'] ?? 0 }}</div>
                <div class="dash-kpi-sub">Month: {{ $kpis['orders_this_month'] ?? 0 }} total</div>
            </div>
            <div class="dash-kpi-icon primary"><i class="fa fa-shopping-cart"></i></div>
        </a>

        <div class="dash-kpi-card">
            <div>
                <div class="dash-kpi-title">Today's Revenue</div>
                <div class="dash-kpi-val">₹{{ number_format($kpis['revenue_today'] ?? 0, 0) }}</div>
                <div class="dash-kpi-sub">Month: ₹{{ number_format($kpis['revenue_this_month'] ?? 0, 0) }}</div>
            </div>
            <div class="dash-kpi-icon success"><i class="fa fa-inr"></i></div>
        </div>

        <a href="{{ route('admin.b2c-orders.index', ['rx_status' => 'pending_review']) }}" class="dash-kpi-card">
            <div>
                <div class="dash-kpi-title">Pending Rx Check</div>
                <div class="dash-kpi-val" style="color: {{ ($kpis['pending_rx'] ?? 0) > 0 ? '#d97706' : '#0f172a' }};">
                    {{ $kpis['pending_rx'] ?? 0 }}
                </div>
                <div class="dash-kpi-sub">Optometrist Review</div>
            </div>
            <div class="dash-kpi-icon warning"><i class="fa fa-eye"></i></div>
        </a>

        <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'processing']) }}" class="dash-kpi-card">
            <div>
                <div class="dash-kpi-title">In Optical Lab</div>
                <div class="dash-kpi-val">{{ $kpis['in_lab'] ?? 0 }}</div>
                <div class="dash-kpi-sub">Lens Edging & Fitting</div>
            </div>
            <div class="dash-kpi-icon purple"><i class="fa fa-cogs"></i></div>
        </a>

        <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'ready_to_ship']) }}" class="dash-kpi-card">
            <div>
                <div class="dash-kpi-title">Ready to Ship</div>
                <div class="dash-kpi-val" style="color: {{ ($kpis['ready_to_ship'] ?? 0) > 0 ? '#059669' : '#0f172a' }};">
                    {{ $kpis['ready_to_ship'] ?? 0 }}
                </div>
                <div class="dash-kpi-sub">Awaiting Courier AWB</div>
            </div>
            <div class="dash-kpi-icon teal"><i class="fa fa-truck"></i></div>
        </a>

        <a href="{{ route('admin.b2c-orders.index', ['payment_status' => 'cod_pending']) }}" class="dash-kpi-card">
            <div>
                <div class="dash-kpi-title">Payment / COD</div>
                <div class="dash-kpi-val" style="color: {{ ($kpis['payment_issues'] ?? 0) > 0 ? '#dc2626' : '#0f172a' }};">
                    {{ $kpis['payment_issues'] ?? 0 }}
                </div>
                <div class="dash-kpi-sub">Pending Settlement</div>
            </div>
            <div class="dash-kpi-icon danger"><i class="fa fa-exclamation-triangle"></i></div>
        </a>
    </div>

    <!-- 2. Order Lifecycle Funnel / Pipeline Flow -->
    <div class="pipeline-card">
        <div class="pipeline-title">
            <i class="fa fa-sliders" style="color: #07484A;"></i> Live Order Pipeline & Fulfilment Workflow
        </div>
        <div class="pipeline-bar">
            <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'pending']) }}" class="pipeline-step">
                <div class="p-count">{{ $pipeline['pending'] ?? 0 }}</div>
                <div class="p-label">1. Placed</div>
            </a>
            <a href="{{ route('admin.b2c-orders.index', ['rx_status' => 'pending_review']) }}" class="pipeline-step">
                <div class="p-count" style="color: {{ ($pipeline['rx_review'] ?? 0) > 0 ? '#d97706' : '#0f172a' }};">{{ $pipeline['rx_review'] ?? 0 }}</div>
                <div class="p-label">2. Rx Review</div>
            </a>
            <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'processing']) }}" class="pipeline-step">
                <div class="p-count">{{ $pipeline['in_lab'] ?? 0 }}</div>
                <div class="p-label">3. Optical Lab</div>
            </a>
            <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'ready_to_ship']) }}" class="pipeline-step">
                <div class="p-count" style="color: {{ ($pipeline['ready_to_ship'] ?? 0) > 0 ? '#059669' : '#0f172a' }};">{{ $pipeline['ready_to_ship'] ?? 0 }}</div>
                <div class="p-label">4. Ready to Ship</div>
            </a>
            <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'shipped']) }}" class="pipeline-step">
                <div class="p-count">{{ $pipeline['shipped'] ?? 0 }}</div>
                <div class="p-label">5. In Transit</div>
            </a>
            <a href="{{ route('admin.b2c-orders.index', ['order_status' => 'delivered']) }}" class="pipeline-step">
                <div class="p-count">{{ $pipeline['delivered'] ?? 0 }}</div>
                <div class="p-label">6. Delivered</div>
            </a>
        </div>
    </div>

    <!-- 3. Live Orders Command Center -->
    <div class="command-card">
        <div class="command-header">
            <h3 class="command-title">
                <i class="fa fa-bolt" style="color: #07484A;"></i> Live Orders Command Center
            </h3>
            <a href="{{ route('admin.b2c-orders.index') }}" class="btn btn-sm" style="background: #07484A; color: #fff; border-radius: 6px; font-weight: 600; font-size: 12px; padding: 6px 14px;">
                View All B2C Orders →
            </a>
        </div>
        <div class="table-responsive">
            <table class="command-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Item & Lens Specs</th>
                        <th>Prescription</th>
                        <th>Total & Payment</th>
                        <th>Status</th>
                        <th style="text-align: right;">Quick Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders ?? [] as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.b2c-orders.show', $order->id) }}" class="order-code">{{ $order->order_number }}</a>
                            <div style="font-size: 11px; color: #94a3b8;">{{ $order->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #0f172a;">{{ $order->customer_name }}</div>
                            <div style="font-size: 11px; color: #64748b;">📞 {{ $order->customer_phone ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 500; color: #1e293b;">
                                {{ $order->items->first()->product_name ?? 'Eyewear Frame' }}
                                @if($order->items->count() > 1)
                                <span style="font-size: 10.5px; color: #0284c7; font-weight: 600;">+{{ $order->items->count() - 1 }} more</span>
                                @endif
                            </div>
                            <div style="font-size: 11px; color: #64748b;">
                                👓 {{ $order->items->first()->lensPackage->name ?? $order->items->first()->lens_type ?? 'Single Vision Standard' }}
                            </div>
                        </td>
                        <td>
                            @if($order->rx_verification_status === 'approved')
                                <span class="badge-rx badge-rx-ok"><i class="fa fa-check-circle"></i> Verified</span>
                            @elseif($order->rx_verification_status === 'pending_review')
                                <span class="badge-rx badge-rx-req"><i class="fa fa-clock-o"></i> Review Req</span>
                            @else
                                <span class="badge-rx badge-rx-none">Zero / None</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">₹{{ number_format($order->grand_total, 2) }}</div>
                            <span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-pay-ok' : 'badge-pay-cod' }}">
                                {{ strtoupper($order->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-status badge-order-placed" style="font-size: 10.5px;">
                                {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.b2c-orders.show', $order->id) }}" class="btn-quick-act" title="View 360° Details">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.b2c-orders.invoice', $order->id) }}" target="_blank" class="btn-quick-act" title="Tax Invoice">
                                    <i class="fa fa-file-text-o"></i>
                                </a>
                                <a href="{{ route('admin.b2c-orders.lab-work-order', $order->id) }}" target="_blank" class="btn-quick-act" title="Lab Work Order">
                                    <i class="fa fa-wrench"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 36px; color: #94a3b8;">
                            <div style="font-size: 28px; margin-bottom: 6px;">📦</div>
                            <div style="font-weight: 600;">No online B2C orders recorded yet.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Bottom Analytics Grid: 7-Day Performance & Optical Categories -->
    <div class="dash-bottom-grid">
        <!-- 7-Day Sales Velocity -->
        <div class="trend-card">
            <h4 style="font-size: 13px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px;">
                <i class="fa fa-line-chart" style="color: #07484A; margin-right: 6px;"></i> Last 7 Days Performance
            </h4>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e2e8f0; color: #64748b; text-align: left;">
                            <th style="padding: 6px 0;">Date</th>
                            <th style="padding: 6px 0; text-align: center;">Orders</th>
                            <th style="padding: 6px 0; text-align: right;">Collected Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyTrend ?? [] as $day)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 8px 0; font-weight: 600; color: #334155;">{{ $day['day'] }}</td>
                            <td style="padding: 8px 0; text-align: center;">
                                <span style="font-weight: 700; color: #0f172a;">{{ $day['orders'] }}</span>
                            </td>
                            <td style="padding: 8px 0; text-align: right; font-weight: 700; color: #059669;">
                                ₹{{ number_format($day['revenue'], 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Optical Product Breakdown -->
        <div class="trend-card">
            <h4 style="font-size: 13px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px;">
                <i class="fa fa-pie-chart" style="color: #07484A; margin-right: 6px;"></i> Order Product Mix
            </h4>
            <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 12px; font-weight: 600; color: #334155;">👓 Optical Frames Ordered</span>
                    <strong style="font-size: 14px; color: #07484A;">{{ $lensVsFrame['frames'] ?? 0 }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 12px; font-weight: 600; color: #334155;">🔬 Prescription Lenses Fitted</span>
                    <strong style="font-size: 14px; color: #059669;">{{ $lensVsFrame['lenses'] ?? 0 }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 12px; font-weight: 600; color: #334155;">🕶️ Sunglasses & Goggles</span>
                    <strong style="font-size: 14px; color: #0284c7;">{{ $lensVsFrame['goggles'] ?? 0 }}</strong>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')

@endsection
