{{--
    Speckart Unified Auth Modal (Login + Register + OTP)
    ────────────────────────────────────────────────────
    Left Section: col-md-7 (Spacious interactive form for Login, Register, and OTP)
    Right Section: col-md-5 (Speckart brand artwork)
    Submits via AJAX — triggers 'speckartLoginSuccess' and refreshes.
--}}

<style>
/* ── Prevent Duplicate Scrollbars on Page & Modal ── */
html.modal-open,
body.modal-open {
    overflow: hidden !important;
    padding-right: 0 !important;
}

/* ── Ensure ALL Modals Sit Above Backdrops & Sticky Header ── */
.modal {
    z-index: 1055 !important;
}

.modal-backdrop {
    z-index: 1050 !important;
}

#speckartLoginModal {
    z-index: 1055 !important;
    overflow-x: hidden !important;
    overflow-y: auto !important;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}

#speckartLoginModal::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}

/* ── Modal Card Sizing & Architecture ── */
#speckartLoginModal .modal-dialog {
    max-width: 940px;
    margin: 1.75rem auto;
    z-index: 1056 !important;
}

#speckartLoginModal .modal-content {
    border: none;
    border-radius: 24px;
    box-shadow: 0 30px 80px -15px rgba(7, 72, 74, 0.35);
    overflow: hidden;
    background: #ffffff;
    position: relative;
    z-index: 1057 !important;
}

.auth-modal-left-pane {
    padding: 40px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: #ffffff;
    min-height: 520px;
}

.auth-modal-right-pane {
    background: #07484A;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: stretch;
    justify-content: stretch;
    min-height: 520px;
}

.auth-modal-right-pane img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
}

/* Close Button */
.auth-modal-close-btn {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid rgba(226, 232, 240, 0.8);
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    cursor: pointer;
    z-index: 30;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
    transition: all 0.2s ease;
}

.auth-modal-close-btn:hover {
    background: #f8fafc;
    color: #07484A;
    transform: scale(1.08);
}

/* Typography */
.auth-view-header {
    margin-bottom: 22px;
}

.auth-view-title-sm {
    font-size: 28px;
    font-weight: 400;
    color: #475569;
    margin: 0;
    line-height: 1.15;
}

.auth-view-title-main {
    font-size: 32px;
    font-weight: 800;
    color: #07484A;
    margin: 2px 0 6px;
    line-height: 1.15;
    letter-spacing: -0.5px;
}

.auth-view-subtitle {
    font-size: 13.5px;
    color: #64748b;
    margin: 0;
    line-height: 1.45;
}

/* Form Controls */
.auth-field-label {
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
    display: block;
}

.auth-field-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}

.auth-field-icon {
    position: absolute;
    left: 14px;
    color: #00B9B9;
    font-size: 16px;
    pointer-events: none;
    transition: color 0.2s;
}

