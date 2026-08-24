@extends('website.layout.master')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════════════════
     PREMIUM MY ADDRESSES — SPECKART
     Modern responsive address book with interactive card controls & edit modal
══════════════════════════════════════════════════════════════════════════════ --}}

<style>
/* ==========================================================================
   PREMIUM ADDRESS BOOK STYLES
   ========================================================================== */
:root {
    --addr-primary: #07484A;
    --addr-primary-dark: #032729;
    --addr-teal: #00B9B9;
    --addr-teal-light: #e6f9f9;
    --addr-gold: #fbbf24;
    --addr-bg: #f8fafc;
    --addr-card-bg: #ffffff;
    --addr-border: #e2e8f0;
    --addr-text-main: #0f172a;
    --addr-text-muted: #64748b;
}

.address-page-section {
    background: linear-gradient(180deg, #f0f7f7 0%, #f8fafc 180px, #f8fafc 100%);
    min-height: 85vh;
    padding: 30px 0 70px;
    font-family: 'Poppins', sans-serif;
}

/* ── Breadcrumb Navigation ── */
.addr-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--addr-text-muted);
    margin-bottom: 22px;
}

.addr-breadcrumb a {
    color: var(--addr-text-muted);
    text-decoration: none;
    transition: color 0.2s ease;
}

.addr-breadcrumb a:hover {
    color: var(--addr-primary);
}

.addr-breadcrumb i {
    font-size: 10px;
    color: #94a3b8;
}

.addr-breadcrumb .active {
    color: var(--addr-primary);
    font-weight: 600;
}

/* ── Hero Top Header Banner ── */
.addr-hero-banner {
    background: linear-gradient(135deg, #07484A 0%, #0a5658 50%, #0c676a 100%);
    border-radius: 20px;
    padding: 28px 32px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 36px -10px rgba(7, 72, 74, 0.25);
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}

.addr-hero-banner::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -30px;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0, 185, 185, 0.25) 0%, transparent 70%);
    pointer-events: none;
}

.addr-hero-title h2 {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 4px;
    letter-spacing: -0.2px;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
}

.addr-hero-title p {
    font-size: 13.5px;
    color: #ccfbf1;
    margin: 0;
}

.btn-addr-add {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #07484A !important;
    font-weight: 700;
    font-size: 13.5px;
    padding: 11px 22px;
    border-radius: 12px;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 6px 18px rgba(245, 158, 11, 0.3);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
    cursor: pointer;
}

.btn-addr-add:hover {
    background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(245, 158, 11, 0.4);
    color: #032b2d !important;
}

/* ── Address Cards Grid ── */
.addr-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1.5px solid var(--addr-border);
    padding: 24px;
    box-shadow: 0 8px 24px rgba(7, 72, 74, 0.04);
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

.addr-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 36px rgba(7, 72, 74, 0.1);
    border-color: rgba(0, 185, 185, 0.6);
}

.addr-card.is-default-card {
    border-color: #00B9B9;
    background: #ffffff;
    box-shadow: 0 10px 28px rgba(0, 185, 185, 0.08);
}

.addr-card-top-accent {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3.5px;
    background: linear-gradient(90deg, #07484A, #00B9B9);
}

.is-default-card .addr-card-top-accent {
    background: linear-gradient(90deg, #00B9B9, #10b981);
    height: 4.5px;
}

/* Card Header */
.addr-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
}

.addr-recipient-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--addr-text-main);
    margin: 0 0 6px;
    line-height: 1.25;
}

.addr-badges-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.addr-type-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    line-height: 1.2;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.type-home {
    background: #eff6ff;
    color: #2563eb;
}

.type-office {
    background: #f5f3ff;
    color: #7c3aed;
}

.type-other {
    background: #f1f5f9;
    color: #475569;
}

