@extends('website.layout.master')
@section('content')

<style>
    .account-info-section input, label, button{
        font-family: 'Poppins', sans-serif !important;
    }
    .account-info-section {
        padding: 40px 0 60px;
        background: #f4faf9;
    }
    .account-info-card {
        background: #fff;
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(7, 72, 74, 0.08);
        border: 1px solid #dcebea;
    }
    .account-info-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
    }
    .account-info-avatar {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        background: #07484A;
        color: #FAF59E;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        font-weight: 700;
    }
    .account-info-header h2 {
        color: #07484A;
        font-weight: 700;
        margin-bottom: 4px;
        font-size: 24px;
    }
    .account-info-header p {
        color: #7d8a8b;
        margin-bottom: 0;
        font-size: 14px;
    }
    .account-info-highlight {
        background: #e6f7f7;
        color: #07484A;
        padding: 14px 20px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 25px;
        border-left: 4px solid #00B9B9;
    }
    .custom-label {
        font-weight: 600;
        color: #07484A;
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
    }
    .input-wrapper {
        position: relative;
    }
    .input-wrapper i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #00B9B9;
        font-size: 15px;
    }
    .custom-input {
        width: 100%;
        padding: 12px 16px 12px 44px;
        border-radius: 12px;
        border: 1px solid #dcebea;
        outline: none;
        transition: 0.3s;
        font-size: 14px;
    }
    .custom-input:focus {
        border-color: #00B9B9;
        box-shadow: 0 0 0 3px rgba(0, 185, 185, 0.15);
    }
    .gender-selector {
        display: flex;
        gap: 15px;
    }
    .gender-selector input[type="radio"] {
        display: none;
    }
    .gender-selector label {
        padding: 12px 24px;
        border-radius: 12px;
        border: 1px solid #dcebea;
        background: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        color: #07484A;
        transition: 0.3s;
    }
    .gender-selector input[type="radio"]:checked + label {
        background: #07484A;
        color: #FAF59E;
        border-color: #07484A;
    }
    .save-btn {
        background: #07484A;
        color: #FAF59E;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        padding: 12px 30px;
        cursor: pointer;
        transition: 0.3s;
    }
    .save-btn:hover {
        background: #042e2f;
        color: #FAF59E;
    }
    .save-back-btn {
        background: #fff;
        color: #07484A;
        border: 1px solid #dcebea;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        padding: 12px 30px;
        display: inline-block;
        transition: 0.3s;
    }
    .save-back-btn:hover {
        background: #f4faf9;
        color: #07484A;
    }
</style>

<section class="account-info-section">
    <div class="container">

        <div class="account-info-card">

            <div class="account-info-header">
                <div class="account-info-avatar">
                    {{ strtoupper(substr($user->first_name ?: ($user->name ?: 'C'), 0, 1)) }}
                </div>

                <div>
                    <h2>Account Information</h2>
                    <p>Manage your personal details and preferences.</p>
                </div>
            </div>

            <div class="account-info-highlight">
                Keep your profile information updated for a better shopping experience.
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 rounded-3 p-3 mb-4" style="background:#e6fdf5; color:#0f5132;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-3 p-3 mb-4" style="background:#fdf2f2; color:#842029;">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('account-info.update') }}" method="POST">
                @csrf

                <div class="row g-4">

                    <div class="col-md-6">
                        <label class="custom-label">First Name *</label>

                        <div class="input-wrapper">
                            <i class="fa-solid fa-user"></i>

                            <input
                                type="text"
                                name="first_name"
                                class="custom-input"
                                value="{{ old('first_name', $user->first_name ?: $user->name) }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="custom-label">Last Name</label>

                        <div class="input-wrapper">
                            <i class="fa-solid fa-user"></i>

                            <input
                                type="text"
                                name="last_name"
                                class="custom-input"
                                value="{{ old('last_name', $user->last_name) }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="custom-label">Email Address *</label>

                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope"></i>

                            <input
                                type="email"
                                name="email"
                                class="custom-input"
                                value="{{ old('email', $user->email) }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="custom-label">Mobile Number</label>

                        <div class="input-wrapper">
                            <i class="fa-solid fa-phone"></i>

                            <input
                                type="text"
                                name="phone"
                                class="custom-input"
                                value="{{ old('phone', $user->phone ?: ($user->mobile ?? '')) }}"
                            >
                        </div>
                    </div>

                </div>

                <div class="mt-4">
                    <label class="custom-label mb-3">
                        Gender
                    </label>

                    <div class="gender-selector">

                        <input type="radio"
                            id="male"
                            name="gender"
                            value="male"
                            {{ old('gender', strtolower($user->gender ?? '')) == 'male' ? 'checked' : '' }}>

                        <label for="male">
                            <i class="fa-solid fa-person"></i>
                            <span>Male</span>
                        </label>

                        <input type="radio"
                            id="female"
                            name="gender"
                            value="female"
                            {{ old('gender', strtolower($user->gender ?? '')) == 'female' ? 'checked' : '' }}>

                        <label for="female">
                            <i class="fa-solid fa-venus"></i>
                            <span>Female</span>
                        </label>

                        <input type="radio"
                            id="other"
                            name="gender"
                            value="other"
                            {{ old('gender', strtolower($user->gender ?? '')) == 'other' ? 'checked' : '' }}>

                        <label for="other">
                            <i class="fa-solid fa-genderless"></i>
                            <span>Other</span>
                        </label>

                    </div>
                </div>

                <div class="mt-5 text-center">
                    <a href="{{ route('profile') }}" class="me-3 save-back-btn">
                        Back to Profile
                    </a>
                    <button type="submit" class="save-btn">
                        Save Changes
                    </button>
                </div>

           </form>

        </div>

    </div>
</section>
@endsection