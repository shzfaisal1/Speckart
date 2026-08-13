<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create Account | Speckarts</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('/admin/assets/images/favicon1.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fff;
        }

        .login-sec-left {
            padding: 40px 50px;
            width: 100%;
            max-width: 580px;
            margin: 0 auto;
        }

        .login-sec-left1 {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }

        .login-sec-left .login-sec-left-heading h4 {
            font-size: 38px;
            font-weight: 400;
            margin-bottom: 0;
            color: #333;
        }

        .login-sec-left .login-sec-left-heading h3 {
            color: #07484A;
            font-size: 44px;
            font-weight: 800;
        }

        .login-sec-left form {
            margin-top: 25px;
        }

        .login-sec-left form .form-control {
            border: 1px solid #000;
            height: 48px;
            border-radius: 4px;
            font-size: 14px;
        }

        .login-sec-left form .form-control:focus {
            border-color: #11ABB0;
            box-shadow: 0 0 0 0.2rem rgba(17, 171, 176, 0.25);
        }

        .login-sec-left form .form-label {
            font-weight: 500;
            font-size: 14px;
            color: #333;
            margin-bottom: 4px;
        }

        .login-sec-left .btn-submit {
            border-radius: 0;
            font-size: 15px;
            font-weight: 600;
            padding: 12px 40px;
            background: #11ABB0;
            color: #fff;
            border: none;
            transition: all 0.3s ease;
        }

        .login-sec-left .btn-submit:hover {
            background: #0d8a8e;
            color: #fff;
        }

        .login-sec-right1 {
            height: 100vh;
            position: sticky;
            top: 0;
            background: #f4fbfb;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-sec-right img {
            width: 100%;
            height: 100vh;
            object-fit: cover;
        }

        .login-link {
            color: #11ABB0;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <section class="login-sec">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-lg-6">
                    <div class="login-sec-left1">
                        <div class="login-sec-left">
                            <div class="login-sec-left-heading">
                                <h4>Create Account,</h4>
                                <h3>Join Speckarts!</h3>
                            </div>

                            @if(session('error'))
                                <div class="alert alert-danger mt-3 py-2 fs-6">{{ session('error') }}</div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger mt-3 py-2 fs-6">
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('register.web.post') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter 10-digit mobile number" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter email address" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Min 6 characters" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password" required>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mt-4">
                                    <button type="submit" class="btn btn-submit">Register Account</button>
                                </div>

                                <div class="mt-4 pt-2 border-top">
                                    <p class="mb-0 text-muted" style="font-size: 14px;">
                                        Already have an account? <a href="{{ route('login.web') }}" class="login-link">Sign In Here</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 d-none d-lg-block">
                    <div class="login-sec-right1">
                        <div class="login-sec-right w-100">
                            <img src="{{ asset('website/assets/img/bg/login.png') }}" alt="Speckarts Registration">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>

</html>
