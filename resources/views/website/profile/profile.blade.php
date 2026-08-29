@extends('website.layout.master')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════════════════
     PREMIUM MY ACCOUNT / PROFILE DASHBOARD — SPECKART
     Warm, human-centric design with friendly greetings & smooth controls
══════════════════════════════════════════════════════════════════════════════ --}}

<style>
/* ==========================================================================
   HUMAN-CENTRIC ULTRA-PREMIUM PROFILE DASHBOARD STYLES
   ========================================================================== */
:root {
    --pf-primary: #07484A;
    --pf-primary-dark: #032729;
    --pf-teal: #00B9B9;
    --pf-teal-light: #e6f9f9;
    --pf-gold: #fbbf24;
    --pf-gold-dark: #d97706;
    --pf-bg: #f8fafc;
    --pf-card-bg: #ffffff;
    --pf-border: #e2e8f0;
    --pf-text-main: #0f172a;
    --pf-text-muted: #64748b;
}

.profile-dashboard-section {
    background: radial-gradient(circle at 10% 10%, rgba(0, 185, 185, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 85%, rgba(7, 72, 74, 0.06) 0%, transparent 45%),
                #f8fafc;
    min-height: 88vh;
    padding: 32px 0 70px;
    font-family: 'Poppins', sans-serif;
    -webkit-font-smoothing: antialiased;
}

/* ── Breadcrumbs ── */
.pf-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--pf-text-muted);
    margin-bottom: 24px;
}

.pf-breadcrumb a {
    color: var(--pf-text-muted);
    text-decoration: none;
    transition: color 0.2s ease;
}

.pf-breadcrumb a:hover {
    color: var(--pf-primary);
}

.pf-breadcrumb i {
    font-size: 10px;
    color: #94a3b8;
}

.pf-breadcrumb .active {
    color: var(--pf-primary);
    font-weight: 600;
}

/* ── LEFT COLUMN: Warm Personal Profile Card ── */
.pf-identity-card {
    background: #ffffff;
    border-radius: 24px;
    border: 1px solid rgba(226, 232, 240, 0.85);
    box-shadow: 0 20px 45px -15px rgba(7, 72, 74, 0.1), 0 2px 8px rgba(0, 0, 0, 0.02);
    overflow: hidden;
    position: relative;
    margin-bottom: 24px;
    transition: box-shadow 0.3s ease;
}

.pf-cover-banner {
    height: 145px;
    background: linear-gradient(135deg, #053335 0%, #07484A 55%, #0e7075 100%);
    position: relative;
    overflow: hidden;
}

.pf-cover-pattern {
    position: absolute;
    inset: 0;
    opacity: 0.18;
    background-image: radial-gradient(circle, #ffffff 1px, transparent 1px);
    background-size: 16px 16px;
}

.pf-cover-greeting-pill {
    position: absolute;
    top: 14px;
    left: 16px;
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #ffffff;
    font-size: 11.5px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    backdrop-filter: blur(6px);
}

.pf-avatar-area {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 24px 30px;
    margin-top: -77px;
    text-align: center;
    position: relative;
    z-index: 2;
}

.pf-avatar-wrapper {
    position: relative;
    width: 154px;
    height: 154px;
    margin-bottom: 16px;
}

.pf-avatar-preview {
    width: 154px;
    height: 154px;
    border-radius: 50%;
    border: 4px solid #ffffff;
    background-size: cover;
    background-position: center;
    background-color: #f1f5f9;
    box-shadow: 0 0 0 4px rgba(0, 185, 185, 0.28), 0 16px 35px rgba(7, 72, 74, 0.22);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.pf-avatar-wrapper:hover .pf-avatar-preview {
    transform: scale(1.02);
    box-shadow: 0 0 0 4px rgba(0, 185, 185, 0.4), 0 18px 40px rgba(7, 72, 74, 0.28);
}

.pf-avatar-edit-btn {
    position: absolute;
    bottom: 4px;
    right: 4px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00B9B9 0%, #07484A 100%);
    border: 3px solid #ffffff;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 15px;
    box-shadow: 0 4px 14px rgba(0, 185, 185, 0.45);
    transition: all 0.2s ease;
}

.pf-avatar-edit-btn:hover {
    transform: scale(1.12);
    background: #00B9B9;
    color: #ffffff;
}

.pf-user-name {
    font-size: 22px;
    font-weight: 800;
    color: #07484A;
    margin-bottom: 5px;
    letter-spacing: -0.3px;
    line-height: 1.25;
}

.pf-user-contact {
    font-size: 12.5px;
    color: #64748b;
    margin-bottom: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: #f1f5f9;
    padding: 4px 14px;
    border-radius: 20px;
    font-weight: 500;
}

/* Loyalty Points Pill */
.pf-points-pill {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1.5px solid #fde68a;
    color: #92400e;
    border-radius: 50px;
    padding: 9px 20px;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.15);
    margin-bottom: 12px;
    text-decoration: none !important;
    transition: all 0.25s ease;
}

.pf-points-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.25);
    color: #78350f;
}

