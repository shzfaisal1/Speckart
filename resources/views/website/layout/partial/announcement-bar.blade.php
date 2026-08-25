{{-- ══════════════════════════════════════════════════════════════════════════════
     PREMIUM TOP ANNOUNCEMENT BAR — SPECKART
     Left: Phone & Email | Center: Free Shipping & Returns | Right: Track & Help
══════════════════════════════════════════════════════════════════════════════ --}}
<div id="speckartAnnouncementBar" class="speckart-announcement-bar" role="region" aria-label="Announcement Bar">
    <div class="container-fluid announcement-inner-container px-lg-4 px-3">
        <div class="row align-items-center w-100 m-0">
            
            <!-- LEFT: Phone & Email Support -->
            <div class="col-lg-3 col-md-4 d-none d-md-flex align-items-center p-0 announcement-left-col">
                <div class="announcement-contact-wrap">
                    <a href="tel:+919876543210" class="announcement-item announcement-link">
                        <i class="bi bi-telephone-fill"></i>
                        <span>+91 98765 43210</span>
                    </a>
                    <span class="announcement-divider">|</span>
                    <a href="mailto:support@speckarts.com" class="announcement-item announcement-link">
                        <i class="bi bi-envelope"></i>
                        <span>support@speckarts.com</span>
                    </a>
                </div>
            </div>

            <!-- CENTER: Free Shipping & Easy Returns Promo -->
            <div class="col-lg-6 col-md-8 col-12 d-flex align-items-center justify-content-center p-0 announcement-center-col">
                <div class="announcement-promo-wrap">
                    <div class="announcement-promo-item">
                        <i class="bi bi-truck text-warning"></i>
                        <span>Free Shipping on Orders Above <strong class="promo-highlight">₹999</strong></span>
                    </div>
                    <span class="announcement-divider d-none d-sm-inline-block">|</span>
                    <div class="announcement-promo-item d-none d-sm-flex">
                        <i class="bi bi-shield-check text-warning"></i>
                        <span>14-Day Easy Returns</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Track Order & Help & Support -->
            <div class="col-lg-3 d-none d-lg-flex align-items-center justify-content-end p-0 announcement-right-col">
                <div class="announcement-links-wrap">
                    <a href="{{ route('my-orders') }}" class="announcement-item announcement-link">
                        <span>Track Order</span>
                    </a>
                    <span class="announcement-divider">|</span>
                    <a href="{{ route('home-eye-test') }}" class="announcement-item announcement-link">
                        <span>Help &amp; Support</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* ==========================================================================
   SPECKART TOP ANNOUNCEMENT BAR
   ========================================================================== */
.speckart-announcement-bar {
    width: 100%;
    background-color: #053738;
    color: #ffffff;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    line-height: 1;
    position: relative;
    z-index: 1000;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.announcement-inner-container {
    min-height: 38px;
    display: flex;
    align-items: center;
    padding-top: 2px;
    padding-bottom: 2px;
}

/* Contact Wrap (Left) */
.announcement-contact-wrap {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    white-space: nowrap;
}

/* Promo Wrap (Center) */
.announcement-promo-wrap {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    white-space: nowrap;
    text-align: center;
}

.announcement-promo-item {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-weight: 500;
    letter-spacing: 0.15px;
    color: #ffffff;
}

.announcement-promo-item i {
    font-size: 14px;
    color: #fbbf24 !important;
}

.promo-highlight {
    color: #fbbf24;
    font-weight: 700;
    letter-spacing: 0.2px;
}

/* Links Wrap (Right) */
.announcement-links-wrap {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    white-space: nowrap;
}

/* Items & Links */
.announcement-item {
    color: #ffffff !important;
    text-decoration: none !important;
    font-size: 12.5px;
    font-weight: 500;
    letter-spacing: 0.15px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: opacity 0.2s ease, color 0.2s ease;
}

.announcement-link:hover {
    color: #5eead4 !important;
    opacity: 0.95;
}

.announcement-item i {
    font-size: 12.5px;
    color: #e2e8f0;
}

/* Translucent vertical divider */
.announcement-divider {
    color: rgba(255, 255, 255, 0.35);
    font-size: 12px;
    font-weight: 300;
    user-select: none;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .announcement-inner-container {
        min-height: 35px;
    }
    .announcement-item,
    .announcement-promo-item {
        font-size: 12px;
    }
    .announcement-promo-item i {
        font-size: 13px;
    }
}

@media (max-width: 575px) {
    .announcement-inner-container {
        min-height: 32px;
    }
    .announcement-promo-item {
        font-size: 11px;
        gap: 5px;
    }
    .announcement-promo-item i {
        font-size: 12px;
    }
}
</style>
