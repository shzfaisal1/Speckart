<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optical Lab Job Sheet - {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            padding: 20px;
            font-size: 13px;
        }
        .job-sheet-container {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 8px;
            padding: 24px;
        }
        .no-print {
            max-width: 850px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print-lab {
            background: #0f172a;
            color: #ffffff;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 16px;
        }
        .lab-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .order-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 16px;
            font-weight: 700;
            background: #0f172a;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 4px;
        }
        .meta-box {
            text-align: right;
            font-size: 12px;
            color: #475569;
        }
        .meta-box strong {
            color: #0f172a;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff;
            background: #0f172a;
            padding: 4px 8px;
            border-radius: 4px 4px 0 0;
            margin-top: 16px;
        }
        .rx-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'JetBrains Mono', monospace;
            margin-bottom: 16px;
        }
        .rx-table th, .rx-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: center;
            font-size: 13px;
        }
        .rx-table th {
            background: #f8fafc;
            color: #334155;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        .rx-table td.eye-label {
            font-weight: 700;
            background: #f1f5f9;
            text-align: left;
        }
        .specs-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
            border: 1px solid #cbd5e1;
            padding: 12px;
            border-radius: 0 0 6px 6px;
            background: #f8fafc;
        }
        .spec-item {
            font-size: 12.5px;
            line-height: 1.6;
        }
        .spec-item strong {
            color: #0f172a;
        }
        .qc-box {
            border: 1px dashed #94a3b8;
            padding: 16px;
            border-radius: 6px;
            margin-top: 16px;
            background: #ffffff;
        }
        .qc-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            font-size: 12px;
            margin-top: 8px;
        }
        .qc-check {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .sign-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
        }
        .sign-line {
            border-bottom: 1px solid #0f172a;
            height: 32px;
            margin-bottom: 4px;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .job-sheet-container {
                border: 2px solid #000000;
                max-width: 100%;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="{{ route('admin.b2c-orders.show', $order->id) }}" style="color: #64748b; text-decoration: none; font-size: 13px;">← Back to Order Detail</a>
        <button onclick="window.print()" class="btn-print-lab">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg>
            Print Optical Job Sheet
        </button>
    </div>

    <div class="job-sheet-container">
        <!-- Header -->
        <div class="header-section">
            <div>
                <div class="lab-title">🔬 Optical Lab Work Order</div>
                <div class="order-badge">{{ $order->order_number }}</div>
            </div>
            <div class="meta-box">
                Order Date: <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong><br>
                Target Delivery: <strong>{{ $order->expected_delivery_date ? $order->expected_delivery_date->format('d/m/Y') : 'Priority 48H' }}</strong><br>
                Job Sheet #: <strong>{{ $order->lab_job_number ?? ('JOB-' . $order->id) }}</strong>
            </div>
        </div>

        @foreach($order->items as $index => $item)
        <div style="margin-bottom: 24px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 16px;">
            <!-- Frame & Lens Specs -->
            <div class="section-title">Item #{{ $index + 1 }} Specifications & Mounting Instructions</div>
            <div class="specs-grid">
                <div class="spec-item">
                    <strong>FRAME DETAILS:</strong><br>
                    Model: <strong>{{ $item->product_name ?? 'Standard Optical Frame' }}</strong><br>
                    SKU: <strong>{{ $item->product_code ?? 'N/A' }}</strong><br>
                    Color: <strong>{{ $item->frame_color ?? 'Standard' }}</strong> | Size: <strong>{{ $item->frame_size ?? 'Medium' }}</strong>
                </div>
                <div class="spec-item">
                    <strong>LENS BLANK REQUIREMENTS:</strong><br>
                    Lens Package: <strong>{{ $item->lensPackage->name ?? $item->lens_type ?? 'Single Vision Standard' }}</strong><br>
                    Index: <strong>{{ $item->lens_index ?? '1.56 Mid-Index' }}</strong><br>
                    Coating/Treatment: <strong>{{ $item->lens_coating ?? $item->coating_apply ?? 'Anti-Glare + Blue-Cut' }}</strong>
                </div>
            </div>

            <!-- Prescription Power Matrix -->
            <div class="section-title">Precision Optical Power Matrix (OD / OS)</div>
            <table class="rx-table">
                <thead>
                    <tr>
                        <th style="width: 16%; text-align: left;">Eye</th>
                        <th style="width: 14%;">SPH</th>
                        <th style="width: 14%;">CYL</th>
                        <th style="width: 14%;">AXIS (°)</th>
                        <th style="width: 14%;">ADD (Near)</th>
                        <th style="width: 14%;">Mono PD</th>
                        <th style="width: 14%;">Fitting Ht</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="eye-label">RIGHT (OD)</td>
                        <td><strong>{{ $item->GL_EYE_RS_D !== null ? sprintf('%+.2f', $item->GL_EYE_RS_D) : '0.00' }}</strong></td>
                        <td><strong>{{ $item->GL_EYE_RC_D !== null ? sprintf('%+.2f', $item->GL_EYE_RC_D) : '0.00' }}</strong></td>
                        <td><strong>{{ $item->GL_EYE_RA_D ?? '—' }}{{ $item->GL_EYE_RA_D ? '°' : '' }}</strong></td>
                        <td><strong>{{ $item->GL_EYE_RADD !== null ? sprintf('+%.2f', $item->GL_EYE_RADD) : '—' }}</strong></td>
                        <td><strong>{{ $item->GL_EYE_RPD ?? '—' }} mm</strong></td>
                        <td><strong>{{ $item->fitting_height ?? '—' }} mm</strong></td>
                    </tr>
                    <tr>
                        <td class="eye-label">LEFT (OS)</td>
                        <td><strong>{{ $item->GL_EYE_LS_D !== null ? sprintf('%+.2f', $item->GL_EYE_LS_D) : '0.00' }}</strong></td>
                        <td><strong>{{ $item->GL_EYE_LC_D !== null ? sprintf('%+.2f', $item->GL_EYE_LC_D) : '0.00' }}</strong></td>
                        <td><strong>{{ $item->GL_EYE_LA_D ?? '—' }}{{ $item->GL_EYE_LA_D ? '°' : '' }}</strong></td>
                        <td><strong>{{ $item->GL_EYE_LADD !== null ? sprintf('+%.2f', $item->GL_EYE_LADD) : '—' }}</strong></td>
                        <td><strong>{{ $item->GL_EYE_LPD ?? '—' }} mm</strong></td>
                        <td><strong>{{ $item->fitting_height ?? '—' }} mm</strong></td>
                    </tr>
                </tbody>
            </table>
            <div style="font-size: 12px; color: #475569; display: flex; justify-content: space-between;">
                <span>Total Binocular PD: <strong>{{ $item->GL_EYE_totalPD ? $item->GL_EYE_totalPD . ' mm' : 'Standard 63mm' }}</strong></span>
                <span>Rx Source: <strong>{{ ucfirst(str_replace('_', ' ', $item->prescription_source ?? 'manual_entry')) }}</strong></span>
                <span>Verified By: <strong>{{ $order->optometrist->name ?? 'Verified Optometrist' }}</strong></span>
            </div>
            @if(!empty($item->prescription_notes))
            <div style="margin-top: 8px; font-size: 11.5px; background: #fffbeb; border: 1px solid #fef3c7; padding: 6px 10px; border-radius: 4px;">
                ⚠️ <strong>Optician Special Instructions:</strong> {{ $item->prescription_notes }}
            </div>
            @endif
        </div>
        @endforeach

        <!-- Quality Control Checklist -->
        <div class="qc-box">
            <strong style="font-size: 12px; text-transform: uppercase;">Laboratory Quality Control (QC) Checklist:</strong>
            <div class="qc-grid">
                <label class="qc-check"><input type="checkbox"> Lensometer Verified</label>
                <label class="qc-check"><input type="checkbox"> Axis Alignment Checked</label>
                <label class="qc-check"><input type="checkbox"> Bevel Fitting & Secure</label>
                <label class="qc-check"><input type="checkbox"> Scratch & Coating QC Pass</label>
            </div>
        </div>

        <!-- Signatures -->
        <div class="sign-grid">
            <div>
                <div class="sign-line"></div>
                <div style="font-size: 11px; color: #64748b;">Lens Edging Technician Signature & Date</div>
            </div>
            <div>
                <div class="sign-line"></div>
                <div style="font-size: 11px; color: #64748b;">QC Inspector Final Approval Signature & Date</div>
            </div>
        </div>
    </div>

</body>
</html>