.pf-points-icon {
    font-size: 15px;
    color: #f59e0b;
}

/* Membership Pill */
.pf-membership-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all 0.25s ease;
}

.pf-membership-active {
    background: linear-gradient(135deg, #07484A 0%, #00B9B9 100%);
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(7, 72, 74, 0.25);
}

.pf-membership-join {
    background: #f0fdfa;
    border: 1.5px dashed #00B9B9;
    color: #0f766e !important;
}

.pf-membership-join:hover {
    background: #00B9B9;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(0, 185, 185, 0.35);
}

/* ── RIGHT COLUMN: Main Hub ── */
.pf-main-hub-card {
    background: #ffffff;
    border-radius: 24px;
    border: 1px solid rgba(226, 232, 240, 0.85);
    box-shadow: 0 20px 45px -15px rgba(7, 72, 74, 0.08), 0 2px 8px rgba(0, 0, 0, 0.02);
    overflow: hidden;
    margin-bottom: 24px;
}

.pf-hub-header {
    padding: 24px 30px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    background: #ffffff;
}

.pf-hub-title h3 {
    font-size: 22px;
    font-weight: 800;
    color: var(--pf-primary);
    margin: 0;
    letter-spacing: -0.3px;
}

.pf-hub-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-pf-action {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none !important;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-pf-back {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    color: #334155 !important;
}

.btn-pf-back:hover {
    background: #e2e8f0;
    color: #0f172a !important;
    transform: translateX(-2px);
}

.btn-pf-logout {
    background: #fef2f2;
    border: 1.5px solid #fecaca;
    color: #dc2626 !important;
}

.btn-pf-logout:hover {
    background: #dc2626;
    color: #ffffff !important;
    border-color: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(220, 38, 38, 0.28);
}

/* ── 6 Service Grid Cards ── */
.pf-services-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    padding: 28px 30px;
}

.pf-service-card {
    background: #ffffff;
    border: 1.5px solid #f1f5f9;
    border-radius: 20px;
    padding: 24px 16px;
    text-decoration: none !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
    min-height: 132px;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
}

.pf-service-card:hover {
    transform: translateY(-5px);
    border-color: rgba(0, 185, 185, 0.45);
    box-shadow: 0 18px 36px -8px rgba(7, 72, 74, 0.14);
    background: #ffffff;
}

.pf-service-icon-box {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
    margin-bottom: 10px;
    transition: transform 0.25s ease;
}

.pf-service-card:hover .pf-service-icon-box {
    transform: scale(1.1);
}

/* Distinct theme colors for icon boxes */
.icon-box-orders {
    background: linear-gradient(135deg, #f0fdf9 0%, #e6fdf5 100%);
    color: #065f46;
    border: 1px solid #ccfbf1;
}

.icon-box-account {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

.icon-box-address {
    background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
    color: #0f766e;
    border: 1px solid #99f6e4;
}

.icon-box-rx {
    background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
    color: #be123c;
    border: 1px solid #fecdd3;
}

.icon-box-voucher {
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    color: #6d28d9;
    border: 1px solid #ddd6fe;
}

.icon-box-points {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    color: #b45309;
    border: 1px solid #fde68a;
}

.pf-service-title {
    font-size: 14.5px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    line-height: 1.25;
    transition: color 0.2s ease;
}

.pf-service-card:hover .pf-service-title {
    color: var(--pf-primary);
}

.pf-service-badge {
    margin-top: 6px;
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    padding: 2.5px 10px;
    border-radius: 20px;
}

/* ── Active Vouchers Widget ── */
.pf-vouchers-widget {
    background: linear-gradient(135deg, #fbfaff 0%, #f5f3ff 100%);
    border: 1.5px solid #ede9fe;
    border-radius: 20px;
    padding: 22px 24px;
    box-shadow: 0 8px 24px rgba(124, 58, 237, 0.06);
    margin-bottom: 24px;
}

.pf-vouchers-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9d5ff;
}

.pf-vouchers-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.pf-vouchers-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #7c3aed;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.pf-voucher-item {
    background: #ffffff;
    border: 1px solid #ddd6fe;
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}

.pf-voucher-code {
    font-family: 'Poppins', monospace;
    font-weight: 800;
    color: #6d28d9;
    font-size: 14px;
    letter-spacing: 0.5px;
}

/* ── Friendly Support Strip ── */
.pf-support-strip {
    background: linear-gradient(135deg, #07484A 0%, #0a5658 100%);
    border-radius: 20px;
    padding: 20px 24px;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}

.pf-support-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.pf-support-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #fde047;
}

.pf-support-info h5 {
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 2px;
    color: #ffffff;
}

.pf-support-info p {
    font-size: 12px;
    color: #ccfbf1;
    margin: 0;
}

.btn-pf-support {
    background: #ffffff;
    color: #07484A !important;
    font-weight: 700;
    font-size: 13px;
    padding: 8px 18px;
    border-radius: 10px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.btn-pf-support:hover {
    background: #fde047;
    color: #032b2d !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

/* ── Responsive Adaptations ── */
@media (max-width: 991px) {
    .pf-services-grid {
        grid-template-columns: repeat(2, 1fr);
        padding: 20px;
        gap: 12px;
    }
    .pf-identity-card {
        margin-bottom: 20px;
    }
}

@media (max-width: 575px) {
    .profile-dashboard-section {
        padding: 20px 0 50px;
    }
    .pf-services-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        padding: 14px;
    }
    .pf-service-card {
        padding: 16px 10px;
        min-height: 135px;
    }
    .pf-service-title {
        font-size: 13px;
    }
    .pf-service-desc {
        font-size: 10.5px;
    }
    .pf-hub-header {
        padding: 16px;
    }
    .pf-support-strip {
        padding: 16px;
    }
}
</style>

@php
    // Fetch active gift vouchers for this user from session + offers table
    $profileVoucher = session()->get('applied_voucher', null);

    // Also get available manual-code vouchers from offers table
    $now = \Carbon\Carbon::now()->toDateString();
    $profileAvailableVouchers = DB::table('offers')
        ->where('offer_type', 'gift_voucher')
        ->whereNotNull('coupon_code')
        ->where('coupon_code', '!=', '')
        ->where('status', 'active')
        ->where(function ($q) use ($now) {
            $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
        })
        ->where(function ($q) use ($now) {
            $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
        })
        ->get();

    $profilePts = $loyaltyPoints ?? 0;
    if (!isset($loyaltyPoints) || $profilePts === 0) {
        if (session()->has('test_loyalty_points')) {
            $profilePts = (int) session()->get('test_loyalty_points');
        } elseif ($user) {
            if (\Illuminate\Support\Facades\Schema::hasTable('tbl_customer')) {
                $custRec = DB::table('tbl_customer')
                    ->where(function ($q) use ($user) {
                        if (!empty($user->phone)) $q->orWhere('contact_no', $user->phone);
                        if (!empty($user->mobile)) $q->orWhere('contact_no', $user->mobile);
                        if (!empty($user->email)) $q->orWhere('email_id', $user->email);
                        $q->orWhere('customer_id', $user->id);
                        $q->orWhere('added_by', $user->id);
                    })
                    ->first();
                if ($custRec) {
                    if (isset($custRec->Loyalty_Points_Bal) && $custRec->Loyalty_Points_Bal !== null) {
                        $profilePts = (int) $custRec->Loyalty_Points_Bal;
                    } elseif (isset($custRec->Loyalty_Points)) {
                        $profilePts = max(0, (int) ($custRec->Loyalty_Points - ($custRec->Loyalty_Points_Redeem ?? 0)));
                    }
                }
            }
            if ($profilePts === 0 && \Illuminate\Support\Facades\Schema::hasTable('tbl_sales')) {
                $earned = (int) DB::table('tbl_sales')->where('user_id', $user->id)->where('sales_type', 0)->sum('earnedPoints');
                $used   = (int) DB::table('tbl_sales')->where('user_id', $user->id)->where('sales_type', 0)->sum('loyalty_point_amount');
                $profilePts = max(0, $earned - $used);
            }
        }
    }

    if (!isset($profileMembership) || empty($profileMembership)) {
        $profileMembership = null;
        if ($user && \Illuminate\Support\Facades\Schema::hasTable('tbl_customer')) {
            $custMem = DB::table('tbl_customer')
                ->where(function ($q) use ($user) {
                    if (!empty($user->phone)) $q->orWhere('contact_no', $user->phone);
                    if (!empty($user->mobile)) $q->orWhere('contact_no', $user->mobile);
                    if (!empty($user->email)) $q->orWhere('email_id', $user->email);
                    $q->orWhere('customer_id', $user->id);
                    $q->orWhere('added_by', $user->id);
                })
                ->first();
            if ($custMem && !empty($custMem->membership_card_id) && !empty($custMem->membership_expiry) && \Carbon\Carbon::parse($custMem->membership_expiry)->isFuture()) {
                $dbCard = DB::table('tbl_membership_card')->where('card_id', $custMem->membership_card_id)->first();
                if ($dbCard) {
                    $profileMembership = [
                        'name' => $dbCard->card_name,
                        'expiry' => \Carbon\Carbon::parse($custMem->membership_expiry)->format('d M Y')
                    ];
                }
            }
        }
        if (!$profileMembership && session()->has('active_membership')) {
            $sessM = session()->get('active_membership');
            if (\Carbon\Carbon::parse($sessM['expiry'])->isFuture()) {
                $profileMembership = [
                    'name' => $sessM['card_name'] ?? 'Gold Member',
                    'expiry' => \Carbon\Carbon::parse($sessM['expiry'])->format('d M Y')
                ];
            }
        }
    }

    // Time-based friendly greeting and clean first name
    $hour = (int) date('H');
    if ($hour < 12) {
        $greeting = 'Good morning';
    } elseif ($hour < 17) {
        $greeting = 'Good afternoon';
    } else {
        $greeting = 'Good evening';
    }
    $firstName = explode(' ', trim($user->name ?: 'Friend'))[0];
@endphp

<section class="profile-dashboard-section">
    <div class="container">

        <!-- Breadcrumbs -->
        <div class="pf-breadcrumb">
            <a href="{{ route('home') }}"><i class="bi bi-house-door-fill"></i> Home</a>
            <i class="bi bi-chevron-right"></i>
            <span class="active">My Account</span>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-3 shadow-sm" style="background:#e6fdf5; color:#065f46;">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-3 shadow-sm" style="background:#fef2f2; color:#991b1b;">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <span class="fw-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <div class="row g-2 g-md-3 g-lg-4">
            
            <!-- ── LEFT COLUMN: Warm Personal Profile Card ── -->
            <div class="col-lg-4">
                <div class="pf-identity-card">
                    <div class="pf-cover-banner">
                        <div class="pf-cover-pattern"></div>
                        {{-- <div class="pf-cover-greeting-pill">
                            <span><i class="bi bi-patch-check-fill me-1"></i> Speckart Member</span>
                        </div> --}}
                    </div>

                    <div class="pf-avatar-area">
                        <div class="pf-avatar-wrapper">
                            <div class="pf-avatar-preview" id="imagePreview"
                                 style="background-image: url('{{ $user->profile_image_url }}');">
                            </div>
                            <label for="imageUpload" class="pf-avatar-edit-btn" title="Change your photo">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" id="imageUpload" accept=".png, .jpg, .jpeg, .webp" style="display:none;" />
                        </div>

                        <h4 class="pf-user-name">{{ $user->name ?: 'Speckart Customer' }}</h4>
                        <div class="pf-user-contact">
                            <i class="bi bi-phone text-teal"></i>
                            <span>{{ $user->phone ?? ($user->mobile ?? ($user->email ?? 'Speckart Member')) }}</span>
                        </div>

                        <!-- Loyalty Points Badge -->
                        <a href="{{ route('cart') }}" class="pf-points-pill" title="View loyalty points on cart">
                           <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="5" width="18" height="14" rx="2.5" stroke="currentColor" stroke-width="2"/>
                                <path d="M3 9H21" stroke="currentColor" stroke-width="2"/>
                                <path d="M7 13H11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M16 12L16.7 13.4L18.25 13.6L17.13 14.7L17.4 16.25L16 15.5L14.6 16.25L14.87 14.7L13.75 13.6L15.3 13.4L16 12Z" fill="currentColor"/>
                            </svg>
                            <span>{{ number_format($profilePts) }} Points to Spend</span>
                            <i class="bi bi-arrow-right-short text-muted"></i>
                        </a>

                        <!-- Membership Badge -->
                        @if($profileMembership)
                            <div class="mt-1">
                                <span class="pf-membership-pill pf-membership-active">
                                    <i class="bi bi-star-fill text-warning me-1"></i> {{ $profileMembership['name'] }}
                                </span>
                            </div>
                        @else
                            <div class="mt-1">
                                <a href="{{ route('website.membership') }}" class="pf-membership-pill pf-membership-join">
                                    <i class="bi bi-star-fill text-warning me-1"></i> Join Gold VIP Club <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        @endif

                        <!-- Quick Sidebar Navigation Links -->
                        
                    </div>
                </div>
            </div>

            <!-- ── RIGHT COLUMN: Main Hub & 6 Action Cards ── -->
            <div class="col-lg-8">
                <div class="pf-main-hub-card">
                    
                    <!-- Hub Header with Friendly Greeting -->
                    <div class="pf-hub-header">
                        <div class="pf-hub-title">
                            <h3>{{ $greeting }}, {{ $firstName }}!</h3>
                        </div>
                        <div class="pf-hub-actions">
                            <a href="{{ route('home') }}" class="btn-pf-action btn-pf-back">
                                <i class="bi bi-arrow-left"></i>
                                <span>Back to Store</span>
                            </a>
                            <a href="{{ route('logout') }}" class="btn-pf-action btn-pf-logout"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Log Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>

                    <!-- 6 Service Action Cards -->
                    <div class="pf-services-grid">
                        
                        <!-- 1. My Orders -->
                        <a href="{{ route('my-orders') }}" class="pf-service-card">
                            <div class="pf-service-icon-box icon-box-orders">
                                <i class="bi bi-bag-check-fill"></i>
                            </div>
                            <span class="pf-service-title">My Orders</span>
                            {{-- <span class="pf-service-badge text-primary" style="background:#eff6ff;">Order History</span> --}}
                        </a>

                        <!-- 2. Account Information -->
                        <a href="{{ route('account-info') }}" class="pf-service-card">
                            <div class="pf-service-icon-box icon-box-account">
                                <i class="bi bi-person-bounding-box"></i>
                            </div>
                            <span class="pf-service-title">Personal Info</span>
                            {{-- <span class="pf-service-badge text-info" style="background:#f0fdfa;">Edit Profile</span> --}}
                        </a>

                        <!-- 3. Address Book -->
                        <a href="{{ route('my-addresses') }}" class="pf-service-card">
                            <div class="pf-service-icon-box icon-box-address">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <span class="pf-service-title">Saved Addresses</span>
                            {{-- <span class="pf-service-badge text-success" style="background:#ecfdf5;">Address Book</span> --}}
                        </a>

                        <!-- 4. My Prescriptions -->
                        <a href="{{ route('my-prescriptions') }}" class="pf-service-card">
                            <div class="pf-service-icon-box icon-box-rx">
                                <i class="bi bi-file-earmark-medical-fill"></i>
                            </div>
                            <span class="pf-service-title">Eye Prescriptions</span>
                            {{-- <span class="pf-service-badge text-danger" style="background:#fff1f2;">Vision Records</span> --}}
                        </a>

                        <!-- 5. Voucher Balance -->
                        @php
                            $sessionVoucher = session()->get('applied_voucher', null);
                            $hasManualVouchers = $profileAvailableVouchers->count() > 0;
                        @endphp
                        <a href="{{ route('cart') }}" class="pf-service-card">
                            <div class="pf-service-icon-box icon-box-voucher">
                                <i class="bi bi-ticket-perforated-fill"></i>
                            </div>
                            <span class="pf-service-title">Gift Vouchers</span>
                            @if($sessionVoucher)
                                <span class="pf-service-badge text-success" style="background:#ecfdf5;">₹{{ number_format($sessionVoucher['amount_applied'] ?? 0) }} Applied</span>
                            @elseif($hasManualVouchers)
                                <span class="pf-service-badge" style="background:#f5f3ff; color:#7c3aed;">{{ $profileAvailableVouchers->count() }} Available</span>
                            @else
                                {{-- <span class="pf-service-badge text-muted" style="background:#f1f5f9;">Cart Rewards</span> --}}
                            @endif
                        </a>

                        <!-- 6. Loyalty Points -->
                        <a href="{{ route('cart') }}" class="pf-service-card">
                            <div class="pf-service-icon-box icon-box-points">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="3" y="5" width="18" height="14" rx="2.5" stroke="currentColor" stroke-width="2"/>
                                    <path d="M3 9H21" stroke="currentColor" stroke-width="2"/>
                                    <path d="M7 13H11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M16 12L16.7 13.4L18.25 13.6L17.13 14.7L17.4 16.25L16 15.5L14.6 16.25L14.87 14.7L13.75 13.6L15.3 13.4L16 12Z" fill="currentColor"/>
                                </svg>
                            </div>
                            <span class="pf-service-title">Loyalty Rewards</span>
                            <span class="pf-service-badge text-warning" style="background:#fffbeb;">{{ number_format($profilePts) }} Points</span>
                        </a>

                    </div>

                </div>

                <!-- ── Active Vouchers Widget ── -->
                @if($sessionVoucher || $hasManualVouchers)
                    <div class="pf-vouchers-widget">
                        <div class="pf-vouchers-header">
                            <div class="pf-vouchers-title">
                                <div class="pf-vouchers-icon"><i class="bi bi-gift-fill"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold" style="color:#5b21b6; font-size:14.5px;">Your Active Gift Vouchers</h6>
                                    <small class="text-muted" style="font-size:11.5px;">Ready to apply during your next checkout</small>
                                </div>
                            </div>
                            <a href="{{ route('cart') }}" class="btn btn-sm fw-bold px-3"
                               style="background:#7c3aed; color:#fff; border-radius:10px; font-size:12px;">
                                <i class="bi bi-cart3 me-1"></i> Go to Cart
                            </a>
                        </div>

                        {{-- Applied Session Voucher --}}
                        @if($sessionVoucher)
                            <div class="pf-voucher-item" style="border-left: 3.5px solid #10b981;">
                                <div>
                                    <div class="pf-voucher-code text-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> {{ $sessionVoucher['code'] }}
                                        <span class="badge ms-2" style="background:#10b981; color:#fff; font-size:10px; border-radius:4px;">APPLIED</span>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size:12px;">
                                        ₹{{ number_format($sessionVoucher['amount_applied'] ?? 0, 2) }} applied to your cart
                                        @if(($sessionVoucher['remaining_balance'] ?? 0) > 0)
                                            · <span class="text-success fw-semibold">₹{{ number_format($sessionVoucher['remaining_balance'], 2) }} balance left</span>
                                        @endif
                                    </div>
                                </div>
                                <i class="bi bi-patch-check-fill fs-4 text-success"></i>
                            </div>
                        @endif

                        {{-- Available Offers Table Vouchers --}}
                        @if($hasManualVouchers)
                            @foreach($profileAvailableVouchers as $pv)
                                <div class="pf-voucher-item">
                                    <div>
                                        <div class="pf-voucher-code">
                                            <i class="bi bi-ticket-fill me-1"></i> {{ strtoupper($pv->coupon_code) }}
                                        </div>
                                        <div class="text-muted mt-1" style="font-size:11.5px;">
                                            Worth <strong style="color:#7c3aed;">₹{{ number_format($pv->voucher_value, 2) }}</strong>
                                            @if(!empty($pv->end_date))
                                                · Valid until {{ \Carbon\Carbon::parse($pv->end_date)->format('d M Y') }}
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('cart') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold" style="font-size:11.5px;">
                                        Apply
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endif

                <!-- ── Friendly Support Strip ── -->
                {{-- <div class="pf-support-strip">
                    <div class="pf-support-left">
                        <div class="pf-support-icon">
                            <i class="bi bi-chat-heart-fill"></i>
                        </div>
                        <div class="pf-support-info">
                            <h5>Need help with frames, lenses, or prescriptions?</h5>
                            <p>Our friendly optical stylists are here for you whenever you need advice.</p>
                        </div>
                    </div>
                    <a href="tel:+919876543210" class="btn-pf-support">
                        <i class="bi bi-telephone-fill text-warning"></i> Call Our Team
                    </a>
                </div> --}}

            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
function readURL(input) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
            $('#imagePreview').hide().fadeIn(400);
        }
        reader.readAsDataURL(file);

        var formData = new FormData();
        formData.append('image', file);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: "{{ route('profile.image.update') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    if (response.image_url) {
                        $('#imagePreview').css('background-image', 'url("' + response.image_url + '")');
                        $('.profile-avatar').attr('src', response.image_url);
                        $('.mobile-user-avatar').attr('src', response.image_url);
                        $('.user-nav-toggle img').attr('src', response.image_url);
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Profile picture updated successfully!');
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Upload failed.');
                    }
                }
            },
            error: function(xhr) {
                var errMsg = 'Upload failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var firstErrKey = Object.keys(xhr.responseJSON.errors)[0];
                    if (firstErrKey && xhr.responseJSON.errors[firstErrKey][0]) {
                        errMsg = xhr.responseJSON.errors[firstErrKey][0];
                    }
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(errMsg);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: errMsg,
                        confirmButtonColor: '#00a297'
                    });
                }
            }
        });
    }
}

$(document).ready(function() {
    $(document).on('change', '#imageUpload', function() {
        readURL(this);
    });
});
</script>
@endsection
