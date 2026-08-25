@extends('website.layout.master')
@section('content')

{{-- ══════════════════════════════════════════════════════════════════════════════
     PREMIUM MY PRESCRIPTIONS — SPECKART
     Clinical-grade, modern responsive UI for saved eye powers & doctor slips
══════════════════════════════════════════════════════════════════════════════ --}}

<style>
/* ==========================================================================
   MY PRESCRIPTIONS — ULTRA PREMIUM MODERN STYLES
   ========================================================================== */
:root {
    --rx-primary: #07484A;
    --rx-primary-dark: #032b2d;
    --rx-teal: #00B9B9;
    --rx-teal-light: #e6f9f9;
    --rx-gold: #fbbf24;
    --rx-bg: #f8fafc;
    --rx-card-bg: #ffffff;
    --rx-border: #e2e8f0;
    --rx-text-main: #0f172a;
    --rx-text-muted: #64748b;
}

.prescription-page-wrap {
    background: linear-gradient(180deg, #f0f7f7 0%, #f8fafc 180px, #f8fafc 100%);
    min-height: 85vh;
    padding: 30px 0 70px;
    font-family: 'Poppins', sans-serif;
}

/* ── Breadcrumb Navigation ── */
.rx-breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--rx-text-muted);
    margin-bottom: 22px;
}

.rx-breadcrumb-nav a {
    color: var(--rx-text-muted);
    text-decoration: none;
    transition: color 0.2s ease;
}

.rx-breadcrumb-nav a:hover {
    color: var(--rx-primary);
}

.rx-breadcrumb-nav i {
    font-size: 10px;
    color: #94a3b8;
}

.rx-breadcrumb-nav .active {
    color: var(--rx-primary);
    font-weight: 600;
}

/* ── Hero Banner & Actions ── */
.rx-hero-card {
    background: linear-gradient(135deg, #07484A 0%, #0a5658 50%, #0c676a 100%);
    border-radius: 20px;
    padding: 30px 32px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 36px -10px rgba(7, 72, 74, 0.28);
    margin-bottom: 30px;
}

.rx-hero-card::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -40px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0, 185, 185, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
    pointer-events: none;
}

.rx-hero-content h2 {
    font-size: 26px;
    font-weight: 800;
    margin-bottom: 6px;
    letter-spacing: -0.3px;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
}

.rx-hero-content p {
    font-size: 14px;
    color: #ccfbf1;
    margin-bottom: 0;
    max-width: 580px;
    line-height: 1.5;
}

.rx-hero-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-rx-primary {
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

.btn-rx-primary:hover {
    background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(245, 158, 11, 0.4);
    color: #032b2d !important;
}

.btn-rx-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    font-weight: 600;
    font-size: 13.5px;
    padding: 11px 20px;
    border-radius: 12px;
    border: 1.5px solid rgba(255, 255, 255, 0.35);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    backdrop-filter: blur(8px);
    transition: all 0.25s ease;
    text-decoration: none !important;
    cursor: pointer;
}

.btn-rx-outline:hover {
    background: #ffffff;
    color: #07484A !important;
    border-color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

/* ── Trust Pillars Strip ── */
.rx-trust-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 30px;
}

.rx-trust-item {
    background: #ffffff;
    border: 1px solid #eef2f6;
    border-radius: 14px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}

.rx-trust-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #f0fdfa;
    color: #0d9488;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.rx-trust-info h6 {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 2px;
}

.rx-trust-info p {
    font-size: 11.5px;
    color: #64748b;
    margin: 0;
    line-height: 1.3;
}

/* ── Prescription Grid Cards ── */
.rx-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 22px;
    box-shadow: 0 6px 20px rgba(7, 72, 74, 0.04);
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

.rx-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 36px rgba(7, 72, 74, 0.1);
    border-color: rgba(7, 72, 74, 0.3);
}

.rx-card-top-accent {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3.5px;
    background: linear-gradient(90deg, #07484A, #00B9B9);
}

.rx-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
}

.rx-card-title-group {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    min-width: 0;
    flex: 1;
}

