@extends('website.layout.master')

@section('content')
<style>
    .confirmation-wrapper {
        background: #f8fafc;
        padding-top: 30px;
        padding-bottom: 70px;
        color: #1e293b;
    }

    .confirmation-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #eef2f6;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.06);
        padding: 40px 36px;
        max-width: 780px;
        margin: 0 auto;
    }

    .success-icon-wrap {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: #f0fdf4;
        border: 2px solid #bbf7d0;
        color: #16a34a;
        font-size: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 18px;
        animation: scaleIn 0.3s ease;
    }

    @keyframes scaleIn {
        from { transform: scale(0.7); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .booking-id-pill {
        background: #f1f5f9;
        border: 1px dashed #cbd5e1;
        padding: 6px 16px;
        border-radius: 20px;
        font-family: monospace;
        font-size: 15px;
        font-weight: 700;
        color: #07484a;
        display: inline-block;
        margin-bottom: 24px;
    }

    .details-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 24px;
        text-align: left;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #edf2f7;
        font-size: 14px;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-item .label {
        color: #64748b;
        font-weight: 500;
    }

    .detail-item .val {
        color: #0f172a;
        font-weight: 700;
        text-align: right;
    }

    .next-steps-banner {
        background: #f0fdf9;
        border: 1.5px solid rgba(7, 72, 74, 0.2);
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 28px;
        text-align: left;
    }

    .next-steps-banner h6 {
        font-weight: 700;
        color: #07484a;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-action-primary {
        background: #07484a;
        color: #ffffff !important;
        padding: 12px 28px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-action-primary:hover {
        background: #0c5a5d;
        transform: translateY(-2px);
    }
</style>

<div class="confirmation-wrapper">
    <div class="container">
        <div class="confirmation-card text-center">
            <div class="success-icon-wrap">
                <i class="bi bi-check-lg"></i>
            </div>

            <h2 class="fw-bold text-dark mb-1">Appointment Confirmed!</h2>
            <p class="text-muted mb-2">Thank you! Your Home Eye-Test request has been successfully booked.</p>
            <div class="booking-id-pill">Booking ID: {{ $appointment->booking_id }}</div>

            <div class="details-box">
                <div class="detail-item">
                    <span class="label"><i class="bi bi-calendar-event"></i> Date of Visit:</span>
                    <span class="val">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, d F Y') }}</span>
                </div>
                <div class="detail-item">
                    <span class="label"><i class="bi bi-clock"></i> Time Slot:</span>
                    <span class="val">{{ $appointment->time_slot }}</span>
                </div>
                <div class="detail-item">
                    <span class="label"><i class="bi bi-people"></i> People for Test:</span>
                    <span class="val">{{ $appointment->people_count }} {{ $appointment->people_count > 1 ? 'People' : 'Person' }}</span>
                </div>
                <div class="detail-item">
                    <span class="label"><i class="bi bi-person"></i> Customer Name:</span>
                    <span class="val">{{ $appointment->name }}</span>
                </div>
                <div class="detail-item">
                    <span class="label"><i class="bi bi-telephone"></i> Contact Phone:</span>
                    <span class="val">{{ $appointment->phone }}</span>
                </div>
                <div class="detail-item">
                    <span class="label"><i class="bi bi-geo-alt"></i> Visit Address:</span>
                    <span class="val">{{ $appointment->address }}, {{ $appointment->city }} - {{ $appointment->pincode }}</span>
                </div>
                <div class="detail-item">
                    <span class="label"><i class="bi bi-wallet2"></i> Payment Mode:</span>
                    <span class="val text-capitalize">{{ str_replace('_', ' ', $appointment->payment_method) }} (₹{{ number_format($appointment->fee, 2) }})</span>
                </div>
            </div>

            <div class="next-steps-banner">
                <h6><i class="bi bi-info-circle-fill"></i> What happens next?</h6>
                <ul class="small text-muted mb-0 ps-3">
                    <li>Our verified optometrist will call you <strong>30 minutes before arrival</strong> to confirm the exact location.</li>
                    <li>They will arrive with computerized portable testing equipment, sanitized trial lenses, and 100+ frames.</li>
                    <li>After the 12-step test, you will receive a <strong>digital prescription</strong> on your WhatsApp and SMS.</li>
                </ul>
            </div>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('products') }}" class="btn-action-primary">
                    <i class="bi bi-eyeglasses"></i> Explore Frames & Specs
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 fw-semibold">
                    <i class="bi bi-house"></i> Return Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
