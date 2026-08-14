@extends('layouts.master')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap');

.cust-wrap {
    font-family: 'Inter', sans-serif;
    padding: 0 4px;
}
.profile-header-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
}
.profile-avatar-lg {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #e6f7f7;
    color: #07484a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 700;
    border: 2px solid #bceae8;
}
.profile-grid-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.summary-mini-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 18px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.summary-mini-label {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
}
.summary-mini-val {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    margin-top: 4px;
}

.section-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.section-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.table-custom {
    width: 100%;
    border-collapse: collapse;
}
.table-custom th {
    background: #f8fafc;
    padding: 10px 14px;
    font-size: 11.5px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    border-bottom: 1px solid #e2e8f0;
}
.table-custom td {
    padding: 12px 14px;
    font-size: 12.5px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.address-item-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 10px;
}
</style>

<div class="cust-wrap">

    <!-- Top Navigation / Header -->
    <div style="margin-bottom: 16px;">
        <a href="{{ route('admin.b2c-customers.index') }}" style="color: #64748b; text-decoration: none; font-size: 13px; font-weight: 600;">
            <i class="fa fa-arrow-left me-1"></i> Back to Customer List
        </a>
    </div>

    <!-- PROFILE HERO CARD -->
    <div class="profile-header-card">
        <div style="display: flex; align-items: center; gap: 18px;">
            <div class="profile-avatar-lg">
                {{ strtoupper(substr($customer->name ?? 'C', 0, 1)) }}
            </div>
            <div>
                <h4 style="font-weight: 700; color: #0f172a; margin-bottom: 4px;">{{ $customer->name ?? 'Customer Profile' }}</h4>
                <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap; font-size: 12.5px; color: #64748b;">
                    <span><i class="fa fa-envelope me-1" style="color: #0d5c56;"></i> {{ $customer->email ?: 'No email' }}</span>
                    <span><i class="fa fa-phone me-1" style="color: #0d5c56;"></i> {{ $customer->phone ?: 'No phone' }}</span>
                    <span style="font-family: 'JetBrains Mono', monospace;">Staff/Cust ID: {{ $customer->staff_id ?: ('CUST' . $customer->id) }}</span>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <form method="POST" action="{{ route('admin.b2c-customers.toggle-status', $customer->id) }}">
                @csrf
                @if($customer->status == 1)
                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; font-weight: 600;">
                        <i class="fa fa-ban me-1"></i> Deactivate Account
                    </button>
                @else
                    <button type="submit" class="btn btn-sm btn-outline-success" style="border-radius: 8px; font-weight: 600;">
                        <i class="fa fa-check me-1"></i> Activate Account
                    </button>
                @endif
            </form>
            <a href="{{ route('admin.b2c-orders.index', ['search' => $customer->phone ?: $customer->email]) }}" class="btn btn-sm" style="background: #07484a; color: #fff; border-radius: 8px; font-weight: 600;">
                <i class="fa fa-shopping-bag me-1"></i> View All Orders
            </a>
        </div>
    </div>

    <!-- LIFETIME SUMMARY STRIP -->
    <div class="profile-grid-summary">
        <div class="summary-mini-card">
            <div class="summary-mini-label">Lifetime Orders</div>
            <div class="summary-mini-val" style="color: #0369a1;">{{ $customer->b2c_orders_count }}</div>
        </div>
        <div class="summary-mini-card">
            <div class="summary-mini-label">Total Spent</div>
            <div class="summary-mini-val" style="color: #0d5c56;">₹{{ number_format((float) ($customer->total_spent ?? 0), 2) }}</div>
        </div>
        <div class="summary-mini-card">
            <div class="summary-mini-label">Avg Order Value (AOV)</div>
            <div class="summary-mini-val" style="color: #d97706;">
                ₹{{ number_format($customer->b2c_orders_count > 0 ? (($customer->total_spent ?? 0) / $customer->b2c_orders_count) : 0, 2) }}
            </div>
        </div>
        <div class="summary-mini-card">
            <div class="summary-mini-label">Saved Addresses</div>
            <div class="summary-mini-val" style="color: #6366f1;">{{ $customer->addresses ? $customer->addresses->count() : 0 }}</div>
        </div>
    </div>

    <div class="row">
        <!-- LEFT COLUMN: Order History -->
        <div class="col-lg-8">
            <div class="section-card">
                <div class="section-title">
                    <i class="fa fa-clock-rotate-left" style="color: #0d5c56;"></i> Order History ({{ $orders->count() }})
                </div>

                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.b2c-orders.show', $order->id) }}" style="font-weight: 700; color: #0d5c56; text-decoration: none;">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $order->items ? $order->items->count() : 0 }} Items</span>
                                    </td>
                                    <td style="font-weight: 700; color: #0f172a;">
                                        ₹{{ number_format((float)$order->grand_total, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge" style="background: #e6f7f7; color: #07484a; text-transform: uppercase; font-size: 11px;">
                                            {{ str_replace('_', ' ', $order->order_status) }}
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('admin.b2c-orders.show', $order->id) }}" class="btn btn-xs btn-outline-secondary" style="font-size: 11px; padding: 3px 8px; border-radius: 6px;">
                                            View 360°
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: #94a3b8; padding: 24px;">
                                        No online orders found for this customer.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Membership, Loyalty, Saved Addresses & Account Details -->
        <div class="col-lg-4">

            <!-- MEMBERSHIP & LOYALTY CARD -->
            <div class="section-card" style="border: 1px solid #c2ecea; background: #fbfdfd;">
                <div class="section-title">
                    <i class="fa fa-crown" style="color: #d97706;"></i> Membership & Rewards
                </div>

                <!-- Membership Block -->
                @if(!empty($customer->membership) && !empty($customer->membership['is_active']))
                    <div style="background: linear-gradient(135deg, #07484A 0%, #00B9B9 100%); color: #fff; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 11px; font-weight: 700; background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 12px; letter-spacing: 0.5px;">👑 ACTIVE VIP</span>
                            <span style="font-size: 11px; opacity: 0.9;">{{ $customer->membership['days_left'] }} Days Left</span>
                        </div>
                        <h5 style="font-weight: 700; margin-bottom: 2px; color: #ffffff;">{{ $customer->membership['card_name'] }}</h5>
                        <div style="font-size: 11.5px; opacity: 0.85;">Valid until: <strong>{{ $customer->membership['expiry'] }}</strong></div>
                        <div style="display: flex; gap: 8px; margin-top: 10px;">
                            @if(!empty($customer->membership['enable_bogo']))
                                <span style="font-size: 10.5px; background: rgba(255,255,255,0.15); padding: 2px 6px; border-radius: 4px;">✓ Buy 1 Get 1 Free</span>
                            @endif
                            @if(!empty($customer->membership['coupon_percent']))
                                <span style="font-size: 10.5px; background: rgba(255,255,255,0.15); padding: 2px 6px; border-radius: 4px;">✓ {{ $customer->membership['coupon_percent'] }}% Extra Off</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 12px 14px; margin-bottom: 14px; text-align: center;">
                        <span style="font-size: 12px; color: #64748b;">Standard Customer (No active VIP membership)</span>
                    </div>
                @endif

                <!-- Loyalty Points Balance Block -->
                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: #92400e; text-transform: uppercase;">Loyalty Points Balance</div>
                            <div style="font-size: 24px; font-weight: 800; color: #b45309; margin-top: 2px;">
                                {{ number_format($customer->loyalty_points) }} <span style="font-size: 13px; font-weight: 600;">Pts</span>
                            </div>
                        </div>
                        <div style="width: 42px; height: 42px; border-radius: 50%; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="fa fa-coins"></i>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 8px; border-top: 1px solid #fef3c7; font-size: 11px; color: #78350f;">
                        <span>Lifetime Earned: <strong>{{ number_format($customer->points_earned ?? 0) }} Pts</strong></span>
                        <span>Redeemed: <strong>{{ number_format($customer->points_used ?? 0) }} Pts</strong></span>
                    </div>
                </div>
            </div>

            <!-- SAVED ADDRESSES -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fa fa-location-dot" style="color: #0d5c56;"></i> Saved Addresses
                </div>

                @if($customer->addresses && $customer->addresses->isNotEmpty())
                    @foreach($customer->addresses as $addr)
                        <div class="address-item-box">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <strong style="font-size: 12.5px; color: #0f172a;">{{ $addr->address_type ?? 'Home' }}</strong>
                                @if($addr->is_default)
                                    <span class="badge bg-success" style="font-size: 10px;">DEFAULT</span>
                                @endif
                            </div>
                            <div style="font-size: 12px; color: #475569; line-height: 1.5;">
                                <div>{{ $addr->full_name }} ({{ $addr->phone }})</div>
                                <div>{{ $addr->house_no }}, {{ $addr->road_area }}</div>
                                @if($addr->landmark)<div>Near {{ $addr->landmark }}</div>@endif
                                <div>PIN: <strong>{{ $addr->pincode }}</strong></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="font-size: 12.5px; color: #94a3b8; text-align: center; padding: 14px 0;">
                        No saved shipping addresses found.
                    </div>
                @endif
            </div>

            <!-- Account Metadata -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fa fa-shield-halved" style="color: #0d5c56;"></i> Account Details
                </div>
                <div style="font-size: 12.5px; color: #334155; display: flex; flex-direction: column; gap: 8px;">
                    <div><strong>User Type:</strong> B2C Online Customer</div>
                    <div><strong>Registered On:</strong> {{ $customer->created_at ? $customer->created_at->format('d M Y, h:i A') : 'N/A' }}</div>
                    <div><strong>Account Status:</strong> {{ $customer->status == 1 ? 'Active' : 'Inactive' }}</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
