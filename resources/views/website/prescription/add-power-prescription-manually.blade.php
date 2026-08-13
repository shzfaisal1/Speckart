   @extends('website.layout.master')
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
                                <div class="container eye-form">
                                    <div class="row mb-3">
                                        <div class="col-md-6 text-center eye-heading">OS (LEFT EYE)</div>
                                        <div class="col-md-6 text-center eye-heading">OD (RIGHT EYE)</div>
                                    </div>

                                    <div class="row align-items-center mb-3">
                                        <div class="col-2 eye-label">boxes</div>
                                        <div class="col-md-5">
                                            <select class="form-select">
                                                <option>1 Box</option>
                                                <option>2 Boxes</option>
                                                <option>3 Boxes</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <select class="form-select">
                                                <option>1 Box</option>
                                                <option>2 Boxes</option>
                                                <option>3 Boxes</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row align-items-center">
                                        <div class="col-2 eye-label">sph</div>
                                        <div class="col-md-5">
                                            <select class="form-select">
                                                <option selected>please select</option>
                                                <option>-1.00</option>
                                                <option>-1.25</option>
                                                <option>-1.50</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <select class="form-select">
                                                <option selected>please select</option>
                                                <option>-1.00</option>
                                                <option>-1.25</option>
                                                <option>-1.50</option>
                                             </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endsection