.rx-type-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f0fdfa 0%, #e6f9f9 100%);
    border: 1px solid #ccfbf1;
    color: #0f766e;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.rx-card-name {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 4px;
    line-height: 1.25;
    word-break: break-word;
}

.rx-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 20px;
    line-height: 1.2;
}

.badge-power-type {
    background: #e0f2fe;
    color: #0369a1;
}

.badge-clinic-test {
    background: #fef3c7;
    color: #92400e;
}

.rx-delete-btn {
    background: #fff;
    border: 1px solid #fee2e2;
    color: #ef4444;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
    padding: 0;
}

.rx-delete-btn:hover {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fca5a5;
    transform: scale(1.08);
}

/* ── Modern Clinical OD / OS Table ── */
.rx-power-table-wrapper {
    background: #f8fafc;
    border: 1px solid #edf2f7;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 16px;
}

.rx-modern-table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: collapse;
    font-size: 12.5px;
}

.rx-modern-table thead th {
    background: #f1f5f9;
    color: #475569;
    font-weight: 700;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 8px 10px;
    text-align: center;
    border-bottom: 1px solid #e2e8f0;
}

.rx-modern-table tbody td {
    padding: 9px 10px;
    text-align: center;
    color: #1e293b;
    font-weight: 600;
    border-bottom: 1px solid #edf2f7;
}

.rx-modern-table tbody tr:last-child td {
    border-bottom: none;
}

.eye-col-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-weight: 800;
    font-size: 11.5px;
    color: #07484A;
    background: #e6f9f9;
    padding: 2px 8px;
    border-radius: 6px;
}

.param-val {
    font-family: 'Poppins', monospace;
    font-weight: 700;
    color: #0f172a;
    font-size: 13px;
}

.param-empty {
    color: #94a3b8;
    font-weight: 400;
}

/* ── File Preview Box (Slip / PDF) ── */
.rx-file-preview-card {
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 12px;
    padding: 14px;
    text-align: center;
    margin-bottom: 16px;
    transition: all 0.2s ease;
    position: relative;
}

.rx-file-preview-card:hover {
    border-color: #00B9B9;
    background: #f0fdfa;
}

.rx-img-thumb {
    max-height: 150px;
    width: auto;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s ease;
}

.rx-img-thumb:hover {
    transform: scale(1.02);
}

.rx-pdf-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 12px;
}

.btn-view-slip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: #07484A;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 6px 14px;
    border-radius: 8px;
    text-decoration: none !important;
    transition: all 0.2s ease;
}

.btn-view-slip:hover {
    background: #07484A;
    color: #ffffff !important;
    border-color: #07484A;
}

/* Remarks Note */
.rx-remarks-note {
    background: #fffbeb;
    border-left: 3px solid #f59e0b;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    color: #78350f;
    margin-bottom: 12px;
    line-height: 1.4;
}

/* ── Card Footer ── */
.rx-card-footer {
    margin-top: auto;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11.5px;
    color: #64748b;
}

.rx-pd-badge {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #0f172a;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
}

/* ── Empty State ── */
.rx-empty-state {
    background: #ffffff;
    border: 1.5px dashed #cbd5e1;
    border-radius: 24px;
    padding: 60px 24px;
    text-align: center;
    max-width: 680px;
    margin: 20px auto;
}

