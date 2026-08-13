@extends('web.layout.master')

@section('content')

<style>
    .my-address-card{
    background:#fff;

    border:1px solid #EAF4F4;

    border-radius:24px;

    padding:24px;

    margin-bottom:20px;

    transition:.35s;

    position:relative;
}

.my-address-card:hover{
    transform:translateY(-4px);

    box-shadow:
    0 15px 35px rgba(7,72,74,.08);
}

.default-address{
    border:2px solid #11ABB0;

    background:
    linear-gradient(
    180deg,
    rgba(250,245,158,.18),
    #fff
    );
}

.my-address-card-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:20px;

    margin-bottom:18px;
}

.my-address-user h4{
    margin:0 0 12px;

    font-size:18px;
    font-weight:700;

    color:#07484A;
}

.my-address-user h4 i{
    color:#11ABB0;
    margin-right:6px;
}

.my-address-badges{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.address-type{
    background:#EDF9F9;

    color:#11ABB0;

    padding:6px 12px;

    border-radius:50px;

    font-size:12px;
    font-weight:600;
}

.default-badge{
    background:#FAF59E;

    color:#07484A;

    padding:6px 12px;

    border-radius:50px;

    font-size:12px;
    font-weight:700;
}

.address-actions{
    display:flex;
    gap:10px;
}

.address-actions a{
    width:42px;
    height:42px;

    display:flex;
    align-items:center;
    justify-content:center;

    border-radius:12px;

    text-decoration:none;

    transition:.3s;
}

.edit-btn{
    background:#EDF9F9;
    color:#11ABB0;
}

.delete-btn{
    background:#FFF1F2;
    color:#E11D48;
}

.edit-btn:hover{
    background:#11ABB0;
    color:#fff;
}

.delete-btn:hover{
    background:#E11D48;
    color:#fff;
}

.my-address-body p{
    margin-bottom:10px;

    color:#6B7280;

    line-height:1.7;
}

.my-address-body p i{
    color:#11ABB0;
    margin-right:8px;
}

.address-footer{
    margin-top:20px;
}

.set-default-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;

    text-decoration:none;

    background:
    linear-gradient(
    135deg,
    #07484A,
    #11ABB0
    );

    color:#fff;

    padding:10px 16px;

    border-radius:12px;

    font-size:14px;
    font-weight:600;
}

.set-default-btn:hover{
    color:#fff;
}

