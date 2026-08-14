@extends('website.layout.master')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>

        :root {
            --ink: #0d2a2b;
            --ink-soft: #4d6b6c;
            --paper: #f4faf9;
            --card: #ffffff;
            --navy: #07484A;
            --navy-deep: #042e2f;
            --amber: #00b9b9;
            --amber-soft: #faf59e;
            --line: #dcebea;
        }

        .profile-section {
            padding: 40px 0 60px;
            background: var(--paper);
            background-image:
                radial-gradient(circle at 8% 12%, rgba(7, 72, 74, .05), transparent 40%),
                radial-gradient(circle at 95% 85%, rgba(0, 185, 185, .08), transparent 45%);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* ---------- LEFT: identity card ---------- */

        .profile-section-left1 {
            background: var(--navy);
            background-image:
                linear-gradient(155deg, var(--navy) 0%, var(--navy-deep) 100%);
            border-radius: 22px;
            padding: 40px 26px 32px;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 18px 40px -12px rgba(4, 46, 47, .45);
        }

        /* lens-inspired concentric rings, subtle, top-right */
        .profile-section-left1::before {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            border: 1px solid rgba(0, 185, 185, .22);
            top: -120px;
            right: -100px;
        }

        .profile-section-left1::after {
            content: '';
            position: absolute;
            width: 170px;
            height: 170px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .1);
            bottom: -90px;
            left: -70px;
        }

        .profile-section-left1 h4 {
            margin-top: 22px;
            margin-bottom: 6px;
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -.01em;
        }

        .profile-section-left1 p {
            color: var(--amber-soft);
            margin-bottom: 0;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: .03em;
        }

        .avatar-upload {
            position: relative;
            max-width: 152px;
            margin: auto;
            z-index: 1;
        }

        /* avatar ring styled like a lens edge: two-tone rim */
        .avatar-preview {
            width: 144px;
            height: 144px;
            margin: auto;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid var(--navy);
            outline: 3px solid var(--amber);
            outline-offset: 3px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, .35);
            transition: transform .35s ease, outline-color .35s ease;
        }

        .avatar-preview:hover {
            transform: scale(1.04);
            outline-color: #fff;
        }

        #imagePreview {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
        }

        .avatar-edit {
            position: absolute;
            right: -2px;
            bottom: 6px;
            z-index: 10;
        }

        .avatar-edit input {
            display: none;
        }

        .avatar-edit label {
            width: 38px;
            height: 38px;
            background: var(--amber);
            border: 3px solid var(--navy-deep);
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 14px rgba(0, 0, 0, .3);
            transition: transform .25s ease, background .25s ease;
        }

        .avatar-edit label:hover {
            transform: scale(1.12) rotate(-6deg);
            background: var(--amber-soft);
        }

        .avatar-edit label:after {
            content: "\f040";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: var(--navy-deep);
            font-size: 14px;
        }

        /* ---------- RIGHT: account card ---------- */

        .card.border-0.shadow-sm.rounded-4 {
            border-radius: 22px !important;
            box-shadow: 0 12px 32px -14px rgba(7, 72, 74, .2) !important;
            border: 1px solid var(--line) !important;
        }

        .card-header.bg-white {
            background: var(--card) !important;
            border-bottom: 1px solid var(--line) !important;
            border-radius: 22px 22px 0 0 !important;
        }

        .card-header h5 {
            color: var(--navy) !important;
            font-weight: 700 !important;
            letter-spacing: -.01em;
        }

        .card-header small.text-muted {
            color: var(--ink-soft) !important;
        }

        .card-header .btn-light.border {
            background: var(--paper);
            border-color: var(--line) !important;
            color: var(--navy);
            border-radius: 12px;
            font-weight: 500;
            transition: .25s;
        }

        .card-header .btn-light.border:hover {
            background: var(--navy);
            color: #fff;
            border-color: var(--navy);
        }

        .card-header .btn-outline-danger {
            border-radius: 12px;
            font-weight: 500;
            border-width: 1.5px;
            transition: .25s;
        }

        .card-header .btn-outline-danger:hover {
            background: #c0392b;
            border-color: #c0392b;
        }

        /* ---------- menu grid ---------- */

        .profile-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .profile-menu li {
            list-style: none;
        }

        .profile-menu a {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 128px;
            padding: 20px 14px;
            text-decoration: none;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            color: var(--ink);
            font-size: 14px;
            font-weight: 600;
            transition: all .3s cubic-bezier(.2, .8, .2, 1);
            box-shadow: 0 2px 10px rgba(7, 72, 74, .06);
        }

        .profile-menu a:hover {
            transform: translateY(-6px);
            border-color: var(--amber);
            box-shadow: 0 16px 30px -10px rgba(0, 185, 185, .35);
        }

        .profile-menu a i {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 20px;
            color: var(--navy);
            background: linear-gradient(135deg, var(--amber-soft), #fff);
            border: 1px solid rgba(7, 72, 74, .1);
            margin-bottom: 12px;
            transition: .3s;
        }

        .profile-menu a:hover i {
            transform: scale(1.08);
            background: var(--amber);
            color: #fff;
        }

        .profile-menu a span {
            text-align: center;
            line-height: 1.35;
        }

        .profile-menu a.active {
            background: linear-gradient(150deg, var(--navy), var(--navy-deep));
            color: #fff;
            border: none;
            box-shadow: 0 16px 30px -10px rgba(7, 72, 74, .5);
        }

        .profile-menu a.active i {
            background: rgba(255, 255, 255, .14);
            color: var(--amber-soft);
            border-color: rgba(255, 255, 255, .2);
        }

        .profile-menu li {
            animation: fadeUp .5s ease forwards;
            opacity: 0;
        }

        .profile-menu li:nth-child(1) { animation-delay: .05s; }
        .profile-menu li:nth-child(2) { animation-delay: .1s; }
        .profile-menu li:nth-child(3) { animation-delay: .15s; }
        .profile-menu li:nth-child(4) { animation-delay: .2s; }
        .profile-menu li:nth-child(5) { animation-delay: .25s; }
        .profile-menu li:nth-child(6) { animation-delay: .3s; }
        .profile-menu li:nth-child(7) { animation-delay: .35s; }
        .profile-menu li:nth-child(8) { animation-delay: .4s; }
        .profile-menu li:nth-child(9) { animation-delay: .45s; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 991px) {
            .profile-section-left1 { margin-bottom: 25px; }
            .profile-menu { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 576px) {
            .profile-menu { grid-template-columns: 1fr 1fr; gap: 12px; }
            .profile-menu a { min-height: 104px; padding: 16px 10px; }
            .avatar-preview { width: 116px; height: 116px; }
        }
    </style>
    @php
        // Fetch active gift vouchers for this user from session + offers table
        $profileVoucher = session()->get('applied_voucher', null);

        // Also get available manual-code vouchers from offers table
        $now = \Carbon\Carbon::now()->toDateString();
        $profileAvailableVouchers = DB::table('offers')
            ->where('offer_type', 'gift_voucher')
            ->whereNotNull('coupon_code')
            ->where('coupon_code', '!=', '')
            ->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->get();
    @endphp
    <section class="profile-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4">
                    <div class="profile-section-left w-100">
                        <div class="profile-section-left1">
                            <div class="avatar-upload">
                                <div class="avatar-edit">
                                    <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg, .webp" />
                                    <label for="imageUpload"></label>
                                </div>
                                <div class="avatar-preview">
                                    <div id="imagePreview"
                                        style="background-image: url('{{ $user->profile_image_url }}');">
                                    </div>
                                </div>
                            </div>
                            @php
                                $profilePts = 0;
                                if (session()->has('test_loyalty_points')) {
                                    $profilePts = (int) session()->get('test_loyalty_points');
                                } elseif ($user) {
                                    $custRec = DB::table('tbl_customer')
                                        ->where('customer_id', $user->id)
                                        ->orWhere('contact_no', $user->contact_no ?? ($user->mobile ?? ''))
                                        ->orWhere('email_id', $user->email ?? '')
                                        ->first();
                                    if ($custRec && !empty($custRec->Loyalty_Points_Bal)) {
                                        $profilePts = (int) $custRec->Loyalty_Points_Bal;
                                    }
                                }
                                if ($profilePts <= 0 && !session()->has('no_test_points')) {
                                    $profilePts = 500;
                                }
                            @endphp
                            <h4>{{ $user->name ?: 'Customer' }}</h4>
                            <p class="mb-2">{{ $user->phone ?? ($user->mobile ?? ($user->email ?? '')) }}</p>
                            <span class="badge px-3 py-2 fw-bold rounded-pill" style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a; font-size: 13px;">
                                <i class="fas fa-coins me-1"></i> {{ number_format($profilePts) }} Loyalty Points
                            </span>

                            @php
                                $profileMembership = null;
                                if ($user) {
                                    $custMem = DB::table('tbl_customer')
                                        ->where('customer_id', $user->id)
                                        ->orWhere('contact_no', $user->contact_no ?? ($user->phone ?? ''))
                                        ->orWhere('email_id', $user->email ?? '')
                                        ->first();
                                    if ($custMem && !empty($custMem->membership_card_id) && !empty($custMem->membership_expiry) && \Carbon\Carbon::parse($custMem->membership_expiry)->isFuture()) {
                                        $dbCard = DB::table('tbl_membership_card')->where('card_id', $custMem->membership_card_id)->first();
                                        if ($dbCard) {
                                            $profileMembership = [
                                                'name' => $dbCard->card_name,
                                                'expiry' => \Carbon\Carbon::parse($custMem->membership_expiry)->format('d M Y')
                                            ];
                                        }
                                    }
                                }
                                if (!$profileMembership && session()->has('active_membership')) {
                                    $sessM = session()->get('active_membership');
                                    if (\Carbon\Carbon::parse($sessM['expiry'])->isFuture()) {
                                        $profileMembership = [
                                            'name' => $sessM['card_name'] ?? 'Gold Member',
                                            'expiry' => \Carbon\Carbon::parse($sessM['expiry'])->format('d M Y')
                                        ];
                                    }
                                }
                            @endphp

                            @if($profileMembership)
                                <div class="mt-2">
                                    <span class="badge px-3 py-1 fw-bold rounded-pill" style="background: linear-gradient(135deg, #00B9B9, #07484A); color: #fff; border: 1px solid #bceae8; font-size: 11.5px;">
                                        👑 {{ $profileMembership['name'] }} (Exp: {{ $profileMembership['expiry'] }})
                                    </span>
                                </div>
                            @else
                                <div class="mt-2">
                                    <a href="{{ route('website.membership') }}" class="badge px-3 py-1 fw-bold rounded-pill text-decoration-none" style="background: rgba(255,255,255,0.15); color: #fff; font-size: 11px; border: 1px solid rgba(255,255,255,0.3);">
                                        👑 Join VIP Club ➔
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div
                            class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 px-4">

                            <div>
                                <h5 class="mb-0 fw-semibold text-dark">My Account</h5>
                                <small class="text-muted">Manage your profile and account settings</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-light border px-3" onclick="window.location.href='{{ url()->previous() }}'">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    <span class="d-md-inline-flex d-none">Back</span>
                                </button>

                                <a href="{{ route('logout') }}" class="btn btn-outline-danger px-3"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-1"></i>
                                    <span class="d-md-inline-flex d-none">Log Out</span>
                                </a>
                            </div>
                            <form id="logout-form"
                                action="{{ route('logout') }}"
                                method="POST"
                                class="d-none">
                                @csrf
                            </form>

                        </div>
                        <div class="card-body">
                            <ul class="profile-menu">
                                <li>
                                    <a href="{{ route('my-orders') }}">
                                        <i class="fas fa-shopping-bag"></i>
                                        <span>My Orders</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('account-info') }}">
                                        <i class="fas fa-user"></i>
                                        <span>Account Information</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('my-addresses') }}">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Address Book</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('my-prescriptions') }}">
                                        <i class="fas fa-file-medical"></i>
                                        <span>My Prescriptions</span>
                                    </a>
                                </li>

                                <!--<li>-->
                                <!--    <a href="#">-->
                                <!--        <i class="fas fa-credit-card"></i>-->
                                <!--        <span>Saved Cards</span>-->
                                <!--    </a>-->
                                <!--</li>-->

                                <li>
                                    @php
                                        $sessionVoucher = session()->get('applied_voucher', null);
                                        $voucherBadgeAmt = 0;
                                        if ($sessionVoucher) {
                                            $voucherBadgeAmt = $sessionVoucher['amount_applied'] ?? ($sessionVoucher['voucher_value'] ?? 0);
                                        } elseif($profileAvailableVouchers->count() > 0) {
                                            $voucherBadgeAmt = $profileAvailableVouchers->sum('voucher_value');
                                        }
                                    @endphp
                                    <a href="{{ route('cart') }}" style="position:relative;">
                                        <i class="fas fa-ticket-alt" style="color:#6b4bcf;"></i>
                                        <span>Voucher Balance</span>
                                        @if($sessionVoucher)
                                            <small class="d-block fw-bold" style="font-size:11px;color:#6b4bcf;">Applied: ₹{{ number_format($sessionVoucher['amount_applied'] ?? 0) }}</small>
                                        @elseif($profileAvailableVouchers->count() > 0)
                                            <small class="d-block fw-bold" style="font-size:11px;color:#6b4bcf;">{{ $profileAvailableVouchers->count() }} voucher{{ $profileAvailableVouchers->count() > 1 ? 's' : '' }} available</small>
                                        @else
                                            <small class="d-block text-muted" style="font-size:11px;">No active vouchers</small>
                                        @endif
                                    </a>
                                </li>

                                <!--<li>-->
                                <!--    <a href="#">-->
                                <!--        <i class="fas fa-wallet"></i>-->
                                <!--        <span>Store Credit</span>-->
                                <!--    </a>-->
                                <!--</li>-->

                                <li>
                                    <a href="{{ route('cart') }}">
                                        <i class="fas fa-coins text-warning" style="color: #f59e0b !important;"></i>
                                        <span>My Loyalty Points</span>
                                        <small class="d-block text-success fw-bold" style="font-size: 11px;">{{ number_format($profilePts) }} pts</small>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- ── My Gift Vouchers Detail Card ────────────── -->
                    @php
                        $anyVoucher = $sessionVoucher ?? null;
                        $hasManualVouchers = $profileAvailableVouchers->count() > 0;
                    @endphp
                    @if($anyVoucher || $hasManualVouchers)
                    <div class="card border-0 shadow-sm rounded-4 mt-4" style="overflow:hidden;">
                        <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between"
                             style="background:linear-gradient(135deg,#f3f0fd,#e9e3fb);border-bottom:1.5px solid #c8b7f0;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:36px;height:36px;background:#6b4bcf;color:#fff;font-size:16px;">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold" style="color:#4c28a8;">My Gift Vouchers</h6>
                                    <small class="text-muted" style="font-size:11px;">Active vouchers linked to your account</small>
                                </div>
                            </div>
                            <a href="{{ route('cart') }}" class="btn btn-sm fw-bold px-3"
                               style="background:#6b4bcf;color:#fff;border-radius:8px;font-size:12px;">
                                <i class="fas fa-cart-shopping me-1"></i> Go to Cart
                            </a>
                        </div>
                        <div class="card-body px-4 py-3">

                            {{-- Applied Voucher (from session) --}}
                            @if($anyVoucher)
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3"
                                     style="background:#eee9fd;border:1.5px solid #c8b7f0;">
                                    <div>
                                        <div class="fw-bold" style="color:#4c28a8;font-size:14px;">
                                            <i class="bi bi-check-circle-fill me-1" style="color:#6b4bcf;"></i>
                                            {{ $anyVoucher['code'] }}
                                            <span class="badge ms-2" style="background:#6b4bcf;color:#fff;font-size:10px;border-radius:5px;">APPLIED</span>
                                        </div>
                                        <div class="text-muted mt-1" style="font-size:12px;">
                                            ₹{{ number_format($anyVoucher['amount_applied'] ?? 0, 2) }} discount applied on current cart
                                            @if(($anyVoucher['remaining_balance'] ?? 0) > 0)
                                                · <span style="color:#6b4bcf;">₹{{ number_format($anyVoucher['remaining_balance'], 2) }} balance remaining</span>
                                            @endif
                                        </div>
                                    </div>
                                    <i class="fas fa-gift" style="font-size:24px;color:#c8b7f0;"></i>
                                </div>
                            @endif

                            {{-- Available Manual Vouchers --}}
                            @if($hasManualVouchers)
                                <div class="d-flex flex-column gap-2">
                                    @foreach($profileAvailableVouchers as $pv)
                                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3"
                                             style="background:#f8f6ff;border:1.5px solid #e3dbf7;">
                                            <div>
                                                <div class="fw-bold" style="color:#4c28a8;font-size:13.5px;">
                                                    <i class="bi bi-ticket-fill me-1"></i>
                                                    {{ strtoupper($pv->coupon_code) }}
                                                </div>
                                                <div class="text-muted mt-1" style="font-size:11.5px;">
                                                    Worth <strong style="color:#6b4bcf;">₹{{ number_format($pv->voucher_value, 2) }}</strong>
                                                    @if(!empty($pv->end_date))
                                                        · Expires {{ \Carbon\Carbon::parse($pv->end_date)->format('d M Y') }}
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="badge px-2 py-1" style="background:#f0ebff;color:#6b4bcf;border:1px solid #c8b7f0;font-size:10px;border-radius:6px;">
                                                ACTIVE
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    </div>
                    @else
                    <div class="card border-0 shadow-sm rounded-4 mt-4">
                        <div class="card-body py-4 px-4 text-center text-muted">
                            <i class="fas fa-ticket-alt mb-2 d-block" style="font-size:28px;color:#c8b7f0;"></i>
                            <div style="font-size:13px;">No active gift vouchers yet.</div>
                            <small>Complete an order to unlock your first Gift Voucher!</small>
                        </div>
                    </div>
                    @endif
                    <!-- ── End Gift Vouchers ── -->

                </div>
            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview').hide().fadeIn(650);
                }
                reader.readAsDataURL(file);

                var formData = new FormData();
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('profile.image.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.image_url) {
                                $('#imagePreview').css('background-image', 'url("' + response.image_url + '")');
                            }
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Profile picture updated successfully!');
                            }
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'Upload failed.');
                            }
                        }
                    },
                    error: function(xhr) {
                        var errMsg = 'Upload failed. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var firstErrKey = Object.keys(xhr.responseJSON.errors)[0];
                            if (firstErrKey && xhr.responseJSON.errors[firstErrKey][0]) {
                                errMsg = xhr.responseJSON.errors[firstErrKey][0];
                            }
                        }
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errMsg);
                        } else {
                            alert(errMsg);
                        }
                    }
                });
            }
        }

        $(document).ready(function() {
            $(document).on('change', '#imageUpload', function() {
                alert("hi");
                readURL(this);
            });
        });
    </script>
@endsection