.auth-field-input {
    width: 100%;
    height: 48px;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px 14px 10px 42px;
    font-size: 14px;
    color: #0f172a;
    background: #f8fafc;
    outline: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.auth-field-input.has-eye {
    padding-right: 42px;
}

.auth-field-input:focus {
    background: #ffffff;
    border-color: #00B9B9;
    box-shadow: 0 0 0 3px rgba(0, 185, 185, 0.15);
}

.auth-field-wrapper:focus-within .auth-field-icon {
    color: #07484A;
}

.auth-field-eye {
    position: absolute;
    right: 14px;
    color: #94a3b8;
    font-size: 16px;
    cursor: pointer;
    padding: 4px;
    transition: color 0.15s;
}

.auth-field-eye:hover {
    color: #07484A;
}

/* ── 4-Digit Segmented OTP Boxes ── */
.otp-boxes-group {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin: 20px 0 24px;
}

.otp-digit-box {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    border: 2px solid #cbd5e1;
    background: #f8fafc;
    text-align: center;
    font-size: 26px;
    font-weight: 800;
    color: #07484A;
    outline: none;
    transition: all 0.2s ease;
}

.otp-digit-box:focus {
    border-color: #00B9B9;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(0, 185, 185, 0.18);
    transform: translateY(-2px);
}

/* Primary Action Button */
.auth-submit-btn {
    width: 100%;
    height: 48px;
    background: linear-gradient(135deg, #07484A 0%, #0d5f63 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 6px 18px rgba(7, 72, 74, 0.25);
    transition: all 0.2s ease;
}

.auth-submit-btn:hover {
    background: linear-gradient(135deg, #032729 0%, #07484A 100%);
    box-shadow: 0 8px 24px rgba(7, 72, 74, 0.35);
    transform: translateY(-1px);
}

/* Secondary Switch Button */
.auth-switch-btn {
    width: 100%;
    height: 44px;
    background: #ffffff;
    color: #07484A !important;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.auth-switch-btn:hover {
    border-color: #07484A;
    background: #f8fafc;
}

/* Trust Badges */
.auth-trust-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    font-size: 12px;
    color: #94a3b8;
    margin-top: 16px;
    font-weight: 500;
}

.auth-trust-footer span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    #speckartLoginModal .modal-dialog {
        max-width: 540px;
        margin: 1rem auto;
    }
    .auth-modal-left-pane {
        padding: 30px 24px;
        min-height: auto;
    }
}
</style>

<!-- ══════════════════════════════════════════════════
     SPECKART AUTH MODAL (LOGIN & REGISTRATION)
