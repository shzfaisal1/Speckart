@extends('layouts.master')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap');

.b2c-wrap {
    font-family: 'Inter', sans-serif;
    padding: 0 4px;
}
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
    font-size: 11.5px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.kpi-value {
    font-size: 22px;
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
    font-size: 18px;
}

/* Filter Bar */
.filter-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.filter-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 12px;
    align-items: center;
}
@media (max-width: 991px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
}
.form-control-custom {
    height: 38px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    padding: 0 12px;
    font-size: 13px;
    width: 100%;
    outline: none;
    transition: border-color 0.15s ease;
}
.form-control-custom:focus {
    border-color: #0d5c56;
    box-shadow: 0 0 0 3px rgba(13,92,86,0.1);
}

.preset-chip {
    padding: 4px 10px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.preset-chip:hover {
    background: #0d5c56;
    color: #ffffff;
    border-color: #0d5c56;
}

/* Table Design */
.table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    overflow: hidden;
}
.b2c-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
}
.b2c-table th {
    background: #f8fafc;
    padding: 12px 16px;
    font-size: 11.5px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
}
.b2c-table td {
    padding: 14px 16px;
    font-size: 13px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.b2c-table tr:hover td {
    background: #fbfdfc;
}

.user-avatar-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #e6f7f7;
    color: #07484a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    border: 1px solid #bceae8;
}

