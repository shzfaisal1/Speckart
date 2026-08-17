@extends('website.layout.master')
@section('content')


<!-- my-order-section -->
<section class="manage-notification-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="manage-notification-section-form">
                    <h3>Manage Notification</h3>
                    <form action="">
                        <div class="manage-notification-section-card">
                            <h4><img src="{{asset('assets/img/icon/wp.png')}}" alt=""> Whatsapp Notification</h4>
                            <div class="checkbox-wrapper-22">
                                <label class="switch" for="checkbox">
                                    <input type="checkbox" id="checkbox"/>
                                    <div class="slider round"></div>
                                </label>
                            </div>
                        </div>

                        <div class="manage-notification-section-card">
                            <h4><img src="{{asset('assets/img/icon/sms.png')}}" alt=""> SMS Notification</h4>
                            <div class="checkbox-wrapper-22">
                                <label class="switch" for="checkbox">
                                    <input type="checkbox" id="checkbox" />
                                    <div class="slider round"></div>
                                </label>
                            </div>
                        </div>

                        <div class="manage-notification-section-card">
                            <h4><img src="{{asset('assets/img/icon/notifi.png')}}" alt=""> Push Notification</h4>
                            <div class="checkbox-wrapper-22">
                                <label class="switch" for="checkbox">
                                    <input type="checkbox" id="checkbox" />
                                    <div class="slider round"></div>
                                </label>
                            </div>
                        </div>

                        <div class="manage-notification-section-card">
                            <h4><img src="{{asset('assets/img/icon/sms1.png')}}" alt=""> Email Notification</h4>
                            <div class="checkbox-wrapper-22">
                                <label class="switch" for="checkbox">
                                    <input type="checkbox" id="checkbox" />
                                    <div class="slider round"></div>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="account-information-section-img">
                    <img src="{{asset('assets/img/bg/Account-Information.png')}}" alt="">
                </div>
            </div>

        </div>
    </div>
</section>
<!-- end my-order-section -->














@endsection