.addr-default-pill {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #059669;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* 3-Dots Action Button */
.addr-action-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.addr-action-btn:hover, .addr-action-btn[aria-expanded="true"] {
    background: #07484A;
    color: #ffffff;
    border-color: #07484A;
}

.addr-dropdown-menu {
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    padding: 6px;
    min-width: 170px;
}

.addr-dropdown-item {
    font-size: 13px;
    font-weight: 600;
    padding: 8px 12px;
    border-radius: 8px;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.15s ease;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
}

.addr-dropdown-item:hover {
    background: #f1f5f9;
    color: var(--addr-primary);
}

.addr-dropdown-item.item-delete:hover {
    background: #fee2e2;
    color: #dc2626;
}

/* Card Body */
.addr-card-body {
    margin-bottom: 16px;
    flex-grow: 1;
}

.addr-full-text {
    font-size: 13.5px;
    color: #475569;
    line-height: 1.55;
    margin-bottom: 12px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
}

.addr-full-text i {
    color: #00B9B9;
    margin-top: 2px;
    font-size: 15px;
    flex-shrink: 0;
}

.addr-phone-text {
    font-size: 13px;
    font-weight: 600;
    color: var(--addr-text-main);
    display: flex;
    align-items: center;
    gap: 8px;
}

.addr-phone-text i {
    color: #07484A;
    font-size: 14px;
}

/* Card Footer */
.addr-card-footer {
    border-top: 1px solid #f1f5f9;
    padding-top: 14px;
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.btn-card-edit {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--addr-primary);
    background: #f0fdfa;
    border: 1px solid #ccfbf1;
    padding: 6px 14px;
    border-radius: 8px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-card-edit:hover {
    background: var(--addr-primary);
    color: #ffffff !important;
    border-color: var(--addr-primary);
}

/* ── Empty State ── */
.addr-empty-state {
    background: #ffffff;
    border: 1.5px dashed #cbd5e1;
    border-radius: 24px;
    padding: 60px 24px;
    text-align: center;
    max-width: 640px;
    margin: 20px auto;
}

.addr-empty-icon-wrap {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e6f9f9 0%, #ccfbf1 100%);
    color: #0f766e;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin-bottom: 20px;
    box-shadow: 0 10px 24px rgba(15, 118, 110, 0.12);
}

.addr-empty-state h4 {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 8px;
}

.addr-empty-state p {
    font-size: 13.5px;
    color: #64748b;
    max-width: 420px;
    margin: 0 auto 24px;
    line-height: 1.5;
}

/* ── Modal Styling ── */
.addr-modal .modal-content {
    border-radius: 22px;
    border: none;
    box-shadow: 0 24px 60px rgba(7, 72, 74, 0.25);
    overflow: hidden;
}

.addr-modal .modal-header {
    background: linear-gradient(135deg, #07484A 0%, #0a5658 100%);
    color: #ffffff;
    padding: 18px 24px;
    border-bottom: none;
}

.addr-modal .modal-title {
    font-size: 17px;
    font-weight: 700;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
}

.addr-modal .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.addr-modal .modal-body {
    padding: 24px;
}

.addr-form-label {
    font-size: 12.5px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
    display: block;
}

.addr-form-control {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13.5px;
    color: #0f172a;
    transition: all 0.2s ease;
}

.addr-form-control:focus {
    border-color: #00B9B9;
    box-shadow: 0 0 0 3px rgba(0, 185, 185, 0.15);
    outline: none;
}

/* Address Type Selector Pills in Modal */
.addr-type-selector {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.addr-type-selector input[type="radio"] {
    display: none;
}

.addr-type-selector label {
    padding: 8px 18px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.addr-type-selector input[type="radio"]:checked + label {
    background: #07484A;
    color: #ffffff;
    border-color: #07484A;
    box-shadow: 0 4px 12px rgba(7, 72, 74, 0.2);
}

.addr-modal .modal-footer {
    background: #f8fafc;
    border-top: 1px solid #edf2f7;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* ── Responsive ── */
@media (max-width: 767px) {
    .address-page-section {
        padding: 20px 0 50px;
    }
    .addr-hero-banner {
        padding: 22px 20px;
    }
    .btn-addr-add {
        width: 100%;
        justify-content: center;
    }
    .addr-card {
        padding: 18px;
    }
}
</style>

<div class="address-page-section">
    <div class="container">

        <!-- Breadcrumbs -->
        <div class="addr-breadcrumb">
            <a href="{{ route('home') }}"><i class="bi bi-house-door-fill"></i> Home</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('profile') }}">My Account</a>
            <i class="bi bi-chevron-right"></i>
            <span class="active">Saved Addresses</span>
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

        <!-- Hero Banner Header -->
        <div class="addr-hero-banner">
            <div class="addr-hero-title">
                <h2><i class="bi bi-geo-alt-fill text-warning"></i> My Saved Addresses</h2>
                <p>Manage your home, office, and other shipping locations for swift checkout</p>
            </div>
            <button class="btn-addr-add" onclick="resetForm()" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Add New Address</span>
            </button>
        </div>

        <!-- Addresses Grid -->
        <div class="row g-4">

            @forelse ($addresses as $address)
                @php
                    $isDefault = !empty($address->is_default);
                    $addrType = strtolower($address->address_type ?? ($address->type ?? 'home'));
                    $typeIcon = 'bi-house-door-fill';
                    $typeClass = 'type-home';

                    if ($addrType === 'office' || $addrType === 'work') {
                        $typeIcon = 'bi-building-fill';
                        $typeClass = 'type-office';
                    } elseif ($addrType === 'other') {
                        $typeIcon = 'bi-geo-alt-fill';
                        $typeClass = 'type-other';
                    }

                    $displayName = $address->full_name ?? trim(($address->first_name ?? '') . ' ' . ($address->last_name ?? ''));
                    if (empty($displayName)) {
                        $displayName = auth()->user()->name ?? 'Recipient';
                    }

                    $formattedAddress = $address->full_address ?? trim(($address->address_line_1 ?? $address->house_no ?? '') . ', ' . ($address->address_line_2 ?? $address->road_area ?? '') . ' ' . ($address->city ?? '') . ' ' . ($address->state ?? '') . ' - ' . ($address->pincode ?? ''));
                @endphp

                <div class="col-lg-4 col-md-6">
                    <div class="addr-card {{ $isDefault ? 'is-default-card' : '' }}">
                        <div class="addr-card-top-accent"></div>

                        <!-- Card Header -->
                        <div class="addr-card-header">
                            <div>
                                <h5 class="addr-recipient-name">{{ $displayName }}</h5>
                                <div class="addr-badges-wrap">
                                    <span class="addr-type-pill {{ $typeClass }}">
                                        <i class="bi {{ $typeIcon }}"></i> {{ ucfirst($addrType) }}
                                    </span>
                                    @if ($isDefault)
                                        <span class="addr-default-pill">
                                            <i class="bi bi-patch-check-fill"></i> Default
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- 3-Dots Action Dropdown -->
                            <div class="dropdown">
                                <button class="addr-action-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Address Actions">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end addr-dropdown-menu">
                                    @if(!$isDefault && isset($address->id))
                                        <li>
                                            <form method="POST" action="{{ route('address.default', $address->id) }}">
                                                @csrf
                                                <button type="submit" class="addr-dropdown-item">
                                                    <i class="bi bi-check2-circle text-success"></i> Set as Default
                                                </button>
                                            </form>
                                        </li>
                                    @endif

                                    <li>
                                        <button type="button" class="addr-dropdown-item" onclick='editAddress(@json($address))'>
                                            <i class="bi bi-pencil-square text-info"></i> Edit Address
                                        </button>
                                    </li>

                                    @if(isset($address->id))
                                        <li>
                                            <form method="POST" action="{{ route('delete_address', $address->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="addr-dropdown-item item-delete" onclick="return confirm('Delete this saved address?')">
                                                    <i class="bi bi-trash3-fill text-danger"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="addr-card-body">
                            <div class="addr-full-text">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>{{ $formattedAddress }}</span>
                            </div>

                            <div class="addr-phone-text">
                                <i class="bi bi-telephone-fill"></i>
                                <span>{{ $address->phone }}</span>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="addr-card-footer">
                            <span class="text-muted small">
                                <i class="bi bi-truck text-teal me-1"></i> Delivery Available
                            </span>
                            <button type="button" class="btn-card-edit" onclick='editAddress(@json($address))'>
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="addr-empty-state">
                        <div class="addr-empty-icon-wrap">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h4>No Saved Addresses Yet</h4>
                        <p>Save your home, office, or other delivery locations once for smooth 1-click checkout on all orders.</p>
                        <button class="btn-addr-add" onclick="resetForm()" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="bi bi-plus-circle-fill"></i> Add Your First Address
                        </button>
                    </div>
                </div>
            @endforelse

        </div>

    </div>
</div>

<!-- ==========================================================================
     MODAL: ADD / EDIT ADDRESS
     ========================================================================== -->
<div class="modal fade addr-modal" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="bi bi-geo-alt-fill text-warning"></i> Add New Address
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="addressForm" method="POST" action="{{ route('store_address') }}">
                @csrf
                <input type="hidden" id="address_id" name="address_id">

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Recipient Names -->
                        <div class="col-md-6">
                            <label class="addr-form-label">First Name *</label>
                            <input type="text" class="form-control addr-form-control" name="first_name" placeholder="e.g. Rahul" value="{{ old('first_name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="addr-form-label">Last Name</label>
                            <input type="text" class="form-control addr-form-control" name="last_name" placeholder="e.g. Sharma" value="{{ old('last_name') }}">
                        </div>

                        <!-- Mobile Number & Country -->
                        <div class="col-md-6">
                            <label class="addr-form-label">Mobile Number *</label>
                            <input type="tel" class="form-control addr-form-control" name="phone" placeholder="10-digit mobile number" maxlength="10" value="{{ old('phone') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="addr-form-label">Country</label>
                            <input type="text" class="form-control addr-form-control" name="country" value="India" readonly style="background:#f8fafc;">
                        </div>

                        <!-- Address Line 1 -->
                        <div class="col-12">
                            <label class="addr-form-label">Flat, House No., Building, Apartment *</label>
                            <input type="text" class="form-control addr-form-control" name="address_line_1" placeholder="e.g. Flat 402, Sunshine Heights" value="{{ old('address_line_1') }}" required>
                        </div>

                        <!-- Address Line 2 -->
                        <div class="col-12">
                            <label class="addr-form-label">Area, Street, Sector, Village (Optional)</label>
                            <input type="text" class="form-control addr-form-control" name="address_line_2" placeholder="e.g. Near City Mall, MG Road" value="{{ old('address_line_2') }}">
                        </div>

                        <!-- City, State & Pincode -->
                        <div class="col-md-4">
                            <label class="addr-form-label">City / Town *</label>
                            <input type="text" class="form-control addr-form-control" name="city" placeholder="e.g. Mumbai" value="{{ old('city') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="addr-form-label">State *</label>
                            <input type="text" class="form-control addr-form-control" name="state" placeholder="e.g. Maharashtra" value="{{ old('state') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="addr-form-label">Pincode *</label>
                            <input type="text" class="form-control addr-form-control" name="pincode" placeholder="6 digits" maxlength="6" value="{{ old('pincode') }}" required>
                        </div>

                        <!-- Address Type Selector -->
                        <div class="col-12 mt-3">
                            <label class="addr-form-label">Address Type</label>
                            <div class="addr-type-selector">
                                <input type="radio" name="type" value="home" id="add_type_home" checked>
                                <label for="add_type_home">
                                    <i class="bi bi-house-door-fill"></i> Home
                                </label>

                                <input type="radio" name="type" value="office" id="add_type_office">
                                <label for="add_type_office">
                                    <i class="bi bi-building-fill"></i> Office / Commercial
                                </label>

                                <input type="radio" name="type" value="other" id="add_type_other">
                                <label for="add_type_other">
                                    <i class="bi bi-geo-alt-fill"></i> Other
                                </label>
                            </div>
                        </div>

                        <!-- Default Checkbox -->
                        <div class="col-12 mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="add_is_default" name="is_default" value="1">
                                <label class="form-check-label fw-semibold text-dark small" for="add_is_default">
                                    Set as default shipping address
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-3 px-3 fw-semibold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-addr-add" id="submitBtn">
                        <i class="bi bi-check2-circle"></i> Save Address
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function editAddress(address) {
    let modal = new bootstrap.Modal(document.getElementById('addAddressModal'));
    modal.show();

    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square text-warning"></i> Edit Address';
    document.getElementById('submitBtn').innerHTML = '<i class="bi bi-check2-circle"></i> Update Address';

    let form = document.getElementById('addressForm');
    let updateUrl = "{{ route('update_address', ':id') }}";
    form.action = updateUrl.replace(':id', address.id);

    document.querySelector('[name="first_name"]').value = address.first_name ?? (address.full_name ? address.full_name.split(' ')[0] : '');
    document.querySelector('[name="last_name"]').value = address.last_name ?? (address.full_name && address.full_name.split(' ').length > 1 ? address.full_name.split(' ').slice(1).join(' ') : '');
    document.querySelector('[name="phone"]').value = address.phone ?? '';
    document.querySelector('[name="address_line_1"]').value = address.address_line_1 ?? (address.house_no ?? '');
    document.querySelector('[name="address_line_2"]').value = address.address_line_2 ?? (address.road_area ?? '');
    document.querySelector('[name="city"]').value = address.city ?? '';
    document.querySelector('[name="state"]').value = address.state ?? '';
    document.querySelector('[name="pincode"]').value = address.pincode ?? '';

    let addrType = (address.address_type || address.type || 'home').toLowerCase();
    if (document.getElementById('add_type_' + addrType)) {
        document.getElementById('add_type_' + addrType).checked = true;
    } else {
        document.getElementById('add_type_home').checked = true;
    }

    document.getElementById('add_is_default').checked = (address.is_default == 1 || address.is_default === true);
    document.getElementById('address_id').value = address.id;
}

function resetForm() {
    const form = document.getElementById('addressForm');
    form.reset();
    form.action = "{{ route('store_address') }}";

    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-geo-alt-fill text-warning"></i> Add New Address';
    document.getElementById('submitBtn').innerHTML = '<i class="bi bi-check2-circle"></i> Save Address';
    document.getElementById('address_id').value = '';
    document.getElementById('add_type_home').checked = true;
    document.getElementById('add_is_default').checked = false;
}
</script>

@if ($errors->any())
<script>
document.addEventListener("DOMContentLoaded", function() {
    var myModal = new bootstrap.Modal(document.getElementById("addAddressModal"));

    @if (old('address_id'))
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square text-warning"></i> Edit Address';
        document.getElementById('submitBtn').innerHTML = '<i class="bi bi-check2-circle"></i> Update Address';
        let updateUrl = "{{ route('update_address', ':id') }}";
        document.getElementById('addressForm').action = updateUrl.replace(':id', "{{ old('address_id') }}");
        document.getElementById('address_id').value = "{{ old('address_id') }}";
    @else
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-geo-alt-fill text-warning"></i> Add New Address';
        document.getElementById('submitBtn').innerHTML = '<i class="bi bi-check2-circle"></i> Save Address';
        document.getElementById('addressForm').action = "{{ route('store_address') }}";
    @endif

    myModal.show();
});
</script>
@endif

@endsection
