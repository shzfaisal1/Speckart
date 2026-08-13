@extends('website.layout.master')

@section('content')
<!-- Breadcrumbs Section -->
<section class="breadcrumbs-section" style="background:#f8f9fa;padding:15px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul id="breadcrumbs" class="d-flex list-unstyled m-0" style="gap:10px;font-size:14px;">
                    <li><a href="{{ route('home') }}" style="color:#6c757d;text-decoration:none;">Home</a></li>
                    <li>/</li>
                    <li class="active" style="color:#00b894;font-weight:600;">Gold & Signature Membership</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Hero Banner Section -->
<section class="membership-hero py-5" style="background: linear-gradient(135deg, #0984e3, #6c5ce7); color: #fff; text-align: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-3" style="font-size:14px; font-weight:700;">
                    👑 EXCLUSIVE B2C MEMBER PRIVILEGES
                </span>
                <h1 class="display-4 font-weight-bold mb-3" style="font-weight:800;">
                    Buy 1 Get 1 Free + VIP Flash & Hourly Sale Access
                </h1>
                <p class="lead mb-4" style="opacity: 0.95; font-size: 1.2rem;">
                    Join the <strong>Gold & Signature Membership</strong> club to enjoy 365 days of BOGO eyewear, <strong>10% Extra Contact Lens Discounts</strong>, VIP Flash Sale access & New Customer Welcome rewards!
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="#membership-cards" class="btn btn-warning btn-lg font-weight-bold px-4 py-3 shadow" style="border-radius:30px; font-weight:700; color:#2d3436;">
                        🛍 View Membership Plans
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Exclusive Member Perks Grid (Featuring the 4 requested perks) -->
<section class="membership-benefits py-5" style="background:#fff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="font-weight-bold" style="font-weight:700; color:#2d3436;">Exclusive Member Perks & Deals</h2>
            <p class="text-muted">Unlock ultimate savings across Eyeglasses, Sunglasses, Contact Lenses & Sales</p>
        </div>

        <div class="row g-4">
            <!-- 1. BOGO Offer -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm text-center p-4" style="border-radius:18px; background:#f8f9fa;">
                    <div class="mb-3" style="font-size:42px;">🛍️</div>
                    <h5 class="font-weight-bold" style="font-weight:700;">Buy 1 Get 1 Free</h5>
                    <p class="text-muted small mb-0">Add 2 eligible frames or sunglasses — cheapest pair automatically becomes <strong>100% FREE</strong>!</p>
                </div>
            </div>

            <!-- 2. 10% Extra Discount on Contact Lens -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm text-center p-4 position-relative" style="border-radius:18px; background:#e0f2fe; border: 1px solid #bae6fd !important;">
                    <span class="badge bg-primary position-absolute" style="top:12px; right:12px; font-size:11px;">SIGNATURE PERK</span>
                    <div class="mb-3" style="font-size:42px;">👁️</div>
                    <h5 class="font-weight-bold" style="font-weight:700; color:#0369a1;">10% Extra Lens OFF</h5>
                    <p class="text-muted small mb-0">Signature Members get an <strong>extra 10% instant discount</strong> on all Contact Lens brands!</p>
                </div>
            </div>

            <!-- 3. Flash Sale Access -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm text-center p-4" style="border-radius:18px; background:#fff7ed; border: 1px solid #ffedd5 !important;">
                    <div class="mb-3" style="font-size:42px;">⚡</div>
                    <h5 class="font-weight-bold" style="font-weight:700; color:#c2410c;">Flash Sale Access</h5>
                    <p class="text-muted small mb-0">Get <strong>early VIP access</strong> to limited-time Flash Sales with extra price drops on top brands.</p>
                </div>
            </div>

            <!-- 4. Hourly Sale Priority -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm text-center p-4" style="border-radius:18px; background:#f0fdf4; border: 1px solid #dcfce7 !important;">
                    <div class="mb-3" style="font-size:42px;">⏳</div>
                    <h5 class="font-weight-bold" style="font-weight:700; color:#15803d;">Hourly Deal Priority</h5>
                    <p class="text-muted small mb-0">Never miss out on <strong>Hourly Price Drops</strong> with priority notifications & reserved stock.</p>
                </div>
            </div>
        </div>

        <!-- 5. New Customer Extra Discount Banner -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="p-4 rounded-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3 shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 18px;">
                    <div>
                        <span class="badge bg-light text-dark px-3 py-1 mb-2 font-weight-bold" style="font-size:12px;">🎉 NEW CUSTOMER WELCOME OFFER</span>
                        <h4 class="mb-1 font-weight-bold" style="font-weight:800;">Get Extra Welcome Discounts on Your First Membership Order!</h4>
                        <p class="mb-0 small opacity-90">First-time customers unlock additional instant discount coupons on membership checkout.</p>
                    </div>
                    <a href="#membership-cards" class="btn btn-light btn-md font-weight-bold text-success px-4 py-2" style="border-radius:25px; font-weight:700;">
                        Claim Offer Now ➔
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Membership Cards Section -->
<section id="membership-cards" class="py-5" style="background:#f1f2f6;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="font-weight-bold" style="font-weight:700; color:#2d3436;">Choose Your Membership Plan</h2>
            <p class="text-muted">Select the plan that best fits your eyewear and contact lens requirements</p>
        </div>

        <div class="row justify-content-center g-4">
            @forelse($membershipCards as $card)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-lg h-100 text-center p-4 position-relative" style="border-radius:20px; overflow:hidden; border:2px solid #fdcb6e; background:#fff;">
                    @if($card->enable_bogo)
                    <div class="position-absolute" style="top:15px; right:15px;">
                        <span class="badge bg-success px-3 py-2" style="font-size:11px; border-radius:20px;">
                            🛍 BOGO ENABLED
                        </span>
                    </div>
                    @endif

                    <div class="mt-3 mb-2">
                        <span style="font-size:45px;">👑</span>
                    </div>
                    <h3 class="font-weight-bold" style="font-weight:800; color:#2d3436;">{{ $card->card_name }}</h3>
                    <div class="my-3">
                        <span class="h1 font-weight-bold" style="font-weight:800; color:#0984e3;">₹{{ number_format($card->price, 0) }}</span>
                        <span class="text-muted">/ {{ $card->validity_days }} Days</span>
                    </div>

                    <ul class="list-unstyled text-start my-4" style="line-height:2.2; font-size:14px; color:#4b5563;">
                        <li>✔️ <strong>Buy 1 Get 1 Free</strong> on 2 pairs of eyewear</li>
                        @if(stripos($card->card_name, 'signature') !== false || stripos($card->card_name, 'gold') !== false)
                            <li style="color:#0284c7;">👁️ <strong>Extra 10% OFF</strong> on Contact Lenses</li>
                        @endif
                        <li>⚡ <strong>Flash Sale</strong> early VIP access</li>
                        <li>⏳ <strong>Hourly Sale</strong> priority deal reservation</li>
                        <li>🎁 <strong>New Customer</strong> extra welcome discount</li>
                        <li>✔️ Valid for <strong>{{ $card->validity_days }} Days</strong> from purchase</li>
                    </ul>

                    <div class="mt-auto">
                        <a href="{{ route('cart') }}" class="btn btn-primary btn-block w-100 py-3 font-weight-bold shadow-sm" style="border-radius:30px; font-size:16px; background:#6c5ce7; border:none;">
                            Get {{ $card->card_name }}
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-md-8 text-center py-4">
                <div class="alert alert-info" style="border-radius:15px;">
                    ℹ️ Membership plans are currently being updated. Please check back shortly or visit our retail store!
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5" style="background:#fff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="font-weight-bold" style="font-weight:700;">Frequently Asked Questions</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="membershipFaq">
                    <div class="accordion-item mb-3 border rounded shadow-sm">
                        <h2 class="accordion-header" id="faqHeading1">
                            <button class="accordion-button font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How does the 10% Extra Contact Lens Discount work?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#membershipFaq">
                            <div class="accordion-body text-muted">
                                Signature Members receive an extra 10% instant discount auto-applied on all contact lens brands (daily, monthly, or yearly disposable lenses) during checkout.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-3 border rounded shadow-sm">
                        <h2 class="accordion-header" id="faqHeading2">
                            <button class="accordion-button collapsed font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                What benefits do I get during Flash Sales and Hourly Sales?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#membershipFaq">
                            <div class="accordion-body text-muted">
                                Active Gold & Signature Members get early access to high-demand Flash Sales before public opening, as well as priority reservation during Hourly Sale price drop events.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-3 border rounded shadow-sm">
                        <h2 class="accordion-header" id="faqHeading3">
                            <button class="accordion-button collapsed font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                How can New Customers claim extra discounts?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#membershipFaq">
                            <div class="accordion-body text-muted">
                                First-time buyers receive a special welcome coupon code at signup that can be combined with membership activation for extra instant savings.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
