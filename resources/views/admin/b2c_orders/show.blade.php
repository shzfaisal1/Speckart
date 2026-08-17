@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap');

.order-detail-wrap {
    font-family: 'Inter', sans-serif;
    padding: 0 4px;
}
.order-header-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 20px 24px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.order-num-title {
    font-family: 'JetBrains Mono', monospace;
    font-size: 24px;
    font-weight: 800;
    color: #07484A;
}
.detail-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}
@media (max-width: 991px) {
    .detail-layout {
        grid-template-columns: 1fr;
    }
}
.card-section {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.card-section-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.card-section-title i {
    color: #07484A;
    margin-right: 6px;
}

/* Prescription Workbench */
.rx-workbench {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 16px;
}
.rx-matrix-table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px;
    margin-top: 10px;
    margin-bottom: 10px;
    background: #ffffff;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #cbd5e1;
}
.rx-matrix-table th {
    background: #f1f5f9;
    color: #334155;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    padding: 8px 10px;
    border-bottom: 1px solid #cbd5e1;
    text-align: center;
}
.rx-matrix-table td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #e2e8f0;
    font-size: 13px;
}
.rx-matrix-table td.eye-head {
    font-weight: 700;
    background: #f8fafc;
    text-align: left;
    color: #07484A;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 24px;
    margin-top: 12px;
}
.timeline::before {
    content: '';
    position: absolute;
    top: 4px;
    bottom: 4px;
    left: 7px;
    width: 2px;
    background: #e2e8f0;
}
.timeline-item {
    position: relative;
    margin-bottom: 16px;
}
.timeline-dot {
    position: absolute;
    left: -24px;
    top: 4px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #07484A;
    border: 3px solid #ffffff;
    box-shadow: 0 0 0 1px #cbd5e1;
}
.timeline-time {
    font-size: 11px;
    color: #64748b;
}
.timeline-content {
    font-size: 12.5px;
    color: #1e293b;
    margin-top: 2px;
}

/* Badges */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11.5px;
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

.btn-theme-primary {
    background: #07484A;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 600;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
    transition: background 0.15s;
}
.btn-theme-primary:hover {
    background: #09595c;
    color: #ffffff;
}
.btn-theme-outline {
    background: #ffffff;
    color: #07484A;
    border: 1px solid #07484A;
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 600;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
}
.btn-theme-outline:hover {
    background: #f0fdfa;
    color: #07484A;
}

.totals-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-size: 13px;
    color: #475569;
}
.totals-row.grand {
    border-top: 2px solid #07484A;
    padding-top: 10px;
    margin-top: 6px;
    font-size: 16px;
    font-weight: 700;
    color: #07484A;
}
</style>

