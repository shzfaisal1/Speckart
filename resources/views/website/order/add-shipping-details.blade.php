@extends('website.layout.master')
@section('content')
    <style>
        .add-shipping-details {
            padding: 40px 0;
            background: #f7fafb;
        }

        .shipping-card,
        .order-summary-card {
            background: #fff;
            border-radius: 24px;
            padding: 30px;
            border: 1px solid #edf2f7;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .06);
        }

        /* Header */
        .shipping-header {
            margin-bottom: 25px;
        }

        .shipping-header h3 {
            font-size: 28px;
            font-weight: 700;
            color: #07484A;
            margin-bottom: 6px;
        }

        .shipping-header p {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 0;
        }

        /* Saved Address Cards (Lenskart inspired layout with Speckarts Theme) */
        .saved-address-card {
            border: 2px solid #e2e8f0;
            border-radius: 18px;
            padding: 20px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
        }

        .saved-address-card:hover {
            border-color: #11ABB0;
            box-shadow: 0 8px 24px rgba(17, 171, 176, 0.12);
        }

        .saved-address-card.active {
            border-color: #07484A;
            background: #f0fdfc;
            box-shadow: 0 10px 30px rgba(7, 72, 74, 0.12);
        }

        .address-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #e0f2f1;
            color: #07484A;
        }

        .saved-address-card.active .address-type-badge {
            background: #07484A;
            color: #ffffff;
        }

        .custom-radio-indicator {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .saved-address-card.active .custom-radio-indicator {
            border-color: #07484A;
            background: #07484A;
        }

        .custom-radio-indicator::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ffffff;
            opacity: 0;
            transform: scale(0);
            transition: all 0.2s ease;
        }

        .saved-address-card.active .custom-radio-indicator::after {
            opacity: 1;
            transform: scale(1);
        }

        .address-actions-btn {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            background: none;
            border: none;
            padding: 0;
            transition: color 0.2s;
        }

        .address-actions-btn:hover {
            color: #07484A;
            text-decoration: underline;
        }

        .btn-add-new-address {
            background: #07484A;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-add-new-address:hover {
            background: #11ABB0;
            color: #fff;
            transform: translateY(-1px);
        }

        /* Address Type Selector */
        .InputGroup {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }

        .InputGroup input {
            display: none;
        }

        .InputGroup label {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: .3s ease;
        }

        .InputGroup label:hover {
            border-color: #11ABB0;
            color: #11ABB0;
        }

        .InputGroup input:checked+label {
            background: linear-gradient(135deg, #07484A, #11ABB0);
            color: #fff;
            border-color: #11ABB0;
            box-shadow: 0 8px 20px rgba(17, 171, 176, .2);
        }

        /* Form Controls */
        .add-shipping-details .form-control {
            height: 54px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 14px 18px;
            font-size: 14px;
            transition: .3s ease;
        }

        .add-shipping-details .form-control:focus {
            border-color: #11ABB0;
            box-shadow: 0 0 0 4px rgba(17, 171, 176, .1);
        }

        /* Button */
        .checkout-btn {
            width: 100%;
            height: 56px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #07484A, #11ABB0);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            transition: .3s ease;
            box-shadow: 0 10px 25px rgba(17, 171, 176, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(17, 171, 176, .3);
            color: #fff;
        }

        /* Order Summary Card */
        .order-summary-card {
            position: sticky;
            top: 100px;
        }

        .order-summary-img {
            border-radius: 18px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .order-summary-img img {
            width: 100%;
            transition: .5s ease;
        }

        .order-summary-img:hover img {
            transform: scale(1.05);
        }

        .summary-title {
            font-size: 22px;
            font-weight: 700;
            color: #07484A;
            margin-bottom: 18px;
        }

        .summary-table tr td {
            padding: 12px 0;
            color: #4b5563;
            font-size: 14px;
        }

        .summary-total {
            border-top: 1px solid #e5e7eb;
        }

        .summary-total td {
            padding-top: 16px !important;
            font-size: 18px;
            font-weight: 700;
            color: #07484A;
        }

        .secure-box {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            border-radius: 14px;
            background: #f0fdfc;
            border: 1px solid #ccfbf1;
            color: #0f766e;
            font-weight: 600;
            font-size: 14px;
        }

        .secure-box i {
            font-size: 18px;
        }

        @media(max-width:991px) {
            .shipping-card,
            .order-summary-card {
                padding: 20px;
            }

            .order-summary-card {
                position: relative;
                top: 0;
            }

            .shipping-header h3 {
                font-size: 22px;
            }
        }
    </style>

    <!-- breadcrumbs-section -->
    <section class="breadcrumbs-section py-3 bg-white border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul id="breadcrumbs" class="m-0 p-0 list-unstyled d-flex align-items-center gap-2" style="font-size: 13px;">
                        <li><a href="{{ route('cart') }}" class="text-muted text-decoration-none">Cart</a></li>
                        <li><i class="bi bi-chevron-right text-muted" style="font-size: 10px;"></i></li>
                        <li class="fw-bold text-dark">Shipping Address</li>
                        <li><i class="bi bi-chevron-right text-muted" style="font-size: 10px;"></i></li>
                        <li class="text-muted">Payment</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- add-shipping-details -->
    <section class="add-shipping-details">
        <div class="container">
            <div class="row g-4">

                <div class="col-lg-7">
                    <div class="shipping-card">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius: 14px; font-size: 14px;" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius: 14px; font-size: 14px;" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger mb-3" style="border-radius: 14px; font-size: 14px;">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Section Header --}}
                        <div class="shipping-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h3>{{ $savedAddresses->count() > 0 ? 'Select Shipping Address' : 'Add Shipping Details' }}</h3>
                                <p>{{ $savedAddresses->count() > 0 ? 'Choose a saved address or add a new delivery location.' : 'Please enter your delivery address information.' }}</p>
                            </div>

                            @if($savedAddresses->count() > 0)
                                <button type="button" class="btn-add-new-address" id="btn-toggle-address-form">
                                    <i class="bi bi-plus-lg"></i> Add New Address
                                </button>
                            @endif
                        </div>

                        {{-- SAVED ADDRESSES LIST (Lenskart Inspired Structure) --}}
                        @if($savedAddresses->count() > 0)
                            <div id="saved-addresses-wrapper" class="mb-4">
                                <form action="{{ route('shipping.select') }}" method="POST" id="form-select-address">
                                    @csrf

                                    @php
                                        // Auto-select address from session if matching, or fallback to first address
                                        $selectedId = $shippingData['address_id'] ?? ($savedAddresses->first()->id ?? null);
                                    @endphp

                                    <div class="d-flex flex-column gap-3 mb-4">
                                        @foreach($savedAddresses as $index => $addr)
                                            @php
                                                $isObj = is_object($addr);
                                                $addrId   = $isObj ? $addr->id : ($addr['id'] ?? $index);
                                                $addrType = $isObj ? ($addr->address_type ?? 'Home') : ($addr['address_type'] ?? 'Home');
                                                $fullName = $isObj ? $addr->full_name : ($addr['full_name'] ?? '');
                                                $phone    = $isObj ? $addr->phone : ($addr['phone'] ?? '');
                                                $houseNo  = $isObj ? $addr->house_no : ($addr['house_no'] ?? '');
                                                $roadArea = $isObj ? $addr->road_area : ($addr['road_area'] ?? '');
                                                $pincode  = $isObj ? $addr->pincode : ($addr['pincode'] ?? '');
                                                $fullAddr = $isObj ? $addr->full_address : ($addr['full_address'] ?? ($houseNo . ', ' . $roadArea . ' - ' . $pincode));

                                                $iconMap = [
                                                    'Home' => 'bi-house-door',
                                                    'Work' => 'bi-building',
                                                    'Friend & Family' => 'bi-people',
                                                    'Other' => 'bi-geo-alt'
                                                ];
                                                $typeIcon = $iconMap[$addrType] ?? 'bi-geo-alt';

                                                $isSelected = ($selectedId == $addrId) || ($index === 0 && !$selectedId);
                                            @endphp

                                            <div class="saved-address-card {{ $isSelected ? 'active' : '' }}"
                                                 data-address-id="{{ $addrId }}"
                                                 onclick="selectAddressCard(this, '{{ $addrId }}')">

                                                <input type="radio" name="address_id" value="{{ $addrId }}" id="addr-radio-{{ $addrId }}" class="d-none" {{ $isSelected ? 'checked' : '' }}>

                                                <div class="d-flex align-items-start justify-content-between mb-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="custom-radio-indicator"></span>
                                                        <span class="address-type-badge">
                                                            <i class="bi {{ $typeIcon }}"></i> {{ $addrType }}
                                                        </span>
                                                        <span class="badge rounded-pill bg-light text-secondary border px-2 py-1" style="font-size: 11px;">
                                                            <i class="bi bi-truck text-success me-1"></i> Fast Delivery
                                                        </span>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-3">
                                                        <button type="button" class="address-actions-btn text-danger" onclick="deleteAddress(event, '{{ $addrId }}')">
                                                            <i class="bi bi-trash me-1"></i> Delete
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="ps-4 ms-1">
                                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 16px;">{{ $fullName }}</h6>
                                                    <p class="text-secondary mb-2" style="font-size: 14px; line-height: 1.5;">{{ $fullAddr }}</p>
                                                    <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 13px;">
                                                        <span><i class="bi bi-person me-1 text-teal"></i> {{ $fullName }}</span>
                                                        <span>|</span>
                                                        <span><i class="bi bi-telephone me-1 text-teal"></i> {{ $phone }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="submit" class="checkout-btn" id="btn-submit-saved-address">
                                        Save Address & Proceed <i class="bi bi-arrow-right fs-5"></i>
                                    </button>
                                </form>

                                {{-- Hidden delete form --}}
                                <form id="form-delete-address" action="" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        @endif

                        {{-- ADD NEW ADDRESS FORM --}}
                        <div id="address-form-wrapper" class="{{ $savedAddresses->count() > 0 ? 'd-none' : '' }}">

                            @if($savedAddresses->count() > 0)
                                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-geo-alt-fill text-teal me-2"></i>Enter New Address Details</h5>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btn-cancel-address-form">
                                        <i class="bi bi-arrow-left me-1"></i> Back to Saved Addresses
                                    </button>
                                </div>
                            @endif

                            {{-- Address Type Selection Tabs --}}
                            <p class="fw-semibold text-secondary mb-2" style="font-size: 13px;">Address Type :</p>
                            <div class="InputGroup">
                                <input type="radio" name="address_type_radio" id="size_1" value="Home" checked onclick="document.getElementById('hidden_address_type').value = 'Home'">
                                <label for="size_1">
                                    <i class="bi bi-house-door me-2"></i>Home
                                </label>

                                <input type="radio" name="address_type_radio" id="size_2" value="Work" onclick="document.getElementById('hidden_address_type').value = 'Work'">
                                <label for="size_2">
                                    <i class="bi bi-building me-2"></i>Work
                                </label>

                                <input type="radio" name="address_type_radio" id="size_3" value="Friend & Family" onclick="document.getElementById('hidden_address_type').value = 'Friend & Family'">
                                <label for="size_3">
                                    <i class="bi bi-people me-2"></i>Friend & Family
                                </label>

                                <input type="radio" name="address_type_radio" id="size_4" value="Other" onclick="document.getElementById('hidden_address_type').value = 'Other'">
                                <label for="size_4">
                                    <i class="bi bi-geo-alt me-2"></i>Other
                                </label>
                            </div>

                            <form action="{{ route('shipping.save') }}" method="POST">
                                @csrf
                                <input type="hidden" name="address_type" id="hidden_address_type" value="Home">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control @error('pincode') is-invalid @enderror" placeholder="6-Digit Pincode *" name="pincode" value="{{ $shippingData['pincode'] ?? old('pincode') }}" maxlength="6" pattern="[0-9]{6}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        @error('pincode')
                                            <span class="text-danger small mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" placeholder="Full Name *" name="full_name" value="{{ $shippingData['full_name'] ?? old('full_name') }}" required>
                                        @error('full_name')
                                            <span class="text-danger small mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <input type="text" class="form-control @error('house_no') is-invalid @enderror"
                                            placeholder="House Number / Building Name *" name="house_no" value="{{ $shippingData['house_no'] ?? old('house_no') }}" required>
                                        @error('house_no')
                                            <span class="text-danger small mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <input type="text" class="form-control @error('road_area') is-invalid @enderror"
                                            placeholder="Road Name / Area / Location *" name="road_area" value="{{ $shippingData['road_area'] ?? old('road_area') }}" required>
                                        @error('road_area')
                                            <span class="text-danger small mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <input type="text" class="form-control" placeholder="Landmark (Optional)" name="landmark" value="{{ $shippingData['landmark'] ?? old('landmark') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" placeholder="10-Digit Mobile Number *" name="phone" value="{{ $shippingData['phone'] ?? old('phone') }}" maxlength="10" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        @error('phone')
                                            <span class="text-danger small mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email Address (Optional)" name="email" value="{{ $shippingData['email'] ?? old('email') }}">
                                        @error('email')
                                            <span class="text-danger small mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>

                                <button type="submit" class="checkout-btn">
                                    Continue to Payment <i class="bi bi-arrow-right fs-5"></i>
                                </button>
                            </form>

                        </div>

                    </div>
                </div>

                <div class="col-lg-5">

                    <div class="order-summary-card">

                        <div class="order-summary-img d-none d-lg-block">
                            <img src="{{ asset('website/assets/img/bg/Add-Shipping-details-bg.png') }}" alt="">
                        </div>

                        <h4 class="summary-title">Order Summary</h4>

                        <table class="table table-borderless summary-table">
                            <tbody>
                                <tr>
                                    <td class="text-start ps-2">Total Item Price</td>
                                    <td class="text-end pe-2">₹{{ number_format(($cartData['frame_subtotal'] ?? 0) + ($cartData['lens_subtotal'] ?? 0), 0) }}</td>
                                </tr>

                                <tr>
                                    <td class="text-start ps-2">Total Discount</td>
                                    <td class="text-end text-success pe-2">- ₹{{ number_format(($cartData['bogo_savings'] ?? 0) + ($cartData['third_item_savings'] ?? 0) + ($cartData['coupon_discount'] ?? 0) + ($cartData['first_frame_free_save'] ?? 0), 0) }}</td>
                                </tr>

                                @if(($cartData['loyalty_discount'] ?? 0) > 0)
                                <tr>
                                    <td class="text-start ps-2">Loyalty Points</td>
                                    <td class="text-end text-success pe-2">- ₹{{ number_format($cartData['loyalty_discount'], 0) }}</td>
                                </tr>
                                @endif

                                <tr class="summary-total">
                                    <td class="text-start ps-2">Total Payable</td>
                                    <td class="text-end pe-2">₹{{ number_format($cartData['grand_total'] ?? 0, 0) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="secure-box">
                            <i class="bi bi-shield-check"></i>
                            <span>100% Secure Checkout</span>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script>
        function selectAddressCard(element, addressId) {
            $('.saved-address-card').removeClass('active');
            $(element).addClass('active');
            $(element).find('input[type="radio"]').prop('checked', true);
        }

        function deleteAddress(event, addressId) {
            event.stopPropagation();
            if (confirm('Are you sure you want to delete this address?')) {
                var deleteForm = document.getElementById('form-delete-address');
                deleteForm.action = '/shipping-details/address/' + addressId;
                deleteForm.submit();
            }
        }

        $(document).ready(function() {
            $('#btn-toggle-address-form').on('click', function() {
                $('#saved-addresses-wrapper').addClass('d-none');
                $('#address-form-wrapper').removeClass('d-none');
            });

            $('#btn-cancel-address-form').on('click', function() {
                $('#address-form-wrapper').addClass('d-none');
                $('#saved-addresses-wrapper').removeClass('d-none');
            });
        });
    </script>
@endsection