════════════════════════════════════════════════════ -->
<div class="modal fade" id="speckartLoginModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Floating Close Button -->
            <button type="button" class="auth-modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="row g-0">

                <!-- ── Left Section: col-md-7 Form Pane ── -->
                <div class="col-12 col-md-7 d-flex flex-column justify-content-center">
                    <div class="auth-modal-left-pane">

                        <!-- ═════════════════════════════════════════
                             VIEW 1: SIGN IN
                        ══════════════════════════════════════════ -->
                        <div id="modal-step-1">
                            <div class="auth-view-header">
                                <h4 class="auth-view-title-sm">Hello,</h4>
                                <h3 class="auth-view-title-main">Welcome!</h3>
                                <p class="auth-view-subtitle">Enter your email or mobile to log in with OTP</p>
                            </div>

                            <div id="modal-login-error" class="alert alert-danger d-none py-2 px-3 rounded-3 mb-3" role="alert" style="font-size: 13px;"></div>

                            <div class="mb-3">
                                <label for="modal-login-input" class="auth-field-label">
                                    Email Address / Mobile <span class="text-danger">*</span>
                                </label>
                                <div class="auth-field-wrapper">
                                    <i class="bi bi-person-circle auth-field-icon"></i>
                                    <input type="text"
                                           id="modal-login-input"
                                           class="auth-field-input"
                                           placeholder="Enter email or 10-digit mobile"
                                           autocomplete="username"
                                           autofocus>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="modal-remember" checked>
                                <label class="form-check-label text-muted" for="modal-remember" style="font-size: 13px; cursor: pointer;">
                                    Remember this device
                                </label>
                            </div>

                            <div class="mb-3">
                                <button type="button" id="modal-send-otp-btn" class="auth-submit-btn">
                                    Continue with OTP <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>

                            <div class="text-center pt-3 border-top">
                                <p class="text-muted mb-2" style="font-size: 13px;">Don't have an account?</p>
                                <button type="button" id="btn-switch-to-register" class="auth-switch-btn">
                                    <i class="bi bi-person-plus"></i> Create New Account
                                </button>
                            </div>

                            <div class="auth-trust-footer">
                                <span><i class="bi bi-lock-fill text-muted"></i> 100% Privacy Ensured</span>
                                <span>•</span>
                                <span><i class="bi bi-shield-check text-muted"></i> Official Speckarts Store</span>
                            </div>
                        </div>

                        <!-- ═════════════════════════════════════════
                             VIEW 2: OTP VERIFICATION
                        ══════════════════════════════════════════ -->
                        <div id="modal-step-2" class="d-none">
                            <div class="auth-view-header">
                                <span class="badge px-3 py-1 mb-2 rounded-pill fw-semibold" style="background:#e6f7f7; color:#07484A; font-size:12px;">
                                    <i class="bi bi-shield-check"></i> Security Verification
                                </span>
                                <h3 class="auth-view-title-main" style="font-size: 26px;">Enter 4-Digit Code</h3>
                                <p class="auth-view-subtitle">
                                    Code sent to <strong id="modal-otp-sent-to" class="text-dark"></strong>
                                    <a href="javascript:void(0)" id="modal-edit-login-id" class="ms-1 text-decoration-none fw-semibold" style="color:#00B9B9; font-size:12.5px;">(Change)</a>
                                </p>
                            </div>

                            <div id="modal-otp-error" class="alert alert-danger d-none py-2 px-3 rounded-3 mb-3" role="alert" style="font-size: 13px;"></div>
                            <div id="modal-otp-success" class="alert alert-success d-none py-2 px-3 rounded-3 mb-3" role="alert" style="font-size: 13px;"></div>

                            <!-- 4 Segmented OTP Digit Boxes -->
                            <div class="otp-boxes-group">
                                <input type="text" maxlength="1" class="otp-digit-box" id="otp-digit-1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" autofocus>
                                <input type="text" maxlength="1" class="otp-digit-box" id="otp-digit-2" inputmode="numeric" pattern="[0-9]*">
                                <input type="text" maxlength="1" class="otp-digit-box" id="otp-digit-3" inputmode="numeric" pattern="[0-9]*">
                                <input type="text" maxlength="1" class="otp-digit-box" id="otp-digit-4" inputmode="numeric" pattern="[0-9]*">
                            </div>

                            <div class="mb-3">
                                <button type="button" id="modal-verify-otp-btn" class="auth-submit-btn">
                                    Verify & Sign In <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                <button type="button" id="modal-back-btn" class="btn btn-link text-decoration-none text-muted p-0 fw-semibold" style="font-size: 13px;">
                                    <i class="bi bi-arrow-left"></i> Change Details
                                </button>
                                <span class="text-muted" style="font-size: 13px;">
                                    Didn't receive? <a href="javascript:void(0)" id="modal-resend-otp" class="fw-bold" style="color: #00B9B9; text-decoration: none;">Resend OTP</a>
                                </span>
                            </div>

                            <div class="auth-trust-footer">
                                <span><i class="bi bi-lock-fill text-muted"></i> 100% Privacy Ensured</span>
                                <span>•</span>
                                <span><i class="bi bi-shield-check text-muted"></i> Official Speckarts Store</span>
                            </div>
                        </div>

                        <!-- ═════════════════════════════════════════
                             VIEW 3: CREATE ACCOUNT (REGISTRATION)
                        ══════════════════════════════════════════ -->
                        <div id="modal-step-register" class="d-none">
                            <div class="auth-view-header mb-3">
                                <h3 class="auth-view-title-main mb-1" style="font-size: 26px;">Create Account</h3>
                                <p class="auth-view-subtitle">Join Speckarts for exclusive frame deals & tracking</p>
                            </div>

                            <div id="modal-reg-error" class="alert alert-danger d-none py-2 px-3 rounded-3 mb-3" role="alert" style="font-size: 12.5px;"></div>

                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="modal-reg-name" class="auth-field-label">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <div class="auth-field-wrapper">
                                    <i class="bi bi-person auth-field-icon"></i>
                                    <input type="text"
                                           id="modal-reg-name"
                                           class="auth-field-input"
                                           placeholder="Enter your full name">
                                </div>
                            </div>

                            <!-- Mobile & Email Row -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label for="modal-reg-phone" class="auth-field-label">
                                        Mobile <span class="text-danger">*</span>
                                    </label>
                                    <div class="auth-field-wrapper">
                                        <i class="bi bi-telephone auth-field-icon"></i>
                                        <input type="tel"
                                               id="modal-reg-phone"
                                               class="auth-field-input"
                                               placeholder="10-digit mobile"
                                               maxlength="15">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="modal-reg-email" class="auth-field-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="auth-field-wrapper">
                                        <i class="bi bi-envelope auth-field-icon"></i>
                                        <input type="email"
                                               id="modal-reg-email"
                                               class="auth-field-input"
                                               placeholder="name@example.com">
                                    </div>
                                </div>
                            </div>

                            <!-- Password & Confirm Password Row -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label for="modal-reg-password" class="auth-field-label">
                                        Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="auth-field-wrapper">
                                        <i class="bi bi-lock auth-field-icon"></i>
                                        <input type="password"
                                               id="modal-reg-password"
                                               class="auth-field-input has-eye"
                                               placeholder="Min 6 characters">
                                        <i class="bi bi-eye auth-field-eye" data-target="modal-reg-password"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="modal-reg-password-confirm" class="auth-field-label">
                                        Confirm Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="auth-field-wrapper">
                                        <i class="bi bi-shield-check auth-field-icon"></i>
                                        <input type="password"
                                               id="modal-reg-password-confirm"
                                               class="auth-field-input has-eye"
                                               placeholder="Re-enter password">
                                        <i class="bi bi-eye auth-field-eye" data-target="modal-reg-password-confirm"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Action -->
                            <div class="mb-3">
                                <button type="button" id="modal-register-btn" class="auth-submit-btn">
                                    Create Account <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>

                            <!-- Already have an account? Sign In -->
                            <div class="text-center pt-3 border-top">
                                <p class="text-muted mb-2" style="font-size: 13px;">Already have an account?</p>
                                <button type="button" id="btn-switch-to-login" class="auth-switch-btn">
                                    <i class="bi bi-person"></i> Sign In Here
                                </button>
                            </div>

                            <!-- Trust Badges -->
                            <div class="auth-trust-footer">
                                <span><i class="bi bi-lock-fill text-muted"></i> 100% Privacy Ensured</span>
                                <span>•</span>
                                <span><i class="bi bi-shield-check text-muted"></i> Official Speckarts Store</span>
                            </div>
                        </div>

                        <!-- ═════════════════════════════════════════
                             VIEW 4: SUCCESS STEP
                        ══════════════════════════════════════════ -->
                        <div id="modal-step-success" class="d-none text-center py-4">
                            <div style="font-size: 54px; color: #00B9B9;">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <h4 class="fw-bold mt-2" style="color: #07484A;">Welcome to Speckarts!</h4>
                            <p class="text-muted" id="modal-success-name"></p>
                        </div>

                    </div>
                </div>

                <!-- ── Right Section: col-md-5 Visual Showcase Pane ── -->
                <div class="col-md-5 d-none d-md-block auth-modal-right-pane">
                    <img src="{{ asset('website/assets/img/bg/login.png') }}"
                         alt="Speckarts Eyewear"
                         onerror="this.onerror=null; this.src='{{ asset('website/assets/img/bg/auth-banner.jpg') }}';">
                </div>

            </div><!-- /.row -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- ══ END AUTH MODAL ═════════════════════════════════════ -->


