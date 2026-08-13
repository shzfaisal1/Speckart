@extends('layouts.master')
@section('styles')
<style>
/* Spinner when input has `loading` class */
input.loading {
    background-image: url('https://i.imgur.com/6RMhx.gif'); /* or any spinner gif you like */
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
}


</style>  
    

@endsection
@section('content')

@php
    $usr = Auth::guard()->user();
@endphp
 
 
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Membsership Card </h3>
                        <a  href="#" class=" btn"   onclick="openAddModal()">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Add New Membership Card
                        </a>
                    </div>
                </div>
            </div>
            
            
        <style>
        
        .membership-card {
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 25px;
            position: relative;
            overflow: hidden;
            transition: 0.4s;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .membership-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 0 30px rgba(0,255,255,0.3);
        }
        
        .membership-card::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, #00f2fe, #4facfe, #00f2fe);
            animation: rotate 6s linear infinite;
            top: -50%;
            left: -50%;
            opacity: 0.15;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .card-content {
            position: relative;
            z-index: 2;
        }
        
        .price {
            font-size: 40px;
            font-weight: bold;
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .badge-popular {
            position: absolute;
            top: 15px;
            right: -40px;
            background: #ff9800;
            padding: 5px 40px;
            transform: rotate(45deg);
            font-size: 12px;
        }
        
        .btn-glow {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
            border: none;
            color: #fff;
            border-radius: 30px;
            padding: 10px;
            width: 100%;
            transition: 0.3s;
        }
        
        .btn-glow:hover {
            box-shadow: 0 0 20px #00f2fe;
        }
        </style>
        
            <div class="row">
        
                @foreach($cards as $card)
                <div class="col-md-4 mb-4">
        
                    <div class="membership-card">
        
                        <div class="card-content text-center">
        
                            <h3>{{ $card->card_name }}</h3>
        
                            <div class="price mb-3">
                                ₹{{ $card->price }}
                            </div>
        
                            <p>⏳ {{ $card->validity_days }} Days Validity</p>
        
                            <hr style="border-color: rgba(255,255,255,0.2)">
        
                            <p>🎁 First Loyalty: {{ $card->loyalty_earn_first }}%</p>
                            <p>🔁 Repeat Loyalty: {{ $card->loyalty_earn_repeat }}%</p>
                            <p>💳 Loyalty Usage Limit: {{ $card->loyalty_use_percent }}%</p>
        
                            <hr style="border-color: rgba(255,255,255,0.2)">
        
                            <p>🎟 Coupon: {{ $card->coupon_percent }}%</p>
                            <p>📅 Voucher Validity: {{ $card->voucher_validity_days }} Days</p>
                            <p>
                                @if($card->enable_bogo)
                                    <span class="badge" style="background:#ff9800;color:#fff;padding:4px 10px;border-radius:20px;">🛍 Buy 1 Get 1 Free: ON</span>
                                @else
                                    <span class="badge" style="background:#555;color:#ccc;padding:4px 10px;border-radius:20px;">🛍 Buy 1 Get 1 Free: OFF</span>
                                @endif
                            </p>
        
                            <div class="d-flex justify-content-center gap-2 mt-3">

                            <!-- Edit Button -->
                            <button class="btn btn-sm btn-warning d-flex align-items-center gap-1"
                                    onclick="openEditModal({{ $card->card_id }})">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                        
                            <!-- Delete Button -->
                            <form action="{{ route('admin.membership-delete', $card->card_id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure to delete this card?')">
                                @csrf
                                @method('DELETE')
                        
                                <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center gap-1" style="margin-left:10px">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        
                        </div>
        
                        </div>
        
                    </div>
        
                </div>
                @endforeach
        
        </div>
    </div>
</section> 


<div class="modal fade" data-backdrop="static" id="cardModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Membership Card</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="cardForm" method="POST" enctype="multipart/form-data">
                    
                    @csrf
                    <input type="hidden" name="card_id" id="card_id">
                    <div class="multisteps-form__content">
                        <div class="multisteps-form__content1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <ul>
                                            <li>All fields marked with * are mandatory.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <label for="">Membership Card Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Card Name"  name="card_name" id="card_name">
                                    <span class="error badge text-danger" id="card_nameError"></span>
                                </div>
                                
                                
                                <div class="col-md-4">
                                    <label for="">Price <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter  Price" name="price" id="price">
                                    <span class="error badge text-danger" id="priceError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Validity Days <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Validity Days" name="validity_days" id="validity_days">
                                    <span class="error badge text-danger" id="validity_daysError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Loyalty Earn First <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Loyalty Earn First" name="loyalty_earn_first" id="loyalty_earn_first">
                                    <span class="error badge text-danger" id="loyalty_earn_firstError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Loyalty Earn Repeat  <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Loyalty Earn Repeat" name="loyalty_earn_repeat" id="loyalty_earn_repeat">
                                    <span class="error badge text-danger" id="loyalty_earn_repeatError"></span>
                                </div> 
                                <div class="col-md-4">
                                    <label for="">Loyalty Use Percentage %  <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Loyalty Use Percentage" name="loyalty_use_percent" id="loyalty_use_percent">
                                    <span class="error badge text-danger" id="loyalty_use_percentError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Coupon Percentage %  <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Coupon Percentage" name="coupon_percent" id="coupon_percent">
                                    <span class="error badge text-danger" id="coupon_percentError"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Voucher Validity Days  </label>
                                    <input type="text" class="form-control" placeholder="Enter Voucher Validity Days" name="voucher_validity_days" id="voucher_validity_days">
                                </div>

                                <div class="col-md-4">
                                    <label for="">Enable Buy 1 Get 1 Free</label>
                                    <div class="d-flex align-items-center mt-1" style="gap:16px">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="enable_bogo" id="bogo_yes" value="1">
                                            <label class="form-check-label text-success" for="bogo_yes"><strong>YES – Enable BOGO</strong></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="enable_bogo" id="bogo_no" value="0" checked>
                                            <label class="form-check-label text-danger" for="bogo_no">NO</label>
                                        </div>
                                    </div>
                                    <small class="text-muted">When ON: cheapest item in cart (Frame/Glass/Goggles) will become ₹0</small>
                                </div>

                            </div>
                        </div>
                        
                       <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let cards = @json($cards);
    
    function openAddModal() {
    
        $('#modalTitle').text('Add New Membership Card');
    
        $('#cardForm')[0].reset();
        $('#card_id').val('');
        $('#formMethod').val('POST');
    
        $('#cardModal').modal('show');
    }
    $("#cardForm").submit(function(e)
    {
        e.preventDefault(); 
        
        let isValid = true;
        let class_name = '';
    
        document.querySelectorAll(".error").forEach(el => el.textContent = "");
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
        let card_name = document.getElementById("card_name" + class_name).value.trim();
        let price = document.getElementById("price" + class_name).value.trim();
        let validity_days = document.getElementById("validity_days" + class_name).value.trim();
        let loyalty_earn_first = document.getElementById("loyalty_earn_first" + class_name).value.trim();
        let loyalty_earn_repeat = document.getElementById("loyalty_earn_repeat" + class_name).value.trim();
        let loyalty_use_percent = document.getElementById("loyalty_use_percent" + class_name).value.trim();
        let coupon_percent = document.getElementById("coupon_percent" + class_name).value.trim();
    
    
        if (card_name=='') {
            document.getElementById("card_nameError" + class_name).textContent = "Card Name Required.";
            document.getElementById("card_name" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (price=='') {
            document.getElementById("priceError" + class_name).textContent = "Price required";
            document.getElementById("price" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (validity_days=='') {
            document.getElementById("validity_daysError" + class_name).textContent = "Validity Days required";
            document.getElementById("validity_days" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (loyalty_earn_first=='') {
            document.getElementById("loyalty_earn_firstError" + class_name).textContent = "Loyalty Earn First required";
            document.getElementById("loyalty_earn_first" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        
        if (loyalty_earn_repeat=='') {
            document.getElementById("loyalty_earn_repeatError" + class_name).textContent = "Loyalty Earn Repeat required";
            document.getElementById("loyalty_earn_repeat" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (loyalty_use_percent=='') {
            document.getElementById("loyalty_use_percentError" + class_name).textContent = "Loyalty Use Percentage required";
            document.getElementById("loyalty_use_percent" + class_name).classList.add("is-invalid");
            isValid = false;
        }
        
        if (coupon_percent=='') {
            document.getElementById("coupon_percentError" + class_name).textContent = "Coupon Percentage required";
            document.getElementById("coupon_percent" + class_name).classList.add("is-invalid");
            isValid = false;
        }
    
        if (!isValid) {
            return;
        }
    
        let form = $("#cardForm")[0];
        let data = new FormData(form);
    
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.membershipcard-add') }}",
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            success: function(response) {
            if ($.isEmptyObject(response.error)) {
                $.toaster({
                    priority: 'success',
                    title: response.success,
                    message: ''
                });
                location.reload();
            } else {
                document.querySelectorAll(".error").forEach(el => el.textContent = "");
                document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    
            }
        }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        });
    });
    
    
    function deleteCard(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete the membership!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
    
    
    function openEditModal(id) {

        let card = cards.find(c => c.card_id == id);
    
        $('#modalTitle').text('Edit Membership Card');
    
        $('#card_id').val(card.card_id);
        $('#card_name').val(card.card_name);
        $('#price').val(card.price);
        $('#validity_days').val(card.validity_days);
        $('#loyalty_earn_first').val(card.loyalty_earn_first);
        $('#loyalty_earn_repeat').val(card.loyalty_earn_repeat);
        $('#loyalty_use_percent').val(card.loyalty_use_percent);
        $('#coupon_percent').val(card.coupon_percent);
        $('#voucher_validity_days').val(card.voucher_validity_days);

        // Set BOGO radio
        let bogoVal = card.enable_bogo ? '1' : '0';
        $('input[name="enable_bogo"][value="' + bogoVal + '"]').prop('checked', true);
    
        $('.error').text('');
        $('.is-invalid').removeClass('is-invalid');
    
        $('#cardModal').modal('show');
    }
</script>

@endsection