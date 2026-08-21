    <!-- navbar -->
    @php
        $initialWishlistCount = 0;
        if (auth()->check()) {
            $initialWishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
        }
        $initialCartCount = count(session('cart', []));
    @endphp

    <style>
        .cart-link {
            text-decoration: none;
        }

        .cart-icon {
            position: relative;
            display: inline-block;
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            min-width: 16px;
            height: 16px;
            padding: 0 5px;
            background: #ff3b30;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        /* ===== Loyalty Points Styles ===== */
        .loyalty-link {
            text-decoration: none;
        }

        .loyalty-icon {
            position: relative;
            display: inline-block;
        }

        .loyalty-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            min-width: 16px;
            height: 16px;
            padding: 0 5px;
            background: #16a085;
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .loyalty-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 16px;
            width: 240px;
            z-index: 999;
        }

        .loyalty-dropdown.show {
            display: block;
        }

        .loyalty-dropdown h6 {
            margin: 0 0 4px;
            font-size: 13px;
            color: #666;
        }

        .loyalty-dropdown .points-value {
            font-size: 22px;
            font-weight: 700;
            color: #16a085;
            margin-bottom: 8px;
        }

        .loyalty-dropdown .points-worth {
            font-size: 12px;
            color: #888;
            margin-bottom: 10px;
        }

        .loyalty-dropdown .btn-redeem {
            display: block;
            text-align: center;
            background: #16a085;
            color: #fff;
            padding: 8px;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
        }
        /* ===== End Loyalty Points Styles ===== */

        /* =========================================================
           PREMIUM MEGA MENU WITH VERTICAL TABS & 3x3 PRODUCT GRID
        ========================================================= */
        .header .nav-links li {
            position: static;
        }

        .header .mega-box {
            position: absolute;
            left: 0;
            right: 0;
            width: 100%;
            padding: 0px 16px 20px;
            top: 55px;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(8px);
            transition: opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1), transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.22s;
            z-index: 999;
        }

        .header .nav-links li:hover > .mega-box {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0);
        }

        .lenskart-mega-container {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 24px 60px -12px rgba(7, 72, 74, 0.18), 0 0 1px 1px rgba(0, 0, 0, 0.04);
            max-width: 1200px;
            width: 96%;
            margin: 0 auto;
            overflow: hidden;
            border: 1px solid #eef2f6;
            text-align: left;
            line-height: normal;
        }

        .lenskart-mega-container * {
            line-height: normal;
            box-sizing: border-box;
        }

        /* Main Flex layout: Left Vertical Tabs + Right Content Area */
        .lenskart-vlayout {
            display: flex;
            min-height: 270px;
        }

        /* LEFT SIDEBAR: Vertical Tab Cards */
        .lenskart-vtabs {
            width: 270px;
            flex-shrink: 0;
            background: #f8fafc;
            border-right: 1px solid #edf2f7;
            padding: 14px 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 8px;
        }

        .lenskart-vtab-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border-radius: 12px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            cursor: pointer;
            text-decoration: none !important;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .lenskart-vtab-item:hover {
            border-color: #07484a55;
            background: #f0fdf9;
            transform: translateX(3px);
        }

        /* Active Tab Card */
        .lenskart-vtab-item.active {
            background: linear-gradient(135deg, #07484a 0%, #0c5a5d 100%);
            border-color: #07484a;
            box-shadow: 0 8px 20px rgba(7, 72, 74, 0.22);
            transform: translateX(3px);
        }

        .lenskart-vtab-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .lenskart-vtab-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            background: #fff;
            flex-shrink: 0;
        }

        .lenskart-vtab-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .lenskart-vtab-title {
            font-size: 13.5px !important;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
            text-transform: none;
            transition: color 0.2s ease;
            white-space: nowrap !important;
        }

        .lenskart-vtab-item.active .lenskart-vtab-title {
            color: #ffffff;
        }

        .lenskart-vtab-badge {
            font-size: 10.5px;
            font-weight: 600;
            color: #0284c7;
            background: #e0f2fe;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            margin-top: 3px;
            padding: 2px 6px;
            border-radius: 10px;
            line-height: 1.2;
            width: fit-content;
            white-space: nowrap !important;
            transition: all 0.2s ease;
        }

        .lenskart-vtab-item.active .lenskart-vtab-badge {
            color: #e0f2fe;
            background: rgba(255, 255, 255, 0.18);
        }

        .lenskart-vtab-arrow {
            font-size: 13px;
            color: #94a3b8;
            transition: all 0.2s ease;
            flex-shrink: 0;
            margin-left: 6px;
        }

        .lenskart-vtab-item.active .lenskart-vtab-arrow {
            color: #ffffff;
            transform: translateX(2px);
        }

        /* RIGHT CONTENT AREA: 3x3 Product Grid */
        .lenskart-vcontent {
            flex: 1;
            padding: 14px 18px;
            background: #ffffff;
            position: relative;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: top;
        }

        .lenskart-vpane {
            display: none;
        }

        .lenskart-vpane.active {
            display: block;
            animation: lenskartFadeIn 0.22s ease-in-out;
        }

        @keyframes lenskartFadeIn {
            from { opacity: 0; transform: translateY(3px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .lenskart-pane-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #f1f5f9;
        }

        .lenskart-pane-header h5 {
            font-size: 14.5px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
            letter-spacing: -0.2px;
        }

        .lenskart-pane-header h5 span {
            color: #07484a;
            font-weight: 800;
        }

        .lenskart-pane-header .view-more-link {
            font-size: 11.5px;
            font-weight: 700;
            color: #07484a;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .lenskart-pane-header .view-more-link:hover {
            transform: translateX(3px);
            color: #053335;
        }

        /* Product Cards Grid: 3 Columns x 3 Rows with minmax(0, 1fr) */
        .lenskart-products-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 9px;
            width: 100%;
        }

        .header .nav-links .lenskart-mega-container a.lenskart-item-card,
        .lenskart-item-card {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 8px 10px !important;
            border-radius: 10px !important;
            text-decoration: none !important;
            background: #ffffff !important;
            border: 1px solid #eef2f6 !important;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
            min-height: 56px !important;
            text-transform: none !important;
            line-height: normal !important;
            overflow: hidden !important;
        }

        .header .nav-links .lenskart-mega-container a.lenskart-item-card:hover,
        .lenskart-item-card:hover {
            background: #ffffff !important;
            border-color: rgba(7, 72, 74, 0.35) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(7, 72, 74, 0.08) !important;
            color: inherit !important;
        }

        .lenskart-item-card-left {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            min-width: 0 !important;
            flex: 1 !important;
            overflow: hidden !important;
        }

        .lenskart-item-thumb {
            width: 50px !important;
            height: 34px !important;
            border-radius: 7px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 2px 4px !important;
            flex-shrink: 0 !important;
            transition: all 0.2s ease !important;
        }

        .lenskart-item-card:hover .lenskart-item-thumb {
            transform: scale(1.02) !important;
            background: #ffffff !important;
            border-color: rgba(7, 72, 74, 0.25) !important;
        }

        .lenskart-item-thumb img {
            max-width: 100% !important;
            max-height: 100% !important;
            width: auto !important;
            height: auto !important;
            object-fit: contain !important;
        }

        .lenskart-item-info {
            display: flex !important;
            flex-direction: column !important;
            min-width: 0 !important;
            text-align: left !important;
            flex: 1 !important;
            overflow: hidden !important;
        }

        .lenskart-item-title {
            font-size: 12.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            text-transform: none !important;
            transition: color 0.2s ease !important;
            letter-spacing: -0.15px !important;
        }

        .lenskart-item-card:hover .lenskart-item-title {
            color: #07484a !important;
        }

        .lenskart-item-price {
            font-size: 11.5px !important;
            font-weight: 500 !important;
            color: #64748b !important;
            margin-top: 1px !important;
            line-height: 1.15 !important;
            text-transform: none !important;
        }

        .lenskart-item-price strong {
            color: #07484a !important;
            font-weight: 700 !important;
            font-size: 12px !important;
        }

        .lenskart-item-chevron {
            color: #94a3b8 !important;
            font-size: 11px !important;
            transition: all 0.2s ease !important;
            flex-shrink: 0 !important;
            margin-left: 4px !important;
        }

        .lenskart-item-card:hover .lenskart-item-chevron {
            color: #07484a !important;
            transform: translateX(3px) !important;
        }

        /* Bottom Mega Footer Bar */
        .lenskart-mega-footer {
            background: #f8fafc;
            border-top: 1px solid #edf2f7;
            padding: 11px 26px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
        }

        .lenskart-footer-perks {
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .lenskart-perk {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #475569;
            font-size: 12px;
            font-weight: 500;
        }

        .lenskart-footer-link {
            color: #07484a !important;
            font-weight: 600 !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12.5px !important;
            transition: all 0.2s ease;
            text-transform: none !important;
        }

        .lenskart-footer-link:hover {
            color: #053335 !important;
            transform: translateX(2px);
        }

        /* Contact Lenses Grid */
        .lenskart-cl-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            padding: 22px 26px;
        }

        .lenskart-cl-col {
            background: #fafbfc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 14px 16px;
        }

        .lenskart-cl-col header {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid rgba(7, 72, 74, 0.12);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .lenskart-cl-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .lenskart-cl-list li a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 8px;
            border-radius: 6px;
            color: #334155 !important;
            font-size: 12.5px !important;
            font-weight: 500 !important;
            text-decoration: none;
            transition: all 0.18s ease;
            text-transform: none !important;
        }

        .lenskart-cl-list li a:hover {
            background: #ffffff;
            color: #07484a !important;
            transform: translateX(3px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .lenskart-cl-color-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* Home Eye-Test & Store locator custom cards */
        .lenskart-promo-card {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 24px;
        }

        .lenskart-promo-img {
            width: 45%;
            height: 180px;
            border-radius: 12px;
            overflow: hidden;
            flex-shrink: 0;
            background: #f1f5f9;
        }

        .lenskart-promo-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .lenskart-promo-content {
            width: 55%;
        }

        .lenskart-promo-content h3 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .lenskart-promo-content p {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .lenskart-btn-primary {
            background: #07484a;
            color: #fff !important;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .lenskart-btn-primary:hover {
            background: #0b5e61;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(7, 72, 74, 0.2);
        }

        .lenskart-cities-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .lenskart-city-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 8px;
            border-radius: 12px;
            background: linear-gradient(135deg, #07484a 0%, #0c5a5d 100%);
            border: 1px solid #07484a;
            text-decoration: none !important;
            transition: all 0.22s ease;
            box-shadow: 0 4px 12px rgba(7, 72, 74, 0.15);
        }

        .lenskart-city-item:hover {
            background: linear-gradient(135deg, #0b5e61 0%, #107275 100%);
            border-color: #0b5e61;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(7, 72, 74, 0.25);
        }

        .lenskart-city-item img {
            width: 34px;
            height: 34px;
            object-fit: contain;
            margin-bottom: 6px;
            filter: brightness(1) drop-shadow(0 2px 4px rgba(0,0,0,0.15));
        }

        .lenskart-city-item span {
            font-size: 12px;
            font-weight: 700;
            color: #ffffff !important;
            letter-spacing: 0.2px;
        }

        .profile-avatar {
            width: 26px !important;
            height: 26px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            border: 1.5px solid rgba(255, 255, 255, 0.9) !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        /* =========================================================
           USER PROFILE DROPDOWN MENU (LEFT ALIGNED + ICONS + HOVER FIX)
        ========================================================= */
        .user-profile-nav-item {
            position: relative !important;
            padding-bottom: 8px !important;
            margin-bottom: -8px !important;
        }

        .user-profile-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 210px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 16px 38px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #edf2f7;
            padding: 8px 6px;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(6px);
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s;
            z-index: 9999;
            text-align: left !important;
        }

        /* Invisible bridge above dropdown to guarantee smooth hover with 0 gap */
        .user-profile-menu-dropdown::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 0;
            right: 0;
            height: 20px;
            background: transparent;
            pointer-events: auto;
        }

        .user-profile-nav-item:hover .user-profile-menu-dropdown,
        .user-profile-nav-item.show-dropdown .user-profile-menu-dropdown,
        .user-profile-nav-item.active .user-profile-menu-dropdown {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0);
        }

        .user-dropdown-list {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
            display: flex;
            flex-direction: column;
            gap: 2px;
            text-align: left !important;
        }

        .user-dropdown-list li {
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
            text-align: left !important;
        }

        .user-dropdown-link {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 12px !important;
            padding: 10px 14px !important;
            border-radius: 10px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            text-decoration: none !important;
            transition: all 0.18s ease !important;
            text-transform: none !important;
            line-height: normal !important;
            text-align: left !important;
            width: 100% !important;
        }

        .user-dropdown-link i {
            font-size: 16px !important;
            color: #07484a !important;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            transition: transform 0.18s ease;
        }

        .user-dropdown-link span {
            text-align: left !important;
            flex: 1;
        }

        .user-dropdown-link:hover {
            background: #f0fdf9 !important;
            color: #07484a !important;
            padding-left: 17px !important;
        }

        .user-dropdown-link:hover i {
            transform: scale(1.15);
        }

        .user-dropdown-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 4px 6px !important;
        }

        .user-dropdown-logout-link {
            width: 100%;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 12px !important;
            padding: 10px 14px !important;
            border-radius: 10px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #dc2626 !important;
            border: none;
            background: transparent;
            cursor: pointer;
            text-align: left !important;
            transition: all 0.18s ease !important;
            text-transform: none !important;
            line-height: normal !important;
        }

        .user-dropdown-logout-link i {
            font-size: 16px !important;
            color: #dc2626 !important;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            transition: transform 0.18s ease;
        }

        .user-dropdown-logout-link span {
            text-align: left !important;
            flex: 1;
        }

        .user-dropdown-logout-link:hover {
            background: #fee2e2 !important;
            color: #991b1b !important;
            padding-left: 17px !important;
        }

        .user-dropdown-logout-link:hover i {
            transform: scale(1.15);
        }

        /* =========================================================
           PREMIUM MOBILE & TABLET DRAWER (OFFCANVAS)
        ========================================================= */
        #mobileSidebar.offcanvas {
            width: 320px !important;
            max-width: 86vw !important;
            height: 100vh !important;
            background: #f8fafc !important;
            border-left: 1px solid rgba(0, 0, 0, 0.1) !important;
            box-shadow: -15px 0 45px rgba(0, 0, 0, 0.3) !important;
            display: flex !important;
            flex-direction: column !important;
            padding: 0 !important;
            overflow: hidden !important;
            top: 0 !important;
            bottom: 0 !important;
            right: 0 !important;
            border-radius: 0 !important;
            z-index: 100000 !important;
        }

        @media (min-width: 576px) and (max-width: 991px) {
            #mobileSidebar.offcanvas {
                width: 370px !important;
                max-width: 90vw !important;
            }
        }

        .mobile-drawer-header {
            background: #07484a !important;
            padding: 12px 16px !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-shrink: 0 !important;
            min-height: 58px !important;
            width: 100% !important;
        }

        .mobile-drawer-logo {
            width: 140px;
            display: block;
            line-height: 1;
        }

        .mobile-drawer-logo img {
            width: 100%;
            height: auto;
            max-height: 34px;
            object-fit: contain;
            filter: brightness(1.05);
        }

        .mobile-drawer-close {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
            padding: 0;
        }

        .mobile-drawer-close:hover {
            background: #ffffff;
            color: #07484a;
            transform: rotate(90deg);
        }

        .mobile-drawer-body {
            padding: 14px !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            flex: 1 1 auto !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
        }

        /* User Header / Hero in Drawer */
        .mobile-user-card {
            background: linear-gradient(135deg, #07484a 0%, #0a3d3f 60%, #042e2f 100%);
            border-radius: 16px;
            padding: 14px 12px;
            color: #ffffff;
            position: relative;
            overflow: visible !important;
            box-shadow: 0 8px 20px -4px rgba(7, 72, 74, 0.35);
            width: 100%;
        }

        /* .mobile-user-card::before {
            content: '';
            position: absolute;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            border: 1px solid rgba(0, 185, 185, 0.2);
            top: -60px;
            right: -50px;
            pointer-events: none;
        } */

        .mobile-user-top {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mobile-user-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
            background: #ffffff;
            flex-shrink: 0;
        }

        .mobile-user-info {
            min-width: 0;
            flex: 1;
        }

        .mobile-user-name {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .mobile-user-meta {
            font-size: 11.5px;
            color: #cbd5e1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mobile-user-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #fde047;
            color: #07484a;
            font-size: 9.5px;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 20px;
            margin-top: 3px;
            text-transform: uppercase;
        }

        /* Mobile Quick Account Links */
        .mobile-user-quick-links {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .mobile-user-quick-btn {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            padding: 7px 3px;
            text-align: center;
            color: #ffffff !important;
            text-decoration: none !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            font-size: 10px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .mobile-user-quick-btn i {
            font-size: 13px;
        }

        .mobile-user-quick-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        /* Guest Welcome Box in Drawer */
        .mobile-guest-card {
            background: linear-gradient(135deg, #07484a 0%, #0a3d3f 100%);
            border-radius: 18px;
            padding: 18px 16px;
            color: #ffffff;
            text-align: center;
            box-shadow: 0 10px 24px -6px rgba(7, 72, 74, 0.3);
        }

        .mobile-guest-card h5 {
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .mobile-guest-card p {
            font-size: 12px;
            color: #cbd5e1;
            margin-bottom: 12px;
        }

        .btn-mobile-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            background: #ffffff;
            color: #07484a !important;
            font-weight: 700;
            font-size: 13px;
            padding: 9px 14px;
            border-radius: 12px;
            text-decoration: none !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .btn-mobile-login:hover {
            background: #f0fdf9;
            transform: translateY(-2px);
        }

        /* 4 Quick Pills Grid (Cart, Wishlist, Eye Test, Stores) */
        .mobile-quick-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 8px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .mobile-quick-pill {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none !important;
            color: #0f172a !important;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
            position: relative;
            min-width: 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }

        .mobile-quick-pill:hover {
            border-color: #07484a;
            background: #f0fdf9;
            transform: translateY(-2px);
        }

        .mobile-quick-pill .icon-box {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .mobile-quick-pill span.pill-label {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            min-width: 0;
            font-size: 11.5px;
            line-height: 1.2;
        }

        .mobile-quick-pill .badge-pill {
            margin-left: auto;
            background: #dc2626;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 10px;
            flex-shrink: 0;
        }

        .mobile-quick-pill .badge-accent {
            margin-left: auto;
            background: #fde047;
            color: #07484a;
            font-size: 9px;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 8px;
            flex-shrink: 0;
        }

        /* Category Accordion Section */
        .mobile-nav-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .mobile-section-label {
            font-size: 11px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 8px 12px 4px;
        }

        .mobile-nav-group {
            border-bottom: 1px solid #f1f5f9;
        }

        .mobile-nav-group:last-child {
            border-bottom: none;
        }

        .mobile-nav-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border-radius: 12px;
            color: #1e293b;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none !important;
            transition: all 0.2s ease;
        }

        .mobile-nav-heading:hover {
            background: #f8fafc;
            color: #07484a;
        }

        .mobile-nav-heading-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mobile-nav-heading-left i {
            font-size: 16px;
            color: #07484a;
            width: 20px;
            text-align: center;
        }

        .mobile-nav-chevron {
            font-size: 12px;
            color: #94a3b8;
            transition: transform 0.25s ease;
        }

        .mobile-nav-group.open .mobile-nav-chevron {
            transform: rotate(180deg);
            color: #07484a;
        }

        .mobile-sub-nav {
            display: none;
            padding: 4px 10px 10px 38px;
            list-style: none;
            margin: 0;
        }

        .mobile-sub-nav li {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .mobile-sub-nav a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            text-decoration: none !important;
            border-radius: 8px;
            transition: all 0.18s ease;
        }

        .mobile-sub-nav a:hover {
            background: #f0fdf9;
            color: #07484a;
            padding-left: 14px;
        }

        /* Banner Card inside Drawer */
        .mobile-promo-banner {
            background: linear-gradient(135deg, #07484a 0%, #00b9b9 100%);
            border-radius: 16px;
            padding: 16px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none !important;
            box-shadow: 0 8px 20px -4px rgba(7, 72, 74, 0.25);
            transition: all 0.2s ease;
        }

        .mobile-promo-banner:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -4px rgba(7, 72, 74, 0.35);
        }

        .mobile-promo-text h6 {
            font-size: 14px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .mobile-promo-text p {
            font-size: 11.5px;
            color: #e6fffa;
            margin-bottom: 0;
        }

        .mobile-promo-badge {
            background: #fde047;
            color: #07484a;
            font-size: 11px;
            font-weight: 800;
            padding: 6px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        /* Drawer Footer Support & Logout */
        .mobile-drawer-footer {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .mobile-support-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
        }

        .mobile-support-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #059669;
            text-decoration: none !important;
            font-weight: 700;
        }

        .mobile-logout-btn {
            width: 100%;
            padding: 9px;
            border-radius: 10px;
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mobile-logout-btn:hover {
            background: #dc2626;
            color: #ffffff;
        }
    </style>

    <!-- navbar -->
    @include('website.layout.partial.login-modal')
    <section class="header" data-is-logged-in="{{ auth()->check() ? 'true' : 'false' }}">
        <div class="container header-container">
            <div class="row mb-0">
                <div class="col-lg-12 px-lg-4 px-2">
                    <div class="top-menu px-0 flex-wrap flex-md-wrap flex-lg-nowrap">
                        <div class="d-lg-block d-md-none d-none">
                            <div class="d-flex align-items-center ms-lg-0 ms-mb-3 ms-3">
                                <a href="/speckart-website" class="logo">
                                    <img src="{{ asset('website/assets/img/logo/Specskart-logo-png.png') }}" alt="Speckarts Logo">
                                </a>
                            </div>
                        </div>
                        <div class="d-lg-none d-md-block d-block">
                            <a style="padding: 2px 12px;display: flex;flex-direction: column;">
                                <div>
                                    <p class="mb-0" style="color:rgb(255, 94, 72)">Get faster delivery <img
                                            src="{{ asset('website/assets/img/icon/Thunder3x.webp') }}" alt="gold-delivery-icon"
                                            title="gold-delivery-icon" class="gold-delivery-icon"></p>
                                </div>
                                <div class="select-location">
                                    <div class="location-text">
                                        <h5>Select Location</h5>
                                    </div>
                                    <div class="location-arrow">
                                        <svg width="10" height="6" viewBox="0 0 8 4" fill="none">
                                            <path d="M4 4 0 0h8L4 4Z" fill="#fff" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="top-menu-list d-lg-none d-md-block d-block">
                            <ul class="ps-0 mb-0 d-flex align-items-center justify-content-end">
                                <!-- Wishlist -->
                                <li>
                                    <a href="{{ route('wishlist') }}" class="wishlist-link">
                                        <p class="wishlist-icon position-relative mb-0">
                                            <img src="{{ asset('website/assets/img/icon/Wishlist.png') }}" alt="Wishlist">
                                            <span class="wishlist-badge badge rounded-pill bg-danger position-absolute {{ $initialWishlistCount > 0 ? '' : 'd-none' }}"
                                                style="top: -5px; right: -8px; font-size: 10px; padding: 2px 5px;">{{ $initialWishlistCount }}</span>
                                        </p>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('cart') }}" class="cart-link">
                                        <p class="cart-icon position-relative mb-0">
                                            <img src="{{ asset('website/assets/img/icon/My-Cart.png') }}" alt="Cart">
                                            <span class="cart-badge badge rounded-pill bg-danger position-absolute {{ $initialCartCount > 0 ? '' : 'd-none' }}"
                                                style="top: -5px; right: -8px; font-size: 10px; padding: 2px 5px;">{{ $initialCartCount }}</span>
                                        </p>
                                    </a>
                                </li>

                                <!-- Menu -->
                                <li class="pe-2">
                                    <button class="btn mobile-menu-btn p-0" data-bs-toggle="offcanvas"
                                        data-bs-target="#mobileSidebar">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 17H14.5M5 12H19M5 7H19" stroke="#fff" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="search-box position-relative">
                            <form action="{{ route('products') }}" method="GET" id="header-search-form">
                                <div class="input-group custom-search">
                                    <input type="text" class="form-control border-0 px-3 ajax-search-input" id="searchInput" name="search" autocomplete="off"
                                        placeholder="Search glasses, lenses, specs..." value="{{ request('search') }}">
                                    <button class="btn search-btn" type="submit" style="border-left:1px solid #000;">
                                        Search
                                    </button>
                                </div>
                            </form>

                            <!-- Search Dropdown -->
                            <div id="search-suggestions-dropdown" class="search-suggestions-dropdown ajax-search-dropdown" style="display:none;">
                                <div class="search-suggestions-content">
                                    <!-- Suggestions populated via AJAX -->
                                </div>
                            </div>
                        </div>
                        <div class="top-menu-list d-lg-block d-md-none d-none">
                            <ul class="ps-0">
                                <li>
                                    <a href="{{ route('wishlist') }}" class="wishlist-link">
                                        <p class="wishlist-icon position-relative">
                                            <img src="{{ asset('website/assets/img/icon/Wishlist.png') }}" alt="Wishlist">
                                            <span class="wishlist-badge badge rounded-pill bg-danger position-absolute {{ $initialWishlistCount > 0 ? '' : 'd-none' }}"
                                                style="top: -4px; right: 3px; font-size: 10px; padding: 2px 5px;">{{ $initialWishlistCount }}</span>
                                        </p>
                                        <p>Wishlist</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('cart') }}" class="cart-link">
                                        <p class="cart-icon position-relative">
                                            <img src="{{ asset('website/assets/img/icon/My-Cart.png') }}" alt="Cart">
                                            <span class="cart-badge badge rounded-pill bg-danger position-absolute {{ $initialCartCount > 0 ? '' : 'd-none' }}"
                                                style="top: -5px; right: -8px; font-size: 10px; padding: 2px 5px;">{{ $initialCartCount }}</span>
                                        </p>
                                        <p>My Cart</p>
                                    </a>
                                </li>

                                <li class="dropdown pe-0 user-profile-nav-item">
                                    @guest
                                        <a href="{{ route('login.web') }}">
                                            <p>
                                                <img src="{{ asset('website/assets/img/icon/Signup.png') }}" alt="Login">
                                            </p>
                                            <p>Sign up / Sign In</p>
                                        </a>
                                    @endguest

                                    @auth
                                        <a href="javascript:void(0);" class="dropdown-toggle user-nav-toggle">
                                            <p>
                                                <img src="{{ Auth::user()->image ? Auth::user()->profile_image_url : asset('website/assets/img/icon/user.png') }}" alt="{{ Auth::user()->name }}" class="profile-avatar" onerror="this.onerror=null;this.src='{{ asset('website/assets/img/icon/user.png') }}';">
                                            </p>
                                            <p>{{ Auth::user()->name }}</p>
                                        </a>
                                        <div class="user-profile-menu-dropdown">
                                            <ul class="user-dropdown-list">
                                                <li>
                                                    <a href="{{ route('profile') }}" class="user-dropdown-link">
                                                        <i class="bi bi-person"></i>
                                                        <span>Profile</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('my-orders') }}" class="user-dropdown-link">
                                                        <i class="bi bi-box-seam"></i>
                                                        <span>My Orders</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('my-addresses') }}" class="user-dropdown-link">
                                                        <i class="bi bi-geo-alt"></i>
                                                        <span>My Addresses</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('my-prescriptions') }}" class="user-dropdown-link">
                                                        <i class="bi bi-file-earmark-medical"></i>
                                                        <span>My Prescriptions</span>
                                                    </a>
                                                </li>
                                                <li class="user-dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('logout.web') }}" style="margin:0; width:100%;">
                                                        @csrf
                                                        <button type="submit" class="user-dropdown-logout-link">
                                                            <i class="bi bi-box-arrow-right"></i>
                                                            <span>Logout</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endauth
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================
             DESKTOP NAVIGATION: VERTICAL TABS + RIGHT 3x3 PRODUCT GRID
        ========================================================= -->
        <nav class="d-lg-block d-md-none d-none">
            <div class="wrapper">
                <div class="row mb-0">
                    <div class="col-lg-12 text-center">
                        <input type="radio" name="slider" id="menu-btn">
                        <input type="radio" name="slider" id="close-btn">
                        <ul class="nav-links gap-3">
                            <label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>

                            <!-- 1. EYEGLASSES (VERTICAL TABS: MEN, WOMEN, KIDS -> RIGHT 3x3 GRID) -->
                            <li class="lenskart-nav-parent">
                                <a href="{{ route('products') }}?category=eyeglasses" class="desktop-item">Eyeglasses</a>
                                <input type="checkbox" id="showMegaEye">
                                <label for="showMegaEye" class="mobile-item">Eyeglasses</label>
                                <div class="mega-box">
                                    <div class="lenskart-mega-container">
                                        <div class="lenskart-vlayout">
                                            <!-- LEFT: Vertical Tabs for Men, Women, Kids -->
                                            <div class="lenskart-vtabs">
                                                <!-- Tab 1: Men -->
                                                <div class="lenskart-vtab-item active" data-vtarget="#eye-pane-men">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/icon/specs-men.png') }}" alt="Men" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">MEN Eyeglasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-patch-check-fill"></i> with FREE lenses</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>

                                                <!-- Tab 2: Women -->
                                                <div class="lenskart-vtab-item" data-vtarget="#eye-pane-women">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/icon/specs-women.png') }}" alt="Women" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">WOMEN Eyeglasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-patch-check-fill"></i> with FREE lenses</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>

                                                <!-- Tab 3: Kids -->
                                                <div class="lenskart-vtab-item" data-vtarget="#eye-pane-kids">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/icon/specs-kid.png') }}" alt="Kids" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">KIDS Eyeglasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-patch-check-fill"></i> with FREE lenses</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>
                                            </div>

                                            <!-- RIGHT: Active Tab Content (3x3 Products Grid) -->
                                            <div class="lenskart-vcontent">
                                                <!-- PANE 1: MEN EYEGLASSES -->
                                                <div class="lenskart-vpane active" id="eye-pane-men">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>MEN'S</span> Eyewear Collection</h5>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=men" class="view-more-link">
                                                            View All Men's Glasses <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=men&brand=John%20Jacobs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs1.png') }}" alt="John Jacobs"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">John Jacobs | Owndays</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹3000</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=men&brand=Vincent%20Chase" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs2.png') }}" alt="Vincent Chase"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Vincent Chase | Speckart Air</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=men&shape=Rectangle" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs3.png') }}" alt="Hustlr"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Hustlr | Shark Tank Edition</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=men&frame_type=Full-Rim" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs4.png') }}" alt="Essentials"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Essentials | Daily Wear</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=men&shape=Rectangle" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs5.png') }}" alt="Rectangle Frames"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Rectangle & Square Frames</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹800</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=men&shape=Round" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs6.png') }}" alt="Round Frames"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Round & Aviator Styles</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹999</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=men&frame_type=Rimless" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/Rimless.png') }}" alt="Rimless Frames"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Rimless & Titanium Frames</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=men&frame_type=Half-Rim" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/Half-Rim.png') }}" alt="Half Rim"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Halfrim Business Frames</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1200</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=men" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs7.png') }}" alt="All Brands"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">All Men Brands & Styles</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- PANE 2: WOMEN EYEGLASSES -->
                                                <div class="lenskart-vpane" id="eye-pane-women">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>WOMEN'S</span> Eyewear Collection</h5>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=women" class="view-more-link">
                                                            View All Women's Glasses <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=women&brand=John%20Jacobs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs1.png') }}" alt="John Jacobs"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">John Jacobs | Owndays</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹3000</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=women&brand=Vincent%20Chase" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs6.png') }}" alt="Vincent Chase"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Vincent Chase | Speckart Air</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=women&shape=Cat-Eye" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs7.png') }}" alt="Cat-Eye"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Hustlr | Cat-Eye & Chic</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=women&frame_type=Full-Rim" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs4.png') }}" alt="Essentials"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Essentials | Lightweight Daily</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=women&shape=Round" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs5.png') }}" alt="Round Frames"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Round & Hexagonal Styles</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹800</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=women&shape=Cat-Eye" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs3.png') }}" alt="Butterfly Frames"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Butterfly & Oversized Glam</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1200</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=women&frame_type=Rimless" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/Rimless.png') }}" alt="Rimless Frames"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Rimless Sleek Metals</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=women&frame_type=Half-Rim" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/Half-Rim.png') }}" alt="Half Rim"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Halfrim Pastel Frames</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1100</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=eyeglasses&gender=women" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs2.png') }}" alt="All Brands"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">All Women Brands & Styles</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- PANE 3: KIDS EYEGLASSES -->
                                                <div class="lenskart-vpane" id="eye-pane-kids">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>KIDS'</span> Eyewear Collection</h5>
                                                        <a href="{{ route('products') }}?category=kids" class="view-more-link">
                                                            View All Kids' Glasses <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=kids&age=5-8%20Yrs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s1.png') }}" alt="Juniors"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Juniors | 5 to 8 years</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹800</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids&age=8-12%20Yrs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s2.png') }}" alt="Tweens"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Tweens | 8 to 12 years</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids&age=8-12%20Yrs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s3.png') }}" alt="Teens"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Teens | 12 to 17 years</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s4.png') }}" alt="Hooper Mini"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Hooper Mini | Flexi Unbreakable</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹999</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s1.png') }}" alt="Creatr"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Creatr Ultra-Light Frames</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹600</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s2.png') }}" alt="Flexi"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Flexi Shock-Absorbing</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹800</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids&shape=Round" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s3.png') }}" alt="Round Kids"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Round & Oval Kid Shapes</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s4.png') }}" alt="Zero Power"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Zero Power Anti-Breakage</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹600</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs4.png') }}" alt="All Kids"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">All Kids Glasses & Frames</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bottom Perks Bar -->
                                        <div class="lenskart-mega-footer">
                                            <div class="lenskart-footer-perks">
                                                <div class="lenskart-perk"><i class="bi bi-shield-check text-success"></i> 1 Year Warranty</div>
                                                <div class="lenskart-perk"><i class="bi bi-arrow-repeat text-primary"></i> 14-Day Free Exchange</div>
                                                <div class="lenskart-perk"><i class="bi bi-truck text-warning"></i> Free Shipping Pan India</div>
                                                <div class="lenskart-perk"><i class="bi bi-patch-check-fill text-info"></i> 100% Authentic</div>
                                            </div>
                                            <a href="{{ route('products') }}?category=eyeglasses" class="lenskart-footer-link">
                                                View All Eyeglasses <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            {{-- <!-- 2. SCREEN GLASSES (VERTICAL TABS: MEN, WOMEN, KIDS -> RIGHT 3x3 GRID) -->
                            <li class="lenskart-nav-parent">
                                <a href="{{ route('products') }}?category=computer-glasses" class="desktop-item">Screen Glasses</a>
                                <input type="checkbox" id="showMegaScreen">
                                <label for="showMegaScreen" class="mobile-item">Screen Glasses</label>
                                <div class="mega-box">
                                    <div class="lenskart-mega-container">
                                        <div class="lenskart-vlayout">
                                            <!-- LEFT: Vertical Tabs for Men, Women, Kids -->
                                            <div class="lenskart-vtabs">
                                                <!-- Tab 1: Men -->
                                                <div class="lenskart-vtab-item active" data-vtarget="#screen-pane-men">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/icon/specs-men.png') }}" alt="Men" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">MEN Screen Glasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-laptop"></i> Zero Power BLU Cut</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>

                                                <!-- Tab 2: Women -->
                                                <div class="lenskart-vtab-item" data-vtarget="#screen-pane-women">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/icon/specs-women.png') }}" alt="Women" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">WOMEN Screen Glasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-laptop"></i> Zero Power BLU Cut</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>

                                                <!-- Tab 3: Kids -->
                                                <div class="lenskart-vtab-item" data-vtarget="#screen-pane-kids">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/icon/specs-kid.png') }}" alt="Kids" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">KIDS Screen Glasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-shield-check"></i> Anti-Eye Strain</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>
                                            </div>

                                            <!-- RIGHT: Active Tab Content (3x3 Products Grid) -->
                                            <div class="lenskart-vcontent">
                                                <!-- PANE 1: MEN SCREEN GLASSES -->
                                                <div class="lenskart-vpane active" id="screen-pane-men">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>MEN'S</span> Blue Cut Screen Specs</h5>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=men" class="view-more-link">
                                                            View All Men's Screen Specs <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=men&brand=Vincent%20Chase" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs2.png') }}" alt="Vincent Chase BLU"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Vincent Chase BLU | Anti-Glare</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=men&brand=John%20Jacobs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs1.png') }}" alt="John Jacobs BLU"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">John Jacobs BLU | Acetate</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹3000</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=men" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs3.png') }}" alt="Hustlr Computer"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Hustlr | Blue Light Blocker</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹600</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=men" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs4.png') }}" alt="BLU Essentials"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Speckarts BLU Essentials</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=men&shape=Rectangle" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs5.png') }}" alt="Rectangle Screen"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Rectangle Screen Specs</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹800</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=men&shape=Round" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs6.png') }}" alt="Round Screen"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Round Anti-Glare Glasses</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹999</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=men" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs7.png') }}" alt="Gaming Specs"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Gaming & Coding Glasses</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1200</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=men" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/Rimless.png') }}" alt="Ultra Light"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Ultra-Light Weight Metals</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1400</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=men" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs1.png') }}" alt="All Screen Specs"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">All Screen Glasses</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- PANE 2: WOMEN SCREEN GLASSES -->
                                                <div class="lenskart-vpane" id="screen-pane-women">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>WOMEN'S</span> Blue Cut Screen Specs</h5>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=women" class="view-more-link">
                                                            View All Women's Screen Specs <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=women&brand=Vincent%20Chase" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs6.png') }}" alt="Vincent Chase BLU"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Vincent Chase BLU | Anti-Glare</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=women&brand=John%20Jacobs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs1.png') }}" alt="John Jacobs BLU"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">John Jacobs BLU | Chic Frames</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹3000</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=women&shape=Cat-Eye" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs7.png') }}" alt="Cat-Eye"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Hustlr | Cat-Eye Blue Filter</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹600</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=women" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs4.png') }}" alt="Daily Blue Cut"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Essentials | Daily Blue Cut</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=women&shape=Round" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs5.png') }}" alt="Round Computer"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Round Pastel Blue Cut</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹800</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=women" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs3.png') }}" alt="Work Glasses"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Work from Home Specials</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹999</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=women" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/Rimless.png') }}" alt="Rimless BLU"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Rimless Lightweight Specs</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=women" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/Half-Rim.png') }}" alt="Half Rim BLU"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Halfrim Chic Blue Cut</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1100</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=computer-glasses&gender=women" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs2.png') }}" alt="All Screen Specs"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">All Screen Glasses</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- PANE 3: KIDS SCREEN GLASSES -->
                                                <div class="lenskart-vpane" id="screen-pane-kids">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>KIDS'</span> Anti-Glare Screen Glasses</h5>
                                                        <a href="{{ route('products') }}?category=kids&category=computer-glasses" class="view-more-link">
                                                            View All Kids' Screen Specs <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=kids&age=5-8%20Yrs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s1.png') }}" alt="Hooper BLU"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Hooper BLU | 5 to 8 yrs</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹800</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids&age=8-12%20Yrs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s2.png') }}" alt="Creatr BLU"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Creatr BLU | 8 to 12 yrs</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹600</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids&age=8-12%20Yrs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s3.png') }}" alt="Flexi Teens"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Flexi Teens | 12 to 17 yrs</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1200</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s4.png') }}" alt="Online Study"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Online Study & Tablet Specs</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹700</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s1.png') }}" alt="Flexi Frames"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Flexi Unbreakable Blue Cut</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹899</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s4.png') }}" alt="All Kids Screen"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">All Kids Screen Glasses</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹600</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bottom Perks Bar -->
                                        <div class="lenskart-mega-footer">
                                            <div class="lenskart-footer-perks">
                                                <div class="lenskart-perk"><i class="bi bi-shield-slash text-danger"></i> Blocks 98% Harmful Blue Ray</div>
                                                <div class="lenskart-perk"><i class="bi bi-laptop text-primary"></i> Ideal for Work, Study & Screen</div>
                                                <div class="lenskart-perk"><i class="bi bi-truck text-warning"></i> Free Express Shipping</div>
                                            </div>
                                            <a href="{{ route('products') }}?category=computer-glasses" class="lenskart-footer-link">
                                                Explore All Computer Glasses <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- 3. KIDS GLASSES (VERTICAL TABS: EYEGLASSES, SCREEN, SUNGLASSES -> RIGHT GRID) -->
                            <li class="lenskart-nav-parent">
                                <a href="{{ route('products') }}?category=kids" class="desktop-item">Kids Glasses</a>
                                <input type="checkbox" id="showMegaKids">
                                <label for="showMegaKids" class="mobile-item">Kids Glasses</label>
                                <div class="mega-box">
                                    <div class="lenskart-mega-container">
                                        <div class="lenskart-vlayout">
                                            <!-- LEFT: Vertical Tabs for Kids categories -->
                                            <div class="lenskart-vtabs">
                                                <div class="lenskart-vtab-item active" data-vtarget="#kids-pane-eye">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/icon/specs-kid.png') }}" alt="Kids Eyeglasses" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">KIDS Eyeglasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-stars"></i> Flexible & Safe</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>

                                                <div class="lenskart-vtab-item" data-vtarget="#kids-pane-screen">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/icon/Zero-Power-Specs.png') }}" alt="Kids Screen" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">KIDS Screen Glasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-laptop"></i> Zero Power BLU</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>

                                                <div class="lenskart-vtab-item" data-vtarget="#kids-pane-sun">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/bg/Sunglasses1.png') }}" alt="Kids Sunglasses" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">KIDS Sunglasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-sun"></i> 100% UV Protection</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>
                                            </div>

                                            <!-- RIGHT: Active Content -->
                                            <div class="lenskart-vcontent">
                                                <!-- PANE 1: KIDS EYEGLASSES -->
                                                <div class="lenskart-vpane active" id="kids-pane-eye">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>KIDS'</span> Prescription Eyeglasses</h5>
                                                        <a href="{{ route('products') }}?category=kids" class="view-more-link">
                                                            View All Kids Glasses <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=kids&age=5-8%20Yrs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s1.png') }}" alt="Juniors"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Juniors (5 to 8 years)</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹800</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids&age=8-12%20Yrs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s2.png') }}" alt="Tweens"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Tweens (8 to 12 years)</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids&age=8-12%20Yrs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s3.png') }}" alt="Teens"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Teens (12 to 17 years)</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s4.png') }}" alt="Hooper Mini"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Hooper Mini | Flexi Unbreakable</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹999</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s1.png') }}" alt="Creatr"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Creatr Ultra-Light Frames</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹600</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/specs4.png') }}" alt="All Kids"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">All Kids Glasses & Frames</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- PANE 2: KIDS SCREEN GLASSES -->
                                                <div class="lenskart-vpane" id="kids-pane-screen">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>KIDS'</span> Blue Cut Screen Specs</h5>
                                                        <a href="{{ route('products') }}?category=kids&category=computer-glasses" class="view-more-link">
                                                            View All Screen Specs <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s2.png') }}" alt="Creatr"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Creatr Flexible Frames</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹800</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s3.png') }}" alt="Flexi"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Flexi Shock-Absorbing</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹999</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/s4.png') }}" alt="Memphis"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Memphis Ultra-Light</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1100</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- PANE 3: KIDS SUNGLASSES -->
                                                <div class="lenskart-vpane" id="kids-pane-sun">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>KIDS'</span> 100% UV Sunglasses</h5>
                                                        <a href="{{ route('products') }}?category=kids&category=sunglasses" class="view-more-link">
                                                            View All Kids Sunglasses <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=kids&category=sunglasses&brand=Vincent%20Chase" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses1.png') }}" alt="Vincent Chase Kids"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Vincent Chase Polarized Kids</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹999</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids&category=sunglasses" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses2.png') }}" alt="Speckarts Air Kids"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Speckarts Air Outdoor</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹799</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=kids&category=sunglasses" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses3.png') }}" alt="Playwear Active"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Playwear Active Specs</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹699</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="lenskart-mega-footer">
                                            <div class="lenskart-footer-perks">
                                                <div class="lenskart-perk"><i class="bi bi-check-circle-fill text-success"></i> Child-Safe Materials</div>
                                                <div class="lenskart-perk"><i class="bi bi-shield-shaded text-primary"></i> Anti-Breakage Hinge</div>
                                                <div class="lenskart-perk"><i class="bi bi-truck text-warning"></i> Free Shipping Pan India</div>
                                            </div>
                                            <a href="{{ route('products') }}?category=kids" class="lenskart-footer-link">
                                                Shop All Kids Collection <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li> --}}

                            <!-- 4. SUNGLASSES (VERTICAL TABS: MEN, WOMEN, TRENDING -> RIGHT 3x3 GRID) -->
                            <li class="lenskart-nav-parent">
                                <a href="{{ route('products') }}?category=sunglasses" class="desktop-item">Sunglasses</a>
                                <input type="checkbox" id="showMegaSun">
                                <label for="showMegaSun" class="mobile-item">Sunglasses</label>
                                <div class="mega-box">
                                    <div class="lenskart-mega-container">
                                        <div class="lenskart-vlayout">
                                            <!-- LEFT: Vertical Tabs for Sunglasses -->
                                            <div class="lenskart-vtabs">
                                                <!-- Tab 1: Men -->
                                                <div class="lenskart-vtab-item active" data-vtarget="#sun-pane-men">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/icon/specs-men.png') }}" alt="Men" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">MEN Sunglasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-sun-fill"></i> 100% UV Protection</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>

                                                <!-- Tab 2: Women -->
                                                <div class="lenskart-vtab-item" data-vtarget="#sun-pane-women">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/icon/specs-women.png') }}" alt="Women" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">WOMEN Sunglasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-sun-fill"></i> 100% UV Protection</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>

                                                <!-- Tab 3: Trending -->
                                                <div class="lenskart-vtab-item" data-vtarget="#sun-pane-trending">
                                                    <div class="lenskart-vtab-left">
                                                        <img src="{{ asset('website/assets/img/icon/sunglasses.png') }}" alt="Trending" class="lenskart-vtab-avatar">
                                                        <div class="lenskart-vtab-info">
                                                            <span class="lenskart-vtab-title">KIDS Sunglasses</span>
                                                            <span class="lenskart-vtab-badge"><i class="bi bi-fire"></i> Best Sellers</span>
                                                        </div>
                                                    </div>
                                                    <i class="bi bi-chevron-right lenskart-vtab-arrow"></i>
                                                </div>
                                            </div>

                                            <!-- RIGHT: Active Tab Content (3x3 Products Grid) -->
                                            <div class="lenskart-vcontent">
                                                <!-- PANE 1: MEN SUNGLASSES -->
                                                <div class="lenskart-vpane active" id="sun-pane-men">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>MEN'S</span> Sunglasses Collection</h5>
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=men" class="view-more-link">
                                                            View All Men's Sunglasses <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=men&brand=Vincent%20Chase" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses1.png') }}" alt="Vincent Chase"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Vincent Chase | Polarized</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=men&brand=John%20Jacobs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses2.png') }}" alt="John Jacobs"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">John Jacobs | Luxury Italian</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹3500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=men&shape=Aviator" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses3.png') }}" alt="Aviator"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Aviators & Navigators</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1200</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=men&shape=Wayfarer" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses4.png') }}" alt="Wayfarer"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Wayfarers & Classics</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹999</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=men&shape=Round" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses1.png') }}" alt="Round Sunglasses"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Round & Hexagonal Sun</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1100</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=men" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/sunglasses.png') }}" alt="All Sunglasses"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">All Men Sunglasses</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹800</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- PANE 2: WOMEN SUNGLASSES -->
                                                <div class="lenskart-vpane" id="sun-pane-women">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>WOMEN'S</span> Sunglasses Collection</h5>
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=women" class="view-more-link">
                                                            View All Women's Sunglasses <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=women&brand=Vincent%20Chase" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses1.png') }}" alt="Vincent Chase"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Vincent Chase | Polarized</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=women&brand=John%20Jacobs" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses2.png') }}" alt="John Jacobs"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">John Jacobs | Luxury Italian</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹3500</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=women&shape=Cat-Eye" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses3.png') }}" alt="Cat-Eye"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Cat-Eye & Butterfly</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1200</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=women&shape=Round" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses4.png') }}" alt="Oversized"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Oversized & Glam</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1400</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses&gender=women" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/icon/sunglasses.png') }}" alt="All Sunglasses"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">All Women Sunglasses</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹800</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- PANE 3: TRENDING SUNGLASSES -->
                                                <div class="lenskart-vpane" id="sun-pane-trending">
                                                    <div class="lenskart-pane-header">
                                                        <h5><span>KIDS</span> Sunglasses Editions</h5>
                                                        <a href="{{ route('products') }}?category=sunglasses" class="view-more-link">
                                                            View All Kids' Sunglasses <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                    <div class="lenskart-products-grid">
                                                        <a href="{{ route('products') }}?category=sunglasses" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses1.png') }}" alt="Polarized"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Polarized Sunglasses</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1499</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses2.png') }}" alt="Power Sun"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Power Sunglasses</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1999</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses3.png') }}" alt="Harry Potter"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Harry Potter Edition</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹2499</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                        <a href="{{ route('products') }}?category=sunglasses" class="lenskart-item-card">
                                                            <div class="lenskart-item-card-left">
                                                                <div class="lenskart-item-thumb"><img src="{{ asset('website/assets/img/bg/Sunglasses4.png') }}" alt="Active Sport"></div>
                                                                <div class="lenskart-item-info">
                                                                    <span class="lenskart-item-title">Hustlr Active Sport</span>
                                                                    <span class="lenskart-item-price">Starts at <strong>₹1299</strong></span>
                                                                </div>
                                                            </div>
                                                            <i class="bi bi-chevron-right lenskart-item-chevron"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="lenskart-mega-footer">
                                            <div class="lenskart-footer-perks">
                                                <div class="lenskart-perk"><i class="bi bi-shield-check text-success"></i> Polarized & UV400 Protection</div>
                                                <div class="lenskart-perk"><i class="bi bi-sunglasses text-primary"></i> Prescription Power Available</div>
                                                <div class="lenskart-perk"><i class="bi bi-truck text-warning"></i> Free Shipping Pan India</div>
                                            </div>
                                            <a href="{{ route('products') }}?category=sunglasses" class="lenskart-footer-link">
                                                Explore All Sunglasses <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- 5. CONTACT LENSES -->
                            <li>
                                <a href="{{ route('products') }}?type=Contact%20Lens" class="desktop-item">Contact Lenses</a>
                                <input type="checkbox" id="showMegaCL">
                                <label for="showMegaCL" class="mobile-item">Contact Lenses</label>
                                <div class="mega-box">
                                    <div class="lenskart-mega-container">
                                        <div class="lenskart-cl-grid">
                                            <!-- Column 1: Brands -->
                                            <div class="lenskart-cl-col">
                                                <header>Popular Brands</header>
                                                <ul class="lenskart-cl-list">
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&brand=Aqualens"><span>Aqualens</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&brand=Bausch%20Lomb"><span>Bausch & Lomb</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&brand=Soflens"><span>Soflens</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&brand=Acuvue"><span>Acuvue</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&brand=Alcon"><span>Alcon</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&brand=Iconnect"><span>Iconnect</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                </ul>
                                            </div>

                                            <!-- Column 2: Modality -->
                                            <div class="lenskart-cl-col">
                                                <header>By Disposability</header>
                                                <ul class="lenskart-cl-list">
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&modality=Daily"><span>Daily Disposable</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&modality=Monthly"><span>Monthly Disposable</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&modality=Day%20%26%20Night"><span>Day & Night</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&modality=Yearly"><span>Yearly Disposable</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&modality=Bi-weekly"><span>Bi-Weekly</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                </ul>
                                            </div>

                                            <!-- Column 3: Power -->
                                            <div class="lenskart-cl-col">
                                                <header>By Power Type</header>
                                                <ul class="lenskart-cl-list">
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens"><span>Spherical - (CYL 0.5)</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens"><span>Spherical + (CYL 0.5)</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens"><span>Cylindrical Power (>0.75)</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens"><span>Toric Power Lenses</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                </ul>
                                            </div>

                                            <!-- Column 4: Color & Solutions -->
                                            <div class="lenskart-cl-col">
                                                <header>Color Lenses & Care</header>
                                                <ul class="lenskart-cl-list">
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&color=Green"><span><span class="lenskart-cl-color-dot" style="background:#10b981;"></span>Green</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&color=Blue"><span><span class="lenskart-cl-color-dot" style="background:#3b82f6;"></span>Blue</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&color=Brown"><span><span class="lenskart-cl-color-dot" style="background:#92400e;"></span>Hazel & Brown</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens&color=Turquoise"><span><span class="lenskart-cl-color-dot" style="background:#06b6d4;"></span>Turquoise</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                    <li><a href="{{ route('products') }}?type=Contact%20Lens"><span>Lens Cleaning Solutions</span> <i class="bi bi-chevron-right text-muted"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="lenskart-mega-footer">
                                            <div class="lenskart-footer-perks">
                                                <div class="lenskart-perk"><i class="bi bi-droplet-fill text-info"></i> High Moisture & Oxygen</div>
                                                <div class="lenskart-perk"><i class="bi bi-patch-check-fill text-success"></i> 100% Authentic Products</div>
                                                <div class="lenskart-perk"><i class="bi bi-truck text-warning"></i> Free Shipping Pan India</div>
                                            </div>
                                            <a href="{{ route('products') }}?type=Contact%20Lens" class="lenskart-footer-link">
                                                View All Contact Lenses <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- 6. HOME EYE-TEST -->
                            <li>
                                <a href="{{ route('home-eye-test') }}" class="desktop-item">Home Eye-test</a>
                                <input type="checkbox" id="showMegaHome">
                                <label for="showMegaHome" class="mobile-item">Home Eye-test</label>
                                <div class="mega-box">
                                    <div class="lenskart-mega-container">
                                        <div class="lenskart-promo-card">
                                            <div class="lenskart-promo-img">
                                                <img src="{{ asset('website/assets/img/bg/eye-test.png') }}" alt="Home Eye Test">
                                            </div>
                                            <div class="lenskart-promo-content">
                                                <h3>Get your eyes checked at home</h3>
                                                <p>A certified refractionist will visit your home with latest 12-step eye testing equipment and 100+ top trial frames to choose from.</p>
                                                <a href="{{ route('home-eye-test') }}" class="lenskart-btn-primary"><i class="bi bi-calendar2-check"></i> Book Appointment at ₹99</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- 7. STORE LOCATOR -->
                            <li>
                                <a href="#" class="desktop-item">Store Locator</a>
                                <input type="checkbox" id="showMegaStore">
                                <label for="showMegaStore" class="mobile-item">Store Locator</label>
                                <div class="mega-box">
                                    <div class="lenskart-mega-container">
                                        <div class="lenskart-promo-card">
                                            <div class="lenskart-promo-content" style="width: 50%;">
                                                <h3>Over 1800+ Speckarts Stores</h3>
                                                <p>Experience eyewear in a whole new way: Visit your nearest store and treat yourself to 5000+ eyewear styles and free eye testing.</p>
                                                <a href="#" class="lenskart-btn-primary"><i class="bi bi-geo-alt-fill"></i> Locate Nearest Store</a>
                                            </div>
                                            <div style="width: 50%;">
                                                <div class="lenskart-cities-grid">
                                                    <a href="#" class="lenskart-city-item">
                                                        <img src="{{ asset('website/assets/img/icon/Andra-Pradesh-W.png') }}" alt="Delhi">
                                                        <span>Delhi</span>
                                                    </a>
                                                    <a href="#" class="lenskart-city-item">
                                                        <img src="{{ asset('website/assets/img/icon/Gujarat-W-1.png') }}" alt="Bangalore">
                                                        <span>Bangalore</span>
                                                    </a>
                                                    <a href="#" class="lenskart-city-item">
                                                        <img src="{{ asset('website/assets/img/icon/Maharashtra-W.png') }}" alt="Mumbai">
                                                        <span>Mumbai</span>
                                                    </a>
                                                    <a href="#" class="lenskart-city-item">
                                                        <img src="{{ asset('website/assets/img/icon/Gujarat-W.png') }}" alt="Ahmedabad">
                                                        <span>Ahmedabad</span>
                                                    </a>
                                                    <a href="#" class="lenskart-city-item">
                                                        <img src="{{ asset('website/assets/img/icon/Lakshadweep-W.png') }}" alt="Chennai">
                                                        <span>Chennai</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                        </ul>
                        <label for="menu-btn" class="btn menu-btn"><i class="fas fa-bars"></i></label>
                    </div>
                </div>
            </div>
        </nav>
    </section>

    <!-- =========================================================
         PREMIUM MOBILE & TABLET DRAWER (OFFCANVAS)
    ========================================================= -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileSidebar">
        
        <!-- Drawer Sticky Header -->
        <div class="offcanvas-header mobile-drawer-header">
            <a href="/" class="mobile-drawer-logo">
                <img src="{{ asset('website/assets/img/logo/Specskart-logo-png.png') }}" alt="Speckarts Logo">
            </a>
            <button type="button" class="mobile-drawer-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Drawer Body Scrollable Container -->
        <div class="offcanvas-body mobile-drawer-body">
            
            <!-- 1. USER ACCOUNT HERO / GUEST CARD -->
            @auth
                <div class="mobile-user-card">
                    <div class="mobile-user-top">
                        <img src="{{ Auth::user()->image ? Auth::user()->profile_image_url : asset('website/assets/img/icon/user.png') }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="mobile-user-avatar"
                             onerror="this.onerror=null;this.src='{{ asset('website/assets/img/icon/user.png') }}';">
                        <div class="mobile-user-info">
                            <div class="mobile-user-name">Hello, {{ Auth::user()->name }}</div>
                            <div class="mobile-user-meta">{{ Auth::user()->phone ?? (Auth::user()->mobile ?? (Auth::user()->email ?? 'Speckarts Member')) }}</div>
                            <span class="mobile-user-badge"><i class="bi bi-patch-check-fill"></i> Gold Member</span>
                        </div>
                    </div>

                    <!-- 4 Quick User Buttons -->
                    <div class="mobile-user-quick-links">
                        <a href="{{ route('profile') }}" class="mobile-user-quick-btn">
                            <i class="bi bi-person-fill"></i>
                            <span>Profile</span>
                        </a>
                        <a href="{{ route('my-orders') }}" class="mobile-user-quick-btn">
                            <i class="bi bi-box-seam-fill"></i>
                            <span>Orders</span>
                        </a>
                        <a href="{{ route('my-addresses') }}" class="mobile-user-quick-btn">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>Addresses</span>
                        </a>
                        <a href="{{ route('my-prescriptions') }}" class="mobile-user-quick-btn">
                            <i class="bi bi-file-earmark-medical-fill"></i>
                            <span>Powers</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="mobile-guest-card">
                    <h5>Welcome to Speckarts</h5>
                    <p>Sign in to view saved frames, orders & prescriptions</p>
                    <a href="{{ route('login.web') }}" class="btn-mobile-login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Sign In / Register</span>
                    </a>
                </div>
            @endauth

            <!-- 2. QUICK ACCESS 2x2 PILLS -->
            <div class="mobile-quick-grid">
                <a href="{{ route('cart') }}" class="mobile-quick-pill">
                    <div class="icon-box" style="background:#fef2f2; color:#ef4444;">
                        <i class="bi bi-bag-check-fill"></i>
                    </div>
                    <span class="pill-label">My Cart</span>
                    <span class="badge-pill {{ $initialCartCount > 0 ? '' : 'd-none' }}">{{ $initialCartCount }}</span>
                </a>

                <a href="{{ route('wishlist') }}" class="mobile-quick-pill">
                    <div class="icon-box" style="background:#fdf2f8; color:#ec4899;">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <span class="pill-label">Wishlist</span>
                    <span class="badge-pill {{ $initialWishlistCount > 0 ? '' : 'd-none' }}">{{ $initialWishlistCount }}</span>
                </a>

                <a href="{{ route('home-eye-test') }}" class="mobile-quick-pill">
                    <div class="icon-box" style="background:#ecfdf5; color:#059669;">
                        <i class="bi bi-house-heart-fill"></i>
                    </div>
                    <span class="pill-label">Eye-Test</span>
                    <span class="badge-accent">₹99</span>
                </a>

                <a href="#" class="mobile-quick-pill">
                    <div class="icon-box" style="background:#eff6ff; color:#3b82f6;">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <span class="pill-label">Stores</span>
                </a>
            </div>

            <!-- 3. FEATURED HOME EYE-TEST BANNER -->
            <a href="{{ route('home-eye-test') }}" class="mobile-promo-banner">
                <div class="mobile-promo-text">
                    <h6>Get Eyes Tested At Home</h6>
                    <p>Certified optometrist with 100+ frames</p>
                </div>
                <span class="mobile-promo-badge">Book @ ₹99</span>
            </a>

            <!-- 4. CATEGORIES ACCORDION -->
            <div class="mobile-nav-section">
                <div class="mobile-section-label">Explore Eyewear</div>

                <!-- Eyeglasses -->
                <div class="mobile-nav-group">
                    <div class="mobile-nav-heading">
                        <div class="mobile-nav-heading-left">
                            <i class="bi bi-eyeglasses"></i>
                            <span>Eyeglasses</span>
                        </div>
                        <i class="bi bi-chevron-down mobile-nav-chevron"></i>
                    </div>
                    <ul class="mobile-sub-nav">
                        <li><a href="{{ route('products') }}?category=eyeglasses">All Eyeglasses <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=eyeglasses&gender=men">Men's Eyeglasses <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=eyeglasses&gender=women">Women's Eyeglasses <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=kids">Kids' Eyeglasses <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=eyeglasses&frame_type=Rimless">Rimless Frames <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=eyeglasses&frame_type=Half%20Rim">Half Rim Frames <i class="bi bi-arrow-right-short"></i></a></li>
                    </ul>
                </div>

                <!-- Screen Glasses -->
                <div class="mobile-nav-group">
                    <div class="mobile-nav-heading">
                        <div class="mobile-nav-heading-left">
                            <i class="bi bi-laptop"></i>
                            <span>Screen Glasses</span>
                        </div>
                        <i class="bi bi-chevron-down mobile-nav-chevron"></i>
                    </div>
                    <ul class="mobile-sub-nav">
                        <li><a href="{{ route('products') }}?category=computer-glasses">All Screen Glasses <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=computer-glasses&gender=men">Men's Screen Specs <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=computer-glasses&gender=women">Women's Screen Specs <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=computer-glasses&gender=kids">Kids' Blue Light Specs <i class="bi bi-arrow-right-short"></i></a></li>
                    </ul>
                </div>

                <!-- Sunglasses -->
                <div class="mobile-nav-group">
                    <div class="mobile-nav-heading">
                        <div class="mobile-nav-heading-left">
                            <i class="bi bi-sun"></i>
                            <span>Sunglasses</span>
                        </div>
                        <i class="bi bi-chevron-down mobile-nav-chevron"></i>
                    </div>
                    <ul class="mobile-sub-nav">
                        <li><a href="{{ route('products') }}?category=sunglasses">All Sunglasses <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=sunglasses&gender=men">Men's Sunglasses <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=sunglasses&gender=women">Women's Sunglasses <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=sunglasses&shape=Aviator">Aviators <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?category=sunglasses&shape=Wayfarer">Wayfarers <i class="bi bi-arrow-right-short"></i></a></li>
                    </ul>
                </div>

                <!-- Contact Lenses -->
                <div class="mobile-nav-group">
                    <div class="mobile-nav-heading">
                        <div class="mobile-nav-heading-left">
                            <i class="bi bi-eye"></i>
                            <span>Contact Lenses</span>
                        </div>
                        <i class="bi bi-chevron-down mobile-nav-chevron"></i>
                    </div>
                    <ul class="mobile-sub-nav">
                        <li><a href="{{ route('products') }}?type=Contact%20Lens">All Contact Lenses <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?type=Contact%20Lens&modality=Daily">Daily Disposable <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?type=Contact%20Lens&modality=Monthly">Monthly Disposable <i class="bi bi-arrow-right-short"></i></a></li>
                        <li><a href="{{ route('products') }}?type=Contact%20Lens&color=Green">Color Lenses <i class="bi bi-arrow-right-short"></i></a></li>
                    </ul>
                </div>

                <!-- Home Eye-Test Direct Link -->
                <div class="mobile-nav-group">
                    <a href="{{ route('home-eye-test') }}" class="mobile-nav-heading">
                        <div class="mobile-nav-heading-left">
                            <i class="bi bi-calendar2-check"></i>
                            <span>Book Home Eye-Test</span>
                        </div>
                        <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:800; font-size:10px; padding:3px 7px; border-radius:8px;">₹99</span>
                    </a>
                </div>

                <!-- Store Locator Direct Link -->
                <div class="mobile-nav-group">
                    <a href="#" class="mobile-nav-heading">
                        <div class="mobile-nav-heading-left">
                            <i class="bi bi-shop"></i>
                            <span>Store Locator</span>
                        </div>
                        <i class="bi bi-chevron-right mobile-nav-chevron" style="font-size:11px;"></i>
                    </a>
                </div>

            </div>

            <!-- 5. DRAWER FOOTER & LOGOUT -->
            <div class="mobile-drawer-footer">
                <div class="mobile-support-row">
                    <span>Need Help?</span>
                    <a href="https://wa.me/919999999999" class="mobile-support-link" target="_blank">
                        <i class="bi bi-whatsapp"></i> WhatsApp Support
                    </a>
                </div>
                
                @auth
                    <form method="POST" action="{{ route('logout.web') }}" style="margin:0; width:100%;">
                        @csrf
                        <button type="submit" class="mobile-logout-btn">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout from Account</span>
                        </button>
                    </form>
                @endauth
            </div>

        </div>
    </div>

    <!-- Mega Menu & Mobile Drawer Script -->
    <script>
        $(document).ready(function() {
            // Mobile Drawer Category Accordion Toggle
            $(document).on('click', '.mobile-nav-heading', function(e) {
                var $group = $(this).closest('.mobile-nav-group');
                var $subNav = $group.find('.mobile-sub-nav');
                
                if ($subNav.length > 0) {
                    e.preventDefault();
                    if ($group.hasClass('open')) {
                        $group.removeClass('open');
                        $subNav.slideUp(200);
                    } else {
                        $('.mobile-nav-group').removeClass('open').find('.mobile-sub-nav').slideUp(200);
                        $group.addClass('open');
                        $subNav.slideDown(200);
                    }
                }
            });

            // Instant hover/click vertical tab switching inside mega menu
            $('.lenskart-vtab-item').on('mouseenter click', function(e) {
                var $this = $(this);
                var targetPaneId = $this.data('vtarget');
                var $container = $this.closest('.lenskart-mega-container');

                // Update active tab on left
                $this.siblings('.lenskart-vtab-item').removeClass('active');
                $this.addClass('active');

                // Switch matching pane on right
                $container.find('.lenskart-vpane').removeClass('active');
                $container.find(targetPaneId).addClass('active');
            });

            // Search dropdown
            $('#searchInput').on('focus', function() {
                $('#searchDropdown').fadeIn(200);
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-box').length) {
                    $('#searchDropdown').fadeOut(200);
                }
            });

            // Loyalty Points: toggle dropdown
            $('#loyaltyToggle').on('click', function(e) {
                e.preventDefault();
                $('#loyaltyDropdown').toggleClass('show');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.loyalty-link, .loyalty-dropdown').length) {
                    $('#loyaltyDropdown').removeClass('show');
                }
            });

            // Smooth User Profile Dropdown hover management (prevents accidental closing)
            var userMenuTimer;
            $('.user-profile-nav-item').on('mouseenter', function() {
                clearTimeout(userMenuTimer);
                $(this).addClass('show-dropdown');
            }).on('mouseleave', function() {
                var $item = $(this);
                userMenuTimer = setTimeout(function() {
                    $item.removeClass('show-dropdown');
                }, 220);
            });
        });

        document.querySelectorAll('.dropdown-toggle').forEach(item => {
            item.addEventListener('click', function(e) {
                if (this.closest('.user-profile-nav-item')) {
                    return; // allow CSS hover and timer to handle desktop user profile
                }
                e.preventDefault();
                this.parentElement.classList.toggle('active');
            });
        });

        document.querySelectorAll('.has-sub-dropdown > a').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentElement.classList.toggle('active');
            });
        });
    </script>
    <!-- end navbar -->