</style>
<section class="my-addresses-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="my-addresses-section-form">
                    <h3>
                        My Addresses 
                        <a href="javascript:void(0)" 
                           data-bs-toggle="modal" 
                           data-bs-target="#addAddressModal"
                           onclick="resetForm()"
                           class="btn">Add New Address</a>
                    </h3>

                    @foreach($addresses as $address)
                    <div class="my-address-card {{ $address->is_default ? 'default-address' : '' }}">

                        <div class="my-address-card-header">

                            <div class="my-address-user">

                                <h4>
                                    <i class="fa-solid fa-user"></i>
                                    {{$address->first_name}} {{$address->last_name}}
                                </h4>

                                <div class="my-address-badges">

                                    <span class="address-type">
                                        <i class="fa-solid fa-location-dot"></i>
                                        {{ ucfirst($address->type) }}
                                    </span>

                                    @if($address->is_default)
                                    <span class="default-badge">
                                        <i class="fa-solid fa-check"></i>
                                        Default
                                    </span>
                                    @endif

                                </div>

                            </div>

                            <div class="address-actions">

                                <a href="javascript:void(0)"
                                onclick='editAddress(@json($address))'
                                class="edit-btn">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>

                                <a href="javascript:void(0)"
                                onclick="if(confirm('Delete this address?')){ document.getElementById('delete-form-{{$address->id}}').submit(); }"
                                class="delete-btn">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>

                            </div>

                        </div>

                        <div class="my-address-body">

                            <p>
                                <i class="fa-solid fa-location-dot"></i>
                                {{$address->address_line_1}}
                            </p>

                            @if($address->address_line_2)
                            <p>{{$address->address_line_2}}</p>
                            @endif

                            <p>
                                {{$address->city}},
                                {{$address->state}}
                                -
                                {{$address->pincode}}
                            </p>

                            <p>
                                <i class="fa-solid fa-phone"></i>
                                {{$address->phone}}
                            </p>

                        </div>

                        @if(!$address->is_default)

                        <div class="address-footer">

                            <a href="javascript:void(0)"
                            onclick="document.getElementById('default-form-{{$address->id}}').submit();"
                            class="set-default-btn">

                                <i class="fa-solid fa-star"></i>
                                Set As Default

                            </a>

                        </div>

                        @endif

                    </div>
                    @endforeach

                </div>
            </div>

            <div class="col-lg-6">
                <div class="account-information-section-img">
                    <img src="{{asset('assets/img/bg/Account-Information.png')}}">
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ====================== SINGLE MODAL ====================== -->
<div class="modal fade address-modal" id="addAddressModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalTitle">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="addressForm" action="{{ route('store_address') }}" method="POST">
                    @csrf

                    <input type="hidden" name="address_id" id="address_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}">
                            @error('first_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                            @error('last_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" name="address_line_1" class="form-control" value="{{ old('address_line_1') }}">
                        @error('address_line_1') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Address Line 2</label>
                        <input type="text" name="address_line_2" class="form-control" value="{{ old('address_line_2') }}">
                        @error('address_line_2') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                            @error('city') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>State <span class="text-danger">*</span></label>
                            <input type="text" name="state" class="form-control" value="{{ old('state') }}">
                            @error('state') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Pincode <span class="text-danger">*</span></label>
                            <input type="text" name="pincode" class="form-control" value="{{ old('pincode') }}">
                            @error('pincode') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- TYPE -->
                    <label>Address Type <span class="text-danger">*</span></label>
                    <div class="segmented-control">
                        <input type="radio" name="type" id="add_type_home" value="home" {{ old('type', 'home') == 'home' ? 'checked' : '' }}>
                        <label for="add_type_home">Home</label>

                        <input type="radio" name="type" id="add_type_office" value="office" {{ old('type') == 'office' ? 'checked' : '' }}>
                        <label for="add_type_office">Office</label>

                        <input type="radio" name="type" id="add_type_other" value="other" {{ old('type') == 'other' ? 'checked' : '' }}>
                        <label for="add_type_other">Other</label>
                    </div>
                    @error('type') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror

                    <div class="address-checkbox mt-3">
                        <input type="checkbox" name="is_default" id="add_is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                        <label for="add_is_default">Set as Default</label>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="submitBtn">Save Address</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


<!-- ====================== SCRIPT ====================== -->
<script>

function editAddress(address) {

    let modal = new bootstrap.Modal(document.getElementById('addAddressModal'));
    modal.show();

    document.getElementById('modalTitle').innerText = "Edit Address";
    document.getElementById('submitBtn').innerText = "Update Address";

    let form = document.getElementById('addressForm');
    let updateUrl = "{{ route('update_address', ':id') }}";
    form.action = updateUrl.replace(':id', address.id);

    document.querySelector('[name="first_name"]').value = address.first_name;
    document.querySelector('[name="last_name"]').value = address.last_name;
    document.querySelector('[name="phone"]').value = address.phone;
    document.querySelector('[name="address_line_1"]').value = address.address_line_1;
    document.querySelector('[name="address_line_2"]').value = address.address_line_2 ?? '';
    document.querySelector('[name="city"]').value = address.city;
    document.querySelector('[name="state"]').value = address.state;
    document.querySelector('[name="pincode"]').value = address.pincode;

    document.getElementById('add_type_home').checked = address.type === 'home';
    document.getElementById('add_type_office').checked = address.type === 'office';
    document.getElementById('add_type_other').checked = address.type === 'other';

    document.getElementById('add_is_default').checked = address.is_default == 1;
    document.getElementById('address_id').value = address.id;
}

function resetForm() {

    let form = document.getElementById('addressForm');
    form.reset();

    form.action = "{{ route('store_address') }}";

    document.getElementById('modalTitle').innerText = "Add New Address";
    document.getElementById('submitBtn').innerText = "Save Address";

    document.getElementById('address_id').value = '';
    document.getElementById('add_type_home').checked = true;
}

</script>

@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var myModal = new bootstrap.Modal(document.getElementById("addAddressModal"));
        
        // Restore Edit State if address_id exists (meaning they were editing)
        @if(old('address_id'))
            document.getElementById('modalTitle').innerText = "Edit Address";
            document.getElementById('submitBtn').innerText = "Update Address";
            let updateUrl = "{{ route('update_address', ':id') }}";
            document.getElementById('addressForm').action = updateUrl.replace(':id', "{{ old('address_id') }}");
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