<div class="order-detail-wrap">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
        <i class="fa fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    <!-- Header Card -->
    <div class="order-header-card d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="order-num-title">{{ $order->order_number }}</span>
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
                <span class="badge-status {{ $order->payment_status == 'paid' ? 'badge-rx-approved' : 'badge-rx-pending' }}">
                    {{ strtoupper($order->payment_status) }}
                </span>
            </div>
            <div style="font-size: 12.5px; color: #64748b;">
                Placed on <strong>{{ $order->created_at->format('d M Y, h:i A') }}</strong> | Source: <strong>{{ strtoupper($order->device_type ?? 'WEB') }}</strong>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex flex-wrap gap-2">
            @php $firstRxItem = $order->items->first(); @endphp
            <button type="button" class="btn btn-outline-info" onclick="openRxWorkbenchModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ $order->rx_verification_status }}', '{{ addslashes($order->optometrist_notes ?? '') }}', '{{ addslashes(json_encode($firstRxItem)) }}')" style="border-radius: 8px; font-weight: 600; font-size: 13px;">
                <i class="fa fa-eye"></i> Eye Prescription (Rx)
            </button>
            <a href="{{ route('admin.b2c-orders.invoice', $order->id) }}" target="_blank" class="btn-theme-outline">
                <i class="fa fa-print"></i> Tax Invoice
            </a>
            <a href="{{ route('admin.b2c-orders.lab-work-order', $order->id) }}" target="_blank" class="btn-theme-primary">
                <i class="fa fa-glasses"></i> Lab Work Order
            </a>
            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#updateStatusModal" style="border-radius: 8px; font-weight: 600; font-size: 13px;">
                <i class="fa fa-edit"></i> Change Status
            </button>
            <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#returnModal" style="border-radius: 8px; font-weight: 600; font-size: 13px;">
                <i class="fa fa-undo"></i> Return / Remake
            </button>
        </div>
    </div>

    <!-- 2-Column Detail Layout -->
    <div class="detail-layout">

        <!-- ── Left Column (65%) ────────────────────────────────────── -->
        <div>

            <!-- 1. Prescription Verification Workbench -->
            <div class="card-section">
                <div class="card-section-title">
                    <span><i class="fa fa-eye"></i> Prescription Verification Workbench</span>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="openRxWorkbenchModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ $order->rx_verification_status }}', '{{ addslashes($order->optometrist_notes ?? '') }}', '{{ addslashes(json_encode($firstRxItem)) }}')" style="border-radius: 6px; font-weight: 600; font-size: 11.5px;">
                            <i class="fa fa-pencil-square-o"></i> Open Side-by-Side Rx Workbench
                        </button>
                        @if($order->rx_verification_status === 'approved')
                            <span class="badge-status badge-rx-approved"><i class="fa fa-check-circle"></i> Approved by {{ $order->optometrist->name ?? 'Optometrist' }}</span>
                        @elseif($order->rx_verification_status === 'clarification_needed')
                            <span class="badge-status badge-rx-clarification"><i class="fa fa-phone"></i> Clarification Needed</span>
                        @else
                            <span class="badge-status badge-rx-pending"><i class="fa fa-clock-o"></i> Pending Review</span>
                        @endif
                    </div>
                </div>

                @foreach($order->items as $item)
                @if($item->hasPrescription())
                <div class="rx-workbench">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <strong style="color: #07484A; font-size: 13px;">👓 {{ $item->product_name }}</strong>
                        <span style="font-size: 11.5px; color: #64748b;">Source: <strong>{{ ucfirst(str_replace('_', ' ', $item->prescription_source ?? 'manual_entry')) }}</strong></span>
                    </div>

                    <!-- Power Matrix -->
                    <table class="rx-matrix-table">
                        <thead>
                            <tr>
                                <th>Eye</th>
                                <th>SPH</th>
                                <th>CYL</th>
                                <th>Axis (°)</th>
                                <th>ADD (Near)</th>
                                <th>Mono PD</th>
                                <th>Fitting Ht</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="eye-head">RIGHT (OD)</td>
                                <td><strong>{{ $item->GL_EYE_RS_D !== null ? sprintf('%+.2f', $item->GL_EYE_RS_D) : '0.00' }}</strong></td>
                                <td><strong>{{ $item->GL_EYE_RC_D !== null ? sprintf('%+.2f', $item->GL_EYE_RC_D) : '0.00' }}</strong></td>
                                <td><strong>{{ $item->GL_EYE_RA_D ?? '—' }}{{ $item->GL_EYE_RA_D ? '°' : '' }}</strong></td>
                                <td><strong>{{ $item->GL_EYE_RADD !== null ? sprintf('+%.2f', $item->GL_EYE_RADD) : '—' }}</strong></td>
                                <td><strong>{{ $item->GL_EYE_RPD ?? '—' }} mm</strong></td>
                                <td><strong>{{ $item->fitting_height ?? '—' }} mm</strong></td>
                            </tr>
                            <tr>
                                <td class="eye-head">LEFT (OS)</td>
                                <td><strong>{{ $item->GL_EYE_LS_D !== null ? sprintf('%+.2f', $item->GL_EYE_LS_D) : '0.00' }}</strong></td>
                                <td><strong>{{ $item->GL_EYE_LC_D !== null ? sprintf('%+.2f', $item->GL_EYE_LC_D) : '0.00' }}</strong></td>
                                <td><strong>{{ $item->GL_EYE_LA_D ?? '—' }}{{ $item->GL_EYE_LA_D ? '°' : '' }}</strong></td>
                                <td><strong>{{ $item->GL_EYE_LADD !== null ? sprintf('+%.2f', $item->GL_EYE_LADD) : '—' }}</strong></td>
                                <td><strong>{{ $item->GL_EYE_LPD ?? '—' }} mm</strong></td>
                                <td><strong>{{ $item->fitting_height ?? '—' }} mm</strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center" style="font-size: 12px; color: #475569;">
                        <span>Total Binocular PD: <strong>{{ $item->GL_EYE_totalPD ? $item->GL_EYE_totalPD . ' mm' : 'Standard 63mm' }}</strong></span>
                        @if($item->prescription_file_url)
                        <a href="{{ asset($item->prescription_file_url) }}" target="_blank" class="btn btn-sm btn-outline-info" style="border-radius: 6px; font-size: 11.5px;">
                            <i class="fa fa-file-image-o"></i> View Doctor Uploaded Prescription
                        </a>
                        @endif
                    </div>
                </div>
                @endif
                @endforeach

                <!-- Verification Action Box -->
                <form method="POST" action="{{ route('admin.b2c-orders.verify-prescription', $order->id) }}" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-top: 12px;">
                    @csrf
                    <div style="font-weight: 600; font-size: 12px; color: #0f172a; margin-bottom: 6px;">Optometrist Verification Action:</div>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <select name="rx_status" class="form-control" style="width: auto; height: 36px; font-size: 12.5px; border-radius: 6px;">
                            <option value="approved" {{ $order->rx_verification_status == 'approved' ? 'selected' : '' }}>✓ Approve Prescription (Ready for Lab)</option>
                            <option value="clarification_needed" {{ $order->rx_verification_status == 'clarification_needed' ? 'selected' : '' }}>🔴 Flag Clarification (Call Customer)</option>
                            <option value="rejected" {{ $order->rx_verification_status == 'rejected' ? 'selected' : '' }}>❌ Reject Prescription</option>
                        </select>
                        <input type="text" name="optometrist_notes" class="form-control" placeholder="Optometrist internal remarks..." value="{{ $order->optometrist_notes }}" style="flex: 1; height: 36px; font-size: 12.5px; border-radius: 6px;">
                        <button type="submit" class="btn btn-theme-primary" style="height: 36px; padding: 0 16px;">
                            Save Verification
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. Ordered Items & Optical Specifications -->
            <div class="card-section">
                <div class="card-section-title">
                    <span><i class="fa fa-shopping-bag"></i> Ordered Items ({{ $order->items->count() }})</span>
                </div>

                @foreach($order->items as $index => $item)
                <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 14px; background: #ffffff;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div class="d-flex gap-3">
                            <div style="width: 60px; height: 60px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                👓
                            </div>
                            <div>
                                <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">{{ $item->product_name ?? 'Eyewear Frame' }}</h4>
                                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                    SKU: <strong>{{ $item->product_code ?? 'N/A' }}</strong> | Color: <strong>{{ $item->frame_color ?? 'Standard' }}</strong> | Size: <strong>{{ $item->frame_size ?? 'Medium' }}</strong>
                                </div>
                                @if($item->lensPackage || $item->lens_type)
                                <div style="font-size: 12px; color: #07484A; font-weight: 600; margin-top: 4px;">
                                    🔬 Lens: {{ $item->lensPackage->name ?? $item->lens_type }}
                                    @if($item->lens_coating) ({{ $item->lens_coating }}) @endif
                                    @if($item->lens_index) — Index {{ $item->lens_index }} @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 16px; font-weight: 700; color: #0f172a;">
                                ₹{{ number_format($item->total_price > 0 ? $item->total_price : ($item->sale_price * $item->qty), 2) }}
                            </div>
                            <div style="font-size: 12px; color: #64748b;">
                                Qty: {{ $item->qty }} × ₹{{ number_format($item->sale_price, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- 3. Activity & Lab Audit Timeline (Temporarily Hidden) --}}
            {{--
            <div class="card-section">
                <div class="card-section-title">
                    <span><i class="fa fa-history"></i> Activity & Audit Log</span>
                </div>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-time">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                        <div class="timeline-content"><strong>Order Placed</strong> by customer via {{ strtoupper($order->device_type ?? 'WEB') }}.</div>
                    </div>
                    @foreach($order->logs as $log)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #0284c7;"></div>
                        <div class="timeline-time">{{ $log->created_at->format('d M Y, h:i A') }} by {{ $log->user->name ?? 'System' }}</div>
                        <div class="timeline-content">
                            <strong>{{ ucfirst(str_replace('_', ' ', $log->action)) }}</strong>
                            @if($log->notes)
                            <div style="color: #475569; font-size: 12px; margin-top: 2px;">{{ $log->notes }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            --}}

        </div>

        <!-- ── Right Column (35%) ───────────────────────────────────── -->
        <div>

            <!-- Customer Details Card -->
            <div class="card-section">
                <div class="card-section-title">
                    <span><i class="fa fa-user"></i> Customer Info</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                    <div>
                        <div style="font-size: 14px; font-weight: 700; color: #0f172a;">{{ $order->customer_name }}</div>
                        <div style="font-size: 12.5px; color: #475569; margin-top: 4px;">
                            📞 <a href="tel:{{ $order->customer_phone }}" style="color: #07484A; font-weight: 600;">{{ $order->customer_phone ?? 'N/A' }}</a><br>
                            ✉️ <a href="mailto:{{ $order->customer_email }}" style="color: #07484A;">{{ $order->customer_email ?? 'N/A' }}</a>
                        </div>
                    </div>
                    @if(!empty($order->membership_type))
                        <span style="font-size: 11px; font-weight: 700; background: linear-gradient(135deg, #07484A, #00B9B9); color: #ffffff; padding: 4px 10px; border-radius: 14px; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;">
                            👑 {{ $order->membership_type }}
                        </span>
                    @endif
                </div>
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                    <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 4px;">Shipping Address</div>
                    <div style="font-size: 12.5px; color: #1e293b; line-height: 1.4;">
                        {{ $order->full_address_text }}
                    </div>
                </div>
            </div>

            {{-- Courier & Dispatch Details Card (Temporarily Hidden) --}}
            {{--
            <div class="card-section">
                <div class="card-section-title">
                    <span><i class="fa fa-truck"></i> Courier & Dispatch Details</span>
                </div>
                <div>
                    <form method="POST" action="{{ route('admin.b2c-orders.update-tracking', $order->id) }}">
                        @csrf
                        <div class="form-group mb-2">
                            <label style="font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 2px;">Courier Partner Name</label>
                            <input type="text" name="courier_partner" class="form-control" value="{{ $order->courier_partner }}" placeholder="e.g. Bluedart, Delhivery, DTDC" style="font-size: 12.5px; height: 36px; border-radius: 6px;" required>
                        </div>
                        <div class="form-group mb-2">
                            <label style="font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 2px;">Tracking Number (AWB)</label>
                            <input type="text" name="tracking_number" class="form-control" value="{{ $order->tracking_number }}" placeholder="AWB / Tracking #" style="font-size: 12.5px; height: 36px; border-radius: 6px;" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-theme-primary btn-block" style="border-radius: 6px;">Save & Mark Shipped</button>
                    </form>
                </div>
            </div>
            --}}

            <!-- Financials & Payment Summary -->
            <div class="card-section">
                <div class="card-section-title">
                    <span><i class="fa fa-credit-card"></i> Financial Breakdown</span>
                </div>
                <div class="totals-row">
                    <span>Subtotal:</span>
                    <span>₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="totals-row">
                    <span>Discount {{ $order->coupon_code ? "({$order->coupon_code})" : '' }}:</span>
                    <span style="color: #059669;">- ₹{{ number_format($order->discount_amount, 2) }}</span>
                </div>
                @endif
                @if($order->tax_amount > 0)
                <div class="totals-row">
                    <span>Estimated GST (12%):</span>
                    <span>₹{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                @endif
                <div class="totals-row">
                    <span>Shipping Fee:</span>
                    <span>{{ $order->shipping_fee > 0 ? '₹' . number_format($order->shipping_fee, 2) : 'FREE' }}</span>
                </div>
                <div class="totals-row grand">
                    <span>Grand Total:</span>
                    <span>₹{{ number_format($order->grand_total, 2) }}</span>
                </div>
                <div style="font-size: 11.5px; color: #64748b; margin-top: 8px;">
                    Payment Method: <strong>{{ strtoupper($order->payments->first()->payment_method ?? $order->payment_status) }}</strong>
                </div>
            </div>

            <!-- Internal Staff Notes -->
            <div class="card-section">
                <div class="card-section-title">
                    <span><i class="fa fa-sticky-note-o"></i> Staff Internal Notes</span>
                </div>
                <div style="max-height: 180px; overflow-y: auto; margin-bottom: 12px;">
                    @forelse($order->notes as $note)
                    <div style="background: #f8fafc; border-radius: 6px; padding: 8px 10px; margin-bottom: 8px; font-size: 12px;">
                        <div class="d-flex justify-content-between" style="color: #64748b; font-size: 11px; margin-bottom: 2px;">
                            <strong>{{ $note->author->name ?? 'Admin Staff' }}</strong>
                            <span>{{ $note->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="color: #1e293b;">{{ $note->note }}</div>
                    </div>
                    @empty
                    <div style="font-size: 12px; color: #94a3b8; text-align: center; padding: 12px 0;">No staff notes yet.</div>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('admin.b2c-orders.add-note', $order->id) }}">
                    @csrf
                    <textarea name="note" class="form-control" rows="2" placeholder="Add internal note (e.g. customer confirmed PD on phone)..." style="font-size: 12px; border-radius: 6px; margin-bottom: 6px;" required></textarea>
                    <button type="submit" class="btn btn-sm btn-outline-secondary btn-block" style="border-radius: 6px;">+ Add Note</button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Modal: Change Order Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('admin.b2c-orders.update-status', $order->id) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Update Order Status</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-semibold">Order Status</label>
                    <select name="order_status" class="form-control">
                        <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Placed</option>
                        <option value="confirmed" {{ $order->order_status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>In Lab / Processing</option>
                        <option value="ready_to_ship" {{ $order->order_status == 'ready_to_ship' ? 'selected' : '' }}>Ready to Ship</option>
                        <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="returned" {{ $order->order_status == 'returned' ? 'selected' : '' }}>Returned / Remake</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-semibold">Status Change Note</label>
                    <input type="text" name="note" class="form-control" placeholder="Reason or comment for status change...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-theme-primary">Save Status</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Return / Optical Remake -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('admin.b2c-orders.process-return', $order->id) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Initiate Return / Optical Lens Remake</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-semibold">Action Type</label>
                    <select name="return_type" class="form-control">
                        <option value="lens_remake">Free Lens Remake (Power Adjustment)</option>
                        <option value="replacement">Frame Replacement / Exchange</option>
                        <option value="refund">Full Return & Refund</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-semibold">Eyewear Return Reason</label>
                    <select name="reason" class="form-control">
                        <option value="power_mismatch">Optical Power Mismatch / Adaptation Issue</option>
                        <option value="fit_issue">Frame Fit / Bridge / Temple Size Issue</option>
                        <option value="frame_damage">Frame Damage / Defect</option>
                        <option value="changed_mind">Customer Changed Mind</option>
                        <option value="other">Other Reason</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-semibold">Exchange Preference</label>
                    <select name="exchange_type" class="form-control">
                        <option value="different_power">Same Frame with New Power</option>
                        <option value="different_frame">Different Frame Model</option>
                        <option value="same_product">Same Product Replacement</option>
                        <option value="none">No Exchange (Refund Only)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-semibold">Staff Notes & Lab Instructions</label>
                    <textarea name="admin_notes" class="form-control" rows="2" placeholder="Details of remake or customer conversation..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Process Return / Remake</button>
            </div>
        </form>
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
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
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
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background: #07484A; color: #ffffff; font-weight: 700; padding: 6px 20px; border-radius: 6px;">
                        <i class="fa fa-check"></i> Save Power & Sync Lab Job Sheet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    if (window.bootstrap && window.bootstrap.Modal) {
        const modal = new bootstrap.Modal(document.getElementById('rxWorkbenchModal'));
        modal.show();
    } else {
        $('#rxWorkbenchModal').modal('show');
    }
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
</script>

@endsection
