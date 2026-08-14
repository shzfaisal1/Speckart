<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice - {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        body {
            background-color: #f8fafc;
            color: #1e293b;
            padding: 24px;
            font-size: 13px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 850px;
            margin: auto;
            padding: 32px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        }
        .no-print-bar {
            max-width: 850px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: #07484A;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover {
            background: #09595c;
        }
        .header-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            border-bottom: 2px solid #07484A;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .company-logo {
            font-size: 24px;
            font-weight: 800;
            color: #07484A;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .company-meta {
            color: #64748b;
            font-size: 12px;
            line-height: 1.4;
        }
        .invoice-title-block {
            text-align: right;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: 700;
            color: #07484A;
            text-transform: uppercase;
        }
        .invoice-meta {
            margin-top: 6px;
            font-size: 12px;
            color: #475569;
        }
        .invoice-meta strong {
            color: #0f172a;
        }
        .parties-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
        }
        .party-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #07484A;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .party-name {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .party-details {
            font-size: 12px;
            color: #475569;
            line-height: 1.4;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .items-table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 600;
            font-size: 12px;
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12.5px;
            vertical-align: top;
        }
        .items-table tr:last-child td {
            border-bottom: 2px solid #e2e8f0;
        }
        .item-name {
            font-weight: 600;
            color: #0f172a;
        }
        .item-spec {
            font-size: 11.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .rx-badge {
            display: inline-block;
            background: #e6fffa;
            color: #0d9488;
            border: 1px solid #99f6e4;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            margin-top: 4px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
            margin-bottom: 24px;
        }
        .payment-info-box {
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
        }
        .payment-info-title {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 0;
            font-size: 12.5px;
        }
        .totals-table td.label {
            color: #64748b;
        }
        .totals-table td.val {
            text-align: right;
            font-weight: 500;
            color: #1e293b;
        }
        .totals-table tr.grand-total td {
            border-top: 2px solid #07484A;
            padding-top: 10px;
            font-size: 15px;
            font-weight: 700;
            color: #07484A;
        }
        .footer-terms {
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.5;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="{{ route('admin.b2c-orders.show', $order->id) }}" style="color: #64748b; text-decoration: none; font-size: 13px;">← Back to Order Detail</a>
        <button onclick="window.print()" class="btn-print">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg>
            Print Tax Invoice
        </button>
    </div>

    <div class="invoice-box">
        <!-- Header -->
        <div class="header-grid">
            <div>
                <div class="company-logo">{{ $store->store_name ?? $store->name ?? 'Aimbeat Technology Pvt Ltd' }}</div>
                <div class="company-meta">
                    {{ $store->store_address ?? $store->address ?? 'Corporate Office' }}<br>
                    @if(!empty($store->gst_no)) GSTIN: <strong>{{ $store->gst_no }}</strong><br> @endif
                    Phone: {{ $store->contact_no ?? '+91 9876543210' }} | Email: {{ $store->email_id ?? $store->email ?? 'info@aimbeat.com' }}
                </div>
            </div>
            <div class="invoice-title-block">
                <div class="invoice-title">Tax Invoice</div>
                <div class="invoice-meta">
                    Invoice / Order #: <strong>{{ $order->order_number }}</strong><br>
                    Date: <strong>{{ $order->created_at->format('d M Y, h:i A') }}</strong><br>
                    Payment Status: <strong style="color: {{ $order->payment_status == 'paid' ? '#059669' : '#d97706' }};">{{ strtoupper($order->payment_status) }}</strong>
                </div>
            </div>
        </div>

        <!-- Bill To & Ship To -->
        <div class="parties-grid">
            <div>
                <div class="party-title">Billed To (Customer)</div>
                <div class="party-name">{{ $order->customer_name }}</div>
                <div class="party-details">
                    Phone: {{ $order->customer_phone ?? 'N/A' }}<br>
                    Email: {{ $order->customer_email ?? 'N/A' }}
                </div>
            </div>
            <div>
                <div class="party-title">Shipping Address</div>
                <div class="party-details">
                    {{ $order->full_address_text }}<br>
                    Delivery Mode: <strong>{{ ucfirst($order->delivery_method ?? 'Standard Delivery') }}</strong>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Item Description</th>
                    <th style="width: 12%; text-align: center;">HSN</th>
                    <th style="width: 8%; text-align: center;">Qty</th>
                    <th style="width: 15%; text-align: right;">Unit Price</th>
                    <th style="width: 15%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->product_name ?? 'Eyewear Frame' }}</div>
                        <div class="item-spec">
                            @if($item->frame_color) Color: {{ $item->frame_color }} | @endif
                            @if($item->frame_size) Size: {{ $item->frame_size }} | @endif
                            @if($item->product_code) SKU: {{ $item->product_code }} @endif
                        </div>
                        @if($item->lensPackage || $item->lens_type)
                        <div class="item-spec" style="color: #07484A; font-weight: 500;">
                            👓 Lens: {{ $item->lensPackage->name ?? $item->lens_type }}
                            @if($item->lens_coating) ({{ $item->lens_coating }}) @endif
                        </div>
                        @endif
                        @if($item->hasPrescription())
                        <span class="rx-badge">Prescription Fitted</span>
                        @endif
                    </td>
                    <td style="text-align: center; color: #64748b;">{{ $item->hsn_code ?? '9003' }}</td>
                    <td style="text-align: center;">{{ $item->qty }}</td>
                    <td style="text-align: right;">₹{{ number_format($item->sale_price, 2) }}</td>
                    <td style="text-align: right; font-weight: 600;">₹{{ number_format($item->total_price > 0 ? $item->total_price : ($item->sale_price * $item->qty), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary & Totals -->
        <div class="summary-grid">
            <div class="payment-info-box">
                <div class="payment-info-title">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/></svg>
                    Payment & Delivery Details
                </div>
                @php $payment = $order->payments->first(); @endphp
                Payment Mode: <strong>{{ strtoupper($payment->payment_method ?? $order->payment_status) }}</strong><br>
                @if(!empty($payment->transaction_id)) Transaction ID: <strong>{{ $payment->transaction_id }}</strong><br> @endif
                @if(!empty($order->courier_partner)) Courier: <strong>{{ $order->courier_partner }}</strong><br> @endif
                @if(!empty($order->tracking_number)) Tracking #: <strong>{{ $order->tracking_number }}</strong><br> @endif
            </div>
            <div>
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="val">₹{{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    @if($order->discount_amount > 0)
                    <tr>
                        <td class="label">Discount {{ $order->coupon_code ? "({$order->coupon_code})" : '' }}:</td>
                        <td class="val" style="color: #059669;">- ₹{{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($order->tax_amount > 0)
                    <tr>
                        <td class="label">Estimated GST (12%):</td>
                        <td class="val">₹{{ number_format($order->tax_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($order->shipping_fee > 0)
                    <tr>
                        <td class="label">Shipping Charges:</td>
                        <td class="val">₹{{ number_format($order->shipping_fee, 2) }}</td>
                    </tr>
                    @else
                    <tr>
                        <td class="label">Shipping:</td>
                        <td class="val" style="color: #059669;">FREE</td>
                    </tr>
                    @endif
                    <tr class="grand-total">
                        <td class="label">Grand Total:</td>
                        <td class="val">₹{{ number_format($order->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Terms Footer -->
        <div class="footer-terms">
            Thank you for shopping with {{ $store->store_name ?? 'Aimbeat Technology Pvt Ltd' }}!<br>
            All custom prescription optical lenses are precision cut. 1 Year Warranty on frame manufacturing defects.<br>
            This is a computer-generated invoice and requires no physical signature.
        </div>
    </div>

</body>
</html>