.rx-empty-icon-wrap {
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

.rx-empty-state h4 {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 8px;
}

.rx-empty-state p {
    font-size: 13.5px;
    color: #64748b;
    max-width: 440px;
    margin: 0 auto 24px;
    line-height: 1.5;
}

/* ── Modals Modern Styling ── */
.rx-modal .modal-content {
    border-radius: 22px;
    border: none;
    box-shadow: 0 24px 60px rgba(7, 72, 74, 0.25);
    overflow: hidden;
}

.rx-modal .modal-header {
    background: linear-gradient(135deg, #07484A 0%, #0a5658 100%);
    color: #ffffff;
    padding: 18px 24px;
    border-bottom: none;
}

.rx-modal .modal-title {
    font-size: 17px;
    font-weight: 700;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
}

.rx-modal .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.rx-modal .btn-close:hover {
    opacity: 1;
}

.rx-modal .modal-body {
    padding: 24px;
}

.rx-form-label {
    font-size: 12.5px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
    display: block;
}

.rx-form-control {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13.5px;
    color: #0f172a;
    transition: all 0.2s ease;
}

.rx-form-control:focus {
    border-color: #00B9B9;
    box-shadow: 0 0 0 3px rgba(0, 185, 185, 0.15);
    outline: none;
}

/* Drag Drop File Zone */
.rx-dropzone {
    border: 2px dashed #cbd5e1;
    background: #f8fafc;
    border-radius: 14px;
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.rx-dropzone:hover {
    border-color: #00B9B9;
    background: #f0fdfa;
}

.rx-dropzone input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.rx-dropzone-icon {
    font-size: 32px;
    color: #00B9B9;
    margin-bottom: 8px;
}

/* Eye Parameter Input Matrix */
.rx-input-matrix {
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    overflow: hidden;
}

.rx-matrix-table {
    width: 100%;
    margin: 0;
    border-collapse: collapse;
}

.rx-matrix-table th {
    background: #f1f5f9;
    font-size: 11.5px;
    font-weight: 700;
    color: #475569;
    padding: 8px;
    text-align: center;
}

.rx-matrix-table td {
    padding: 6px;
    text-align: center;
    background: #ffffff;
    border-top: 1px solid #edf2f7;
}

.rx-matrix-table input {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px;
    font-size: 12.5px;
    font-weight: 600;
    text-align: center;
    width: 100%;
    transition: all 0.2s ease;
}

.rx-matrix-table input:focus {
    border-color: #00B9B9;
    box-shadow: 0 0 0 2px rgba(0, 185, 185, 0.2);
    outline: none;
}

/* ── Responsive ── */
@media (max-width: 991px) {
    .rx-trust-strip {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    .rx-hero-card {
        padding: 22px 20px;
    }
    .rx-hero-content h2 {
        font-size: 22px;
    }
}

@media (max-width: 575px) {
    .prescription-page-wrap {
        padding: 20px 0 50px;
    }
    .rx-hero-actions {
        width: 100%;
        margin-top: 14px;
    }
    .btn-rx-primary, .btn-rx-outline {
        width: 100%;
        justify-content: center;
    }
    .rx-modern-table thead th,
    .rx-modern-table tbody td {
        padding: 6px 4px;
        font-size: 11px;
    }
    .param-val {
        font-size: 11.5px;
    }
}
</style>

<div class="prescription-page-wrap">
    <div class="container">

        <!-- Breadcrumbs -->
        <div class="rx-breadcrumb-nav">
            <a href="{{ route('home') }}"><i class="bi bi-house-door-fill"></i> Home</a>
            <i class="bi bi-chevron-right"></i>
            <a href="{{ route('profile') }}">My Account</a>
            <i class="bi bi-chevron-right"></i>
            <span class="active">My Prescriptions</span>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-3 shadow-sm" style="background:#e6fdf5; color:#065f46;">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 shadow-sm" style="background:#fef2f2; color:#991b1b;">
                <div class="d-flex align-items-center gap-2 mb-1 fw-bold">
                    <i class="bi bi-exclamation-triangle-fill"></i> Please correct the following:
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Hero Card with Quick CTAs -->
        <div class="rx-hero-card">
            <div class="row align-items-center gy-3">
                <div class="col-lg-7">
                    <div class="rx-hero-content">
                        <h2><i class="bi bi-file-earmark-medical-fill text-warning"></i> My Eye Prescriptions</h2>
                        <p>Manage your saved prescription powers and doctor slips. Easily apply them during checkout for precision-crafted lenses.</p>
                    </div>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <div class="rx-hero-actions justify-content-lg-end">
                        <button type="button" class="btn-rx-primary" data-bs-toggle="modal" data-bs-target="#uploadRxModal">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                            <span>Upload Doctor Slip</span>
                        </button>
                        <button type="button" class="btn-rx-outline" data-bs-toggle="modal" data-bs-target="#manualRxModal">
                            <i class="bi bi-pencil-square"></i>
                            <span>Enter Power Manually</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trust Highlights Strip -->
        <div class="rx-trust-strip">
            <div class="rx-trust-item">
                <div class="rx-trust-icon"><i class="bi bi-shield-check"></i></div>
                <div class="rx-trust-info">
                    <h6>Doctor Slip Verification</h6>
                    <p>Every uploaded slip is verified by in-house optometrists</p>
                </div>
            </div>
            <div class="rx-trust-item">
                <div class="rx-trust-icon"><i class="bi bi-lightning-charge-fill"></i></div>
                <div class="rx-trust-info">
                    <h6>1-Click Lens Checkout</h6>
                    <p>Apply saved powers instantly on any frame</p>
                </div>
            </div>
            <div class="rx-trust-item">
                <div class="rx-trust-icon"><i class="bi bi-lock-fill"></i></div>
                <div class="rx-trust-info">
                    <h6>100% Medical Privacy</h6>
                    <p>Your ophthalmic records are securely encrypted</p>
                </div>
            </div>
        </div>

        <!-- Prescriptions Grid -->
        <div class="row g-4">

            @forelse($prescriptions as $rx)
                <div class="col-lg-6">
                    <div class="rx-card">
                        <div class="rx-card-top-accent"></div>

                        <!-- Card Top Bar -->
                        <div class="rx-card-header">
                            <div class="rx-card-title-group">
                                <div class="rx-type-avatar">
                                    @if(!empty($rx->rx_file))
                                        <i class="bi bi-file-earmark-medical"></i>
                                    @else
                                        <i class="bi bi-eyeglasses"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h5 class="rx-card-name">{{ $rx->prescription_name }}</h5>
                                    <span class="rx-badge-pill badge-power-type">
                                        <i class="bi bi-check-circle-fill"></i> {{ $rx->power_type ?? 'Single Vision' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Delete Action Button -->
                            <form action="{{ route('my-prescriptions.delete', $rx->id) }}" method="POST" class="delete-rx-form m-0">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        class="rx-delete-btn delete-rx-btn" 
                                        title="Delete Prescription"
                                        data-rx-name="{{ addslashes($rx->prescription_name) }}">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Card Body Content -->
                        @if(!empty($rx->rx_file))
                            <!-- Uploaded Doctor Slip Card -->
                            @php
                                $isPdf = strtolower(pathinfo($rx->rx_file, PATHINFO_EXTENSION)) === 'pdf';
                            @endphp

                            <div class="rx-file-preview-card">
                                @if($isPdf)
                                    <div class="rx-pdf-box">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-1"></i>
                                        <div class="text-start">
                                            <div class="fw-bold text-dark mb-1" style="font-size: 13px;">Doctor Prescription PDF</div>
                                            <a href="{{ asset($rx->rx_file) }}" target="_blank" class="btn-view-slip">
                                                <i class="bi bi-box-arrow-up-right"></i> View Document
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ asset($rx->rx_file) }}" target="_blank" title="Click to view full image">
                                        <img src="{{ asset($rx->rx_file) }}" alt="Prescription Slip" class="rx-img-thumb img-fluid">
                                    </a>
                                    <div class="mt-2">
                                        <a href="{{ asset($rx->rx_file) }}" target="_blank" class="btn-view-slip">
                                            <i class="bi bi-arrows-fullscreen"></i> View Full Slip
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <!-- Clinical Parameter OD/OS Matrix Table -->
                            <div class="rx-power-table-wrapper">
                                <table class="rx-modern-table">
                                    <thead>
                                        <tr>
                                            <th>Eye</th>
                                            <th>SPH (Sphere)</th>
                                            <th>CYL (Cylinder)</th>
                                            <th>Axis</th>
                                            <th>Add</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="eye-col-tag">OD (Right)</span></td>
                                            <td><span class="{{ $rx->r_sph ? 'param-val' : 'param-empty' }}">{{ $rx->r_sph ?: '0.00' }}</span></td>
                                            <td><span class="{{ $rx->r_cyl ? 'param-val' : 'param-empty' }}">{{ $rx->r_cyl ?: '0.00' }}</span></td>
                                            <td><span class="{{ $rx->r_axis ? 'param-val' : 'param-empty' }}">{{ $rx->r_axis ? $rx->r_axis . '°' : '-' }}</span></td>
                                            <td><span class="{{ $rx->r_add ? 'param-val' : 'param-empty' }}">{{ $rx->r_add ?: '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><span class="eye-col-tag">OS (Left)</span></td>
                                            <td><span class="{{ $rx->l_sph ? 'param-val' : 'param-empty' }}">{{ $rx->l_sph ?: '0.00' }}</span></td>
                                            <td><span class="{{ $rx->l_cyl ? 'param-val' : 'param-empty' }}">{{ $rx->l_cyl ?: '0.00' }}</span></td>
                                            <td><span class="{{ $rx->l_axis ? 'param-val' : 'param-empty' }}">{{ $rx->l_axis ? $rx->l_axis . '°' : '-' }}</span></td>
                                            <td><span class="{{ $rx->l_add ? 'param-val' : 'param-empty' }}">{{ $rx->l_add ?: '-' }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!-- Optional Doctor Notes / Remarks -->
                        @if(!empty($rx->remarks))
                            <div class="rx-remarks-note">
                                <i class="bi bi-info-circle-fill me-1"></i> <strong>Note:</strong> {{ $rx->remarks }}
                            </div>
                        @endif

                        <!-- Card Footer Info -->
                        <div class="rx-card-footer">
                            <span>
                                <i class="bi bi-calendar3 me-1"></i> Saved on {{ \Carbon\Carbon::parse($rx->created_at)->format('d M Y') }}
                            </span>
                            @if(!empty($rx->pd))
                                <span class="rx-pd-badge">
                                    <i class="bi bi-rulers text-teal"></i> PD: {{ $rx->pd }} mm
                                </span>
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                @if($eyeTests->isEmpty())
                    <div class="col-12">
                        <div class="rx-empty-state">
                            <div class="rx-empty-icon-wrap">
                                <i class="bi bi-file-earmark-medical"></i>
                            </div>
                            <h4>No Saved Prescriptions Found</h4>
                            <p>Upload your doctor's slip or manually enter your eye power once to enjoy seamless 1-click ordering on all glasses.</p>
                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <button type="button" class="btn-rx-primary" data-bs-toggle="modal" data-bs-target="#uploadRxModal">
                                    <i class="bi bi-cloud-arrow-up-fill"></i> Upload Doctor Slip
                                </button>
                                <button type="button" class="btn-rx-outline" style="background:#07484A; color:#fff !important;" data-bs-toggle="modal" data-bs-target="#manualRxModal">
                                    <i class="bi bi-pencil-square"></i> Enter Power Manually
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endforelse

            <!-- In-Clinic Eye Test Checkup Records -->
            @foreach($eyeTests as $test)
                <div class="col-lg-6">
                    <div class="rx-card">
                        <div class="rx-card-top-accent" style="background: linear-gradient(90deg, #f59e0b, #fbbf24);"></div>

                        <div class="rx-card-header">
                            <div class="rx-card-title-group">
                                <div class="rx-type-avatar" style="background: #fef3c7; color: #b45309; border-color: #fde68a;">
                                    <i class="bi bi-clipboard2-pulse"></i>
                                </div>
                                <div>
                                    <h5 class="rx-card-name">Optometrist Clinic Checkup</h5>
                                    <span class="rx-badge-pill badge-clinic-test">
                                        <i class="bi bi-patch-check-fill"></i> In-Clinic Eye Test
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="rx-power-table-wrapper">
                            <table class="rx-modern-table">
                                <thead>
                                    <tr>
                                        <th>Eye</th>
                                        <th>SPH</th>
                                        <th>CYL</th>
                                        <th>Axis</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="eye-col-tag">OD (Right)</span></td>
                                        <td><span class="{{ $test->re_sph ? 'param-val' : 'param-empty' }}">{{ $test->re_sph ?: '0.00' }}</span></td>
                                        <td><span class="{{ $test->re_cyl ? 'param-val' : 'param-empty' }}">{{ $test->re_cyl ?: '0.00' }}</span></td>
                                        <td><span class="{{ $test->re_axis ? 'param-val' : 'param-empty' }}">{{ $test->re_axis ? $test->re_axis . '°' : '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="eye-col-tag">OS (Left)</span></td>
                                        <td><span class="{{ $test->le_sph ? 'param-val' : 'param-empty' }}">{{ $test->le_sph ?: '0.00' }}</span></td>
                                        <td><span class="{{ $test->le_cyl ? 'param-val' : 'param-empty' }}">{{ $test->le_cyl ?: '0.00' }}</span></td>
                                        <td><span class="{{ $test->le_axis ? 'param-val' : 'param-empty' }}">{{ $test->le_axis ? $test->le_axis . '°' : '-' }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="rx-card-footer">
                            <span><i class="bi bi-calendar3 me-1"></i> Test Date: {{ \Carbon\Carbon::parse($test->created_at)->format('d M Y') }}</span>
                            <span class="fw-semibold text-dark"><i class="bi bi-person-fill text-teal me-1"></i> {{ $test->cust_name }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

    </div>
</div>

<!-- ==========================================================================
     MODAL 1: UPLOAD DOCTOR SLIP
     ========================================================================== -->
<div class="modal fade rx-modal" id="uploadRxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-cloud-arrow-up-fill text-warning"></i> Upload Doctor Prescription Slip
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('my-prescriptions.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label class="rx-form-label">Prescription Label / Title</label>
                        <input type="text" name="prescription_name" class="form-control rx-form-control" placeholder="e.g. Doctor Slip (August 2026)">
                    </div>

                    <div class="mb-3">
                        <label class="rx-form-label">Power Type *</label>
                        <select name="power_type" class="form-select rx-form-control">
                            <option value="Single Vision" selected>Single Vision (Distance / Reading)</option>
                            <option value="Bifocal">Bifocal (Distance + Near)</option>
                            <option value="Progressive">Progressive (Multi-Focal)</option>
                            <option value="Contact Lens">Contact Lens Power</option>
                        </select>
                    </div>

                    <!-- File Dropzone -->
                    <div class="mb-3">
                        <label class="rx-form-label">Select Prescription File (Photo / PDF) *</label>
                        <div class="rx-dropzone" id="rxDropzone">
                            <input type="file" name="rx_file" id="rxFileInput" accept=".jpg,.jpeg,.png,.pdf,.webp" required onchange="handleFileSelect(this)">
                            <div class="rx-dropzone-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                            <div class="fw-bold text-dark mb-1" id="rxFileName">Click to Browse or Drag &amp; Drop</div>
                            <div class="text-muted small">Supported: JPG, PNG, WEBP, PDF (Max 10MB)</div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="rx-form-label">Doctor / Clinic Notes (Optional)</label>
                        <textarea name="remarks" class="form-control rx-form-control" rows="2" placeholder="e.g. Prescribed by Dr. Sharma for anti-glare screen work"></textarea>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-3 px-3 fw-semibold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-rx-primary">
                        <i class="bi bi-check2-circle"></i> Save &amp; Upload Slip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL 2: ENTER EYE POWER MANUALLY
     ========================================================================== -->
<div class="modal fade rx-modal" id="manualRxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square text-warning"></i> Enter Eye Power Manually
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('my-prescriptions.manual') }}" method="POST">
                @csrf
                <div class="modal-body">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="rx-form-label">Prescription Label</label>
                            <input type="text" name="prescription_name" class="form-control rx-form-control" placeholder="e.g. Distance / Office Specs Power">
                        </div>
                        <div class="col-md-6">
                            <label class="rx-form-label">Power Type *</label>
                            <select name="power_type" class="form-select rx-form-control" required>
                                <option value="Single Vision" selected>Single Vision</option>
                                <option value="Bifocal">Bifocal</option>
                                <option value="Progressive">Progressive</option>
                                <option value="Contact Lens">Contact Lens</option>
                            </select>
                        </div>
                    </div>

                    <!-- Clinical Matrix Table Inputs -->
                    <div class="mb-3">
                        <label class="rx-form-label d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-eye-fill text-teal me-1"></i> Eye Power Parameters</span>
                            <small class="text-muted fw-normal">Values typically range from -20.00 to +20.00</small>
                        </label>

                        <div class="rx-input-matrix table-responsive">
                            <table class="rx-matrix-table">
                                <thead>
                                    <tr>
                                        <th style="width: 22%;">Eye</th>
                                        <th>SPH (Sphere)</th>
                                        <th>CYL (Cylinder)</th>
                                        <th>Axis (Degree)</th>
                                        <th>Add (Near)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span class="eye-col-tag">Right Eye (OD)</span>
                                        </td>
                                        <td><input type="text" name="r_sph" placeholder="-0.00" class="form-control-sm"></td>
                                        <td><input type="text" name="r_cyl" placeholder="-0.00" class="form-control-sm"></td>
                                        <td><input type="text" name="r_axis" placeholder="00" maxlength="2" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);" class="form-control-sm"></td>
                                        <td><input type="text" name="r_add" placeholder="+0.00" class="form-control-sm"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="eye-col-tag">Left Eye (OS)</span>
                                        </td>
                                        <td><input type="text" name="l_sph" placeholder="-0.00" class="form-control-sm"></td>
                                        <td><input type="text" name="l_cyl" placeholder="-0.00" class="form-control-sm"></td>
                                        <td><input type="text" name="l_axis" placeholder="00" maxlength="2" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);" class="form-control-sm"></td>
                                        <td><input type="text" name="l_add" placeholder="+0.00" class="form-control-sm"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="rx-form-label">Pupillary Distance (PD in mm)</label>
                            <input type="text" name="pd" class="form-control rx-form-control" placeholder="e.g. 62 mm (Optional)">
                        </div>
                        <div class="col-md-6">
                            <label class="rx-form-label">Remarks / Special Instructions</label>
                            <input type="text" name="remarks" class="form-control rx-form-control" placeholder="e.g. For screen use &amp; night driving">
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-3 px-3 fw-semibold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-rx-primary">
                        <i class="bi bi-check2-circle"></i> Save Prescription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL 3: CONFIRM DELETE
     ========================================================================== -->
<div class="modal fade rx-modal" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:64px; height:64px; background:#fee2e2;">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2 text-dark">Delete this prescription?</h5>
                <p class="text-muted mb-0 small">
                    Are you sure you want to delete <strong id="confirmRxName" class="text-dark"></strong>? This record will be permanently removed.
                </p>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4 pt-0 d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-light rounded-3 flex-fill fw-semibold py-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger rounded-3 flex-fill fw-semibold py-2" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
function handleFileSelect(input) {
    const label = document.getElementById('rxFileName');
    if (input.files && input.files[0]) {
        label.innerHTML = '<i class="bi bi-file-earmark-check text-success me-1"></i> ' + input.files[0].name;
    }
}

(function() {
    let formToDelete = null;
    const confirmModalEl = document.getElementById('confirmDeleteModal');
    if (!confirmModalEl) return;

    const confirmModal = new bootstrap.Modal(confirmModalEl);
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const confirmName = document.getElementById('confirmRxName');

    document.querySelectorAll('.delete-rx-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            formToDelete = btn.closest('.delete-rx-form');
            confirmName.textContent = '"' + (btn.getAttribute('data-rx-name') || 'this item') + '"';
            confirmModal.show();
        });
    });

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (!formToDelete) return;
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Deleting…';
            formToDelete.submit();
        });
    }

    confirmModalEl.addEventListener('hidden.bs.modal', function() {
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Delete';
        }
        formToDelete = null;
    });
})();
</script>

@endsection