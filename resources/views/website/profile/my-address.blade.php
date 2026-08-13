@extends('web.layout.master')

@section('content')
    <style>
        /* ========================================
                    MY ADDRESS SECTION
                    ======================================== */

        .my-address-section {
            /* background: linear-gradient(180deg,
                    rgba(250, 245, 158, .15) 0%,
                    #f8fbfb 25%,
                    #f8fbfb 100%); */
            min-height: 100vh;
            padding: 30px 0;
        }

        /* ========================================
                    PAGE HEADER
                    ======================================== */

        .page-title {
            color: #07484A;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #7c8a8b;
            font-size: 15px;
            margin-bottom: 0;
        }

        /* ========================================
                    BUTTONS
                    ======================================== */

        .btn-theme {
            background: #07484A;
            border: 1px solid #07484A;
            color: #FAF59E;
            border-radius: 14px;
            padding: 12px 22px;
            font-weight: 600;
            transition: .3s;
        }

        .btn-theme:hover {
            background: #063739;
            border-color: #063739;
            color: #FAF59E;
            transform: translateY(-2px);
        }

        .btn-outline-theme {
            border: 1px solid #00B9B9;
            color: #00B9B9;
            background: #fff;
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-outline-theme:hover {
            background: #00B9B9;
            color: #fff;
        }

        /* ========================================
                    ADDRESS CARD
                    ======================================== */

        .address-card {
            position: relative;
            background: #fff;
            border-radius: 24px;
            padding: 24px;
            border: 1px solid rgba(7, 72, 74, .08);
            box-shadow: 0 10px 30px rgba(7, 72, 74, .06);
            transition: .35s ease;
            height: 100%;
            overflow: hidden;
        }

        .address-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(7, 72, 74, .12);
        }

        /* Left Accent */

        .address-card {
            border: 1px solid rgba(7, 72, 74, .08);
            background: #fff;
        }

        .address-card:hover {
            border-color: #00B9B9;
            box-shadow:
                0 15px 35px rgba(7, 72, 74, .10),
                0 0 0 3px rgba(0, 185, 185, .08);
        }

        /* ========================================
                    DEFAULT ADDRESS
                    ======================================== */

        .active-address {
            border: 2px solid #00B9B9;
        }

        .address-badge {
            position: static;
            display: inline-flex;
            align-items: center;
            margin-top: 8px;
            padding: 6px 12px;
            border-radius: 50px;
            background: #07484A;
            color: #FAF59E;
            font-size: 12px;
            font-weight: 600;
        }

        /* ========================================
                    HEADER
                    ======================================== */

        .address-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .address-header h5 {
            color: #07484A;
            font-weight: 700;
            margin-bottom: 8px;
        }

        /* ========================================
                    ADDRESS TYPE
                    ======================================== */

        .address-type {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 30px;
            background: rgba(0, 185, 185, .12);
            color: #00B9B9;
            font-size: 12px;
            font-weight: 600;
        }

        /* ========================================
                    BODY
                    ======================================== */

        .address-body {
            margin-top: 10px;
        }

        .address-body p {
            color: #555;
            line-height: 1.8;
            margin-bottom: 18px;
            font-size: 15px;
        }

        /* ========================================
                    CONTACT
                    ======================================== */

        .contact-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #07484A;
            font-weight: 600;
            font-size: 15px;
        }

        .contact-info i {
            color: #00B9B9;
        }

        /* ========================================
                    DROPDOWN
                    ======================================== */

        .dropdown .btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dropdown-menu {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 10px;
            padding: 10px 14px;
        }

        .dropdown-item:hover {
            background: #f5fafa;
        }

        /* ========================================
                    EMPTY ADDRESS
                    ======================================== */

        .empty-address {
            background: #fff;
            border-radius: 24px;
            padding: 60px 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(7, 72, 74, .06);
        }

        .empty-address img {
            width: 120px;
            margin-bottom: 20px;
        }

        .empty-address h4 {
            color: #07484A;
            font-weight: 700;
        }

        .empty-address p {
            color: #7c8a8b;
        }

        /* ========================================
                    MODAL
                    ======================================== */

        .address-modal {
            border: none;
            border-radius: 24px;
            overflow: hidden;
        }

        .address-modal .modal-header {
            background: #07484A;
            color: #FAF59E;
            padding: 20px 25px;
        }

        .address-modal .modal-body {
            padding: 25px;
        }

        .address-modal .modal-footer {
            padding: 20px 25px;
        }

        .address-modal .form-label {
            font-weight: 600;
            color: #07484A;
        }

        .address-modal .form-control {
            border-radius: 14px;
            border: 1px solid #dfe7e7;
            min-height: 50px;
        }

        .address-modal .form-control:focus {
            border-color: #00B9B9;
            box-shadow: 0 0 0 .2rem rgba(0, 185, 185, .15);
        }

        /* ========================================
                    ADDRESS TYPE CHIPS
                    ======================================== */

        .address-type-wrap {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .address-type-wrap input {
            display: none;
        }

        .address-type-wrap label {
            padding: 12px 18px;
            border-radius: 50px;
            border: 1px solid #dfe7e7;
            cursor: pointer;
            transition: .3s;
            font-weight: 500;
        }

        .address-type-wrap label i {
            margin-right: 6px;
        }

        .address-type-wrap input:checked+label {
            background: #07484A;
            color: #FAF59E;
            border-color: #07484A;
        }

        /* ========================================
                    CHECKBOX
                    ======================================== */

        .form-check-input:checked {
            background: #00B9B9;
            border-color: #00B9B9;
        }

        /* ========================================
                    MOBILE
                    ======================================== */

        @media(max-width:768px) {

            .btn-theme {
                padding: 10px 13px;
            }

            .my-address-section {
                padding: 20px 0;
            }

            .page-title {
                font-size: 22px;
            }

            .address-card {
                padding: 18px;
                border-radius: 18px;
            }

            .address-header {
                gap: 10px;
            }

            .address-header h5 {
                font-size: 16px;
            }

            .address-badge {
                position: static;
                display: inline-block;
                margin-bottom: 15px;
            }

            .btn-theme {
                padding: 10px 16px;
                font-size: 14px;
            }

            .contact-info {
                font-size: 14px;
            }

            .address-body p {
                font-size: 14px;
            }
        }

        .address-modal {
            border: none;
            border-radius: 24px;
            overflow: hidden;
        }

        .address-modal .modal-header {
            background: #07484A;
            color: #FAF59E;
            padding: 20px 25px;
        }

        .address-modal .modal-body {
            padding: 25px;
        }

        .address-modal .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid #eee;
        }

        .address-modal .form-label {
            font-weight: 600;
            color: #07484A;
        }

        .address-modal .form-control {
            border-radius: 14px;
            min-height: 50px;
            border: 1px solid #dfe7e7;
        }

        .address-modal .form-control:focus {
            border-color: #00B9B9;
            box-shadow: 0 0 0 .2rem rgba(0, 185, 185, .15);
        }

        .btn-theme {
            background: #07484A;
            border: 1px solid #07484A;
            color: #FAF59E;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
        }

        .btn-theme:hover {
            background: #063739;
            color: #FAF59E;
        }

        .address-type-wrap {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .address-type-wrap input {
            display: none;
        }

        .address-type-wrap label {
            border: 1px solid #dfe7e7;
            padding: 12px 20px;
            border-radius: 50px;
            cursor: pointer;
            transition: .3s;
            font-weight: 500;
        }

        .address-type-wrap input:checked+label {
            background: #07484A;
            color: #FAF59E;
            border-color: #07484A;
        }

        .form-check-input:checked {
            background-color: #00B9B9;
            border-color: #00B9B9;
        }
    </style>

    <section class="my-address-section py-4">
        <div class="container">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="page-title">My Addresses</h3>
                    <p class="page-subtitle">
                        Manage your delivery addresses
                    </p>
                </div>

                <button class="btn btn-theme" onclick="resetForm()" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="bi bi-plus-lg me-2"></i>
                    Add Address
                </button>
            </div>

            <div class="row g-4">

                @foreach ($addresses as $address)
                    <div class="col-lg-4 col-md-6">

                        <div class="address-card {{ $address->is_default ? 'active-address' : '' }}">

                            <div class="address-header">

                                <div>
                                    <h5>
                                        {{ $address->first_name }}
                                        {{ $address->last_name }}
                                    </h5>

                                    @if ($address->is_default)
                                        <span class="address-badge">
                                            Default Address
                                        </span>
                                    @endif

                                    <span class="address-type">
                                        {{ ucfirst($address->type) }}
                                    </span>
                                </div>

                                <div class="dropdown">

                                    <button class="btn btn-light rounded-circle" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>

                                    <ul class="dropdown-menu">

                                        <li>
                                            <button class="dropdown-item"
                                                onclick='editAddress(@json($address))'>
                                                Edit
                                            </button>
                                        </li>

                                        <li>
                                            <form method="POST" action="{{ route('delete_address', $address->id) }}">
                                                @csrf
                                                @method('DELETE')

                                                <button class="dropdown-item text-danger"
                                                    onclick="return confirm('Delete this address?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </li>

                                    </ul>

                                </div>

                            </div>

                            <div class="address-body">

                                <p>
                                    {{ $address->address_line_1 }}
                                    {{ $address->address_line_2 }}
                                    <br>

                                    {{ $address->city }},
                                    {{ $address->state }}
                                    -
                                    {{ $address->pincode }}
                                </p>

                                <div class="contact-info">
                                    <i class="bi bi-telephone"></i>
                                    {{ $address->phone }}
                                </div>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

        </div>
    </section>

    <div class="modal fade" id="addAddressModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content address-modal">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        Add New Address
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="addressForm" method="POST" action="{{ route('store_address') }}">

                    @csrf

                    <input type="hidden" id="address_id" name="address_id">

                    <div class="modal-body">

                        <div class="row g-3">

                            <!-- First Name -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    First Name *
                                </label>

                                <input type="text" class="form-control" name="first_name"
                                    value="{{ old('first_name') }}" required>
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Last Name *
                                </label>

                                <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}"
                                    required>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Mobile Number *
                                </label>

                                <input type="text" class="form-control" name="phone" value="{{ old('phone') }}"
                                    required>
                            </div>

                            <!-- Country -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Country *
                                </label>

                                <input type="text" class="form-control" name="country"
                                    value="{{ old('country', 'India') }}" required>
                            </div>

                            <!-- Address 1 -->
                            <div class="col-12">
                                <label class="form-label">
                                    Address Line 1 *
                                </label>

                                <input type="text" class="form-control" name="address_line_1"
                                    value="{{ old('address_line_1') }}" required>
                            </div>

                            <!-- Address 2 -->
                            <div class="col-12">
                                <label class="form-label">
                                    Address Line 2
                                </label>

                                <input type="text" class="form-control" name="address_line_2"
                                    value="{{ old('address_line_2') }}">
                            </div>

                            <!-- City -->
                            <div class="col-md-4">
                                <label class="form-label">
                                    City *
                                </label>

                                <input type="text" class="form-control" name="city" value="{{ old('city') }}"
                                    required>
                            </div>

                            <!-- State -->
                            <div class="col-md-4">
                                <label class="form-label">
                                    State *
                                </label>

                                <input type="text" class="form-control" name="state" value="{{ old('state') }}"
                                    required>
                            </div>

                            <!-- Pincode -->
                            <div class="col-md-4">
                                <label class="form-label">
                                    Pincode *
                                </label>

                                <input type="text" class="form-control" name="pincode" value="{{ old('pincode') }}"
                                    required>
                            </div>

                            <!-- Address Type -->
                            <div class="col-12">

                                <label class="form-label d-block mb-2">
                                    Address Type
                                </label>

                                <div class="address-type-wrap">

                                    <input type="radio" name="type" value="home" id="add_type_home" checked>

                                    <label for="add_type_home">
                                        <i class="bi bi-house-door"></i>
                                        Home
                                    </label>

                                    <input type="radio" name="type" value="office" id="add_type_office">

                                    <label for="add_type_office">
                                        <i class="bi bi-building"></i>
                                        Office
                                    </label>

                                    <input type="radio" name="type" value="other" id="add_type_other">

                                    <label for="add_type_other">
                                        <i class="bi bi-geo-alt"></i>
                                        Other
                                    </label>

                                </div>

                            </div>

                            <!-- Default -->
                            <div class="col-12">

                                <div class="form-check">

                                    <input class="form-check-input" type="checkbox" id="add_is_default"
                                        name="is_default" value="1">

                                    <label class="form-check-label">
                                        Set as Default Address
                                    </label>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-theme" id="submitBtn">
                            Save Address
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        function editAddress(address) {

            let modal = new bootstrap.Modal(
                document.getElementById('addAddressModal')
            );

            modal.show();

            document.getElementById('modalTitle').innerText =
                "Edit Address";

            document.getElementById('submitBtn').innerText =
                "Update Address";

            let form =
                document.getElementById('addressForm');

            let updateUrl =
                "{{ route('update_address', ':id') }}";

            form.action =
                updateUrl.replace(':id', address.id);

            document.querySelector('[name="first_name"]').value =
                address.first_name ?? '';

            document.querySelector('[name="last_name"]').value =
                address.last_name ?? '';

            document.querySelector('[name="phone"]').value =
                address.phone ?? '';

            document.querySelector('[name="address_line_1"]').value =
                address.address_line_1 ?? '';

            document.querySelector('[name="address_line_2"]').value =
                address.address_line_2 ?? '';

            document.querySelector('[name="city"]').value =
                address.city ?? '';

            document.querySelector('[name="state"]').value =
                address.state ?? '';

            document.querySelector('[name="pincode"]').value =
                address.pincode ?? '';

            document.getElementById('add_type_home').checked =
                address.type === 'home';

            document.getElementById('add_type_office').checked =
                address.type === 'office';

            document.getElementById('add_type_other').checked =
                address.type === 'other';

            document.getElementById('add_is_default').checked =
                address.is_default == 1;

            document.getElementById('address_id').value =
                address.id;
        }

        function resetForm() {

            const form = document.getElementById('addressForm');

            form.reset();

            form.action =
                "{{ route('store_address') }}";

            document.getElementById('modalTitle').textContent =
                'Add New Address';

            document.getElementById('submitBtn').textContent =
                'Save Address';

            document.getElementById('address_id').value = '';

            document.getElementById('add_type_home').checked = true;

            document.getElementById('add_is_default').checked = false;
        }
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById("addAddressModal"));

                // Restore Edit State if address_id exists (meaning they were editing)
                @if (old('address_id'))
                    document.getElementById('modalTitle').innerText = "Edit Address";
                    document.getElementById('submitBtn').innerText = "Update Address";
                    let updateUrl = "{{ route('update_address', ':id') }}";
                    document.getElementById('addressForm').action = updateUrl.replace(':id',
                        "{{ old('address_id') }}");
                    document.getElementById('address_id').value = "{{ old('address_id') }}";
                @else
                    document.getElementById('modalTitle').innerText = "Add New Address";
                    document.getElementById('submitBtn').innerText = "Save Address";
                    document.getElementById('addressForm').action = "{{ route('store_address') }}";
                @endif

                myModal.show();
            });
        </script>
    @endif
@endsection