.badge-status {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-active { background: #dcfce7; color: #15803d; }
.badge-inactive { background: #fee2e2; color: #b91c1c; }

.btn-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #475569;
    transition: all 0.15s ease;
    text-decoration: none !important;
}
.btn-action-icon:hover {
    background: #0d5c56;
    color: #ffffff;
    border-color: #0d5c56;
}

.empty-state-wrap {
    text-align: center;
    padding: 50px 20px;
    color: #64748b;
}
.empty-state-wrap i {
    font-size: 42px;
    color: #cbd5e1;
    margin-bottom: 14px;
    display: block;
}
</style>

<div class="b2c-wrap">

    <!-- KPI STRIP -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div>
                <div class="kpi-title">Registered Customers</div>
                <div class="kpi-value">{{ number_format($kpis['total_customers']) }}</div>
            </div>
            <div class="kpi-icon" style="background: #e6f7f7; color: #0d5c56;">
                <i class="fa fa-users"></i>
            </div>
        </div>

        <div class="kpi-card">
            <div>
                <div class="kpi-title">Active Accounts</div>
                <div class="kpi-value" style="color: #10b981;">{{ number_format($kpis['active_customers']) }}</div>
            </div>
            <div class="kpi-icon" style="background: #f0fdf4; color: #10b981;">
                <i class="fa fa-user-check"></i>
            </div>
        </div>

        <div class="kpi-card">
            <div>
                <div class="kpi-title">Customers With Orders</div>
                <div class="kpi-value" style="color: #6366f1;">{{ number_format($kpis['customers_with_orders']) }}</div>
            </div>
            <div class="kpi-icon" style="background: #e0e7ff; color: #6366f1;">
                <i class="fa fa-shopping-bag"></i>
            </div>
        </div>

        <div class="kpi-card">
            <div>
                <div class="kpi-title">Lifetime B2C Revenue</div>
                <div class="kpi-value" style="color: #0d5c56;">₹{{ number_format($kpis['total_online_revenue']) }}</div>
            </div>
            <div class="kpi-icon" style="background: #fffbeb; color: #d97706;">
                <i class="fa fa-inr"></i>
            </div>
        </div>
    </div>

    <!-- FILTER CARD -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.b2c-customers.index') }}" id="customerFilterForm">
            <div class="filter-grid">
                <!-- Omni Search -->
                <div>
                    <input type="text" name="search" class="form-control-custom" placeholder="Search by Name, Email, Phone, Staff ID..." value="{{ request('search') }}">
                </div>

                <!-- Order Activity Filter -->
                <div>
                    <select name="order_activity" class="form-control-custom">
                        <option value="">All Customer Types</option>
                        <option value="has_orders" {{ request('order_activity') == 'has_orders' ? 'selected' : '' }}>Has Placed Orders</option>
                        <option value="no_orders" {{ request('order_activity') == 'no_orders' ? 'selected' : '' }}>Registered (No Orders Yet)</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" class="form-control-custom">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-sm" style="background: #07484a; color: #fff; border-radius: 8px; height: 38px; padding: 0 16px; font-weight: 600; font-size: 13px;">
                        <i class="fa fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.b2c-customers.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; height: 38px; padding: 0 14px; font-size: 13px; display: inline-flex; align-items: center;">
                        Reset
                    </a>
                </div>
            </div>

            <!-- Date Range & Presets Row -->
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-top: 14px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span style="font-size: 12px; font-weight: 600; color: #64748b;">Registered Date:</span>
                    <input type="date" id="dateFrom" name="date_from" class="form-control-custom" style="width: 140px; height: 32px; font-size: 12px;" value="{{ request('date_from') }}">
                    <span style="font-size: 11px; color: #94a3b8;">to</span>
                    <input type="date" id="dateTo" name="date_to" class="form-control-custom" style="width: 140px; height: 32px; font-size: 12px;" value="{{ request('date_to') }}">
                </div>

                <div style="display: flex; align-items: center; gap: 6px;">
                    <button type="button" class="preset-chip" onclick="setDatePreset('today')">Today</button>
                    <button type="button" class="preset-chip" onclick="setDatePreset('yesterday')">Yesterday</button>
                    <button type="button" class="preset-chip" onclick="setDatePreset('last7')">Last 7 Days</button>
                    <button type="button" class="preset-chip" onclick="setDatePreset('thisMonth')">This Month</button>
                </div>
            </div>
        </form>
    </div>

    <!-- CUSTOMERS TABLE -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="b2c-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Contact Details</th>
                        <th>Registered Date</th>
                        <th>Orders & Spent</th>
                        <th>Last Activity</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <!-- Customer Avatar & Name -->
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="user-avatar-circle">
                                        {{ strtoupper(substr($customer->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.b2c-customers.show', $customer->id) }}" style="font-weight: 700; color: #0f172a; text-decoration: none; font-size: 13.5px;">
                                            {{ $customer->name ?? 'Website Customer' }}
                                        </a>
                                        <div style="font-size: 11px; color: #64748b; font-family: 'JetBrains Mono', monospace;">
                                            ID: {{ $customer->staff_id ?: ('CUST' . $customer->id) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Details -->
                            <td>
                                <div style="font-size: 12.5px; color: #1e293b;">
                                    <i class="fa fa-envelope me-1" style="color: #0d5c56; font-size: 11px;"></i> {{ $customer->email ?: 'N/A' }}
                                </div>
                                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                    <i class="fa fa-phone me-1" style="color: #64748b; font-size: 11px;"></i> {{ $customer->phone ?: 'N/A' }}
                                </div>
                            </td>

                            <!-- Registered Date -->
                            <td>
                                <div style="font-size: 12.5px; font-weight: 600; color: #334155;">
                                    {{ $customer->created_at ? $customer->created_at->format('d M Y') : 'N/A' }}
                                </div>
                                <div style="font-size: 11px; color: #94a3b8;">
                                    {{ $customer->created_at ? $customer->created_at->format('h:i A') : '' }}
                                </div>
                            </td>

                            <!-- Orders & Spent -->
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 11px; border-radius: 6px; padding: 4px 8px;">
                                        {{ $customer->b2c_orders_count }} {{ Str::plural('Order', $customer->b2c_orders_count) }}
                                    </span>
                                </div>
                                <div style="font-size: 12px; font-weight: 700; color: #0d5c56; margin-top: 3px;">
                                    ₹{{ number_format((float) ($customer->total_spent ?? 0), 2) }}
                                </div>
                            </td>

                            <!-- Last Activity -->
                            <td>
                                @if($customer->b2cOrders->isNotEmpty())
                                    @php $lastOrder = $customer->b2cOrders->first(); @endphp
                                    <div style="font-size: 12px; font-weight: 600; color: #0f172a;">
                                        {{ $lastOrder->created_at ? $lastOrder->created_at->format('d M Y') : 'Recent' }}
                                    </div>
                                    <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600;">
                                        {{ ucfirst($lastOrder->order_status) }}
                                    </div>
                                @else
                                    <span style="font-size: 11.5px; color: #94a3b8; font-style: italic;">No orders placed yet</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td>
                                @if($customer->status == 1)
                                    <span class="badge-status badge-active">
                                        <i class="fa fa-circle" style="font-size: 6px;"></i> Active
                                    </span>
                                @else
                                    <span class="badge-status badge-inactive">
                                        <i class="fa fa-circle" style="font-size: 6px;"></i> Inactive
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="{{ route('admin.b2c-customers.show', $customer->id) }}" class="btn-action-icon" title="View 360° Profile">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.b2c-orders.index', ['search' => $customer->phone ?: $customer->email]) }}" class="btn-action-icon" title="View Orders">
                                        <i class="fa fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state-wrap">
                                    <i class="fa fa-inbox"></i>
                                    <h5 style="font-weight: 600; color: #1e293b;">No registered B2C customers found</h5>
                                    <p style="font-size: 12.5px; margin-bottom: 0;">Online customers registering on Speckarts storefront will automatically appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>

<script>
function setDatePreset(preset) {
    const today = new Date();
    let fromDate = new Date();
    let toDate = new Date();

    const formatDate = (d) => d.toISOString().split('T')[0];

    if (preset === 'today') {
        fromDate = today;
        toDate = today;
    } else if (preset === 'yesterday') {
        fromDate.setDate(today.getDate() - 1);
        toDate.setDate(today.getDate() - 1);
    } else if (preset === 'last7') {
        fromDate.setDate(today.getDate() - 6);
        toDate = today;
    } else if (preset === 'thisMonth') {
        fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
        toDate = today;
    }

    document.getElementById('dateFrom').value = formatDate(fromDate);
    document.getElementById('dateTo').value = formatDate(toDate);
    document.getElementById('customerFilterForm').submit();
}
</script>
@endsection