<!-- ══════════════════════════════════════════════════
     AUTH MODAL JAVASCRIPT
════════════════════════════════════════════════════ -->
<script>
(function () {
    'use strict';

    var csrfToken = document.querySelector('meta[name="csrf-token"]')
                   ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                   : '';

    var currentLoginId = '';

    // ── Password Eye Toggle ───────────────────────────
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('auth-field-eye')) {
            var targetId = e.target.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    e.target.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    e.target.classList.replace('bi-eye-slash', 'bi-eye');
                }
            }
        }
    });

    // ── Switch between Login and Register Views ───────
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.id === 'btn-switch-to-register' || e.target.closest('#btn-switch-to-register'))) {
            showRegisterView();
        }
        if (e.target && (e.target.id === 'btn-switch-to-login' || e.target.closest('#btn-switch-to-login'))) {
            showLoginView();
        }
    });

    function showRegisterView() {
        document.getElementById('modal-step-1').classList.add('d-none');
        document.getElementById('modal-step-2').classList.add('d-none');
        document.getElementById('modal-step-register').classList.remove('d-none');
        document.getElementById('modal-step-success').classList.add('d-none');
    }

    function showLoginView() {
        document.getElementById('modal-step-register').classList.add('d-none');
        document.getElementById('modal-step-2').classList.add('d-none');
        document.getElementById('modal-step-1').classList.remove('d-none');
        document.getElementById('modal-step-success').classList.add('d-none');
    }

    // ── Send OTP (Login) ──────────────────────────────
    document.addEventListener('click', function (e) {
        if (e.target && (e.target.id === 'modal-send-otp-btn' || e.target.closest('#modal-send-otp-btn'))) {
            sendOtp();
        }
        if (e.target && (e.target.id === 'modal-register-btn' || e.target.closest('#modal-register-btn'))) {
            registerUser();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            var step1 = document.getElementById('modal-step-1');
            var step2 = document.getElementById('modal-step-2');
            var stepReg = document.getElementById('modal-step-register');
            if (step1 && !step1.classList.contains('d-none')) sendOtp();
            if (step2 && !step2.classList.contains('d-none')) verifyOtp();
            if (stepReg && !stepReg.classList.contains('d-none')) registerUser();
        }
    });

    function sendOtp() {
        var input = document.getElementById('modal-login-input');
        var loginId = input ? input.value.trim() : '';
        var errBox = document.getElementById('modal-login-error');

        if (!loginId) {
            showError(errBox, 'Please enter your email or mobile number.');
            if (input) input.focus();
            return;
        }

        currentLoginId = loginId;

        var btn = document.getElementById('modal-send-otp-btn');
        btn.disabled = true;
        btn.innerHTML = 'Sending… <i class="bi bi-arrow-repeat"></i>';
        hideError(errBox);

        fetch('{{ route("login.web.otp.ajax") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ email: loginId }),
        })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.innerHTML = 'Continue with OTP <i class="bi bi-arrow-right"></i>';
            if (data.status === 'success') {
                document.getElementById('modal-step-1').classList.add('d-none');
                document.getElementById('modal-step-2').classList.remove('d-none');
                document.getElementById('modal-otp-sent-to').textContent = loginId;
                clearOtpBoxes();
                var firstBox = document.getElementById('otp-digit-1');
                if (firstBox) firstBox.focus();
            } else {
                showError(errBox, data.message || 'Account not registered. Please create an account.');
                if (typeof toastr !== 'undefined') {
                    toastr.error(data.message || 'Account not registered. Please register first.');
                }
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = 'Continue with OTP <i class="bi bi-arrow-right"></i>';
            showError(errBox, 'Network error. Please try again.');
        });
    }

    // ── OTP Boxes Auto-Advance & Key Navigation ───────
    var otpBoxIds = ['otp-digit-1', 'otp-digit-2', 'otp-digit-3', 'otp-digit-4'];
    otpBoxIds.forEach(function(id, idx) {
        var el = document.getElementById(id);
        if (!el) return;

        el.addEventListener('input', function(e) {
            var val = e.target.value.replace(/\D/g, '');
            e.target.value = val ? val.slice(-1) : '';
            if (val && idx < 3) {
                var next = document.getElementById(otpBoxIds[idx + 1]);
                if (next) next.focus();
            }
            var code = getOtpCode();
            if (code.length === 4) {
                verifyOtp(code);
            }
        });

        el.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !el.value && idx > 0) {
                var prev = document.getElementById(otpBoxIds[idx - 1]);
                if (prev) {
                    prev.focus();
                    prev.value = '';
                }
            }
        });

        el.addEventListener('paste', function(e) {
            e.preventDefault();
            var pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 4);
            for (var i = 0; i < pasteData.length; i++) {
                var target = document.getElementById(otpBoxIds[i]);
                if (target) target.value = pasteData[i];
            }
            if (pasteData.length > 0) {
                var targetIdx = Math.min(pasteData.length, 3);
                var focusTarget = document.getElementById(otpBoxIds[targetIdx]);
                if (focusTarget) focusTarget.focus();
            }
            var code = getOtpCode();
            if (code.length === 4) {
                verifyOtp(code);
            }
        });
    });

    function getOtpCode() {
        return otpBoxIds.map(function(id) {
            var el = document.getElementById(id);
            return el ? el.value : '';
        }).join('');
    }

    function clearOtpBoxes() {
        otpBoxIds.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.value = '';
        });
    }

    // ── Verify OTP (Login) ────────────────────────────
    document.addEventListener('click', function (e) {
        if (e.target && (e.target.id === 'modal-verify-otp-btn' || e.target.closest('#modal-verify-otp-btn'))) {
            verifyOtp(getOtpCode());
        }
        if (e.target && (e.target.id === 'modal-back-btn' || e.target.closest('#modal-back-btn') || e.target.id === 'modal-edit-login-id')) {
            showLoginView();
        }
        if (e.target && e.target.id === 'modal-resend-otp') {
            sendOtp();
        }
    });

    function verifyOtp(otp) {
        otp = otp || getOtpCode();
        var errBox = document.getElementById('modal-otp-error');

        if (!otp || otp.length < 4) {
            showError(errBox, 'Please enter the complete 4-digit code.');
            return;
        }

        var btn = document.getElementById('modal-verify-otp-btn');
        btn.disabled = true;
        btn.innerHTML = 'Verifying… <i class="bi bi-arrow-repeat"></i>';
        hideError(errBox);

        fetch('{{ route("login.web.verify.ajax") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ otp: otp, login_id: currentLoginId }),
        })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.innerHTML = 'Verify & Sign In <i class="bi bi-arrow-right"></i>';

            if (data.status === 'success') {
                document.getElementById('modal-step-2').classList.add('d-none');
                document.getElementById('modal-step-success').classList.remove('d-none');
                document.getElementById('modal-success-name').textContent = 'Welcome, ' + (data.userName || 'Customer') + '!';

                if (typeof toastr !== 'undefined') {
                    toastr.success('Welcome back, ' + (data.userName || 'Customer') + '!');
                }

                window.dispatchEvent(new CustomEvent('speckartLoginSuccess', {
                    detail: { userName: data.userName }
                }));

                setTimeout(function() {
                    window.location.reload();
                }, 600);

            } else {
                showError(errBox, data.message || 'Invalid OTP. Please try again.');
                clearOtpBoxes();
                var first = document.getElementById('otp-digit-1');
                if (first) first.focus();
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = 'Verify & Sign In <i class="bi bi-arrow-right"></i>';
            showError(errBox, 'Network error. Please try again.');
        });
    }

    // ── AJAX Registration ─────────────────────────────
    function registerUser() {
        var nameInput    = document.getElementById('modal-reg-name');
        var phoneInput   = document.getElementById('modal-reg-phone');
        var emailInput   = document.getElementById('modal-reg-email');
        var passInput    = document.getElementById('modal-reg-password');
        var passConfInput= document.getElementById('modal-reg-password-confirm');
        var errBox       = document.getElementById('modal-reg-error');

        var name     = nameInput ? nameInput.value.trim() : '';
        var phone    = phoneInput ? phoneInput.value.trim() : '';
        var email    = emailInput ? emailInput.value.trim() : '';
        var password = passInput ? passInput.value : '';
        var password_confirmation = passConfInput ? passConfInput.value : '';

        if (!name) {
            showError(errBox, 'Please enter your full name.');
            if (nameInput) nameInput.focus();
            return;
        }
        if (!phone) {
            showError(errBox, 'Please enter your mobile number.');
            if (phoneInput) phoneInput.focus();
            return;
        }
        if (!email) {
            showError(errBox, 'Please enter your email address.');
            if (emailInput) emailInput.focus();
            return;
        }
        if (!password || password.length < 6) {
            showError(errBox, 'Password must be at least 6 characters.');
            if (passInput) passInput.focus();
            return;
        }
        if (password !== password_confirmation) {
            showError(errBox, 'Passwords do not match.');
            if (passConfInput) passConfInput.focus();
            return;
        }

        var btn = document.getElementById('modal-register-btn');
        btn.disabled = true;
        btn.innerHTML = 'Creating Account… <i class="bi bi-arrow-repeat"></i>';
        hideError(errBox);

        fetch('{{ route("register.web.ajax") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept':       'application/json',
            },
            body: JSON.stringify({
                name: name,
                phone: phone,
                email: email,
                password: password,
                password_confirmation: password_confirmation
            }),
        })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.innerHTML = 'Create Account <i class="bi bi-arrow-right"></i>';

            if (data.status === 'success') {
                document.getElementById('modal-step-register').classList.add('d-none');
                document.getElementById('modal-step-success').classList.remove('d-none');
                document.getElementById('modal-success-name').textContent = 'Account created for ' + (data.userName || name) + '!';

                if (typeof toastr !== 'undefined') {
                    toastr.success('Welcome to Speckarts, ' + (data.userName || name) + '!');
                }

                window.dispatchEvent(new CustomEvent('speckartLoginSuccess', {
                    detail: { userName: data.userName }
                }));

                setTimeout(function() {
                    window.location.reload();
                }, 600);

            } else {
                showError(errBox, data.message || 'Registration failed. Please check your details.');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = 'Create Account <i class="bi bi-arrow-right"></i>';
            showError(errBox, 'Network error. Please try again.');
        });
    }

    function resendOtp() {
        document.getElementById('modal-step-2').classList.add('d-none');
        document.getElementById('modal-step-1').classList.remove('d-none');
    }

    function goBackToStep1() {
        document.getElementById('modal-step-2').classList.add('d-none');
        document.getElementById('modal-step-1').classList.remove('d-none');
    }

    function resetModal() {
        document.getElementById('modal-step-1').classList.remove('d-none');
        document.getElementById('modal-step-2').classList.add('d-none');
        document.getElementById('modal-step-register').classList.add('d-none');
        document.getElementById('modal-step-success').classList.add('d-none');
        var inp = document.getElementById('modal-login-input');
        if (inp) inp.value = '';
        var otp = document.getElementById('modal-otp-input');
        if (otp) otp.value = '';
        hideError(document.getElementById('modal-login-error'));
        hideError(document.getElementById('modal-otp-error'));
        hideError(document.getElementById('modal-reg-error'));
    }

    // ── Helpers ──────────────────────────────────────
    function showError(el, msg) {
        if (!el) return;
        el.textContent = msg;
        el.classList.remove('d-none');
    }
    function hideError(el) {
        if (!el) return;
        el.classList.add('d-none');
        el.textContent = '';
    }

    // ── Prevent Double Scrollbar on Body/HTML ────────
    var modalEl = document.getElementById('speckartLoginModal');
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function () {
            document.documentElement.classList.add('modal-open');
            document.body.classList.add('modal-open');
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
        });
        modalEl.addEventListener('hidden.bs.modal', function () {
            document.documentElement.classList.remove('modal-open');
            document.body.classList.remove('modal-open');
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
            showLoginView();
        });
    }
})();
</script>
