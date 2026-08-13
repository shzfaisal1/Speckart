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
                            <p>Saved Prescriptions</p>
                            <div class="d-grid gap-3 px-4 pb-4">
                                <div class="prescription-card active">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Eye</th>
                                                <th>SPH</th>
                                                <th>Boxes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>L</td>
                                                <td>-3.75</td>
                                                <td>1</td>
                                            </tr>
                                            <tr>
                                                <td>R</td>
                                                <td>-2.50</td>
                                                <td>1</td>
                                            </tr>
                                            <tr>
                                                <td>p</td>
                                                <td>-2.50</td>
                                                <td>1</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="prescription-card">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Eye</th>
                                                <th>SPH</th>
                                                <th>Boxes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>L</td>
                                                <td>-3.75</td>
                                                <td>1</td>
                                            </tr>
                                            <tr>
                                                <td>R</td>
                                                <td>-2.50</td>
                                                <td>1</td>
                                            </tr>
                                           
                                        </tbody>
                                    </table>
                                </div>
                                <div class="prescription-card">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Eye</th>
                                                <th>SPH</th>
                                                <th>Boxes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <tr>
                                                <td>R</td>
                                                <td>-2.50</td>
                                                <td>1</td>
                                            </tr>
                                            <tr>
                                                <td>R</td>
                                                <td>-2.50</td>
                                                <td>1</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    @endsection
