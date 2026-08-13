@extends('web.layout.master')
@section('content')
    <style>
        /* ── Layout ── */
        .ps-wrap {
            /* display: grid; */
            /* grid-template-columns: 260px 1fr; */
            gap: 16px;
            align-items: start;
        }

        /* ── Sidebar ── */
        .ps-left {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 20px rgba(7, 72, 74, .09);
            position: sticky;
            top: 80px;
        }

        /* Cover banner */
        .ps-cover {
            height: 80px;
            background: linear-gradient(135deg, #07484A 0%, #00B9B9 100%);
            position: relative;
        }

        .ps-cover-pattern {
            position: absolute;
            inset: 0;
            opacity: .12;
            background-image: radial-gradient(circle, #FAF59E 1px, transparent 1px);
            background-size: 18px 18px;
        }

        /* Avatar */
        .ps-avatar-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 1.25rem 1.25rem;
            margin-top: -38px;
        }

        .avatar-ring {
            position: relative;
            width: 150px;
            height: 150px;
        }

        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid #fff;
            background-size: cover;
            background-position: center;
            background-color: #e8f5f5;
            box-shadow: 0 4px 14px rgba(7, 72, 74, .18);
        }

        .avatar-edit-btn {
            position: absolute;
            bottom: 1px;
            right: 1px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #00B9B9;
            border: 2.5px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 9px;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 185, 185, .45);
        }

        .ps-name {
            font-size: 18px;
            font-weight: 800;
            color: #07484A;
            margin: 12px 0 3px;
            text-align: center;
        }

        .ps-sub {
            font-size: 14px;
            color: #000000;
            margin: 0 0 10px;
            text-align: center;
        }

        .ps-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 999px;
            background: #FAF59E;
            color: #07484A;
        }

        /* Stats */
        .ps-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            background: #f0f0f0;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
        }

        .ps-stat {
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 6px;
            gap: 2px;
        }

        .ps-stat-val {
            font-size: 15px;
            font-weight: 700;
            color: #07484A;
        }

        .ps-stat-lbl {
            font-size: 12px;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        /* Nav */
        .ps-nav {
            padding: 8px 0;
        }

        .ps-nav-section {
            font-size: 9.5px;
            color: #bbb;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 600;
            padding: 10px 1.25rem 4px;
            margin: 0;
        }

        .ps-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 1.25rem;
            font-size: 14px;
            color: #000000;
            text-decoration: none;
            transition: all .15s;
            border-left: 3px solid transparent;
        }

        .ps-nav-item:hover {
            background: #f0fafa;
            color: #07484A;
            border-left-color: #00B9B9;
            text-decoration: none;
        }

        .ps-nav-item.active {
            background: #f0fafa;
            color: #07484A;
            font-weight: 600;
            border-left-color: #07484A;
        }

        .ps-nav-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            transition: all .15s;
            color: #000000;
        }

        .ps-nav-item.active .ps-nav-icon {
            background: linear-gradient(135deg, #07484A, #00B9B9);
            color: #fff;
        }

        .ps-nav-item:hover .ps-nav-icon {
            background: #d6f5f5;
            color: #07484A;
        }

        .nav-label {
            flex: 1;
        }

        .nav-arrow {
            font-size: 11px;
            color: #ddd;
            transition: all .15s;
        }

        .ps-nav-item:hover .nav-arrow,
        .ps-nav-item.active .nav-arrow {
            color: #00B9B9;
            transform: translateX(2px);
        }

        .ps-nav-divider {
            border: none;
            border-top: 1px solid #f5f5f5;
            margin: 4px 0;
        }

        /* Logout */
        .ps-logout {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 1.25rem;
            font-size: 13px;
            color: #c0392b;
            text-decoration: none;
            border-top: 1px solid #f5f5f5;
            transition: background .15s;
            margin-top: 4px;
        }

        .ps-logout:hover {
            background: #fdf0ef;
            color: #c0392b;
        }

        .ps-logout-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: #fdf0ef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: #c0392b;
        }

        /* ── Right panel ── */
        .ps-right {
            background: #fff;
            border-radius: 16px;
            min-height: 480px;
            box-shadow: 0 2px 20px rgba(7, 72, 74, .07);
            overflow: hidden;
        }

        .ps-right-header {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ps-right-header-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #07484A, #00B9B9);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #fff;
            flex-shrink: 0;
        }

        .ps-right-title {
            font-size: 16px;
            font-weight: 600;
            color: #07484A;
            margin: 0;
        }

        .ps-right-sub {
            font-size: 12px;
            color: #aaa;
            margin: 0;
        }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .ps-wrap {
                grid-template-columns: 1fr;
            }

            .ps-left {
                position: static;
            }
        }

        .ps-menu-header {
            font-size: 15px;
            color: #bbb;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 600;
            padding: 4px 2px 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ps-menu-header::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f0f0f0;
        }

        .ps-menu-header {
            font-size: 14px;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 600;
            padding: 10px 0 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ps-menu-header::before {
            content: '';
            flex: 1;
            height: 1px;
            background: #f0f0f0;
        }

        .ps-menu-header::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f0f0f0;
        }
    </style>


    <section class="profile-section py-4">
        <div class="container">
            <div class="ps-wrap">

                {{-- ── LEFT SIDEBAR ── --}}
                <div class="ps-left">
                    <div class="ps-cover">
                        <div class="ps-cover-pattern"></div>
                    </div>

                    <div class="ps-avatar-wrap">
                        <div class="avatar-ring">
                            <div class="avatar-preview" id="imagePreview"
                                style="background-image:url('{{ $user->image ? asset('uploads/profile/' . $user->image) : asset('assets/img/bg/profile.png') }}')">
                            </div>
                            <label for="imageUpload" class="avatar-edit-btn" title="Change photo">
                                <i class="bi bi-pencil-fill"></i>
                            </label>
                            <input type="file" id="imageUpload" accept=".png,.jpg,.jpeg" class="d-none">
                        </div>
                        <p class="ps-name">{{ $user->name ?: 'Customer' }}</p>
                        <p class="ps-sub">{{ $user->email ?: $user->mobile ?: '' }}</p>
                        <span class="ps-badge"><i class="bi bi-star-fill"></i> Premium Member</span>
                    </div>

                    {{-- Stats row --}}
                    <div class="ps-stats">
                        <div class="ps-stat">
                            <span class="ps-stat-val">{{ $ordersCount ?? 0 }}</span>
                            <span class="ps-stat-lbl">Orders</span>
                        </div>
                        <div class="ps-stat">
                            <span class="ps-stat-val">{{ $wishlistCount ?? 0 }}</span>
                            <span class="ps-stat-lbl">Saved</span>
                        </div>
                        <div class="ps-stat">
                            <span class="ps-stat-val">₹{{ $storeCredit ?? 0 }}</span>
                            <span class="ps-stat-lbl">Credits</span>
                        </div>
                    </div>

                    <nav class="ps-nav">
                        <nav class="ps-nav p-3">

                            {{-- Orders & Account --}}
                            <div class="ps-menu-header">Orders & Account</div>
                            <div class="row row-cols-3 g-2 mb-3">
                                <div class="col">
                                    <a href="{{ route('my_order') }}"
                                        class="ps-nav-item {{ request()->routeIs('my_order') ? 'active' : '' }}">
                                        <div class="ps-nav-icon"><i class="bi bi-bag-check"></i></div>
                                        <span class="nav-label">My Orders</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="#"
                                        class="ps-nav-item {{ request()->routeIs('my_3d_model') ? 'active' : '' }}">
                                        <div class="ps-nav-icon"><i class="bi bi-badge-3d"></i></div>
                                        <span class="nav-label">My 3D Model</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="{{ route('account_information') }}"
                                        class="ps-nav-item {{ request()->routeIs('account_information') ? 'active' : '' }}">
                                        <div class="ps-nav-icon"><i class="bi bi-person"></i></div>
                                        <span class="nav-label">Account Info</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Preferences --}}
                            <div class="ps-menu-header">Preferences</div>
                            <div class="row row-cols-3 g-2 mb-3">
                                <div class="col">
                                    <a href="{{ route('manage_notification') }}"
                                        class="ps-nav-item {{ request()->routeIs('manage_notification') ? 'active' : '' }}">
                                        <div class="ps-nav-icon"><i class="bi bi-bell"></i></div>
                                        <span class="nav-label">Notifications</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="{{ route('my_address') }}"
                                        class="ps-nav-item {{ request()->routeIs('my_address') ? 'active' : '' }}">
                                        <div class="ps-nav-icon"><i class="bi bi-geo-alt"></i></div>
                                        <span class="nav-label">Address Book</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="{{ route('my_prescription') }}"
                                        class="ps-nav-item {{ request()->routeIs('my_prescription') ? 'active' : '' }}">
                                        <div class="ps-nav-icon"><i class="bi bi-file-medical"></i></div>
                                        <span class="nav-label">Prescriptions</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Payments --}}
                            <div class="ps-menu-header">Payments</div>
                            <div class="row row-cols-3 g-2">
                                <div class="col">
                                    <a href="#" class="ps-nav-item">
                                        <div class="ps-nav-icon"><i class="bi bi-credit-card"></i></div>
                                        <span class="nav-label">Saved Cards</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="#" class="ps-nav-item">
                                        <div class="ps-nav-icon"><i class="bi bi-ticket-perforated"></i></div>
                                        <span class="nav-label">Vouchers</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="#" class="ps-nav-item">
                                        <div class="ps-nav-icon"><i class="bi bi-wallet2"></i></div>
                                        <span class="nav-label">Store Credit</span>
                                    </a>
                                </div>
                            </div>

                        </nav>
                    </nav>

                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="ps-logout">
                        <div class="ps-logout-icon"><i class="bi bi-box-arrow-right"></i></div>
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>

                {{-- ── RIGHT CONTENT ── --}}
                {{-- <div class="ps-right">
                    @yield('profile_content')
                </div> --}}

            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $("#imageUpload").change(function() {
            const input = this;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    $('#imagePreview').css('background-image', `url(${e.target.result})`).hide().fadeIn(650);
                };
                reader.readAsDataURL(input.files[0]);
                const formData = new FormData();
                formData.append('image', input.files[0]);
                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: "{{ route('profile.image.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: r => r.success ? toastr.success(r.message) : toastr.error(r.message),
                    error: () => toastr.error('Upload failed.')
                });
            }
        });
    </script>
@endsection
