<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('/admin/assets/images/favicon1.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick-theme.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        /* login-sec */
        .login-sec-left {
            padding: 0 30px 0 70px;
            width: 100%;
        }

        .login-sec-left1 {
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-sec-left .login-sec-left-heading h4 {
            font-size: 46px;
            font-weight: 400;
            margin-bottom: 0;
        }

        .login-sec-left .login-sec-left-heading h3 {
            color: #07484A;
            font-size: 52px;
            font-weight: 800;
        }

        .login-sec-left form {
            margin-top: 40px;
        }

        .login-sec-left form .form-control {
            border: 1px solid #000;
            height: 48px;
        }

        .login-sec-left form .form-check-label {
            color: #787878;
        }

        .login-sec-left form .form-check-input {
            border-color: #787878;
        }

        .login-sec-left .btn {
            border-radius: 0;
            font-size: 14px;
            padding: 10px 40px;
            margin-right: 10px;
            margin-top: 20px;
            background: #11ABB0;
            color: #fff;
        }

        .login-sec-left .btn:last-child {
            background: transparent;
            color: #000;
            border: 1px solid #000;
        }

        .login-sec-right {
            position: fixed;
            right: 0;
        }

        .login-sec-left .btn:hover {
            background: transparent;
            border-color: #11ABB0;
            color: #11ABB0;
        }

        .login-sec-left .btn:last-child:hover {
            background: #000;
            color: #fff;
            border-color: #000;
        }

        /* end login-sec */
    </style>

</head>

<body>

    <section class="login-sec">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login-sec-left1">
                        <div class="login-sec-left">
                            <div class="login-sec-left-heading">
                                <h4>Hello,</h4>
                                <h3>Welcome!</h3>
                            </div>
                            @if(session('success'))
                                <div class="alert alert-success mt-3">{{ session('success') }}</div>
                            @endif
                            <form action="{{ route('login.web.post') }}" method="POST">
                                @csrf
                                <div class="mb-3 mt-3">
                                    <label for="email" class="form-label">Enter Email Address / Mobile</label>
                                    <input type="text" class="form-control" id="email" name="email" required>
                                </div>

                                <div class="form-check mb-3">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="remember"> Remember me
                                    </label>
                                </div>

                                <div class="d-flex gap-2 flex-wrap align-items-center">
                                    <!-- This button submits the form -->
                                    <button type="submit" class="btn btn-primary">Continue</button>

                                    <!-- Register Button -->
                                    <a href="{{ route('register.web') }}" class="btn btn-secondary">Create Account</a>
                                </div>

                                <div class="mt-4 pt-3 border-top">
                                    <p class="mb-0 text-muted" style="font-size: 14px;">
                                        Don't have an account? <a href="{{ route('register.web') }}" style="color:#11ABB0; font-weight:600; text-decoration:none;">Register Now</a>
                                    </p>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login-sec-right1">
                        <div class="login-sec-right">
                            <img src="{{asset('assets/img/bg/login.png')}}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


</body>

</html>
