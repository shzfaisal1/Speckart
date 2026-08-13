 @extends('web.layout.master')
    @section('content')
        <div class="power">
            <div class="container">
                <div class="row len text-center justify-content-center">
                    <div class="col-md-6 popup-body">
                        <div id="step1" class="step-content ">

                            <div class="icon-wrapper mx-auto mt-4 mb-3">
                                <div class="icon-circle">
                                    <img src="{{ asset('assets/img/productimg/mask1.png') }}" alt="">
                                </div>
                            </div>
                            <h5 class="fw-semibold mb-4">Want to add Eye Power?</h5>
                            <div class="d-grid gap-3 px-4 pb-4 hello">
                                <div class="d-grid gap-3 px-4 pb-4 hello">
                                    <a href="{{ url('/add-power-saved-prescription') }}">Use Saved Prescription</a>
                                    <a href="{{ url('/add-power-prescription-manually') }}">Enter Prescription Manually</a>
                                    <a href="{{ url('/shopping-cart') }}">Call me later for Eye Power</a>
                                </div>

                            </div>
                        </div>
                    </div>      
                </div>
               
            </div>
        </div>
    @endsection
