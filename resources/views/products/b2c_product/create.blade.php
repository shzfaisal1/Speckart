@extends('layouts.master')

@section('styles')
<style>
/* ============================
   Product Builder – Premium UI
   ============================ */

:root {
    --primary: #4f46e5;
    --primary-light: #6366f1;
    --primary-dark: #3730a3;
    --accent: #06b6d4;
    --success: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    --bg: #f8fafc;
    --card: #ffffff;
    --border: #e2e8f0;
    --text: #1e293b;
    --muted: #64748b;
    --radius: 12px;
    --shadow: 0 4px 24px rgba(79,70,229,.08);
}

body { background: var(--bg); }

.pb-page { padding: 24px; min-height: 100vh; }

/* ---------- Header ---------- */
.pb-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 28px;
}
.pb-header .back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--primary);
    font-weight: 600;
    font-size: .875rem;
    text-decoration: none;
    transition: opacity .2s;
}
.pb-header .back-btn:hover { opacity: .75; }
.pb-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
}
.pb-header .badge-type {
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    color: #fff;
    border-radius: 20px;
    padding: 4px 14px;
    font-size: .75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
}

/* ---------- Layout ---------- */
.pb-layout {
    display: grid;
    grid-template-columns: 1fr 330px;
    gap: 24px;
    align-items: flex-start;
}
.pb-sidebar {
    position: sticky;
    top: 24px;
    max-height: calc(100vh - 48px);
    overflow-y: auto;
    scrollbar-width: thin;
    padding-bottom: 20px;
}
@media (max-width: 1024px) {
    .pb-layout { grid-template-columns: 1fr; }
    .pb-sidebar { position: static; max-height: none; }
}

/* ---------- Cards ---------- */
.pb-card {
    background: var(--card);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    overflow: hidden;
    margin-bottom: 20px;
}
.pb-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.pb-card-header h5 {
    margin: 0;
    font-weight: 700;
    font-size: 1rem;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
}
.pb-card-header h5 .icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: .8rem;
}
.pb-card-body { padding: 20px; }

/* ---------- Form Elements ---------- */
.pb-label {
    font-size: .8rem;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 6px;
    display: block;
}
.pb-label .req { color: var(--danger); margin-left: 2px; }

.pb-input {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: .9rem;
    color: var(--text);
    background: #fff;
    transition: border-color .2s, box-shadow .2s;
    outline: none;
}
.pb-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79,70,229,.12);
}
.pb-input.is-invalid {
    border-color: var(--danger) !important;
    background-color: #fff5f5 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
}

@keyframes fadeInShake {
    0% { opacity: 0; transform: translateY(-8px); }
    40% { opacity: 1; transform: translateX(-4px); }
    70% { transform: translateX(4px); }
    100% { transform: translateX(0); }
}

.pb-err {
    font-size: .75rem;
    color: var(--danger);
    margin-top: 4px;
    display: block;
}

/* ---------- Toggle Radio ---------- */
.pb-radio-group {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.pb-radio-btn {
    position: relative;
    cursor: pointer;
}
.pb-radio-btn input { position: absolute; opacity: 0; width: 0; height: 0; }
.pb-radio-btn span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: .85rem;
    font-weight: 500;
    color: var(--muted);
    transition: all .2s;
    user-select: none;
}
.pb-radio-btn input:checked + span {
    border-color: var(--primary);
    background: rgba(79,70,229,.07);
    color: var(--primary);
    font-weight: 600;
}

.pb-sublabel {
    font-size: .8rem;
    color: var(--muted);
    margin: -2px 0 10px;
}

/* ---------- Supported Product Types (chip cards) ---------- */
.pb-chip-group {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}
@media (max-width: 576px) {
    .pb-chip-group { grid-template-columns: 1fr; }
}
.pb-chip {
    position: relative;
    cursor: pointer;
}
.pb-chip input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
.pb-chip .chip-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 2px;
    width: 100%;
    padding: 10px 34px 10px 16px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    background: #fff;
    transition: border-color .2s, background .2s, box-shadow .2s;
    user-select: none;
    box-sizing: border-box;
}
.pb-chip .chip-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .85rem;
    font-weight: 600;
    color: var(--text);
    transition: color .2s;
}
.pb-chip .chip-icon { font-size: .95rem; line-height: 1; }
.pb-chip .chip-subtitle {
    font-size: .72rem;
    color: var(--muted);
    padding-left: 24px;
}
.pb-chip .chip-check {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 1.5px solid var(--border);
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .6rem;
    color: #fff;
    transition: all .2s;
}
.pb-chip input:checked + .chip-card {
    border-color: var(--primary);
    background: rgba(79,70,229,.07);
    box-shadow: 0 0 0 3px rgba(79,70,229,.1);
}
.pb-chip input:checked + .chip-card .chip-title { color: var(--primary); }
.pb-chip input:checked + .chip-card .chip-check {
    background: var(--primary);
    border-color: var(--primary);
}
.pb-chip input:checked + .chip-card .chip-check::after { content: '\2713'; }
.pb-chip input:focus-visible + .chip-card {
    box-shadow: 0 0 0 3px rgba(79,70,229,.2);
}
.pb-chip.disabled { cursor: default; opacity: .85; }
.pb-chip-empty {
    font-size: .82rem;
    color: var(--muted);
    padding: 8px 0;
}

/* ---------- Package Selector Panel ---------- */
.package-select-list::-webkit-scrollbar { width: 5px; }
.package-select-list::-webkit-scrollbar-track { background: rgba(0,0,0,.04); border-radius: 4px; }
.package-select-list::-webkit-scrollbar-thumb { background: rgba(79,70,229,.25); border-radius: 4px; }
.package-select-list::-webkit-scrollbar-thumb:hover { background: rgba(79,70,229,.45); }
#package-selector-panel { will-change: opacity, transform; }

/* ---------- Variants ---------- */
.variant-list { display: flex; flex-direction: column; gap: 16px; }

.variant-card {
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: box-shadow .2s;
    background: #fff;
}
.variant-card:hover { box-shadow: 0 6px 24px rgba(79,70,229,.12); }

.variant-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: linear-gradient(135deg, #f1f5ff 0%, #e0f2fe 100%);
    border-bottom: 1px solid var(--border);
    cursor: pointer;
}
.variant-card-header .v-title {
    font-weight: 700;
    font-size: .9rem;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
}
.variant-card-header .v-num {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: var(--primary);
    color: #fff;
    font-size: .75rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.variant-card-header .v-actions { display: flex; gap: 8px; }
.btn-remove-variant {
    background: rgba(239,68,68,.1);
    color: var(--danger);
    border: none;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: .78rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
}
.btn-remove-variant:hover { background: rgba(239,68,68,.2); }

.variant-card-body { padding: 16px; }
.variant-card-body .row-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 14px;
}

.variant-section-title {
    font-size: .78rem;
    font-weight: 700;
    color: var(--primary);
    text-transform: uppercase;
    letter-spacing: .06em;
    margin: 16px 0 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.variant-section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(to right, var(--primary), transparent);
}

/* ---------- Add Variant Button ---------- */
.btn-add-variant {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: .875rem;
    cursor: pointer;
    transition: opacity .2s, transform .15s;
    box-shadow: 0 4px 12px rgba(79,70,229,.3);
}
.btn-add-variant:hover { opacity: .9; transform: translateY(-1px); }
.btn-add-variant:active { transform: none; }

/* ---------- Sidebar ---------- */
.sb-card { margin-bottom: 16px; }

/* ---------- Submit Button ---------- */
.btn-submit-product {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: opacity .2s, transform .15s;
    box-shadow: 0 6px 20px rgba(79,70,229,.35);
    letter-spacing: .02em;
}
.btn-submit-product:hover { opacity: .92; transform: translateY(-1px); }

/* ---------- Image Preview ---------- */
.img-preview-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
}
.img-thumb {
    width: 72px; height: 72px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid var(--border);
}

/* ---------- Autocomplete dropdown ---------- */
.ac-dropdown {
    position: absolute;
    z-index: 9999;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 8px;
    box-shadow: var(--shadow);
    max-height: 180px;
    overflow-y: auto;
    width: 100%;
}
.ac-dropdown a {
    display: block;
    padding: 8px 14px;
    font-size: .875rem;
    color: var(--text);
    cursor: pointer;
    text-decoration: none;
}
.ac-dropdown a:hover { background: #f1f5ff; }

/* ---------- Alert ---------- */
.pb-alert {
    background: rgba(239,68,68,.08);
    border: 1px solid rgba(239,68,68,.3);
    border-radius: 8px;
    padding: 10px 14px;
    font-size: .85rem;
    color: var(--danger);
    margin-bottom: 16px;
}

/* ---------- Promotional Tags ---------- */
.promo-tags-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.promo-tag-item {
    position: relative;
    cursor: pointer;
    flex: 1 1 auto;
    min-width: 130px;
}
.promo-tag-item input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}
.promo-tag-item span {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 9px 16px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: .85rem;
    font-weight: 500;
    color: var(--muted);
    transition: all .2s;
    user-select: none;
    background: #fff;
    text-align: center;
    white-space: nowrap;
}
.promo-tag-item input:checked + span {
    border-color: var(--primary);
    background: rgba(79,70,229,.07);
    color: var(--primary);
    font-weight: 600;
}
.promo-tag-item span .tag-icon {
    font-size: .95rem;
}
</style>
@endsection

@section('content')
@php
    $isEdit    = isset($variants) && $variants->isNotEmpty();
    $editFirst = $isEdit ? $first : null;
@endphp

