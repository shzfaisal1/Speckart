<!DOCTYPE html>
<html lang="en">

<head>
    <title>Spectkart-Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('admin/assets/images/favicon1.png') }}" type="image/x-icon">
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
            margin-top: 40px;
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

        /* otp start */
        .otp-container {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .otp-input {
            width: 60px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            border: 1.5px solid #b0b0b0;
            border-radius: 10px;
            outline: none;
            transition: 0.3s;
        }

        .otp-input:focus {
            border-color: #009688;
            box-shadow: 0 0 5px rgba(0, 150, 136, 0.3);
        }

        .resend {
            color: #009688;
            font-weight: 500;
            text-decoration: none;
            font-size: 16px;
            transition: 0.3s;
        }

        .resend:hover {
            text-decoration: underline;
        }

        /* otp end */
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
                                <h4>Enter</h4>
                                <h3>Your OTP !</h3>
                                <p>Please enter the one-time password (OTP) that was sent to your mobile number via SMS</p>
                            </div>

                            {{-- Testing OTP Notice Banner --}}
                            <div class="alert alert-info border-0 shadow-sm rounded-3 mt-3 py-2 px-3 d-flex align-items-center gap-2" style="background:#e0f7fa; color:#00695c; font-size: 14px;">
                                <i class="fa fa-info-circle fs-5"></i>
                                <span><strong>Testing OTP:</strong> Enter <mark class="px-2 py-1 bg-white fw-bold rounded border" style="font-size:16px; color:#004d40;">{{ $otp ?? session('web_otp') ?? '1234' }}</mark> to log in.</span>
                            </div>

                            @if(isset($success))
                                <div class="alert alert-success mt-3">{{ $success }}</div>
                            @elseif(session('success'))
                                <div class="alert alert-success mt-3">{{ session('success') }}</div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                            @endif
                            <form action="{{ route('otp.web.post') }}" method="POST">
                                @csrf
                                <input type="hidden" name="login_type" value="{{ $login_type ?? '' }}">
                                <input type="hidden" name="login_id" value="{{ $login_id ?? '' }}">
                                <input type="hidden" name="generated_otp" value="{{ $otp ?? '' }}">

                                <div class="otp-container">
                                    <input type="text" name="otp[]" maxlength="1" class="otp-input" required autofocus>
                                    <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                                    <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                                    <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                                </div>

                                <div class="d-flex align-items-center justify-content-between my-2">
                                    <a href="{{ route('login.web') }}" class="resend" style="position: static;">Resend OTP</a>
                                </div>

                                <!-- This button submits the form -->
                                <button type="submit" class="btn btn-primary">Continue</button>

                                <!-- This button opens the link -->
                                <a href="{{ route('login.web') }}" class="btn btn-secondary">Back to Login</a>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login-sec-right1">
                        <div class="login-sec-right">
                            <img src="{{asset('website/assets/img/bg/login.png')}}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>

</body>

</html>
