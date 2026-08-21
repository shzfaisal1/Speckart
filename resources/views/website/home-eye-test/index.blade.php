@extends('website.layout.master')

@section('content')
<style>
    /* =========================================================
       LENSKART-STYLE HOME EYE-TEST STYLES
    ========================================================= */
    .home-eyetest-wrapper {
        background: #f8fafc;
        padding-top: 20px;
        padding-bottom: 60px;
        color: #1e293b;
        font-family: inherit;
    }

    /* Hero Section */
    .eyetest-hero {
        background: linear-gradient(135deg, #07484a 0%, #0c5a5d 60%, #107275 100%);
        border-radius: 24px;
        padding: 44px 40px;
        color: #ffffff;
        margin-bottom: 36px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 16px 36px -8px rgba(7, 72, 74, 0.25);
    }

    .eyetest-hero::after {
        content: '';
        position: absolute;
        right: -60px;
        bottom: -60px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .eyetest-hero h1 {
        font-size: 34px;
        font-weight: 800;
        margin-bottom: 12px;
        line-height: 1.25;
        letter-spacing: -0.5px;
    }

    .eyetest-hero h1 span {
        color: #fde047;
    }

    .eyetest-hero p {
        font-size: 16px;
        color: #e2e8f0;
        max-width: 620px;
        margin-bottom: 24px;
        line-height: 1.6;
    }

    .eyetest-perks-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 28px;
    }

    .eyetest-hero-perk {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .eyetest-hero-perk i {
        color: #fde047;
        font-size: 15px;
    }

    .hero-booking-badge {
        background: #fde047;
        color: #07484a;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 12px;
        letter-spacing: 0.5px;
    }

    /* Booking Form Card */
    .booking-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #eef2f6;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.05);
        padding: 32px;
        margin-bottom: 36px;
    }

    .booking-card-header {
        border-bottom: 1px solid #edf2f7;
        padding-bottom: 16px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .booking-card-header h3 {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .booking-card-header .price-tag {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #16a34a;
        padding: 6px 14px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 14px;
    }

    .booking-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .booking-section-title .step-num {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #07484a;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Date Selector Pills */
    .date-picker-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
        margin-bottom: 24px;
    }

    @media (max-width: 992px) {
        .date-picker-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    @media (max-width: 576px) {
        .date-picker-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .date-card-radio {
        display: none;
    }

    .date-card-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 12px 6px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }

    .date-card-label:hover {
        border-color: #07484a55;
        background: #f0fdf9;
    }

    .date-card-radio:checked + .date-card-label {
        background: #07484a;
        border-color: #07484a;
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(7, 72, 74, 0.2);
        transform: translateY(-2px);
    }

    .date-card-label .day-text {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 2px;
    }

    .date-card-radio:checked + .date-card-label .day-text {
        color: #cbd5e1;
    }

    .date-card-label .date-text {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .date-card-radio:checked + .date-card-label .date-text {
        color: #ffffff;
    }

    /* Time Slots Grid */
    .slot-picker-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    @media (max-width: 768px) {
        .slot-picker-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 480px) {
        .slot-picker-grid {
            grid-template-columns: 1fr;
        }
    }

    .slot-radio {
        display: none;
    }

    .slot-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 14px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        transition: all 0.2s ease;
        text-align: center;
    }

    .slot-label:hover {
        border-color: #07484a55;
        background: #f0fdf9;
    }

    .slot-radio:checked + .slot-label {
        background: #07484a;
        border-color: #07484a;
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(7, 72, 74, 0.2);
    }

    /* People Count Selector */
    .people-count-pills {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
    }

    .people-radio {
        display: none;
    }

    .people-label {
        flex: 1;
        padding: 10px 14px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        text-align: center;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        transition: all 0.2s ease;
    }

    .people-radio:checked + .people-label {
        background: #07484a;
        border-color: #07484a;
        color: #ffffff;
    }

    /* Form Fields */
    .form-control-custom {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: #ffffff;
    }

    .form-control-custom:focus {
        border-color: #07484a;
        box-shadow: 0 0 0 3px rgba(7, 72, 74, 0.1);
        outline: none;
    }

    /* Summary & Submit Card */
    .price-breakdown-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        margin-top: 18px;
    }

    .price-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .price-row.total {
        border-top: 1px dashed #cbd5e1;
        padding-top: 10px;
        margin-top: 10px;
        font-size: 17px;
        font-weight: 800;
        color: #07484a;
    }

    .btn-book-now {
        background: linear-gradient(135deg, #07484a 0%, #0c5a5d 100%);
        color: #ffffff !important;
        padding: 14px 28px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        width: 100%;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.22s ease;
        box-shadow: 0 8px 24px rgba(7, 72, 74, 0.25);
    }

    .btn-book-now:hover {
        background: linear-gradient(135deg, #0b5e61 0%, #107275 100%);
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(7, 72, 74, 0.35);
    }

    /* 12-Step Checkup Showcase Section */
    .features-section {
        margin-bottom: 48px;
    }

    .feature-step-card {
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 16px;
        padding: 22px;
        height: 100%;
        transition: all 0.2s ease;
    }

    .feature-step-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
        border-color: rgba(7, 72, 74, 0.25);
    }

    .feature-step-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #f0fdf9;
        border: 1px solid rgba(7, 72, 74, 0.15);
        color: #07484a;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }

    .feature-step-card h5 {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .feature-step-card p {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
        margin: 0;
    }

    /* Trial Frames Banner */
    .trial-frames-banner {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #eef2f6;
        padding: 32px;
        display: flex;
        align-items: center;
        gap: 32px;
        margin-bottom: 48px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.04);
    }

    @media (max-width: 768px) {
        .trial-frames-banner {
            flex-direction: column;
            padding: 24px;
        }
    }

    /* FAQ Accordion */
    .faq-accordion .accordion-item {
        border: 1px solid #edf2f7;
        border-radius: 12px !important;
        margin-bottom: 12px;
        overflow: hidden;
    }

    .faq-accordion .accordion-button {
        font-weight: 700;
        font-size: 15px;
        color: #0f172a;
        background: #ffffff;
        padding: 16px 20px;
    }

    .faq-accordion .accordion-button:not(.collapsed) {
        color: #07484a;
        background: #f0fdf9;
    }

    .faq-accordion .accordion-body {
        font-size: 14px;
        color: #475569;
        line-height: 1.6;
        padding: 16px 20px;
    }
</style>

<div class="home-eyetest-wrapper">
    <div class="container">

        <!-- 1. HERO BANNER -->
        <div class="eyetest-hero">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="hero-booking-badge"><i class="bi bi-shield-check"></i> Safe & Certified Service</span>
                    <h1>Get Your Eyes Checked at Home <span>@ ₹99</span></h1>
                    <p>Experience India's most trusted 12-step eye testing by certified optometrists using computerized portable vision equipment, plus <strong>100+ top frames</strong> to try right in your living room!</p>
                    
                    <div class="eyetest-perks-row">
                        <div class="eyetest-hero-perk"><i class="bi bi-patch-check-fill"></i> 12-Step Certified Checkup</div>
                        <div class="eyetest-hero-perk"><i class="bi bi-eyeglasses"></i> 100+ Frames to Try at Home</div>
                        <div class="eyetest-hero-perk"><i class="bi bi-cash-stack"></i> 100% Cash-Back on Specs</div>
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <img src="{{ asset('website/assets/img/bg/eye-test.png') }}" alt="Home Eye Test" class="img-fluid rounded-4 shadow-lg" style="max-height: 260px; object-fit: cover; border: 3px solid rgba(255,255,255,0.3);">
                </div>
            </div>
        </div>

        <!-- 2. INTERACTIVE BOOKING FORM -->
        <div class="booking-card" id="bookingFormSection">
            <div class="booking-card-header">
                <h3><i class="bi bi-calendar2-check-fill text-success"></i> Book Your Home Eye-Test Slot</h3>
                <span class="price-tag"><i class="bi bi-tag-fill"></i> ₹99 per Person (100% Redeemable)</span>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('home-eye-test.book') }}" method="POST" id="homeEyeTestForm">
                @csrf

                <!-- STEP 1: Select Date -->
                <div class="mb-4">
                    <div class="booking-section-title">
                        <span class="step-num">1</span>
                        <span>Select Date of Visit</span>
                    </div>
                    <div class="date-picker-grid">
                        @foreach($availableDates as $index => $dateItem)
                            <div>
                                <input type="radio" name="appointment_date" id="date_{{ $index }}" value="{{ $dateItem['full_date'] }}" class="date-card-radio" {{ $index === 0 ? 'checked' : '' }} required>
                                <label for="date_{{ $index }}" class="date-card-label">
                                    <span class="day-text">{{ $dateItem['day_name'] }}</span>
                                    <span class="date-text">{{ $dateItem['date_num'] }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- STEP 2: Select Time Slot -->
                <div class="mb-4">
                    <div class="booking-section-title">
                        <span class="step-num">2</span>
                        <span>Select Preferred Time Slot</span>
                    </div>
                    <div class="slot-picker-grid">
                        @foreach($timeSlots as $index => $slot)
                            <div>
                                <input type="radio" name="time_slot" id="slot_{{ $index }}" value="{{ $slot }}" class="slot-radio" {{ $index === 0 ? 'checked' : '' }} required>
                                <label for="slot_{{ $index }}" class="slot-label">
                                    <i class="bi bi-clock"></i> {{ $slot }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- STEP 3: Number of People -->
                <div class="mb-4">
                    <div class="booking-section-title">
                        <span class="step-num">3</span>
                        <span>How many people need eye testing?</span>
                    </div>
                    <div class="people-count-pills">
                        <div>
                            <input type="radio" name="people_count" id="people_1" value="1" class="people-radio" checked onchange="updatePrice(1)">
                            <label for="people_1" class="people-label">1 Person (₹99)</label>
                        </div>
                        <div>
                            <input type="radio" name="people_count" id="people_2" value="2" class="people-radio" onchange="updatePrice(2)">
                            <label for="people_2" class="people-label">2 People (₹198)</label>
                        </div>
                        <div>
                            <input type="radio" name="people_count" id="people_3" value="3" class="people-radio" onchange="updatePrice(3)">
                            <label for="people_3" class="people-label">3 People (₹297)</label>
                        </div>
                        <div>
                            <input type="radio" name="people_count" id="people_4" value="4" class="people-radio" onchange="updatePrice(4)">
                            <label for="people_4" class="people-label">4+ People (₹396)</label>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: Contact & Address Information -->
                <div class="mb-4">
                    <div class="booking-section-title">
                        <span class="step-num">4</span>
                        <span>Your Contact & Address Details</span>
                    </div>

                    @if(!empty($savedAddresses) && count($savedAddresses) > 0)
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Or pick a saved address:</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($savedAddresses as $addr)
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill fill-address-btn"
                                        data-name="{{ $addr->name }}"
                                        data-phone="{{ $addr->phone }}"
                                        data-pincode="{{ $addr->pincode }}"
                                        data-city="{{ $addr->city }}"
                                        data-state="{{ $addr->state }}"
                                        data-address="{{ $addr->address }}"
                                        data-landmark="{{ $addr->landmark }}">
                                        <i class="bi bi-geo-alt"></i> {{ $addr->type ?? 'Address' }}: {{ $addr->city }} ({{ $addr->pincode }})
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="inputName" class="form-control form-control-custom" placeholder="e.g. Rahul Sharma" value="{{ $user->name ?? old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" id="inputPhone" class="form-control form-control-custom" placeholder="10-digit mobile number" value="{{ $user->mobile ?? old('phone') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Email Address (Optional)</label>
                            <input type="email" name="email" id="inputEmail" class="form-control form-control-custom" placeholder="name@example.com" value="{{ $user->email ?? old('email') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Pincode <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" id="inputPincode" class="form-control form-control-custom" placeholder="6-digit PIN" maxlength="6" value="{{ old('pincode') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="inputCity" class="form-control form-control-custom" placeholder="e.g. Delhi, Mumbai" value="{{ old('city') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Complete House / Flat / Street Address <span class="text-danger">*</span></label>
                            <textarea name="address" id="inputAddress" rows="2" class="form-control form-control-custom" placeholder="House/Flat No, Building Name, Street, Area" required>{{ old('address') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Landmark (Optional)</label>
                            <input type="text" name="landmark" id="inputLandmark" class="form-control form-control-custom" placeholder="Near Metro, Temple, etc." value="{{ old('landmark') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Special Notes (Optional)</label>
                            <input type="text" name="notes" class="form-control form-control-custom" placeholder="Any specific instructions for optometrist" value="{{ old('notes') }}">
                        </div>
                    </div>
                </div>

                <!-- STEP 5: Payment Option -->
                <div class="mb-4">
                    <div class="booking-section-title">
                        <span class="step-num">5</span>
                        <span>Payment Mode</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 d-flex align-items-center gap-3 bg-light">
                                <input type="radio" name="payment_method" id="pay_on_visit" value="pay_on_visit" checked class="form-check-input mt-0">
                                <label for="pay_on_visit" class="form-check-label mb-0 cursor-pointer">
                                    <strong class="d-block text-dark">Pay on Visit (Recommended)</strong>
                                    <small class="text-muted">Pay ₹99 via Cash or UPI to optometrist upon test completion</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 d-flex align-items-center gap-3 bg-light">
                                <input type="radio" name="payment_method" id="pay_online" value="online" class="form-check-input mt-0">
                                <label for="pay_online" class="form-check-label mb-0 cursor-pointer">
                                    <strong class="d-block text-dark">Pay Online</strong>
                                    <small class="text-muted">UPI, Cards, NetBanking</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Summary & Submit -->
                <div class="price-breakdown-card">
                    <div class="price-row">
                        <span>Home Eye-Test Service Fee (<span id="summaryPeopleCount">1</span> Person):</span>
                        <span id="summaryFee">₹99.00</span>
                    </div>
                    <div class="price-row text-success fw-semibold">
                        <span><i class="bi bi-gift"></i> Eyeglasses Cash-Back Voucher:</span>
                        <span>-₹99.00</span>
                    </div>
                    <div class="price-row total">
                        <span>Net Payable Amount:</span>
                        <span id="summaryTotal">₹99.00</span>
                    </div>
                    <p class="small text-muted mb-3 mt-2"><i class="bi bi-info-circle"></i> You will receive an instant ₹99 voucher code usable on any eyeglasses or sunglasses purchase!</p>

                    <button type="submit" class="btn-book-now" id="submitBookingBtn">
                        <i class="bi bi-check-circle-fill"></i> Confirm & Book Appointment
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. 12-STEP EYE CHECKUP PROCESS -->
        <div class="features-section">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark">Comprehensive 12-Step Eye Checkup</h2>
                <p class="text-muted">Carried out using computerized portable auto-refractometers and ophthalmology grade charts.</p>
            </div>
            <div class="row g-3">
                <div class="col-md-4 col-sm-6">
                    <div class="feature-step-card">
                        <div class="feature-step-icon"><i class="bi bi-laptop"></i></div>
                        <h5>1. Auto-Refractometer Scan</h5>
                        <p>Objective computerized measurement of refractive errors (Sphere, Cylinder & Axis).</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="feature-step-card">
                        <div class="feature-step-icon"><i class="bi bi-eye"></i></div>
                        <h5>2. Visual Acuity Testing</h5>
                        <p>High-resolution digital Snellen chart test for measuring distance vision sharpness.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="feature-step-card">
                        <div class="feature-step-icon"><i class="bi bi-palette"></i></div>
                        <h5>3. Red-Green Duochrome Test</h5>
                        <p>Checks exact spherical power balance to prevent under or over-correction.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="feature-step-card">
                        <div class="feature-step-icon"><i class="bi bi-bullseye"></i></div>
                        <h5>4. Astigmatism & Fan Test</h5>
                        <p>Pinpoints the exact cylindrical angle and axis for crisp, blur-free vision.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="feature-step-card">
                        <div class="feature-step-icon"><i class="bi bi-book"></i></div>
                        <h5>5. Reading & Near Vision Test</h5>
                        <p>Determines reading additions for presbyopia, progressives, and bifocal lenses.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="feature-step-card">
                        <div class="feature-step-icon"><i class="bi bi-shield-check"></i></div>
                        <h5>6. Certified Prescription</h5>
                        <p>Instant digital prescription sent to your WhatsApp and saved to your account.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. 100+ TRIAL FRAMES SHOWCASE -->
        <div class="trial-frames-banner">
            <div style="flex: 1;">
                <span class="badge bg-success mb-2 px-3 py-2">Try Before You Buy</span>
                <h3 class="fw-bold text-dark mb-2">100+ Top Trial Frames in Your Living Room</h3>
                <p class="text-muted mb-0">Our optometrist brings a specialized case containing 100+ trending frames across Vincent Chase, John Jacobs, Hooper Kids, and more. Try them with your family, pick your favorite style, and place your order on the spot!</p>
            </div>
            <div class="d-flex gap-2">
                <img src="{{ asset('website/assets/img/icon/specs1.png') }}" alt="Frame" class="p-2 border rounded bg-light" style="width: 70px; height: 50px; object-fit: contain;">
                <img src="{{ asset('website/assets/img/icon/specs2.png') }}" alt="Frame" class="p-2 border rounded bg-light" style="width: 70px; height: 50px; object-fit: contain;">
                <img src="{{ asset('website/assets/img/icon/specs3.png') }}" alt="Frame" class="p-2 border rounded bg-light" style="width: 70px; height: 50px; object-fit: contain;">
            </div>
        </div>

        <!-- 5. FAQ SECTION -->
        <div class="mb-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark">Frequently Asked Questions</h2>
                <p class="text-muted">Everything you need to know about our home eye-test service.</p>
            </div>

            <div class="accordion faq-accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                            How does the Home Eye-Test work?
                        </button>
                    </h2>
                    <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            A certified, vaccinated optometrist visits your home at your selected date and time slot with portable auto-refractometer equipment, trial lenses, and 100+ designer frames. They perform a 12-step eye checkup and provide a certified digital prescription.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                            Is the ₹99 fee refundable?
                        </button>
                    </h2>
                    <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes! 100% of the ₹99 fee is provided back to you as an instant discount voucher that you can redeem when buying eyeglasses, sunglasses, or contact lenses.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                            Can I get my whole family tested during the same visit?
                        </button>
                    </h2>
                    <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Absolutely! You can choose how many people need eye testing (up to 4+ family members) when booking your appointment.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq4">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                            What equipment does the optometrist carry?
                        </button>
                    </h2>
                    <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Our refractionists carry a computerized handheld auto-refractometer, a trial lens kit, digital eye test charts, PD ruler, and a collection of 100+ high-quality frame styles to try.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function updatePrice(count) {
        var fee = 99 * count;
        document.getElementById('summaryPeopleCount').innerText = count;
        document.getElementById('summaryFee').innerText = '₹' + fee.toFixed(2);
        document.getElementById('summaryTotal').innerText = '₹' + fee.toFixed(2);
    }

    document.querySelectorAll('.fill-address-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('inputName').value = this.dataset.name || '';
            document.getElementById('inputPhone').value = this.dataset.phone || '';
            document.getElementById('inputPincode').value = this.dataset.pincode || '';
            document.getElementById('inputCity').value = this.dataset.city || '';
            document.getElementById('inputAddress').value = this.dataset.address || '';
            document.getElementById('inputLandmark').value = this.dataset.landmark || '';
        });
    });
</script>
@endsection
