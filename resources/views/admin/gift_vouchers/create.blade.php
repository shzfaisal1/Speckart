@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.voucher-form-wrap {
    font-family: 'Inter', sans-serif;
    color: #1a1d29;
    padding: 0 8px 40px;
}
.voucher-form-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    padding: 22px 26px;
    margin-bottom: 20px;
}
.voucher-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #07484A;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 10px;
}
.voucher-card-title i {
    color: #7e22ce;
    font-size: 16px;
}
.form-label-custom {
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 6px;
}
.required-star {
    color: #ef4444;
}
.form-hint {
    font-size: 11.5px;
    color: #64748b;
    margin-top: 4px;
}
.input-with-unit {
    position: relative;
    display: flex;
    align-items: center;
}
.input-with-unit input {
    padding-left: 28px;
    font-weight: 600;
    font-size: 14px;
}
.input-unit-symbol {
    position: absolute;
    left: 10px;
    color: #64748b;
    font-weight: 600;
    font-size: 13px;
}

/* Radio Cards */
.radio-card-wrap {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.radio-card-label {
    flex: 1;
    min-width: 140px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    font-size: 13px;
    font-weight: 500;
}
.radio-card-label:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.radio-card-label.active {
    border-color: #7e22ce;
    background: #faf5ff;
    color: #6b21a8;
    font-weight: 600;
    box-shadow: 0 0 0 1px #7e22ce;
}
.radio-card-label input[type="radio"] {
    accent-color: #7e22ce;
}

/* Status & Guards Toggle */
.status-toggle {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 22px;
    margin: 0;
    vertical-align: middle;
}
.status-toggle input { opacity: 0; width: 0; height: 0; }
.status-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    border-radius: 22px;
    transition: all 0.3s ease;
}
.status-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: all 0.3s ease;
}
.status-toggle input:checked + .status-slider { background-color: #10b981; }
.status-toggle input:checked + .status-slider:before { transform: translateX(22px); }

/* Live Digital Gift Card Mockup */
.gift-card-preview {
    background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 60%, #7e22ce 100%);
    border-radius: 16px;
    padding: 24px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(76,29,149,0.35);
    margin-bottom: 20px;
}
.gift-card-preview::after {
    content: "";
    position: absolute;
    top: -50px;
    right: -50px;
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
}
.card-brand-tag {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #e9d5ff;
    font-weight: 700;
}
.card-val-display {
    font-size: 32px;
    font-weight: 800;
    margin: 14px 0 6px;
    letter-spacing: -0.5px;
    color: #ffffff;
}
.card-code-display {
    background: rgba(0,0,0,0.3);
    border: 1px dashed rgba(255,255,255,0.4);
    border-radius: 8px;
    padding: 6px 12px;
    font-family: monospace;
    font-size: 13px;
    letter-spacing: 1px;
    display: inline-block;
    color: #fef08a;
    font-weight: 700;
}
.card-footer-info {
    font-size: 11px;
    color: #ddd6fe;
    margin-top: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Product Search Table */
.product-search-box {
    position: relative;
    margin-bottom: 12px;
}
.product-search-box .search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}
.product-search-box input {
    padding-left: 36px;
    border-radius: 8px;
}
.product-thumb {
    width: 32px;
    height: 32px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}
.selected-count-badge {
    background: #ecfdf5;
    color: #047857;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 20px;
    border: 1px solid #a7f3d0;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
}

/* Wizard Action Buttons */
.wizard-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 10px;
}
.btn-save-draft {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.2s ease;
}
.btn-save-draft:hover { background: #e2e8f0; color: #1e293b; }
.btn-save-activate {
    background: linear-gradient(135deg, #07484A, #0a5e60);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 24px;
    font-weight: 600;
    font-size: 13px;
    box-shadow: 0 4px 12px rgba(7,72,74,0.25);
    transition: all 0.2s ease;
}
.btn-save-activate:hover { color: #fff; box-shadow: 0 6px 16px rgba(7,72,74,0.35); transform: translateY(-1px); }
</style>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid voucher-form-wrap">

            <!-- Breadcrumbs / Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-1" style="font-weight:700;color:#07484A;">
                        <i class="fa fa-ticket text-purple me-2"></i>{{ isset($voucher) ? 'Edit Gift Voucher' : 'Create Gift Voucher' }}
                    </h4>
                    <p class="text-muted mb-0" style="font-size:12.5px;">Setup dedicated gift voucher campaigns with targeted membership & product eligibility</p>
                </div>
                <a href="{{ url(config('app.admin_path') . '/gift-vouchers') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;">
                    <i class="fa fa-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <form id="voucherForm">
                @csrf
                @if(isset($voucher))
                    <input type="hidden" name="id" value="{{ $voucher->id }}">
                @endif

                <div class="row">

                    <!-- ── LEFT COLUMN: VOUCHER CONFIGURATION (70%) ── -->
                    <div class="col-lg-8">

                        <!-- 1. Voucher Basics -->
                        <div class="voucher-form-card">
                            <div class="voucher-card-title">
                                <i class="fa fa-info-circle"></i> 1. Voucher Basics
                            </div>

                            <div class="mb-3">
                                <label class="form-label-custom">Voucher Campaign Name <span class="required-star">*</span></label>
                                <input type="text" class="form-control" id="voucher_name" name="name"
                                       placeholder="e.g. Gold Member ₹1000 Welcome Voucher"
                                       value="{{ $voucher->name ?? old('name') }}" required>
                                <div class="form-hint">Internal name for this campaign</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-custom">Voucher Value (₹) <span class="required-star">*</span></label>
                                    <div class="input-with-unit">
                                        <span class="input-unit-symbol">₹</span>
                                        <input type="number" class="form-control" id="voucher_value" name="voucher_value"
                                               placeholder="1000" min="1" step="0.01"
                                               value="{{ $voucher->voucher_value ?? old('voucher_value', 1000) }}" required>
                                    </div>
                                    <div class="form-hint">Face value deducted from customer's order</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-custom">Voucher Code <span class="required-star">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="voucher_code" name="code"
                                               placeholder="GV-GOLD1000" style="text-transform:uppercase;font-family:monospace;font-weight:600;"
                                               value="{{ $voucher->code ?? old('code') }}" required>
                                        <button class="btn btn-outline-secondary" type="button" id="btnGenerateCode" title="Generate Code">
                                            <i class="fa fa-bolt text-warning"></i> Auto
                                        </button>
                                    </div>
                                    <div class="form-hint">Unique code customer enters at checkout</div>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label-custom">Description (Optional)</label>
                                <textarea class="form-control" name="description" rows="2" placeholder="e.g. Special welcome perk for Gold VIP members">{{ $voucher->description ?? old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- 2. Membership & Audience Targeting -->
                        <div class="voucher-form-card">
                            <div class="voucher-card-title">
                                <i class="fa fa-crown text-warning"></i> 2. Membership & Audience Targeting
                            </div>

                            <label class="form-label-custom">Which Membership / User Type can use this Voucher? <span class="required-star">*</span></label>
                            <div class="radio-card-wrap mb-3">
                                <label class="radio-card-label {{ (!isset($voucher) || $voucher->membership_scope === 'all_users') ? 'active' : '' }}" for="m_scope_all">
                                    <input type="radio" name="membership_scope" id="m_scope_all" value="all_users"
                                           {{ (!isset($voucher) || $voucher->membership_scope === 'all_users') ? 'checked' : '' }}>
                                    <span>All Users</span>
                                </label>
                                <label class="radio-card-label {{ (isset($voucher) && $voucher->membership_scope === 'any_membership') ? 'active' : '' }}" for="m_scope_any">
                                    <input type="radio" name="membership_scope" id="m_scope_any" value="any_membership"
                                           {{ (isset($voucher) && $voucher->membership_scope === 'any_membership') ? 'checked' : '' }}>
                                    <span>Any Active Membership</span>
                                </label>
                                <label class="radio-card-label {{ (isset($voucher) && $voucher->membership_scope === 'specific_membership') ? 'active' : '' }}" for="m_scope_specific">
                                    <input type="radio" name="membership_scope" id="m_scope_specific" value="specific_membership"
                                           {{ (isset($voucher) && $voucher->membership_scope === 'specific_membership') ? 'checked' : '' }}>
                                    <span>Specific Membership Plan</span>
                                </label>
                            </div>

                            <!-- Specific Membership Selector -->
                            <div id="panel_specific_membership" style="display: {{ (isset($voucher) && $voucher->membership_scope === 'specific_membership') ? 'block' : 'none' }};">
                                <label class="form-label-custom">Select Membership Plan <span class="required-star">*</span></label>
                                <select class="form-select" id="membership_card_id" name="membership_card_id">
                                    <option value="">-- Choose Membership Plan --</option>
                                    @foreach($memberships as $card)
                                        <option value="{{ $card->card_id }}" {{ (isset($voucher) && $voucher->membership_card_id == $card->card_id) ? 'selected' : '' }}>
                                            {{ $card->card_name }} (₹{{ number_format($card->price, 0) }} / {{ $card->validity_days ?? 365 }} Days)
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-hint">Only customers who have this membership active or in cart can use this voucher</div>
                            </div>
                        </div>

                        <!-- 3. Business Rules & Margin Guards -->
                        <div class="voucher-form-card">
                            <div class="voucher-card-title">
                                <i class="fa fa-shield text-danger"></i> 3. Business Rules & Margin Guards
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-custom">Min. Net Cart Value (₹)</label>
                                    <div class="input-with-unit">
                                        <span class="input-unit-symbol">₹</span>
                                        <input type="number" class="form-control" id="min_cart_amount" name="min_cart_amount"
                                               placeholder="0.00" min="0" step="0.01"
                                               value="{{ $voucher->min_cart_amount ?? old('min_cart_amount') }}">
                                    </div>
                                    <div class="form-hint">Net cart amount required after all frame discounts (Leave 0 for no minimum)</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-custom">Validity Duration (Days)</label>
                                    <input type="number" class="form-control" id="validity_days" name="validity_days"
                                           placeholder="30" min="1"
                                           value="{{ $voucher->validity_days ?? old('validity_days', 30) }}">
                                    <div class="form-hint">Days valid from issue date</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                           value="{{ isset($voucher) && $voucher->start_date ? $voucher->start_date->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="{{ isset($voucher) && $voucher->end_date ? $voucher->end_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>

                            <!-- Anti-Stacking Guards -->
                            <div class="p-3 mb-0" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <div style="font-size:13px;font-weight:700;color:#0f172a;">
                                            <i class="fa fa-lock text-danger me-1"></i> Allow Stacking with BOGO Free Frames?
                                        </div>
                                        <div style="font-size:11.5px;color:#64748b;">
                                            When <strong>OFF</strong>, customers cannot combine a 100% Free BOGO frame with this voucher (Prevents ₹0 free carts)
                                        </div>
                                    </div>
                                    <label class="status-toggle mb-0">
                                        <input type="checkbox" id="allow_bogo_stacking" name="allow_bogo_stacking" value="1"
                                               {{ (isset($voucher) && $voucher->allow_bogo_stacking) ? 'checked' : '' }}>
                                        <span class="status-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Apply Voucher On (BOGO-style Product Eligibility) -->
                        <div class="voucher-form-card">
                            <div class="voucher-card-title">
                                <i class="fa fa-tags text-primary"></i> 4. Apply Voucher On (Product Scope)
                            </div>

                            <div class="radio-card-wrap mb-3">
                                <label class="radio-card-label {{ (!isset($voucher) || $voucher->apply_on === 'all_products') ? 'active' : '' }}" for="apply_all">
                                    <input type="radio" name="apply_on" id="apply_all" value="all_products"
                                           {{ (!isset($voucher) || $voucher->apply_on === 'all_products') ? 'checked' : '' }}>
                                    <span>All Products</span>
                                </label>
                                <label class="radio-card-label {{ (isset($voucher) && $voucher->apply_on === 'specific_category') ? 'active' : '' }}" for="apply_cat">
                                    <input type="radio" name="apply_on" id="apply_cat" value="specific_category"
                                           {{ (isset($voucher) && $voucher->apply_on === 'specific_category') ? 'checked' : '' }}>
                                    <span>Specific Category</span>
                                </label>
                                <label class="radio-card-label {{ (isset($voucher) && $voucher->apply_on === 'specific_brand') ? 'active' : '' }}" for="apply_brand">
                                    <input type="radio" name="apply_on" id="apply_brand" value="specific_brand"
                                           {{ (isset($voucher) && $voucher->apply_on === 'specific_brand') ? 'checked' : '' }}>
                                    <span>Specific Brand</span>
                                </label>
                                <label class="radio-card-label {{ (isset($voucher) && $voucher->apply_on === 'specific_products') ? 'active' : '' }}" for="apply_prod">
                                    <input type="radio" name="apply_on" id="apply_prod" value="specific_products"
                                           {{ (isset($voucher) && $voucher->apply_on === 'specific_products') ? 'checked' : '' }}>
                                    <span>Specific Products</span>
                                </label>
                            </div>

                            <!-- Category Selector -->
                            <div id="panel_category" style="display: {{ (isset($voucher) && $voucher->apply_on === 'specific_category') ? 'block' : 'none' }};" class="mb-3">
                                <label class="form-label-custom">Select Categories <span class="required-star">*</span></label>
                                <select class="form-control" id="select_categories" name="categories[]" multiple>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ (isset($voucher) && is_array($voucher->category_ids) && in_array($cat->id, $voucher->category_ids)) ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Brand Selector -->
                            <div id="panel_brand" style="display: {{ (isset($voucher) && $voucher->apply_on === 'specific_brand') ? 'block' : 'none' }};" class="mb-3">
                                <label class="form-label-custom">Select Brands <span class="required-star">*</span></label>
                                <select class="form-control" id="select_brands" name="brands[]" multiple>
                                    @foreach($brands as $b)
                                        <option value="{{ $b->brand_id }}" {{ (isset($voucher) && is_array($voucher->brand_ids) && in_array($b->brand_id, $voucher->brand_ids)) ? 'selected' : '' }}>
                                            {{ $b->brand_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Specific Product Picker Table -->
                            <div id="panel_products" style="display: {{ (isset($voucher) && $voucher->apply_on === 'specific_products') ? 'block' : 'none' }};">
                                <div class="product-search-box">
                                    <span class="search-icon"><i class="fa fa-search"></i></span>
                                    <input type="text" class="form-control" id="productSearchInput" placeholder="Search products by name, SKU, or brand...">
                                </div>
                                <div style="max-height:240px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:8px;">
                                    <table class="table table-sm table-hover mb-0" id="productSelectTable">
                                        <thead style="background:#f8fafc;position:sticky;top:0;">
                                            <tr>
                                                <th style="width:36px"><input type="checkbox" class="form-check-input" id="selectAllProducts"></th>
                                                <th style="width:40px">Image</th>
                                                <th>Product Name</th>
                                                <th>SKU</th>
                                                <th>Brand</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productTableBody">
                                            <tr><td colspan="6" class="text-center text-muted py-3">Type above to search products...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="selected-count-badge" id="selectedProductsBadge" style="display:none;">
                                    <i class="fa fa-check-circle"></i> <span id="selectedProductsCount">0</span> products selected
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ── RIGHT COLUMN: LIVE PREVIEW & ACTIONS (30%) ── -->
                    <div class="col-lg-4">

                        <!-- Digital Gift Card Mockup -->
                        <div class="gift-card-preview">
                            <div class="card-brand-tag">Speckarts Gift Card</div>
                            <div class="card-val-display">₹<span id="preview_card_val">{{ number_format($voucher->voucher_value ?? 1000, 2) }}</span></div>
                            <div class="card-code-display" id="preview_card_code">{{ $voucher->code ?? 'GV-GOLD1000' }}</div>
                            <div class="card-footer-info">
                                <div><i class="fa fa-crown text-warning me-1"></i><span id="preview_membership_badge">All Users</span></div>
                                <div>Valid: <span id="preview_validity_days">{{ $voucher->validity_days ?? 30 }}d</span></div>
                            </div>
                        </div>

                        <!-- Rule Summary Card -->
                        <div class="voucher-form-card">
                            <div class="voucher-card-title">
                                <i class="fa fa-list-check text-success"></i> Summary
                            </div>
                            <ul class="list-unstyled mb-0" style="font-size:12.5px;color:#475569;">
                                <li class="mb-2 d-flex justify-content-between">
                                    <span>Voucher Value:</span>
                                    <strong class="text-dark">₹<span id="sum_val">{{ number_format($voucher->voucher_value ?? 1000, 2) }}</span></strong>
                                </li>
                                <li class="mb-2 d-flex justify-content-between">
                                    <span>Min. Net Order:</span>
                                    <strong class="text-dark"><span id="sum_min_cart">{{ isset($voucher) && $voucher->min_cart_amount ? '₹' . number_format($voucher->min_cart_amount, 2) : 'No Minimum' }}</span></strong>
                                </li>
                                <li class="mb-2 d-flex justify-content-between">
                                    <span>Target Membership:</span>
                                    <strong class="text-purple" id="sum_membership">All Users</strong>
                                </li>
                                <li class="mb-2 d-flex justify-content-between">
                                    <span>Product Scope:</span>
                                    <strong class="text-dark" id="sum_scope">All Products</strong>
                                </li>
                                <li class="d-flex justify-content-between">
                                    <span>BOGO Stacking:</span>
                                    <strong id="sum_bogo_stack" class="text-danger">Blocked <i class="fa fa-ban text-danger ms-1"></i></strong>
                                </li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="wizard-actions">
                            <button type="button" class="btn btn-save-draft" onclick="saveVoucher('draft')">
                                <i class="fa fa-save me-1"></i> Save Draft
                            </button>
                            <button type="button" class="btn btn-save-activate" onclick="saveVoucher('active')">
                                <i class="fa fa-check-circle me-1"></i> Save &amp; Activate
                            </button>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let selectedProducts = new Set();
let productSearchTimer = null;

$(document).ready(function () {
    $('#select_categories').select2({ placeholder: "Choose categories...", width: '100%' });
    $('#select_brands').select2({ placeholder: "Choose brands...", width: '100%' });

    // Preload selected products in edit mode
    @if(isset($voucher) && $voucher->apply_on === 'specific_products' && isset($selected_products))
        @foreach($voucher->product_ids as $pId)
            selectedProducts.add({{ $pId }});
        @endforeach
        renderProductTable(@json($selected_products));
        updateProductCount();
    @endif

    // Radio Card Label Click Style
    $('input[type="radio"]').on('change', function () {
        const name = $(this).attr('name');
        $(`input[name="${name}"]`).each(function () {
            $(this).closest('.radio-card-label').removeClass('active');
        });
        $(this).closest('.radio-card-label').addClass('active');
        updateLivePreview();
    });

    // Membership Scope Change
    $('input[name="membership_scope"]').on('change', function () {
        if ($(this).val() === 'specific_membership') {
            $('#panel_specific_membership').slideDown(200);
        } else {
            $('#panel_specific_membership').slideUp(200);
        }
        updateLivePreview();
    });

    // Apply On Change
    $('input[name="apply_on"]').on('change', function () {
        const val = $(this).val();
        $('#panel_category').toggle(val === 'specific_category');
        $('#panel_brand').toggle(val === 'specific_brand');
        $('#panel_products').toggle(val === 'specific_products');
        updateLivePreview();
    });

    // Auto-generate Code button
    $('#btnGenerateCode').on('click', function () {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let rand = '';
        for (let i = 0; i < 5; i++) rand += chars.charAt(Math.floor(Math.random() * chars.length));
        const val = $('#voucher_value').val();
        const suffix = val ? parseInt(val) : '1000';
        $('#voucher_code').val('GV-' + suffix + '-' + rand);
        updateLivePreview();
    });

    // Live update listeners
    $('#voucher_value, #voucher_code, #validity_days, #min_cart_amount, #membership_card_id, #allow_bogo_stacking').on('input change', function () {
        updateLivePreview();
    });

    // Product search AJAX
    $('#productSearchInput').on('input', function () {
        clearTimeout(productSearchTimer);
        const query = $(this).val().trim();
        if (query.length < 2) return;

        productSearchTimer = setTimeout(() => {
            $.ajax({
                url: "{{ url(config('app.admin_path') . '/offers/search-products') }}",
                data: { search: query },
                success: function (products) {
                    renderProductTable(products);
                }
            });
        }, 350);
    });

    updateLivePreview();
});

function renderProductTable(products) {
    const tbody = document.getElementById('productTableBody');
    if (!products || !products.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-3">No products found</td></tr>`;
        return;
    }

    tbody.innerHTML = products.map(p => {
        const checked = selectedProducts.has(p.id) ? 'checked' : '';
        const imgSrc  = p.product_image ? '{{ asset("") }}' + p.product_image : '{{ asset("assets/images/speckart-Icons/Dashboard.png") }}';
        return `
            <tr>
                <td><input type="checkbox" class="form-check-input product-cb" value="${p.id}" ${checked}></td>
                <td><img src="${imgSrc}" class="product-thumb" alt=""></td>
                <td><strong>${p.product_name || '—'}</strong></td>
                <td><code>${p.product_code || '—'}</code></td>
                <td>${p.Company || '—'}</td>
                <td>₹${parseFloat(p.Retail_Price || 0).toLocaleString('en-IN')}</td>
            </tr>`;
    }).join('');

    $('.product-cb').on('change', function () {
        const id = parseInt(this.value);
        if (this.checked) selectedProducts.add(id);
        else selectedProducts.delete(id);
        updateProductCount();
        updateLivePreview();
    });
}

$('#selectAllProducts').on('change', function () {
    const isChecked = this.checked;
    $('.product-cb').each(function () {
        this.checked = isChecked;
        const id = parseInt(this.value);
        if (isChecked) selectedProducts.add(id);
        else selectedProducts.delete(id);
    });
    updateProductCount();
    updateLivePreview();
});

function updateProductCount() {
    const count = selectedProducts.size;
    $('#selectedProductsCount').text(count);
    $('#selectedProductsBadge').toggle(count > 0);
}

function updateLivePreview() {
    const val = parseFloat($('#voucher_value').val() || 0);
    const code = ($('#voucher_code').val() || 'GV-GOLD1000').toUpperCase();
    const days = $('#validity_days').val() || 30;
    const minCart = parseFloat($('#min_cart_amount').val() || 0);
    const mScope = $('input[name="membership_scope"]:checked').val() || 'all_users';
    const applyOn = $('input[name="apply_on"]:checked').val() || 'all_products';
    const bogoStack = $('#allow_bogo_stacking').is(':checked');

    $('#preview_card_val, #sum_val').text(val.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
    $('#preview_card_code').text(code);
    $('#preview_validity_days').text(days + 'd');

    $('#sum_min_cart').text(minCart > 0 ? '₹' + minCart.toLocaleString('en-IN', { minimumFractionDigits: 2 }) : 'No Minimum');

    let membershipLabel = 'All Users';
    if (mScope === 'any_membership') membershipLabel = 'Any Active Member';
    else if (mScope === 'specific_membership') {
        const selText = $('#membership_card_id option:selected').text();
        membershipLabel = selText && !selText.includes('Choose') ? selText.trim() : 'Specific Plan';
    }
    $('#preview_membership_badge, #sum_membership').text(membershipLabel);

    const scopeLabels = {
        'all_products': 'All Products',
        'specific_category': 'Selected Categories',
        'specific_brand': 'Selected Brands',
        'specific_products': selectedProducts.size + ' Products'
    };
    $('#sum_scope').text(scopeLabels[applyOn] || 'All Products');

    if (bogoStack) {
        $('#sum_bogo_stack').removeClass('text-danger').addClass('text-success').html('Allowed <i class="fa fa-check text-success ms-1"></i>');
    } else {
        $('#sum_bogo_stack').removeClass('text-success').addClass('text-danger').html('Blocked <i class="fa fa-ban text-danger ms-1"></i>');
    }
}

function saveVoucher(status) {
    const name = $('#voucher_name').val().trim();
    const code = $('#voucher_code').val().trim();
    const val  = parseFloat($('#voucher_value').val());

    if (!name) {
        Swal.fire('Required', 'Please enter a Voucher Name.', 'warning');
        $('#voucher_name').focus();
        return;
    }
    if (!code) {
        Swal.fire('Required', 'Please enter a Voucher Code.', 'warning');
        $('#voucher_code').focus();
        return;
    }
    if (!val || val <= 0) {
        Swal.fire('Required', 'Please enter a valid Voucher Value.', 'warning');
        $('#voucher_value').focus();
        return;
    }

    const formData = new FormData(document.getElementById('voucherForm'));
    formData.set('status', status);

    // Append selected products if applicable
    if ($('input[name="apply_on"]:checked').val() === 'specific_products') {
        selectedProducts.forEach(id => formData.append('products[]', id));
    }

    const isEdit = "{{ isset($voucher) ? 'true' : 'false' }}" === 'true';
    const url = isEdit ? "{{ url(config('app.admin_path') . '/gift-vouchers/' . ($voucher->id ?? 0)) }}" : "{{ url(config('app.admin_path') . '/gift-vouchers') }}";

    if (isEdit) formData.append('_method', 'PUT');

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: isEdit ? 'Voucher Updated!' : 'Voucher Created!',
                html: res.message,
                confirmButtonColor: '#07484A',
                confirmButtonText: 'View Vouchers'
            }).then(() => {
                window.location.href = "{{ url(config('app.admin_path') . '/gift-vouchers') }}";
            });
        },
        error: function (xhr) {
            let errors = xhr.responseJSON?.errors;
            let msg = xhr.responseJSON?.message || 'Something went wrong.';
            if (errors) {
                msg = '<ul class="text-start mb-0 ps-3">' + Object.values(errors).flat().map(e => `<li>${e}</li>`).join('') + '</ul>';
            }
            Swal.fire('Error', msg, 'error');
        }
    });
}
</script>
@endsection