<div class="pb-page">

    {{-- Header --}}
    <div class="pb-header">
        <a href="{{ route('admin.catalog.index') }}" class="back-btn">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h2>{{ $isEdit ? 'Edit B2C' : 'Create B2C' }} Product</h2>
        <span class="badge-type" id="badge-type">{{ $type }}</span>
    </div>

    {{-- Flash errors --}}
    <div id="pb-global-errors" class="pb-alert" style="display:none;"></div>

    <form id="pb-form" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="product_type" id="product_type" value="{{ $type }}">
        @if($isEdit)
        <input type="hidden" name="_method" value="PUT">
        @endif

        <div class="pb-layout">

            {{-- ===== LEFT COLUMN ===== --}}
            <div>

                {{-- Product Information --}}
                <div class="pb-card">
                    <div class="pb-card-header">
                        <h5><span class="icon"><i class="fa fa-tag"></i></span> Product Information</h5>
                    </div>
                    <div class="pb-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="pb-label">Product Code</label>
                                <input type="text" name="product_code_master" id="product_code_master"
                                    class="pb-input" placeholder="e.g. PC-2024-001"
                                    value="{{ $editFirst?->parent_product_code ?? '' }}">
                                <span class="pb-err" id="product_code_masterError"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="pb-label">Product Name <span class="req">*</span></label>
                                <input type="text" name="product_name" id="product_name"
                                    class="pb-input" placeholder="e.g. Aviator Classic"
                                    value="{{ $editFirst?->product_name ?? '' }}">
                                <span class="pb-err" id="product_nameError"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="pb-label">Brand / Company</label>
                                <select name="Company" id="Company" class="pb-input">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->brand_name }}" {{ ($editFirst?->Company ?? '') == $brand->brand_name ? 'selected' : '' }}>
                                            {{ $brand->brand_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="pb-err" id="CompanyError"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="pb-label">Category</label>
                                <select name="category_id" id="category_id" class="pb-input" onchange="loadSubcategories(this.value); updateVariantFields();">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        @php
                                            // Detect lens category from name OR allowed_filters from DB
                                            $catFilters = $category->allowed_filters ?? [];
                                            $nameLower  = strtolower($category->name);
                                            $isLensCategory = str_contains($nameLower, 'lens')
                                                || str_contains($nameLower, 'lense')
                                                || str_contains($nameLower, 'contact')
                                                || in_array('modality', $catFilters);
                                        @endphp
                                        <option value="{{ $category->id }}"
                                            data-is-lens="{{ $isLensCategory ? '1' : '0' }}"
                                            {{ ($editFirst?->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="pb-err" id="category_idError"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="pb-label">Subcategory</label>
                                <select name="subcategory_id" id="subcategory_id" class="pb-input">
                                    <option value="">Select Subcategory</option>
                                </select>
                                <span class="pb-err" id="subcategory_idError"></span>
                            </div>
                             <div class="col-md-6 mb-3">
                                <label class="pb-label">Special Collection</label>
                                <select name="special_collection" id="special_collection" class="pb-input">
                                    <option value="">— None —</option>
                                    @if(isset($collections) && $collections->count() > 0)
                                        @foreach($collections as $collection)
                                            <option value="{{ $collection->slug }}" {{ ($editFirst?->special_collection ?? '') == $collection->slug ? 'selected' : '' }}>
                                                {{ $collection->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="pb-err" id="special_collectionError"></span>
                            </div>
                             <div class="col-md-6 mb-3 frame-only-field">
                                <label class="pb-label">Shape</label>
                                <select name="Shape" id="Shape" class="pb-input">
                                    <option value="">Select Shape</option>
                                    @foreach($shapes as $shapeObj)
                                        <option value="{{ $shapeObj->shape_name }}" {{ ($editFirst?->Shape ?? '') == $shapeObj->shape_name ? 'selected' : '' }}>
                                            {{ $shapeObj->shape_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="pb-err" id="ShapeError"></span>
                            </div>
                             <div class="col-md-6 mb-3 frame-only-field">
                                <label class="pb-label">Type</label>
                                <select name="Type" id="Type" class="pb-input">
                                    <option value="">Select Type</option>
                                    @foreach($types as $typeObj)
                                        <option value="{{ $typeObj->type_name }}" {{ ($editFirst?->Type ?? '') == $typeObj->type_name ? 'selected' : '' }}>
                                            {{ $typeObj->type_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="pb-err" id="TypeError"></span>
                            </div>

                            {{-- Step 1: What type of product is this frame? --}}
                            <div class="col-md-12 mb-3 frame-only-field" id="product-types-section">
                                <label class="pb-label">
                                    <span style="background: var(--primary); color: #fff; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: .72rem; font-weight: 700; margin-right: 8px;">1</span>
                                    What type of product is this frame?
                                </label>
                                <p class="pb-sublabel" style="padding-left: 28px;">Select all product types this frame supports. Packages will be suggested based on your selection.</p>
                                @php
                                    $productTypes = \App\Models\ProductType::where('is_active', 1)->get();
                                    $selectedProductTypeIds = $selectedProductTypeIds ?? (
                                        $editFirst?->supported_product_types ?? []
                                    );
                                    $savedLensPackageIds = $editFirst?->selected_lens_packages ?? [];
                                    if (is_string($savedLensPackageIds)) {
                                        $decoded = json_decode($savedLensPackageIds, true);
                                        $savedLensPackageIds = is_array($decoded) ? $decoded : [];
                                    }
                                @endphp

                                <div class="pb-chip-group" id="supported_product_types_group">
                                    @foreach($productTypes as $productType)
                                        <label class="pb-chip">
                                            <input
                                                type="checkbox"
                                                name="supported_product_types[]"
                                                value="{{ $productType->id }}"
                                                {{ in_array($productType->id, $selectedProductTypeIds) ? 'checked' : '' }}>
                                            <span class="chip-card">
                                                <span class="chip-check"></span>
                                                <span class="chip-title"><span class="chip-icon">{{ $productType->icon }}</span>{{ $productType->name }}</span>
                                                @if(!empty($productType->subtitle))
                                                    <span class="chip-subtitle">{{ $productType->subtitle }}</span>
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <span class="pb-err" id="supported_product_typesError"></span>
                            </div>

                            {{-- Step 2: Assign Packages (auto-appears when product types are selected) --}}
                            @if(isset($lensPackages) && $lensPackages->count())
                            <div class="col-md-12 mb-3 frame-only-field" id="package-section" style="display: {{ !empty($selectedProductTypeIds) || !empty($savedLensPackageIds) ? 'block' : 'none' }};">
                                <div style="border: 2px solid #e0e7ff; border-radius: 12px; overflow: hidden;">

                                    {{-- Section header --}}
                                    <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                        <div>
                                            <div style="color: #fff; font-size: .9rem; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                                                <span style="background: rgba(255,255,255,.2); border-radius: 50%; width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; font-size: .72rem; font-weight: 800;">2</span>
                                                📦 Which packages can customers choose for this frame?
                                            </div>
                                            <div style="color: rgba(255,255,255,.8); font-size: .75rem; margin-top: 3px; padding-left: 30px;">Packages shown below are matched to the product type(s) you selected above.</div>
                                        </div>
                                        <span id="pkg-selected-badge" style="background: rgba(255,255,255,.2); color: #fff; font-size: .75rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap; display: none;">
                                            0 selected
                                        </span>
                                    </div>

                                    {{-- Grouped packages by product type --}}
                                    <div style="padding: 16px; background: #f8f7ff;">
                                        @php
                                            $lpMatchesProductType = function($lp, $pt) {
                                                // 1. Direct Category match (if categories are synced in the future)
                                                if ($lp->categories && $lp->categories->contains('id', $pt->id)) {
                                                    return true;
                                                }
                                                // 2. PowerType match strictly mapped by description/name
                                                if ($lp->powerTypes && $lp->powerTypes->isNotEmpty()) {
                                                    $ptNameLower = strtolower(trim($pt->name));
                                                    $lpPowerTypeDescs = $lp->powerTypes->pluck('description')->map(function($desc) {
                                                        return strtolower(trim($desc));
                                                    })->toArray();

                                                    if (str_contains($ptNameLower, 'powered') || str_contains($ptNameLower, 'eyeglass')) {
                                                        // Powered Eyeglass -> requires "With Power"
                                                        return in_array('with power', $lpPowerTypeDescs) || in_array('powered eyeglass', $lpPowerTypeDescs);
                                                    }
                                                    if (str_contains($ptNameLower, 'zero')) {
                                                        // Zero Power -> requires "Zero Power"
                                                        return in_array('zero power', $lpPowerTypeDescs);
                                                    }
                                                    if (str_contains($ptNameLower, 'reading') || str_contains($ptNameLower, 'glass')) {
                                                        // Reading Glasses -> requires "Progressive/Bifocals" or "Reading Glasses"
                                                        return in_array('progressive/bifocals', $lpPowerTypeDescs) || in_array('reading glasses', $lpPowerTypeDescs);
                                                    }
                                                    if (str_contains($ptNameLower, 'Progressive/Bifocals')) {
                                                        // Sunglass -> requires "Frame Only" or "Sunglass"
                                                        return in_array('frame only', $lpPowerTypeDescs) || in_array('Progressive/Bifocals', $lpPowerTypeDescs);
                                                    }
                                                }
                                                return false;
                                            };
                                        @endphp

                                        {{-- Per product-type group --}}
                                        @foreach($productTypes as $pt)
                                            @php
                                                $ptPackages = $lensPackages->filter(function($lp) use ($pt, $lpMatchesProductType) {
                                                    return $lpMatchesProductType($lp, $pt);
                                                });
                                            @endphp
                                            @if($ptPackages->count())
                                            <div class="pkg-type-group"
                                                 data-type-id="{{ $pt->id }}"
                                                 style="display: {{ in_array($pt->id, $selectedProductTypeIds) ? 'block' : 'none' }}; margin-bottom: 14px;">
                                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                                    <span style="font-size: .78rem; font-weight: 700; color: #4f46e5; text-transform: uppercase; letter-spacing: .04em;">
                                                        {{ $pt->icon }} {{ $pt->name }}
                                                        <span style="font-weight: 500; color: #6b7280; text-transform: none; letter-spacing: 0;">({{ $ptPackages->count() }} {{ $ptPackages->count() == 1 ? 'package' : 'packages' }})</span>
                                                    </span>
                                                    <div style="display: flex; gap: 6px;">
                                                        <button type="button"
                                                            class="pkg-select-all"
                                                            data-type-id="{{ $pt->id }}"
                                                            style="font-size: .72rem; padding: 3px 10px; border: 1px solid #4f46e5; background: #fff; color: #4f46e5; border-radius: 5px; cursor: pointer; transition: all .15s; font-weight: 600;">
                                                            ✓ Select All
                                                        </button>
                                                        <button type="button"
                                                            class="pkg-clear-all"
                                                            data-type-id="{{ $pt->id }}"
                                                            style="font-size: .72rem; padding: 3px 10px; border: 1px solid #d1d5db; background: #fff; color: #6b7280; border-radius: 5px; cursor: pointer; transition: all .15s; font-weight: 600;">
                                                            ✕ Clear
                                                        </button>
                                                    </div>
                                                </div>
                                                <div style="display: flex; flex-direction: column; gap: 7px;">
                                                    @foreach($ptPackages as $lp)
                                                    <label class="pkg-item"
                                                        data-package-id="{{ $lp->id }}"
                                                        data-type-id="{{ $pt->id }}"
                                                        style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border: 1.5px solid {{ in_array($lp->id, $savedLensPackageIds) ? '#4f46e5' : '#e5e7eb' }}; border-radius: 9px; background: {{ in_array($lp->id, $savedLensPackageIds) ? 'rgba(79,70,229,.06)' : '#fff' }}; cursor: pointer; transition: all .18s; user-select: none;">
                                                        <input
                                                            type="checkbox"
                                                            name="selected_lens_packages[]"
                                                            value="{{ $lp->id }}"
                                                            {{ in_array($lp->id, $savedLensPackageIds) ? 'checked' : '' }}
                                                            style="width: 17px; height: 17px; accent-color: #4f46e5; cursor: pointer; flex-shrink: 0;">
                                                        <div style="flex: 1; min-width: 0;">
                                                            <div style="font-size: .86rem; font-weight: 700; color: #1e1b4b;">{{ $lp->name }}</div>
                                                            @if($lp->short_description)
                                                                <div style="font-size: .73rem; color: #6b7280; margin-top: 2px;">{{ $lp->short_description }}</div>
                                                            @endif
                                                        </div>
                                                        <div style="text-align: right; flex-shrink: 0;">
                                                            @if($lp->current_price)
                                                                <div style="font-size: .88rem; font-weight: 800; color: #4f46e5;">₹{{ number_format($lp->current_price, 0) }}</div>
                                                                @if($lp->original_price && $lp->original_price > $lp->current_price)
                                                                    <div style="font-size: .7rem; color: #9ca3af; text-decoration: line-through;">₹{{ number_format($lp->original_price, 0) }}</div>
                                                                @endif
                                                            @else
                                                                <span style="font-size: .78rem; color: #6b7280; background: #f3f4f6; padding: 2px 8px; border-radius: 4px;">Free</span>
                                                            @endif
                                                        </div>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach

                                        {{-- Empty state when types selected but no packages match --}}
                                        <div id="pkg-no-match-msg" style="display: none; text-align: center; padding: 24px 16px;">
                                            <div style="font-size: 2rem;">📭</div>
                                            <div style="font-size: .88rem; font-weight: 700; color: #374151; margin-top: 6px;">No packages assigned to the selected product type</div>
                                            <div style="font-size: .76rem; color: #6b7280; margin-top: 4px; max-width: 420px; margin-left: auto; margin-right: auto;">
                                                Go to <strong>Lens System → Lens Packages</strong>, edit your package, and check the <strong>Power Type Categories</strong> (e.g. With Power, Zero Power, Progressive, Frame Only) to assign it to this product type.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="col-md-12 mb-3">
                                <label class="pb-label">Description</label>
                                <textarea name="Description" id="Description" rows="3"
                                    class="pb-input" placeholder="Short description of the product…" style="resize:vertical;">{{ $editFirst?->Description ?? '' }}</textarea>
                            </div>
                           
                        </div>
                    </div>
                </div>

                {{-- Classification & Filters --}}
                <div class="pb-card" id="classification-card">
                    <div class="pb-card-header">
                        <h5><span class="icon"><i class="fa fa-filter"></i></span> Classification & Filters</h5>
                    </div>
                    <div class="pb-card-body">
                        <div class="row">

                            {{-- ========== FRAME FIELDS (hidden for Lens) ========== --}}
                            <div id="frame-classification-fields" style="display:contents;">
                                <div class="col-md-6 mb-3">
                                    <label class="pb-label">Age Group</label>
                                    @php
                                        $savedAge = $editFirst?->age ?? [];
                                        if (is_string($savedAge)) {
                                            $decoded = json_decode($savedAge, true);
                                            $savedAge = json_last_error() === JSON_ERROR_NONE ? $decoded : explode(',', $savedAge);
                                        }
                                        $savedAge = is_array($savedAge) ? array_map('trim', $savedAge) : [];
                                    @endphp
                                    <select name="age[]" id="age" class="pb-input select2-multiple" multiple data-placeholder="Select Age Group">
                                        @foreach(['Kids', 'Teen', 'Adult', 'Senior'] as $opt)
                                            <option value="{{ $opt }}" {{ in_array($opt, $savedAge) ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="pb-label">Occasion</label>
                                    @php
                                        $savedOcc = $editFirst?->occasion ?? [];
                                        if (is_string($savedOcc)) {
                                            $decoded = json_decode($savedOcc, true);
                                            $savedOcc = json_last_error() === JSON_ERROR_NONE ? $decoded : explode(',', $savedOcc);
                                        }
                                        $savedOcc = is_array($savedOcc) ? array_map('trim', $savedOcc) : [];
                                    @endphp
                                    <select name="occasion[]" id="occasion" class="pb-input select2-multiple" multiple data-placeholder="Select Occasion">
                                        @foreach(['Party', 'Casual', 'Office', 'Sports', 'Formal', 'Everyday'] as $opt)
                                            <option value="{{ $opt }}" {{ in_array($opt, $savedOcc) ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="pb-label">Recommended Face Shape</label>
                                    @php
                                        $savedShape = $editFirst?->face_shape ?? [];
                                        if (is_string($savedShape)) {
                                            $decoded = json_decode($savedShape, true);
                                            $savedShape = json_last_error() === JSON_ERROR_NONE ? $decoded : explode(',', $savedShape);
                                        }
                                        $savedShape = is_array($savedShape) ? array_map('trim', $savedShape) : [];
                                    @endphp
                                    <select name="face_shape[]" id="face_shape" class="pb-input select2-multiple" multiple data-placeholder="Select Face Shape">
                                        @foreach(['Round', 'Oval', 'Square', 'Heart', 'Diamond', 'Rectangle'] as $opt)
                                            <option value="{{ $opt }}" {{ in_array($opt, $savedShape) ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="pb-label">Gender</label>
                                    @php
                                        $savedGender = $editFirst?->Gender ?? [];
                                        if (is_string($savedGender)) {
                                            $decoded = json_decode($savedGender, true);
                                            $savedGender = json_last_error() === JSON_ERROR_NONE ? $decoded : explode(',', $savedGender);
                                        }
                                        $savedGender = is_array($savedGender) ? array_map('trim', $savedGender) : [];
                                    @endphp
                                    <select name="gender[]" id="gender" class="pb-input select2-multiple" multiple data-placeholder="Select Gender">
                                        @foreach(['Men', 'Women', 'Unisex', 'Kids'] as $opt)
                                            <option value="{{ $opt }}" {{ in_array($opt, $savedGender) ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>{{-- end frame-classification-fields --}}

                            {{-- ========== LENS FIELDS (hidden for Frame) ========== --}}
                            <div id="lens-classification-fields" style="display:none;">
                                <div class="col-md-6 mb-3" style="display:block;">
                                    <label class="pb-label">Power Type / Usage Category</label>
                                    <select name="power_type" id="power_type" class="pb-input">
                                        <option value="">Select Power Type</option>
                                        <option value="Single Vision">Single Vision (Spherical)</option>
                                        <option value="Toric">Toric (Astigmatism / Cylindrical)</option>
                                        <option value="Multifocal">Multifocal / Progressive (Presbyopia)</option>
                                        <option value="Zero Power">Zero Power / Plano (Cosmetic / Color)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" style="display:block;">
                                    <label class="pb-label">Gender</label>
                                    @php
                                        $savedGenderL = $editFirst?->Gender ?? [];
                                        if (is_string($savedGenderL)) {
                                            $decoded = json_decode($savedGenderL, true);
                                            $savedGenderL = json_last_error() === JSON_ERROR_NONE ? $decoded : explode(',', $savedGenderL);
                                        }
                                        $savedGenderL = is_array($savedGenderL) ? array_map('trim', $savedGenderL) : [];
                                    @endphp
                                    <select name="gender[]" id="gender_lens" class="pb-input select2-multiple" multiple data-placeholder="Select Gender">
                                        @foreach(['Men', 'Women', 'Unisex'] as $opt)
                                            <option value="{{ $opt }}" {{ in_array($opt, $savedGenderL) ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>{{-- end lens-classification-fields --}}

                        </div>
                    </div>
                </div>

                {{-- Promotional Tags --}}
                <div class="pb-card">
                    <div class="pb-card-header">
                        <h5><span class="icon"><i class="fa fa-fire"></i></span> Promotional Tags</h5>
                    </div>
                    <div class="pb-card-body">
                        <label class="pb-label" style="margin-bottom:12px;">Select all that apply</label>
                        <div class="promo-tags-grid">
                            <label class="promo-tag-item">
                                <input type="checkbox" name="promotion_tag[]" value="trending_now"
                                    {{ in_array('trending_now', explode(',', $editFirst?->promotion_tag ?? '')) ? 'checked' : '' }}>
                                <span><i class="fa fa-bolt tag-icon" style="color:#f59e0b;"></i> Trending Now</span>
                            </label>
                            <label class="promo-tag-item">
                                <input type="checkbox" name="promotion_tag[]" value="best_seller"
                                    {{ in_array('best_seller', explode(',', $editFirst?->promotion_tag ?? '')) ? 'checked' : '' }}>
                                <span><i class="fa fa-star tag-icon" style="color:#10b981;"></i> Best Seller</span>
                            </label>
                            <label class="promo-tag-item">
                                <input type="checkbox" name="promotion_tag[]" value="new_arrival"
                                    {{ in_array('new_arrival', explode(',', $editFirst?->promotion_tag ?? '')) ? 'checked' : '' }}>
                                <span><i class="fa fa-certificate tag-icon" style="color:#6366f1;"></i> New Arrival</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Variants --}}
                <div class="pb-card">
                    <div class="pb-card-header">
                        <h5><span class="icon"><i class="fa fa-cubes"></i></span> Product Variants</h5>
                        <button type="button" class="btn-add-variant" id="btn-add-variant" onclick="addVariant()">
                            <i class="fa fa-plus"></i> Add Variant
                        </button>
                    </div>
                    <div class="pb-card-body">
                        <div class="variant-list" id="variant-list">
                            {{-- Variants rendered here by JS (or pre-populated on edit) --}}
                        </div>
                        <div id="no-variants-msg" style="text-align:center; padding:40px; color:var(--muted);">
                            <i class="fa fa-layer-group" style="font-size:2rem; margin-bottom:8px; display:block;"></i>
                            <p style="margin:0; font-size:.9rem;">No variants yet. Click <strong>+ Add Variant</strong> to begin.</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===== RIGHT SIDEBAR ===== --}}
            <div class="pb-sidebar">

                {{-- Status & Publishing --}}
                <div class="pb-card sb-card">
                    <div class="pb-card-header">
                        <h5><span class="icon"><i class="fa fa-toggle-on"></i></span> Product Status</h5>
                    </div>
                    <div class="pb-card-body">
                        <input type="hidden" name="is_b2c" value="1">
                        <div>
                            <label class="pb-label">Store Visibility</label>
                            <div class="pb-radio-group">
                                <label class="pb-radio-btn">
                                    <input type="radio" name="status" value="1" {{ ($editFirst?->status ?? '1') == '1' ? 'checked' : '' }}>
                                    <span><i class="fa fa-check-circle" style="color:var(--success);"></i> Active (Live)</span>
                                </label>
                                <label class="pb-radio-btn">
                                    <input type="radio" name="status" value="0" {{ ($editFirst?->status ?? '1') == '0' ? 'checked' : '' }}>
                                    <span><i class="fa fa-eye-slash" style="color:var(--muted);"></i> Inactive (Draft)</span>
                                </label>
                            </div>
                            <small style="color:var(--muted); margin-top:8px; display:block; font-size:0.75rem;"><i class="fa fa-info-circle"></i> Active products are immediately visible to customers on the website.</small>
                        </div>
                    </div>
                </div>

                {{-- Inventory Settings --}}
                <!--<div class="pb-card sb-card">-->
                <!--    <div class="pb-card-header">-->
                <!--        <h5><span class="icon"><i class="fa fa-boxes"></i></span> Inventory</h5>-->
                <!--    </div>-->
                <!--    <div class="pb-card-body">-->
                <!--        <div class="mb-3">-->
                <!--            <label class="pb-label">Track Inventory</label>-->
                <!--            <div class="pb-radio-group">-->
                <!--                <label class="pb-radio-btn">-->
                <!--                    <input type="radio" name="Track_Inventory" value="1" {{ ($editFirst?->Track_Inventory ?? '1') == '1' ? 'checked' : '' }}>-->
                <!--                    <span><i class="fa fa-check-circle"></i> Yes</span>-->
                <!--                </label>-->
                <!--                <label class="pb-radio-btn">-->
                <!--                    <input type="radio" name="Track_Inventory" value="0" {{ ($editFirst?->Track_Inventory ?? '1') == '0' ? 'checked' : '' }}>-->
                <!--                    <span><i class="fa fa-times-circle"></i> No</span>-->
                <!--                </label>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--        <div class="mb-0">-->
                <!--            <label class="pb-label">Allow Negative Inventory</label>-->
                <!--            <div class="pb-radio-group">-->
                <!--                <label class="pb-radio-btn">-->
                <!--                    <input type="radio" name="Allow_Negative_Inventory" value="1" {{ ($editFirst?->Allow_Negative_Inventory ?? '1') == '1' ? 'checked' : '' }}>-->
                <!--                    <span><i class="fa fa-check-circle"></i> Yes</span>-->
                <!--                </label>-->
                <!--                <label class="pb-radio-btn">-->
                <!--                    <input type="radio" name="Allow_Negative_Inventory" value="0" {{ ($editFirst?->Allow_Negative_Inventory ?? '1') == '0' ? 'checked' : '' }}>-->
                <!--                    <span><i class="fa fa-times-circle"></i> No</span>-->
                <!--                </label>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->

                {{-- Readiness Checklist --}}
                <div class="pb-card sb-card">
                    <div class="pb-card-header">
                        <h5><span class="icon"><i class="fa fa-list-check"></i></span> Readiness Checklist</h5>
                    </div>
                    <div class="pb-card-body" style="font-size:.85rem; color:var(--text); font-weight:500;">
                        <ul class="readiness-list" style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:12px;">
                            <li id="check-name"><i class="fa fa-times-circle" style="color:var(--danger); width:18px; font-size:1.1em;"></i> Product name filled</li>
                            <li id="check-category"><i class="fa fa-times-circle" style="color:var(--danger); width:18px; font-size:1.1em;"></i> Category selected</li>
                            <li id="check-variants"><i class="fa fa-times-circle" style="color:var(--danger); width:18px; font-size:1.1em;"></i> At least 1 variant added</li>
                            <li id="check-variant-data"><i class="fa fa-times-circle" style="color:var(--danger); width:18px; font-size:1.1em;"></i> Variants have image, price, SKU</li>
                        </ul>
                    </div>
                </div>

                {{-- Save Button --}}
                <button type="submit" class="btn-submit-product" id="btn-submit">
                    <i class="fa fa-save" style="margin-right:6px;"></i>
                    {{ $isEdit ? 'Update Product' : 'Save Product' }}
                </button>
                <div id="submit-loader" style="display:none; text-align:center; margin-top:12px;">
                    <div class="spinner-border text-primary spinner-grow" role="status"></div>
                    <p style="font-size:.8rem; color:var(--muted); margin-top:6px;">Saving…</p>
                </div>

            </div>
        </div>
    </form>
</div>

{{-- Variant template (hidden) --}}
<template id="variant-tpl">
    <div class="variant-card" data-variant-idx="__IDX__">
        <div class="variant-card-header" onclick="toggleVariant(this)">
            <div class="v-title">
                <span class="v-num">__NUM__</span>
                Variant #__NUM__
                <small class="v-sku-label" style="color:var(--muted); font-weight:400;"></small>
            </div>
            <div class="v-actions" onclick="event.stopPropagation()">
                <button type="button" class="btn-remove-variant" onclick="removeVariant(this)">
                    <i class="fa fa-trash"></i> Remove
                </button>
            </div>
        </div>
        <div class="variant-card-body">
            <input type="hidden" name="variants[__IDX__][id]" value="">

            {{-- SKU --}}
            <div class="mb-3">
                <label class="pb-label">SKU / Product Code <span class="req">*</span></label>
                <input type="text" name="variants[__IDX__][product_code]"
                    class="pb-input sku-input" placeholder="e.g. FR-001-BLK"
                    oninput="updateSkuLabel(this)">
                <span class="pb-err sku-err"></span>
            </div>

            {{-- Optical Specifications --}}
            <div class="variant-section-title"><i class="fa fa-glasses"></i> Optical Specifications</div>
            <div class="optical-fields-container row-grid" id="optical-fields-container-__IDX__">
                __OPT_FIELDS__
            </div>

            {{-- Measurements & Details —— Frame only (hidden for Contact Lenses) --}}
            <div class="frame-measurements-wrapper">
                <div class="variant-section-title measurements-section-title" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 12px;" onclick="toggleMeasurements(this)">
                    <span><i class="fa fa-ruler-horizontal"></i> Measurements & Lab Dimensions (Optional)</span>
                    <i class="fa fa-chevron-down meas-toggle-icon" style="font-size: .8rem; color: #64748b; transition: transform .2s;"></i>
                </div>

                <div class="measurements-collapsible-wrapper" style="display: none; margin-bottom: 16px;">
                    <div class="frame-measurements row-grid">
                        <div>
                            <label class="pb-label">Lens Width</label>
                            <input type="text" name="variants[__IDX__][lens_width]" class="pb-input" placeholder="e.g. 52mm">
                        </div>
                        <div>
                            <label class="pb-label">Temple Length</label>
                            <input type="text" name="variants[__IDX__][temple_length]" class="pb-input" placeholder="e.g. 140mm">
                        </div>
                        <div>
                            <label class="pb-label">Frame Width</label>
                            <input type="text" name="variants[__IDX__][frame_width]" class="pb-input" placeholder="e.g. 138mm">
                        </div>
                        <div>
                            <label class="pb-label">Polarized</label>
                            <select name="variants[__IDX__][polarized]" class="pb-input">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div>
                            <label class="pb-label">UV Protection</label>
                            <input type="text" name="variants[__IDX__][uv_protection]" class="pb-input" placeholder="e.g. UV400">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="variant-section-title"><i class="fa fa-tag"></i> Pricing</div>
            <div class="row-grid">
                <div>
                    <label class="pb-label">Purchase Price</label>
                    <input type="number" step="0.01" name="variants[__IDX__][Purchase_Price]"
                        class="pb-input" placeholder="0.00">
                    <span class="pb-err purchase-price-err"></span>
                </div>
                <div>
                    <label class="pb-label">Retail Price (MRP) <span class="req">*</span></label>
                    <input type="number" step="0.01" name="variants[__IDX__][Retail_Price]"
                        class="pb-input" placeholder="0.00">
                    <span class="pb-err retail-price-err"></span>
                </div>
                <div>
                    <label class="pb-label">Discount / Sale Price</label>
                    <input type="number" step="0.01" name="variants[__IDX__][discount_price]"
                        class="pb-input" placeholder="0.00">
                    <span class="pb-err discount-price-err"></span>
                </div>
                <div style="display: none;">
                    <label class="pb-label">Tax / HSN Code</label>
                    <input type="hidden" name="variants[__IDX__][tax_hsn_code]"
                        class="pb-input" value="900490">
                    <span class="pb-err hsn-err"></span>
                </div>
            </div>

            {{-- Images --}}
            <div class="variant-section-title"><i class="fa fa-images"></i> Images</div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="pb-label">Main Image <span class="req">*</span></label>
                    <input type="file" name="variants[__IDX__][main_image]"
                        class="pb-input" accept="image/*" onchange="previewMainImage(this)">
                    <small class="text-muted" style="font-size:0.75rem; display:block; margin-top:3px;"><i class="fa fa-info-circle"></i> Ratio: <strong>2:1 landscape</strong> (e.g. 800×400px)</small>
                    <span class="pb-err main-image-err"></span>
                    <div class="img-preview-wrap main-img-preview"></div>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="pb-label">Gallery Images</label>
                    <input type="file" name="variants[__IDX__][images][]"
                        class="pb-input" accept="image/*" multiple onchange="previewGallery(this)">
                    <small class="text-muted" style="font-size:0.75rem; display:block; margin-top:3px;"><i class="fa fa-info-circle"></i> Ratio: <strong>2:1 landscape</strong> (e.g. 800×400px)</small>
                    <div class="img-preview-wrap gallery-preview"></div>
                </div>
            </div>
        </div>
    </div>
</template>

@php
// Build optical spec fields for Lens
$optFieldsLens = [
    'Color'     => ['label'=>'Lens Color / Tint'],
    'Modality'  => ['label'=>'Modality (Disposability)'],
    'pack_size' => ['label'=>'Pack Size (Box)'],
    'WC'        => ['label'=>'Water Content (%)'],
    'Dk_t'      => ['label'=>'Oxygen Transmissibility (Dk/t)'],
    'BC'        => ['label'=>'Base Curve (BC)'],
    'DIA'       => ['label'=>'Diameter (DIA)'],
    'SPH'       => ['label'=>'Sphere (SPH) Range'],
    'CYL'       => ['label'=>'Cylinder (CYL) Range'],
    'AXIS'      => ['label'=>'Axis Range'],
];

// Build optical spec fields for Frame
$optFieldsFrame = [
    'Color'           => ['label'=>'Primary Color'],
    'Secondary_Color' => ['label'=>'Secondary Color (Optional)'],
    'Size'            => ['label'=>'Size'],
    'Material'        => ['label'=>'Material'],
    'Temple_Detail'   => ['label'=>'Temple Detail'],
    'Bridge_Size'     => ['label'=>'Bridge Size'],
    'Quality'         => ['label'=>'Quality'],
];

// Build Lens fields HTML
$optHtmlLens = '';
foreach ($optFieldsLens as $key => $cfg) {
    $optHtmlLens .= '<div>';
    $optHtmlLens .= '<label class="pb-label">' . $cfg['label'] . '</label>';
    if ($key === 'Modality') {
        $optHtmlLens .= '<select name="variants[__IDX__][' . $key . ']" class="pb-input">';
        $optHtmlLens .= '<option value="">Select Modality</option>';
        $optHtmlLens .= '<option value="Daily">Daily Disposables</option>';
        $optHtmlLens .= '<option value="Fortnightly">Fortnightly (2-Week)</option>';
        $optHtmlLens .= '<option value="Monthly">Monthly Disposables</option>';
        $optHtmlLens .= '<option value="Quarterly">Quarterly (3-Month)</option>';
        $optHtmlLens .= '<option value="Yearly">Yearly (Annual)</option>';
        $optHtmlLens .= '</select>';
    } elseif ($key === 'pack_size') {
        $optHtmlLens .= '<select name="variants[__IDX__][' . $key . ']" class="pb-input">';
        $optHtmlLens .= '<option value="">Select Pack Size</option>';
        $optHtmlLens .= '<option value="1">1 Lens (Single)</option>';
        $optHtmlLens .= '<option value="2">2 Lenses (1 Pair)</option>';
        $optHtmlLens .= '<option value="6">6 Lenses (Box)</option>';
        $optHtmlLens .= '<option value="10">10 Lenses (Box)</option>';
        $optHtmlLens .= '<option value="12">12 Lenses (Box)</option>';
        $optHtmlLens .= '<option value="30">30 Lenses (Box)</option>';
        $optHtmlLens .= '<option value="90">90 Lenses (Box)</option>';
        $optHtmlLens .= '</select>';
    } else {
        $optHtmlLens .= '<input type="text" name="variants[__IDX__][' . $key . ']" class="pb-input" placeholder="' . $cfg['label'] . '">';
    }
    $optHtmlLens .= '</div>';
}

// Build Frame fields HTML
$optHtmlFrame = '';
foreach ($optFieldsFrame as $key => $cfg) {
    $settingKey = strtolower($key);
    if ($key === 'Secondary_Color') {
        $settingKey = 'color';
    }
    $showField = !$setting || ($setting->$settingKey ?? '0') == '0' || in_array($key, ['Color','Secondary_Color','Size','Material','Temple_Detail','Bridge_Size','Quality']);
    if (!$showField) continue;

    $optHtmlFrame .= '<div>';
    $optHtmlFrame .= '<label class="pb-label">' . $cfg['label'] . '</label>';
    if (($key === 'Color' || $key === 'Secondary_Color')) {
        $defaultVal = $key === 'Color' ? '#1a1a1a' : '#ffffff';
        $optHtmlFrame .= '<div style="display: flex; gap: 8px; align-items: center; flex: 1;">';
        $optHtmlFrame .= '<input type="color" name="variants[__IDX__][' . $key . ']" class="pb-input color-picker-box" value="' . $defaultVal . '" style="width: 46px; height: 38px; padding: 2px; cursor: pointer; border-radius: 8px; flex-shrink: 0;" title="Pick ' . $cfg['label'] . '" oninput="syncColorHex(this)">';
        $optHtmlFrame .= '<input type="text" class="pb-input color-hex-text" value="' . $defaultVal . '" placeholder="#hex" style="font-family: monospace; font-size: .85rem;" oninput="syncColorPicker(this)">';
        $optHtmlFrame .= '</div>';
    } elseif ($key === 'Size') {
        $optHtmlFrame .= '<select name="variants[__IDX__][' . $key . '][]" class="pb-input select2-size" multiple>';
        foreach ($sizes as $size) {
            $optHtmlFrame .= '<option value="' . htmlspecialchars($size->size_name) . '">' . htmlspecialchars($size->size_name) . '</option>';
        }
        $optHtmlFrame .= '</select>';
    } elseif ($key === 'Material') {
        $optHtmlFrame .= '<select name="variants[__IDX__][' . $key . ']" class="pb-input">';
        $optHtmlFrame .= '<option value="">Select Material</option>';
        foreach ($materials as $material) {
            $optHtmlFrame .= '<option value="' . htmlspecialchars($material->material_name) . '">' . htmlspecialchars($material->material_name) . '</option>';
        }
        $optHtmlFrame .= '</select>';
    } else {
        $optHtmlFrame .= '<input type="text" name="variants[__IDX__][' . $key . ']" class="pb-input" placeholder="' . $cfg['label'] . '">';
    }
    $optHtmlFrame .= '</div>';
}
@endphp

<script>

// ============================================================
// Autocomplete route map
// ============================================================
const AC_ROUTES = {
    'colorname-dropdown':    '{{ route("admin.colorname-dropdown") }}',
    'sizename-dropdown':     '{{ route("admin.sizename-dropdown") }}',
    'typename-dropdown':     '{{ route("admin.typename-dropdown") }}',
    'shapename-dropdown':    '{{ route("admin.shapename-dropdown") }}',
    'materialname-dropdown': '{{ route("admin.materialname-dropdown") }}',
    'companyname-dropdown':  '{{ route("admin.companyname-dropdown") }}',
};

const PRODUCT_TYPE = '{{ $type }}';

// ============================================================
// State
// ============================================================
let variantIndex = 0;
const OPT_FIELDS_HTML_LENS = `{!! addslashes($optHtmlLens) !!}`;
const OPT_FIELDS_HTML_FRAME = `{!! addslashes($optHtmlFrame) !!}`;
let currentProductType = PRODUCT_TYPE;

function getOptFieldsHtml(type, idx) {
    if (type === 'Lens') {
        return OPT_FIELDS_HTML_LENS.replaceAll('__IDX__', idx);
    } else {
        return OPT_FIELDS_HTML_FRAME.replaceAll('__IDX__', idx);
    }
}

// Pre-existing variants for edit mode
const EXISTING_VARIANTS = @json($isEdit ? $variants->values()->toArray() : []);

// ============================================================
// Category & Subcategory AJAX Loader & Variant Fields Dynamic Toggle
// ============================================================
function updateVariantFields() {
    const catSelect = document.getElementById('category_id');
    if (!catSelect) return;

    const selectedOption = catSelect.options[catSelect.selectedIndex];
    const catName = (selectedOption?.text || '').trim().toLowerCase();
    
    // Detect product mode: 'lens', 'sunglass', or 'frame'
    const isLens = selectedOption && (selectedOption.getAttribute('data-is-lens') === '1' || catName.includes('contact') || catName.includes('lens'));
    const isSunglass = catName.includes('sunglass') || catName.includes('goggle');
    
    // Update active product type
    currentProductType = isLens ? 'Lens' : (isSunglass ? 'Sunglass' : 'Frame');

    // Update badge & hidden input
    const badge = document.getElementById('badge-type');
    if (badge) badge.textContent = isLens ? 'Contact Lens' : (isSunglass ? 'Sunglasses' : 'Eyewear Frame');
    const prodTypeInput = document.getElementById('product_type');
    if (prodTypeInput) prodTypeInput.value = isLens ? 'Lens' : 'Frame';

    // ── 1. Show/Hide Frame-only fields in Card 1 (Shape, Type, Step 1 & Step 2 Lens Packages) ──
    document.querySelectorAll('.frame-only-field').forEach(el => {
        el.style.display = isLens ? 'none' : 'block';
    });

    // ── 2. Update optical spec fields in all existing variant cards ──
    document.querySelectorAll('.variant-card').forEach(card => {
        const idx = card.getAttribute('data-variant-idx');
        const container = card.querySelector('.optical-fields-container');
        if (container) {
            const values = {};
            container.querySelectorAll('input, select').forEach(el => {
                const nameMatch = el.name.match(/variants\[\d+\]\[([^\]]+)\]/);
                if (nameMatch) values[nameMatch[1]] = el.value;
            });

            container.innerHTML = getOptFieldsHtml(currentProductType === 'Lens' ? 'Lens' : 'Frame', idx);

            if (currentProductType !== 'Lens') {
                const sizeSelect = container.querySelector('.select2-size');
                if (sizeSelect) {
                    $(sizeSelect).select2({ placeholder: 'Select Size(s)', allowClear: true, width: '100%' });
                }
            }

            container.querySelectorAll('input, select').forEach(el => {
                const nameMatch = el.name.match(/variants\[\d+\]\[([^\]]+)\]/);
                if (nameMatch && values[nameMatch[1]] !== undefined) el.value = values[nameMatch[1]];
            });
        }

        // ── 3. Toggle Measurements section inside each variant card ──
        const frameMeasWrapper = card.querySelector('.frame-measurements-wrapper');
        if (frameMeasWrapper) {
            frameMeasWrapper.style.display = isLens ? 'none' : 'block';
        }
        
        const measWrapper = card.querySelector('.measurements-collapsible-wrapper');
        if (isSunglass && measWrapper) {
            // Auto-open and highlight Polarized & UV Protection for sunglasses
            measWrapper.style.display = 'block';
            const polSelect = card.querySelector('select[name$="[polarized]"]');
            const uvInput = card.querySelector('input[name$="[uv_protection]"]');
            if (polSelect && polSelect.value === '0') polSelect.value = '1';
            if (uvInput && !uvInput.value) uvInput.value = 'UV400';
        }
    });

    // ── 4. Toggle Classification & Filters section ──
    const frameClassif = document.getElementById('frame-classification-fields');
    const lensClassif  = document.getElementById('lens-classification-fields');
    if (frameClassif) frameClassif.style.display = isLens ? 'none' : 'contents';
    if (lensClassif)  lensClassif.style.display  = isLens ? 'contents' : 'none';

    evaluateChecklist();
}

function initializeProductType() {
    const catSelect = document.getElementById('category_id');
    if (catSelect) {
        const selectedOption = catSelect.options[catSelect.selectedIndex];
        if (selectedOption && selectedOption.value !== "") {
            const isLens = selectedOption.getAttribute('data-is-lens') === '1';
            currentProductType = isLens ? 'Lens' : 'Frame';
        }
    }
    // Update badge & hidden input
    const badge = document.getElementById('badge-type');
    if (badge) badge.textContent = currentProductType;
    const prodTypeInput = document.getElementById('product_type');
    if (prodTypeInput) prodTypeInput.value = currentProductType;
}

function loadSubcategories(categoryId, selectedSubcategoryId = null) {
    // Toggle Sunglass Colour fields based on category name
    const catSelect = document.getElementById('category_id');
    if (catSelect) {
        const selectedText = catSelect.options[catSelect.selectedIndex]?.text.trim().toLowerCase() || '';
        const wrappers = document.querySelectorAll('.sunglass-color-wrapper');
        if (selectedText.includes('sunglass') || selectedText.includes('goggle')) {
            wrappers.forEach(w => w.style.display = 'block');
        } else {
            wrappers.forEach(w => {
                w.style.display = 'none';
                const input = w.querySelector('input');
                if (input) input.value = '';
            });
        }
    }

    const subSelect = document.getElementById('subcategory_id');
    if (!subSelect) return;

    // Reset options
    subSelect.innerHTML = '<option value="">Select Subcategory</option>';

    if (!categoryId) return;

    fetch('{{ route("admin.products.subcategories") }}?category_id=' + categoryId)
        .then(r => r.json())
        .then(data => {
            data.forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.name;
                if (selectedSubcategoryId && sub.id == selectedSubcategoryId) {
                    opt.selected = true;
                }
                subSelect.appendChild(opt);
            });
        })
        .catch(err => console.error('Error loading subcategories:', err));
}

// ============================================================
// Init
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    // Initialize top-level Select2 dropdowns
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2-multiple').select2({
            width: '100%',
            allowClear: true
        });
    }

    // Setup company autocomplete
    setupGlobalAc('Company', 'companyListName', 'companyname-dropdown', 'brand_name');

    // Setup categories on edit mode
    @if($isEdit)
        const initialCategoryId = '{{ $editFirst?->category_id ?? "" }}';
        const initialSubcategoryId = '{{ $editFirst?->subcategory_id ?? "" }}';
        if (initialCategoryId) {
            loadSubcategories(initialCategoryId, initialSubcategoryId);
        }
    @endif

    // Detect if category is lens on page load to initialize the state
    initializeProductType();

    if (EXISTING_VARIANTS.length > 0) {
        EXISTING_VARIANTS.forEach(v => addVariant(v));
    } else {
        addVariant(); // Start with one empty variant
    }
    updateNoVariantsMsg();
});

// ============================================================
// addVariant
// ============================================================
function addVariant(data) {
    const idx  = variantIndex++;
    const num  = document.querySelectorAll('.variant-card').length + 1;
    const tpl  = document.getElementById('variant-tpl').innerHTML;

    let html = tpl
        .replaceAll('__IDX__', idx)
        .replaceAll('__NUM__', num);

    // Inject optical fields
    html = html.replace('__OPT_FIELDS__', getOptFieldsHtml(currentProductType, idx));

    const container = document.getElementById('variant-list');
    const div = document.createElement('div');
    div.innerHTML = html;
    container.appendChild(div.firstElementChild);

    const card = container.lastElementChild;

    // Apply measurement section visibility based on current category type
    const isLensNow = (currentProductType === 'Lens');
    card.querySelectorAll('.frame-measurements').forEach(el => el.style.display = isLensNow ? 'none' : 'grid');
    card.querySelectorAll('.lens-measurements').forEach(el  => el.style.display = isLensNow ? 'grid' : 'none');

    const sizeSelect = card.querySelector('.select2-size');
    if (sizeSelect) {
        $(sizeSelect).select2({
            placeholder: "Select Size(s)",
            allowClear: true,
            width: '100%'
        });
    }

    // Pre-fill data if editing
    if (data) {
        const setVal = (name, val) => {
            const el = card.querySelector(`[name="variants[${idx}][${name}]"]`);
            if (el && val) {
                if (el.type === 'color' && !val.startsWith('#')) {
                    el.value = '#000000';
                } else {
                    el.value = val;
                }
            }
        };

        card.querySelector(`[name="variants[${idx}][id]"]`).value = data.id || '';
        setVal('product_code',        data.product_code);

        if (data.Color && data.Color.includes(' / ')) {
            const parts = data.Color.split(' / ');
            data.Color = parts[0];
            data.Secondary_Color = parts[1];
        }
        setVal('Color',               data.Color);
        setVal('Secondary_Color',     data.Secondary_Color);

        // Pre-fill multiple select sizes
        if (data.Size && sizeSelect) {
            const sizesArray = data.Size.split(',').map(s => s.trim());
            $(sizeSelect).val(sizesArray).trigger('change');
        }

        setVal('Type',                data.Type);
        setVal('Gender',              data.Gender);
        setVal('Shape',               data.Shape);
        setVal('Material',            data.Material);
        setVal('Temple_Detail',       data.Temple_Detail);
        setVal('Bridge_Size',         data.Bridge_Size);
        setVal('Quality',             data.Quality);

        // New Measurements and Inventory fields
        setVal('lens_width',          data.lens_width);
        setVal('temple_length',       data.temple_length);
        setVal('frame_width',         data.frame_width);
        setVal('sunglass_colour',     data.sunglass_colour);
        setVal('stock_quantity',      data.stock_quantity);
        setVal('stock_status',        data.stock_status);
        setVal('polarized',           data.polarized);
        setVal('uv_protection',       data.uv_protection);

        setVal('Purchase_Base_Price', data.Purchase_Base_Price);
        setVal('Purchase_Price',      data.Purchase_Price);
        setVal('Retail_Price',        data.Retail_Price);
        setVal('discount_price',      data.discount_price);
        setVal('tax_hsn_code',        data.tax_hsn_code);
        setVal('BB_Price',            data.BB_Price);

        // Show existing images
        const uploadBase = '{{ asset("uploads") }}';
        if (data.main_image) {
            const wrap = card.querySelector('.main-img-preview');
            const path = `${uploadBase}/${PRODUCT_TYPE.toLowerCase()}/product/${data.parent_product_code || data.product_id}/${data.main_image}`;
            wrap.innerHTML = `<img src="${path}" class="img-thumb">`;
        }
        try {
            const imgs = JSON.parse(data.product_image || '[]');
            if (imgs.length) {
                const wrap = card.querySelector('.gallery-preview');
                wrap.innerHTML = imgs.map(i => {
                    const iPath = `${uploadBase}/${PRODUCT_TYPE.toLowerCase()}/product/${data.parent_product_code || data.product_id}/${i}`;
                    return `
                    <div class="img-preview-item" style="position:relative; display:inline-block; margin-right:5px; margin-bottom:5px;">
                        <img src="${iPath}" class="img-thumb" style="display:block;">
                        <button type="button" class="btn btn-sm btn-danger" 
                                style="position:absolute; top:-5px; right:-5px; border-radius:50%; padding: 0 4px; font-size: 10px; line-height: 1.2;"
                                onclick="removeGalleryImage(this, '${idx}', '${i}')">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>`;
                }).join('');
            }
        } catch(e) {}

        // Update SKU label
        const skuInput = card.querySelector('.sku-input');
        if (skuInput && data.product_code) {
            const label = card.querySelector('.v-sku-label');
            if (label) label.textContent = '– ' + data.product_code;
        }
    }

    updateVariantNumbers();
    updateNoVariantsMsg();
    updateVariantCount();
}

function removeGalleryImage(btn, variantIdx, imageName) {
    if (!confirm('Are you sure you want to remove this image?')) return;
    
    // Add hidden input so backend knows to delete it
    const card = btn.closest('.variant-card');
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = `variants[${variantIdx}][deleted_images][]`;
    hiddenInput.value = imageName;
    card.appendChild(hiddenInput);
    
    // Remove the thumbnail container from the DOM
    btn.closest('.img-preview-item').remove();
}

// ============================================================
// SweetAlert2 UI Helpers
// ============================================================
function showSweetWarning(title, htmlMessage) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'warning',
            title: title || 'Attention Required',
            html: htmlMessage,
            confirmButtonText: 'Got it',
            confirmButtonColor: '#4f46e5',
            customClass: {
                popup: 'rounded-xl shadow-2xl',
                confirmButton: 'px-5 py-2.5 rounded-lg font-medium'
            }
        });
    } else {
        alert(title + '\n' + htmlMessage.replace(/<[^>]*>?/gm, ''));
    }
}

function showSweetToast(icon, message) {
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        Toast.fire({
            icon: icon || 'info',
            title: message
        });
    }
}

// ============================================================
// removeVariant
// ============================================================
function removeVariant(btn) {
    const card = btn.closest('.variant-card');
    if (document.querySelectorAll('.variant-card').length <= 1) {
        showSweetWarning('Cannot Remove Variant', 'A product must have at least one variant.');
        return;
    }
    card.remove();
    updateVariantNumbers();
    updateNoVariantsMsg();
    updateVariantCount();
}

// ============================================================
// toggleVariant
// ============================================================
function toggleVariant(header) {
    const body = header.nextElementSibling;
    if (body) {
        body.style.display = body.style.display === 'none' ? 'block' : 'none';
    }
}

// ============================================================
// helpers
// ============================================================
function updateVariantNumbers() {
    document.querySelectorAll('.variant-card').forEach((card, i) => {
        const numEl = card.querySelector('.v-num');
        if (numEl) numEl.textContent = i + 1;
        const title = card.querySelector('.v-title');
        if (title) {
            const numNode = title.querySelector('.v-num');
            title.childNodes.forEach(n => {
                if (n.nodeType === 3 && n.textContent.includes('Variant #')) {
                    n.textContent = ` Variant #${i + 1} `;
                }
            });
        }
    });
}

function updateVariantCount() {
    const cnt = document.querySelectorAll('.variant-card').length;
    const el  = document.getElementById('variant-count-display');
    if (el) el.textContent = cnt;
}

function updateNoVariantsMsg() {
    const cnt  = document.querySelectorAll('.variant-card').length;
    const msg  = document.getElementById('no-variants-msg');
    if (msg) msg.style.display = cnt === 0 ? 'block' : 'none';
}

function updateSkuLabel(input) {
    const card  = input.closest('.variant-card');
    const label = card.querySelector('.v-sku-label');
    if (label) label.textContent = input.value ? '– ' + input.value : '';
}

// ============================================================
// Image Previews
// ============================================================
function previewMainImage(input) {
    const parent = input.closest('.col-md-4') || input.parentElement;
    const wrap = parent.querySelector('.main-img-preview');
    if (!wrap) return;

    wrap.innerHTML = '';
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 5 * 1024 * 1024) {
            showSweetWarning('File Too Large', `The image <strong>${file.name}</strong> exceeds the 5MB limit.<br>Please upload an image under 5MB.`);
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = e => {
            const img = new Image();
            img.onload = () => {
                const ratio = img.naturalWidth / img.naturalHeight;
                if (ratio < 1.2 || ratio > 3.2) {
                    showSweetWarning('Invalid Aspect Ratio', `
                        Eyewear product photos must be in landscape format with approx <strong>2:1 aspect ratio</strong> (e.g. 800×400px, 1000×500px, 1280×640px).<br><br>
                        <div style="background:#f1f5f9; padding:8px 12px; border-radius:6px; font-size:0.85rem; color:#475569; margin-top:8px;">
                            Selected file: <strong>${img.naturalWidth} × ${img.naturalHeight}px</strong> (Ratio: ${ratio.toFixed(2)}:1)
                        </div>
                    `);
                    input.value = '';
                    wrap.innerHTML = '';
                    return;
                }
                wrap.innerHTML = `<img src="${e.target.result}" class="img-thumb">`;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function previewGallery(input) {
    const parent = input.closest('.col-md-8') || input.parentElement;
    const wrap = parent.querySelector('.gallery-preview');
    if (!wrap) return;

    // Remove only previous local previews, keep existing server images
    wrap.querySelectorAll('.local-preview').forEach(el => el.remove());
    
    let hasOversized = false;
    Array.from(input.files).forEach(file => {
        if (file.size > 5 * 1024 * 1024) {
            hasOversized = true;
            return;
        }
        const reader = new FileReader();
        reader.onload = e => {
            const img = new Image();
            img.onload = () => {
                const ratio = img.naturalWidth / img.naturalHeight;
                if (ratio < 1.2 || ratio > 3.2) {
                    showSweetToast('warning', `Skipped "${file.name}": Non-landscape ratio (${img.naturalWidth}×${img.naturalHeight}px)`);
                    return;
                }
                const previewImg = document.createElement('img');
                previewImg.src = e.target.result;
                previewImg.className = 'img-thumb local-preview';
                wrap.appendChild(previewImg);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    if (hasOversized) {
        showSweetToast('warning', 'Some files were skipped because they exceed the 5MB limit.');
    }
}

// ============================================================
// Autocomplete (variant optical fields)
// ============================================================
let acTimer = null;

function handleAcInput(input) {
    clearTimeout(acTimer);
    const query    = input.value.trim();
    const routeKey = input.getAttribute('data-ac-route');
    const dataKey  = input.getAttribute('data-ac-key');
    const route    = AC_ROUTES[routeKey];

    // Find the sibling dropdown
    const dropdown = input.nextElementSibling;
    if (!dropdown || !dropdown.classList.contains('ac-dropdown')) return;

    if (query.length < 2) { dropdown.style.display = 'none'; return; }

    acTimer = setTimeout(() => {
        fetch(route + '?name=' + encodeURIComponent(query) + '&product_type=' + PRODUCT_TYPE)
            .then(r => r.json())
            .then(data => {
                dropdown.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const a = document.createElement('a');
                        a.textContent = item[dataKey];
                        a.href = '#';
                        a.addEventListener('mousedown', function(e) {
                            e.preventDefault();
                            input.value = this.textContent;
                            dropdown.style.display = 'none';
                        });
                        dropdown.appendChild(a);
                    });
                    dropdown.style.display = 'block';
                } else {
                    dropdown.style.display = 'none';
                }
            })
            .catch(() => dropdown.style.display = 'none');
    }, 250);
}

document.addEventListener('click', function(e) {
    document.querySelectorAll('.ac-dropdown').forEach(d => {
        if (!d.contains(e.target)) d.style.display = 'none';
    });
});

// Global Company autocomplete
function setupGlobalAc(inputId, dropdownId, routeKey, dataKey) {
    const input    = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    if (!input || !dropdown) return;

    input.addEventListener('input', function() {
        clearTimeout(acTimer);
        const q = this.value.trim();
        if (q.length < 2) { dropdown.style.display = 'none'; return; }
        acTimer = setTimeout(() => {
            fetch(AC_ROUTES[routeKey] + '?name=' + encodeURIComponent(q) + '&product_type=' + PRODUCT_TYPE)
                .then(r => r.json())
                .then(data => {
                    dropdown.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const a = document.createElement('a');
                            a.textContent = item[dataKey];
                            a.href = '#';
                            a.addEventListener('mousedown', function(e) {
                                e.preventDefault();
                                input.value = this.textContent;
                                dropdown.style.display = 'none';
                            });
                            dropdown.appendChild(a);
                        });
                        dropdown.style.display = 'block';
                    } else {
                        dropdown.style.display = 'none';
                    }
                });
        }, 250);
    });
}

// ============================================================
// Readiness Checklist
// ============================================================
function evaluateChecklist() {
    let nameInput = document.getElementById('product_name');
    let nameFilled = nameInput ? nameInput.value.trim().length > 0 : false;
    let catInput = document.getElementById('category_id');
    let catSelected = catInput ? catInput.value !== "" : false;
    
    const variants = document.querySelectorAll('.variant-card');
    let hasVariants = variants.length > 0;
    
    let allVariantsValid = hasVariants;
    variants.forEach(card => {
        let skuInput = card.querySelector('input[name$="[product_code]"]');
        let sku = skuInput ? skuInput.value.trim() : '';
        let priceInput = card.querySelector('input[name$="[Retail_Price]"]');
        let price = priceInput ? (parseFloat(priceInput.value) || 0) : 0;
        let imgInput = card.querySelector('input[name$="[main_image]"]');
        let hasImg = (imgInput && imgInput.files && imgInput.files.length > 0) 
            || card.querySelector('.main-img-preview img') !== null 
            || card.querySelector('.current-main-img') !== null
            || card.querySelector('img') !== null;
        
        if (!sku) {
            allVariantsValid = false;
        }
    });

    function setCheckState(id, passed) {
        let li = document.getElementById(id);
        if (!li) return;
        let icon = li.querySelector('i');
        if (passed) {
            icon.className = 'fa fa-check-circle';
            icon.style.color = 'var(--success)';
            li.style.color = 'var(--text)';
        } else {
            icon.className = 'fa fa-times-circle';
            icon.style.color = 'var(--danger)';
            li.style.color = 'var(--muted)';
        }
    }

    setCheckState('check-name', nameFilled);
    setCheckState('check-category', catSelected);
    setCheckState('check-variants', hasVariants);
    setCheckState('check-variant-data', allVariantsValid && hasVariants);
}

function toggleMeasurements(header) {
    const card = header.closest('.variant-card') || header.parentElement;
    const wrapper = card.querySelector('.measurements-collapsible-wrapper');
    const icon = header.querySelector('.meas-toggle-icon');
    if (!wrapper) return;
    
    if (wrapper.style.display === 'none' || wrapper.style.display === '') {
        wrapper.style.display = 'block';
        if (icon) icon.style.transform = 'rotate(180deg)';
    } else {
        wrapper.style.display = 'none';
        if (icon) icon.style.transform = 'rotate(0deg)';
    }
}

function syncColorHex(picker) {
    const parent = picker.closest('div');
    const hexInput = parent.querySelector('.color-hex-text');
    if (hexInput) {
        hexInput.value = picker.value.toUpperCase();
    }
    updateLivePreview();
}

function syncColorPicker(hexInput) {
    const parent = hexInput.closest('div');
    const picker = parent.querySelector('.color-picker-box');
    let val = hexInput.value.trim();
    if (!val.startsWith('#')) {
        val = '#' + val;
    }
    if (/^#[0-9A-F]{6}$/i.test(val) && picker) {
        picker.value = val;
    }
    updateLivePreview();
}

document.addEventListener('DOMContentLoaded', () => {
    // Listen to changes
    const form = document.getElementById('pb-form');
    if (form) {
        form.addEventListener('input', evaluateChecklist);
        form.addEventListener('change', evaluateChecklist);
    }
    
    // Observer for variant nodes being added/removed
    const variantContainer = document.getElementById('variant-list') || document.getElementById('variants-container');
    if (variantContainer) {
        const observer = new MutationObserver(evaluateChecklist);
        observer.observe(variantContainer, { childList: true, subtree: true });
    }
    
    setTimeout(evaluateChecklist, 400);
});

// ============================================================
// Duplicate SKU Validation (Live)
// ============================================================
document.getElementById('pb-form').addEventListener('focusout', function(e) {
    if (e.target && e.target.classList.contains('sku-input')) {
        const sku = e.target.value.trim();
        const errSpan = e.target.closest('.mb-3').querySelector('.sku-err');
        
        if (sku.length >= 3) {
            fetch('{{ route("admin.products.check-sku") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    sku: sku,
                    exclude_id: '{{ $isEdit ? $product_id : "" }}'
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.exists) {
                    e.target.classList.add('is-invalid');
                    if (errSpan) errSpan.innerHTML = '<i class="fa fa-times-circle"></i> This SKU is already in use.';
                } else {
                    e.target.classList.remove('is-invalid');
                    if (errSpan && errSpan.innerHTML.includes('already in use')) {
                        errSpan.innerHTML = '';
                    }
                }
                evaluateChecklist();
            })
            .catch(err => console.error('SKU validation failed:', err));
        }
    }
});

// ============================================================
// Form Submission
// ============================================================
document.getElementById('pb-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const submitBtn = document.getElementById('btn-submit');
    submitBtn.disabled = true;

    // Reset all previous error messages and red highlights
    document.querySelectorAll('.pb-input').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.pb-err').forEach(el => el.innerHTML = '');

    let allErrors = [];
    let firstInvalidElement = null;

    // 1. Validate Product Name
    const pNameInput = document.getElementById('product_name');
    const pName = pNameInput ? pNameInput.value.trim() : '';
    if (!pName) {
        allErrors.push('<i class="fa fa-tag text-primary mr-1"></i> <strong>Product Name:</strong> Please enter a product name at the top.');
        pNameInput.classList.add('is-invalid');
        const pNameErr = document.getElementById('product_nameError');
        if (pNameErr) pNameErr.innerHTML = '<i class="fa fa-exclamation-circle text-danger mr-1"></i> Product Name is required.';
        if (!firstInvalidElement) firstInvalidElement = pNameInput;
    }

    // 2. Validate Category
    const catSelect = document.getElementById('category_id');
    const catVal = catSelect ? catSelect.value : '';
    if (!catVal) {
        allErrors.push('<i class="fa fa-folder text-primary mr-1"></i> <strong>Category:</strong> Please select a category.');
        catSelect.classList.add('is-invalid');
        const catErr = document.getElementById('category_idError');
        if (catErr) catErr.innerHTML = '<i class="fa fa-exclamation-circle text-danger mr-1"></i> Please select a category.';
        if (!firstInvalidElement) firstInvalidElement = catSelect;
    }

    // 3. Validate at least 1 variant
    const variantCards = document.querySelectorAll('.variant-card');
    if (variantCards.length === 0) {
        allErrors.push('<i class="fa fa-plus-circle text-primary mr-1"></i> <strong>Variants:</strong> Please add at least one variant.');
    }

    // 4. Validate each variant (SKU, MRP, Sale Price, Main Image)
    const seenSkus = [];
    variantCards.forEach((card, i) => {
        const vNum = i + 1;

        // SKU validation
        const skuInput = card.querySelector('input[name$="[product_code]"]');
        const skuErr   = card.querySelector('.sku-err');
        const sku      = skuInput ? skuInput.value.trim() : '';

        if (!sku) {
            allErrors.push(`<i class="fa fa-tag text-primary mr-1"></i> <strong>Variant #${vNum}:</strong> SKU code is required.`);
            if (skuInput) skuInput.classList.add('is-invalid');
            if (skuErr) skuErr.innerHTML = '<i class="fa fa-exclamation-circle text-danger mr-1"></i> SKU / Barcode is required.';
            if (!firstInvalidElement) firstInvalidElement = skuInput;
        } else if (sku.length < 3) {
            allErrors.push(`<i class="fa fa-tag text-primary mr-1"></i> <strong>Variant #${vNum}:</strong> SKU code must be at least 3 characters.`);
            if (skuInput) skuInput.classList.add('is-invalid');
            if (skuErr) skuErr.innerHTML = '<i class="fa fa-exclamation-circle text-danger mr-1"></i> SKU must be at least 3 characters.';
            if (!firstInvalidElement) firstInvalidElement = skuInput;
        } else if (seenSkus.includes(sku.toLowerCase())) {
            allErrors.push(`<i class="fa fa-exclamation-triangle text-warning mr-1"></i> <strong>Duplicate SKU:</strong> "${sku}" is used multiple times in this form.`);
            if (skuInput) skuInput.classList.add('is-invalid');
            if (skuErr) skuErr.innerHTML = `<i class="fa fa-exclamation-triangle text-warning mr-1"></i> Duplicate SKU "${sku}" cannot be repeated.`;
            if (!firstInvalidElement) firstInvalidElement = skuInput;
        } else {
            seenSkus.push(sku.toLowerCase());
        }

        // Retail Price (MRP) validation
        const mrpInput = card.querySelector('input[name$="[Retail_Price]"]');
        const mrpErr   = card.querySelector('.retail-price-err');
        const mrp      = mrpInput && mrpInput.value !== '' ? parseFloat(mrpInput.value) : NaN;

        if (isNaN(mrp) || mrp <= 0) {
            allErrors.push(`<i class="fa fa-coins text-danger mr-1"></i> <strong>Variant #${vNum}:</strong> Retail Price (MRP) is required and must be greater than 0.`);
            if (mrpInput) mrpInput.classList.add('is-invalid');
            if (mrpErr) mrpErr.innerHTML = '<i class="fa fa-exclamation-circle text-danger mr-1"></i> MRP is required (> 0).';
            if (!firstInvalidElement) firstInvalidElement = mrpInput;
        }

        // Discount / Sale Price validation
        const saleInput = card.querySelector('input[name$="[discount_price]"]');
        const saleErr   = card.querySelector('.discount-price-err');
        const sale      = saleInput && saleInput.value !== '' ? parseFloat(saleInput.value) : 0;

        if (sale > 0 && !isNaN(mrp) && sale > mrp) {
            allErrors.push(`<i class="fa fa-coins text-danger mr-1"></i> <strong>Pricing Mistake in Variant #${vNum}:</strong> Sale Price (₹${sale}) cannot exceed MRP (₹${mrp}).`);
            if (saleInput) saleInput.classList.add('is-invalid');
            if (saleErr) saleErr.innerHTML = `<i class="fa fa-exclamation-circle text-danger mr-1"></i> Sale Price (₹${sale}) cannot exceed MRP (₹${mrp}).`;
            if (!firstInvalidElement) firstInvalidElement = saleInput;
        }

        // Main Image validation
        const imgInput = card.querySelector('input[name$="[main_image]"]');
        const imgErr   = card.querySelector('.main-image-err');
        const hasImg   = (imgInput && imgInput.files && imgInput.files.length > 0) || card.querySelector('.main-img-preview img') !== null;

        if (!hasImg) {
            allErrors.push(`<i class="fa fa-camera text-primary mr-1"></i> <strong>Variant #${vNum}:</strong> Needs a photo. Click "Choose File" to upload an image.`);
            if (imgInput) imgInput.classList.add('is-invalid');
            if (imgErr) imgErr.innerHTML = '<i class="fa fa-exclamation-circle text-danger mr-1"></i> Main product image is required.';
            if (!firstInvalidElement) firstInvalidElement = imgInput;
        }
    });

    if (allErrors.length > 0) {
        submitBtn.disabled = false;
        showError(allErrors);
        if (firstInvalidElement) {
            firstInvalidElement.focus();
            firstInvalidElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }

    // Build FormData
    const formData = new FormData(this);

    // Determine URL and method
    @if($isEdit)
    const url = '{{ route("admin.products.update", $product_id) }}';
    @else
    const url = '{{ route("admin.products.store") }}';
    @endif

    // Show loader
    document.getElementById('btn-submit').disabled = true;
    document.getElementById('submit-loader').style.display = 'block';

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(async r => {
        const text = await r.text();
        if (!r.ok) {
            try {
                const json = JSON.parse(text);
                if (json.message) return { error: [json.message] };
                if (json.error) return json;
            } catch(e) {}
            throw new Error(`Server Error (${r.status}): ${text.substring(0, 100)}`);
        }
        try {
            return JSON.parse(text);
        } catch(e) {
            throw new Error(`Malformed JSON: ${text.substring(0, 100)}`);
        }
    })
    .then(resp => {
        document.getElementById('btn-submit').disabled = false;
        document.getElementById('submit-loader').style.display = 'none';

        if (resp.error && resp.error.length > 0) {
            showError(resp.error);
        } else if (resp.success) {
            // Show toast then redirect
            if (typeof $.toaster === 'function') {
                $.toaster({ priority: 'success', title: 'Success', message: resp.success });
            }
            setTimeout(() => {
                if (resp.redirect) window.location.href = resp.redirect;
            }, 1200);
        }
    })
    .catch(err => {
        document.getElementById('btn-submit').disabled = false;
        document.getElementById('submit-loader').style.display = 'none';
        showError('Save failed: ' + err.message);
        console.error(err);
    });
});

function humanizeError(rawMsg) {
    if (!rawMsg) return 'An unexpected error occurred.';
    let msg = rawMsg.trim();
    
    // Replace raw database / technical validation strings with clear non-technical guidance using FontAwesome icons
    msg = msg.replace(/The product_name field is required\./i, '<i class="fa fa-tag text-primary mr-1"></i> <strong>Product Name:</strong> Please type a name for this eyewear product at the top.');
    msg = msg.replace(/The product_name field must not be greater than (\d+) characters\./i, '<i class="fa fa-tag text-primary mr-1"></i> <strong>Product Name:</strong> Too long (maximum $1 characters).');
    msg = msg.replace(/The category_id field is required\./i, '<i class="fa fa-folder text-primary mr-1"></i> <strong>Category:</strong> Please select a category (e.g. Eyeglasses, Sunglasses, Contact Lenses).');
    msg = msg.replace(/The variants field is required\./i, '<i class="fa fa-plus-circle text-primary mr-1"></i> <strong>Variants:</strong> Please add at least one color variant before saving.');
    msg = msg.replace(/The product_type field is required\./i, '<i class="fa fa-exclamation-triangle text-warning mr-1"></i> <strong>Product Type:</strong> Please select a category to assign the product type.');
    
    // Variant-specific humanization
    msg = msg.replace(/Variant #(\d+): SKU is required\./i, '<i class="fa fa-tag text-primary mr-1"></i> <strong>Variant #$1:</strong> Missing SKU / Product Barcode code.');
    msg = msg.replace(/Variant #(\d+): SKU must be at least (\d+) characters\./i, '<i class="fa fa-tag text-primary mr-1"></i> <strong>Variant #$1:</strong> SKU code must be at least $2 letters.');
    msg = msg.replace(/Variant #(\d+): SKU '([^']+)' already exists\./i, '<i class="fa fa-exclamation-triangle text-warning mr-1"></i> <strong>Variant #$1:</strong> SKU "$2" is already used by an existing product in your inventory. Please use a unique SKU.');
    msg = msg.replace(/Variant #(\d+): Retail Price \(MRP\) cannot be negative\./i, '<i class="fa fa-coins text-danger mr-1"></i> <strong>Variant #$1:</strong> Retail Price (MRP) cannot be a negative number.');
    msg = msg.replace(/Variant #(\d+): Discount \/ Sale Price \(₹([^)]+)\) cannot exceed Retail Price \(₹([^)]+)\)\./i, '<i class="fa fa-coins text-danger mr-1"></i> <strong>Pricing Mistake in Variant #$1:</strong> Sale Price (₹$2) cannot be higher than MRP (₹$3).');
    msg = msg.replace(/Variant #(\d+): Main Image is required/i, '<i class="fa fa-camera text-primary mr-1"></i> <strong>Variant #$1:</strong> Needs a photo. Click "Choose File" to upload an image.');
    msg = msg.replace(/Variant #(\d+): Main Image must be in landscape ~?2:1 ratio[^\.]*\./i, '<i class="fa fa-camera text-primary mr-1"></i> <strong>Variant #$1:</strong> Main Image must be in landscape ~2:1 ratio (e.g. 800×400px).');
    
    return msg;
}

function showError(errors) {
    let errorList = Array.isArray(errors) ? errors : [errors];
    
    // Remove existing error container if present
    const existing = document.getElementById('human-error-banner');
    if (existing) existing.remove();

    const banner = document.createElement('div');
    banner.id = 'human-error-banner';
    banner.style.cssText = `
        background: #fef2f2;
        border: 1.5px solid #f87171;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.12);
        animation: fadeInShake 0.35s ease-in-out;
    `;

    let html = `
        <div style="display: flex; align-items: flex-start; gap: 14px;">
            <div style="background: #ef4444; color: #fff; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.1rem; font-weight: 700; box-shadow: 0 2px 6px rgba(239,68,68,0.3);">
                <i class="fa fa-exclamation"></i>
            </div>
            <div style="flex: 1;">
                <h4 style="margin: 0 0 6px 0; color: #991b1b; font-size: 0.96rem; font-weight: 700;">
                    Please check the following items before saving:
                </h4>
                <ul style="margin: 0; padding-left: 18px; color: #b91c1c; font-size: 0.88rem; line-height: 1.6;">
    `;

    errorList.forEach(err => {
        const friendlyMsg = humanizeError(err);
        html += `<li style="margin-bottom: 4px;">${friendlyMsg}</li>`;
    });

    html += `
                </ul>
            </div>
            <button type="button" onclick="this.closest('#human-error-banner').remove()" style="background: transparent; border: none; color: #991b1b; font-size: 1.3rem; cursor: pointer; padding: 0 6px; line-height: 1;" title="Dismiss">&times;</button>
        </div>
    `;

    banner.innerHTML = html;

    const mainContainer = document.querySelector('.pb-form-wrap') || document.querySelector('.pb-page') || document.body;
    mainContainer.prepend(banner);

    // Smooth scroll directly to the banner
    banner.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
/* ═══════════════════════════════════════════════
   Package Section — Smart UX (redesigned)
═══════════════════════════════════════════════ */
(function () {
    const packageSection   = document.getElementById('package-section');
    const typeInputs       = document.querySelectorAll('input[name="supported_product_types[]"]');
    const pkgGroups        = document.querySelectorAll('.pkg-type-group:not(.pkg-uncategorized)');
    const noMatchMsg       = document.getElementById('pkg-no-match-msg');
    const badge            = document.getElementById('pkg-selected-badge');
    const pkgItems         = document.querySelectorAll('.pkg-item');

    /* ── helpers ── */
    function getCheckedTypeIds() {
        return Array.from(typeInputs).filter(i => i.checked).map(i => i.value.toString());
    }

    function updateBadge() {
        if (!badge) return;
        const total = document.querySelectorAll('.pkg-item input[type="checkbox"]:checked').length;
        if (total > 0) {
            badge.textContent = total + ' package' + (total > 1 ? 's' : '') + ' selected';
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    function applyItemStyle(label) {
        const cb = label.querySelector('input[type="checkbox"]');
        if (!cb) return;
        if (cb.checked) {
            label.style.borderColor = '#4f46e5';
            label.style.background  = 'rgba(79,70,229,.06)';
        } else {
            label.style.borderColor = '#e5e7eb';
            label.style.background  = '#fff';
        }
    }

    function showHideSection() {
        if (!packageSection) return;
        const anyChecked = getCheckedTypeIds().length > 0;
        if (anyChecked) {
            packageSection.style.display = 'block';
        } else {
            packageSection.style.display = 'none';
        }
    }

    function refreshGroups() {
        const checkedIds = getCheckedTypeIds();
        let totalVisiblePackages = 0;

        pkgGroups.forEach(group => {
            const typeId = group.getAttribute('data-type-id');
            if (checkedIds.includes(typeId)) {
                group.style.display = 'block';
                totalVisiblePackages += group.querySelectorAll('.pkg-item').length;
            } else {
                group.style.display = 'none';
                // Uncheck hidden packages so they are not submitted
                group.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    cb.checked = false;
                    applyItemStyle(cb.closest('.pkg-item'));
                });
            }
        });

        // Show empty state message if no packages belong to selected type
        if (noMatchMsg) {
            noMatchMsg.style.display = (totalVisiblePackages === 0 && checkedIds.length > 0) ? 'block' : 'none';
        }

        updateBadge();
    }

    /* ── Listeners: product type chip changes ── */
    typeInputs.forEach(input => {
        input.addEventListener('change', function () {
            showHideSection();
            refreshGroups();

            // Smooth scroll into view when section appears
            if (this.checked && packageSection && packageSection.style.display !== 'none') {
                setTimeout(() => {
                    packageSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 80);
            }
        });
    });

    /* ── Select All / Clear buttons ── */
    document.querySelectorAll('.pkg-select-all').forEach(btn => {
        btn.addEventListener('click', function () {
            const typeId = this.getAttribute('data-type-id');
            document.querySelectorAll('.pkg-item[data-type-id="' + typeId + '"] input[type="checkbox"]').forEach(cb => {
                cb.checked = true;
                applyItemStyle(cb.closest('.pkg-item'));
            });
            updateBadge();
        });
    });

    document.querySelectorAll('.pkg-clear-all').forEach(btn => {
        btn.addEventListener('click', function () {
            const typeId = this.getAttribute('data-type-id');
            document.querySelectorAll('.pkg-item[data-type-id="' + typeId + '"] input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
                applyItemStyle(cb.closest('.pkg-item'));
            });
            updateBadge();
        });
    });

    /* ── Hover & check styling on each package item ── */
    pkgItems.forEach(item => {
        item.addEventListener('mouseenter', function () {
            const cb = this.querySelector('input[type="checkbox"]');
            if (cb && !cb.checked) {
                this.style.borderColor = '#a5b4fc';
                this.style.background  = '#f5f3ff';
            }
        });
        item.addEventListener('mouseleave', function () { applyItemStyle(this); });

        const cb = item.querySelector('input[type="checkbox"]');
        if (cb) {
            cb.addEventListener('change', function () {
                applyItemStyle(item);
                updateBadge();
            });
        }

        // Set initial style
        applyItemStyle(item);
    });

    /* ── Initial run on page load ── */
    showHideSection();
    refreshGroups();
})();

</script>
@endsection