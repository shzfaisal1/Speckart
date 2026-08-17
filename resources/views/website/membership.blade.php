@extends('website.layout.master')

@section('content')

{{-- ═══════════════════════════════════════════════════
     SPECKART — Ultra-Premium Membership Club UI
     Gold & Signature VIP Privileges · Responsive
═══════════════════════════════════════════════════ --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
/* ══════════════════════════════════════
   MEMBERSHIP DESIGN SYSTEM TOKENS
══════════════════════════════════════ */
:root {
    --vip-gold: #f59e0b;
    --vip-gold-dark: #b45309;
    --vip-gold-light: #fef3c7;
    --vip-gold-gradient: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
    --vip-primary: #329a9a;
    --vip-primary-dark: #07484a;
    --vip-dark-bg: #0b132b;
    --vip-card-bg: #ffffff;
    --vip-text: #0f172a;
    --vip-text-secondary: #475569;
    --vip-text-muted: #94a3b8;
    --vip-border: #e2e8f0;
    --vip-radius: 18px;
    --vip-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    --vip-shadow-hover: 0 20px 40px -10px rgba(15, 23, 42, 0.12);
    --vip-transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.vip-page, .vip-page * {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    box-sizing: border-box;
}

.vip-page {
    background: #f8fafc;
    color: var(--vip-text);
}

/* ══════════════════════════════════════
   BREADCRUMBS
══════════════════════════════════════ */
.vip-breadcrumb-wrap {
    background: #ffffff;
    border-bottom: 1px solid var(--vip-border);
    padding: 12px 0;
}
.vip-breadcrumb {
    margin-bottom: 0;
    font-size: 13px;
    font-weight: 500;
}
.vip-breadcrumb a {
    color: var(--vip-text-secondary);
    text-decoration: none;
    transition: var(--vip-transition);
}
.vip-breadcrumb a:hover {
    color: var(--vip-primary);
}
.vip-breadcrumb .breadcrumb-item.active {
    color: var(--vip-gold-dark);
    font-weight: 700;
}

/* ══════════════════════════════════════
   HERO BANNER SECTION
══════════════════════════════════════ */
.vip-hero {
    background: linear-gradient(135deg, #07484a 0%, #0f172a 55%, #1e1b4b 100%);
    color: #ffffff;
    padding: 64px 0;
    position: relative;
    overflow: hidden;
}
.vip-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(251, 191, 36, 0.15) 0%, rgba(251, 191, 36, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.vip-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(251, 191, 36, 0.15);
    border: 1px solid rgba(251, 191, 36, 0.4);
    color: #fcd34d;
    font-size: 12.5px;
    font-weight: 800;
    padding: 6px 18px;
    border-radius: 30px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.2);
}
.vip-hero-title {
    font-size: 42px;
    font-weight: 900;
    letter-spacing: -1px;
    line-height: 1.15;
    margin-top: 18px;
    margin-bottom: 16px;
    background: linear-gradient(180deg, #ffffff 0%, #e2e8f0 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.vip-hero-title span {
    background: var(--vip-gold-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.vip-hero-subtitle {
    font-size: 16px;
    color: #cbd5e1;
    line-height: 1.6;
    max-width: 720px;
    margin: 0 auto 28px;
}
.vip-btn-hero {
    background: var(--vip-gold-gradient);
    color: #0f172a;
    font-weight: 800;
    font-size: 14.5px;
    padding: 13px 32px;
    border-radius: 30px;
    border: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 6px 20px rgba(217, 119, 6, 0.35);
    transition: var(--vip-transition);
}
.vip-btn-hero:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(217, 119, 6, 0.45);
    color: #0f172a;
}

/* ══════════════════════════════════════
   MEMBER PERKS SECTION
══════════════════════════════════════ */
.vip-perks-section {
    padding: 56px 0;
    background: #ffffff;
}
.vip-section-header {
    text-align: center;
    margin-bottom: 40px;
}
.vip-section-header h2 {
    font-size: 28px;
    font-weight: 800;
    color: var(--vip-text);
    letter-spacing: -0.5px;
    margin-bottom: 8px;
}
.vip-section-header p {
    font-size: 14.5px;
    color: var(--vip-text-secondary);
    margin: 0;
}

.vip-perk-card {
    background: #ffffff;
    border-radius: var(--vip-radius);
    border: 1px solid var(--vip-border);
    padding: 24px 20px;
    text-align: center;
    height: 100%;
    transition: var(--vip-transition);
    position: relative;
}
.vip-perk-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--vip-shadow-hover);
    border-color: #cbd5e1;
}
.vip-perk-icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin: 0 auto 16px;
    transition: var(--vip-transition);
}
.vip-perk-card:hover .vip-perk-icon-wrap {
    transform: scale(1.08);
}

.perk-bogo { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }
.perk-lens { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
.perk-flash { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
.perk-hourly { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

.vip-perk-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--vip-text);
    margin-bottom: 8px;
}
.vip-perk-desc {
    font-size: 13px;
    color: var(--vip-text-secondary);
    line-height: 1.5;
    margin: 0;
}

/* Welcome Offer Banner */
.vip-welcome-banner {
    background: linear-gradient(135deg, #07484a 0%, #115e59 100%);
    border-radius: 20px;
    padding: 28px 32px;
    color: #ffffff;
    box-shadow: 0 10px 30px rgba(7, 72, 74, 0.2);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 36px;
    border: 1px solid rgba(255,255,255,0.1);
}
.vip-welcome-badge {
    background: rgba(255, 255, 255, 0.15);
    color: #fef08a;
    font-size: 11.5px;
    font-weight: 800;
    padding: 4px 12px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.vip-welcome-title {
    font-size: 20px;
    font-weight: 800;
    margin: 8px 0 4px;
}
.vip-welcome-desc {
    font-size: 13.5px;
    color: #ccfbf1;
    margin: 0;
}
.vip-btn-banner {
    background: var(--vip-gold-gradient);
    color: #0f172a;
    font-size: 13.5px;
    font-weight: 800;
    padding: 10px 24px;
    border-radius: 25px;
    text-decoration: none;
    transition: var(--vip-transition);
    box-shadow: 0 4px 14px rgba(0,0,0,0.15);
}
.vip-btn-banner:hover {
    transform: translateY(-2px);
    color: #0f172a;
    box-shadow: 0 6px 18px rgba(0,0,0,0.25);
}

/* ══════════════════════════════════════
   MEMBERSHIP CARDS SECTION
══════════════════════════════════════ */
.vip-plans-section {
    padding: 56px 0;
    background: #f8fafc;
}
.vip-plan-card {
    background: #ffffff;
    border-radius: 22px;
    border: 1.5px solid var(--vip-border);
    padding: 32px 24px;
    text-align: center;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    transition: var(--vip-transition);
    box-shadow: var(--vip-shadow);
}
.vip-plan-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--vip-shadow-hover);
}

.vip-plan-card.popular-plan {
    border-color: var(--vip-gold);
    box-shadow: 0 12px 35px rgba(245, 158, 11, 0.15);
    background: linear-gradient(180deg, #fffdfa 0%, #ffffff 100%);
}
.vip-popular-ribbon {
    position: absolute;
    top: -1px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--vip-gold-gradient);
    color: #0f172a;
    font-size: 11px;
    font-weight: 900;
    padding: 4px 16px;
    border-radius: 0 0 12px 12px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3);
}

.vip-crown-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--vip-gold-light);
    color: var(--vip-gold-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin: 10px auto 16px;
}
.vip-plan-name {
    font-size: 22px;
    font-weight: 900;
    color: var(--vip-text);
    margin-bottom: 12px;
    letter-spacing: -0.3px;
}

.vip-plan-price-wrap {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px dashed var(--vip-border);
}
.vip-plan-price {
    font-size: 38px;
    font-weight: 900;
    color: var(--vip-primary-dark);
    letter-spacing: -1px;
}
.vip-plan-validity {
    font-size: 13px;
    color: var(--vip-text-muted);
    font-weight: 600;
}

.vip-plan-features {
    list-style: none;
    padding: 0;
    margin: 0 0 28px 0;
    text-align: left;
}
.vip-plan-features li {
    font-size: 13.5px;
    color: var(--vip-text-secondary);
    padding: 7px 0;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    line-height: 1.4;
}
.vip-plan-features li i {
    color: var(--vip-primary);
    font-size: 15px;
    margin-top: 1px;
    flex-shrink: 0;
}
.vip-plan-features li.highlight i {
    color: var(--vip-gold-dark);
}

.vip-btn-plan {
    width: 100%;
    background: linear-gradient(135deg, var(--vip-primary), var(--vip-primary-dark));
    color: #ffffff;
    font-size: 14.5px;
    font-weight: 800;
    padding: 13px 20px;
    border-radius: 12px;
    border: none;
    text-decoration: none;
    display: block;
    transition: var(--vip-transition);
    box-shadow: 0 4px 14px rgba(50, 154, 154, 0.3);
    margin-top: auto;
}
.vip-btn-plan:hover {
    background: linear-gradient(135deg, #2db0b0, #053335);
    color: #ffffff;
    box-shadow: 0 6px 20px rgba(50, 154, 154, 0.4);
    transform: translateY(-2px);
}

.vip-plan-card.popular-plan .vip-btn-plan {
    background: var(--vip-gold-gradient);
    color: #0f172a;
    box-shadow: 0 4px 14px rgba(217, 119, 6, 0.35);
}
.vip-plan-card.popular-plan .vip-btn-plan:hover {
    box-shadow: 0 8px 22px rgba(217, 119, 6, 0.5);
    color: #0f172a;
}

/* ══════════════════════════════════════
   FAQ ACCORDION SECTION
══════════════════════════════════════ */
.vip-faq-section {
    padding: 56px 0;
    background: #ffffff;
}
.vip-accordion .accordion-item {
    border: 1px solid var(--vip-border);
    border-radius: 14px !important;
    margin-bottom: 12px;
    overflow: hidden;
    box-shadow: var(--vip-shadow);
    transition: var(--vip-transition);
}
.vip-accordion .accordion-item:hover {
    border-color: #cbd5e1;
}
.vip-accordion .accordion-button {
    font-size: 15px;
    font-weight: 700;
    color: var(--vip-text);
    background: #ffffff;
    padding: 18px 20px;
    box-shadow: none !important;
}
.vip-accordion .accordion-button:not(.collapsed) {
    color: var(--vip-primary-dark);
    background: var(--vip-primary-soft);
}
.vip-accordion .accordion-body {
    font-size: 13.5px;
    color: var(--vip-text-secondary);
    line-height: 1.6;
    padding: 16px 20px;
}
</style>

<div class="vip-page">

    {{-- ═══ BREADCRUMBS SECTION ═══ --}}
    <div class="vip-breadcrumb-wrap">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb vip-breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-house-door me-1"></i>Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gold & Signature Membership</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- ═══ HERO BANNER SECTION ═══ --}}
    <section class="vip-hero text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <span class="vip-badge-pill">
                        <i class="bi bi-award-fill"></i> EXCLUSIVE VIP MEMBER PRIVILEGES
                    </span>
                    <h1 class="vip-hero-title">
                        Buy 1 Get 1 Free + <span>VIP Sale Privileges</span>
                    </h1>
                    <p class="vip-hero-subtitle">
                        Join the <strong>Speckart Gold & Signature Membership Club</strong> for 365 days of BOGO eyewear, <strong>10% Extra Contact Lens Discounts</strong>, early VIP Flash Sale access, and instant New Customer Welcome rewards!
                    </p>
                    <div>
                        <a href="#membership-plans" class="vip-btn-hero">
                            <i class="bi bi-gem"></i> Explore Membership Plans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ MEMBER PERKS GRID ═══ --}}
    <section class="vip-perks-section">
        <div class="container">
            <div class="vip-section-header">
                <h2>Exclusive Member Privileges</h2>
                <p>Unlock unmatched year-round savings across Eyeglasses, Sunglasses & Contact Lenses</p>
            </div>

            <div class="row g-4">
                {{-- 1. BOGO Offer --}}
                <div class="col-6 col-md-6 col-lg-3">
                    <div class="vip-perk-card">
                        <div class="vip-perk-icon-wrap perk-bogo">
                            <i class="bi bi-bag-check-fill"></i>
                        </div>
                        <h5 class="vip-perk-title">Buy 1 Get 1 Free</h5>
                        <p class="vip-perk-desc">Add 2 eligible frames or sunglasses — cheapest pair automatically becomes <strong>100% FREE</strong>!</p>
                    </div>
                </div>

                {{-- 2. 10% Extra Discount on Contact Lens --}}
                <div class="col-6 col-md-6 col-lg-3">
                    <div class="vip-perk-card">
                        <div class="vip-perk-icon-wrap perk-lens">
                            <i class="bi bi-eye-fill"></i>
                        </div>
                        <h5 class="vip-perk-title">10% Extra Lens OFF</h5>
                        <p class="vip-perk-desc">Signature Members receive an <strong>extra 10% instant discount</strong> on top contact lens brands.</p>
                    </div>
                </div>

                {{-- 3. Flash Sale Access --}}
                <div class="col-6 col-md-6 col-lg-3">
                    <div class="vip-perk-card">
                        <div class="vip-perk-icon-wrap perk-flash">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <h5 class="vip-perk-title">Flash Sale Early Access</h5>
                        <p class="vip-perk-desc">Enjoy <strong>early VIP access</strong> to limited-time Flash Sales with extra price drop discounts.</p>
                    </div>
                </div>

                {{-- 4. Hourly Sale Priority --}}
                <div class="col-6 col-md-6 col-lg-3">
                    <div class="vip-perk-card">
                        <div class="vip-perk-icon-wrap perk-hourly">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h5 class="vip-perk-title">Hourly Deal Priority</h5>
                        <p class="vip-perk-desc">Never miss out on <strong>Hourly Price Drops</strong> with priority notification & stock reservation.</p>
                    </div>
                </div>
            </div>

            {{-- 5. New Customer Extra Discount Banner --}}
            <div class="vip-welcome-banner">
                <div>
                    <span class="vip-welcome-badge"><i class="bi bi-gift-fill me-1"></i> NEW CUSTOMER WELCOME OFFER</span>
                    <h4 class="vip-welcome-title">Get Extra Welcome Discounts on Your First Order!</h4>
                    <p class="vip-welcome-desc">First-time customers unlock additional instant discount coupons on membership activation.</p>
                </div>
                <a href="#membership-plans" class="vip-btn-banner">
                    Claim Offer Now <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- ═══ DYNAMIC MEMBERSHIP CARDS ═══ --}}
    <section id="membership-plans" class="vip-plans-section">
        <div class="container">
            <div class="vip-section-header">
                <h2>Choose Your VIP Membership Plan</h2>
                <p>Select the plan that best fits your eyewear and contact lens requirements</p>
            </div>

            <div class="row justify-content-center g-4">
                @forelse($membershipCards as $card)
                @php
                    $isSignature = (stripos($card->card_name, 'signature') !== false);
                    $isGold = (stripos($card->card_name, 'gold') !== false);
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="vip-plan-card {{ ($isSignature || $loop->first) ? 'popular-plan' : '' }}">
                        @if($isSignature || $loop->first)
                            <div class="vip-popular-ribbon">MOST POPULAR</div>
                        @endif

                        <div class="vip-crown-icon">
                            <i class="bi {{ $isSignature ? 'bi-gem' : 'bi-award' }}"></i>
                        </div>
                        <h3 class="vip-plan-name">{{ $card->card_name }}</h3>

                        <div class="vip-plan-price-wrap">
                            <span class="vip-plan-price">₹{{ number_format($card->price, 0) }}</span>
                            <div class="vip-plan-validity">Valid for {{ $card->validity_days }} Days</div>
                        </div>

                        <ul class="vip-plan-features">
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span><strong>Buy 1 Get 1 Free</strong> on 2 pairs of eyewear</span>
                            </li>
                            @if($isSignature || $isGold)
                            <li class="highlight">
                                <i class="bi bi-stars"></i>
                                <span><strong>Extra 10% OFF</strong> on Contact Lenses</span>
                            </li>
                            @endif
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span><strong>Flash Sale</strong> early VIP access</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span><strong>Hourly Sale</strong> priority deal reservation</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span><strong>New Customer</strong> extra welcome discount</span>
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Coverage for <strong>{{ $card->validity_days }} Days</strong></span>
                            </li>
                        </ul>

                        <a href="{{ route('cart') }}" class="vip-btn-plan">
                            Get {{ $card->card_name }} Plan
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-md-8 text-center py-4">
                    <div class="alert alert-info border-0 shadow-sm rounded-4" style="background:#e0f2fe; color:#0369a1;">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i> Membership plans are currently being updated. Please check back shortly!
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ═══ FAQ SECTION ═══ --}}
    <section class="vip-faq-section">
        <div class="container">
            <div class="vip-section-header">
                <h2>Frequently Asked Questions</h2>
                <p>Everything you need to know about Gold & Signature Membership perks</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion vip-accordion" id="membershipFaq">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How does the 10% Extra Contact Lens Discount work?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#membershipFaq">
                                <div class="accordion-body">
                                    Signature & Gold Members receive an extra 10% instant discount auto-applied on all contact lens brands (daily, monthly, or yearly disposable lenses) during checkout.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What benefits do I get during Flash Sales and Hourly Sales?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#membershipFaq">
                                <div class="accordion-body">
                                    Active Gold & Signature Members get early access to high-demand Flash Sales before public opening, as well as priority reservation during Hourly Sale price drop events.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How can New Customers claim extra discounts?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#membershipFaq">
                                <div class="accordion-body">
                                    First-time buyers receive a special welcome coupon code at signup that can be combined with membership activation for extra instant savings.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

@endsection
