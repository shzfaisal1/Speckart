{{--
    Login Modal Popup
    ─────────────────
    Triggered when a guest user clicks ❤️ (Wishlist) or any auth-required action.
    Mirrors the exact design of loginweb.blade.php (Hello, Welcome! style).
    Submits via AJAX — no page reload. On success fires JS event 'speckartLoginSuccess'.
--}}

<!-- ══════════════════════════════════════════════════
     SPECKART LOGIN MODAL
════════════════════════════════════════════════════ -->
<div class="modal fade" id="speckartLoginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:860px;">
        <div class="modal-content border-0 rounded-4 overflow-hidden" style="min-height:420px;">

            <!-- Close button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-3"
                    data-bs-dismiss="modal" aria-label="Close"
                    style="background-color:#fff; border-radius:50%; padding:8px; box-shadow:0 2px 8px rgba(0,0,0,.15); z-index:10;"></button>

            <div class="row g-0 h-100">

                <!-- ── Left: Form Side ── -->
                <div class="col-lg-6 d-flex align-items-center">
                    <div class="p-4 p-lg-5 w-100">

                        <!-- Step 1: Phone / Email Input -->
                        <div id="modal-step-1">
                            <div class="mb-4">
                                <h4 class="fw-400 mb-0" style="font-size:32px; font-weight:400;">Hello,</h4>
                                <h3 style="color:#07484A; font-size:36px; font-weight:800;">Welcome!</h3>
                            </div>

                            <div id="modal-login-error" class="alert alert-danger d-none py-2" role="alert"></div>

                            <div class="mb-3">
                                <label for="modal-login-input" class="form-label text-muted" style="font-size:13px;">
                                    Enter Email Address / Mobile
                                </label>
                                <input type="text"
                                       id="modal-login-input"
                                       class="form-control"
                                       placeholder="Enter Email Address / Mobile"
                                       style="border:1px solid #000; height:48px; border-radius:6px; font-size:15px;">
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="modal-remember">
                                <label class="form-check-label text-muted" for="modal-remember" style="font-size:13px;">
                                    Remember me
                                </label>
                            </div>

                            <div class="d-flex gap-2 flex-wrap mt-3">
                                <button id="modal-send-otp-btn"
                                        class="btn px-4 py-2"
                                        style="background:#11ABB0; color:#fff; border-radius:0; font-size:14px; border:none;">
                                    Continue
                                </button>
                                <a href="{{ route('login.web') }}"
                                   class="btn px-4 py-2"
                                   style="background:transparent; color:#000; border:1px solid #000; border-radius:0; font-size:14px;">
                                    Login with Password
                                </a>
                            </div>
                        </div>

                        <!-- Step 2: OTP Input (hidden initially) -->
                        <div id="modal-step-2" class="d-none">
                            <div class="mb-4">
                                <h4 class="fw-400 mb-0" style="font-size:28px; font-weight:400;">Verify OTP</h4>
                                <p class="text-muted mt-1" style="font-size:13px;">
                                    OTP sent to <strong id="modal-otp-sent-to"></strong>
                                </p>
                            </div>

                            <div id="modal-otp-error" class="alert alert-danger d-none py-2" role="alert"></div>
                            <div id="modal-otp-success" class="alert alert-success d-none py-2" role="alert"></div>

                            <div class="mb-3">
                                <label for="modal-otp-input" class="form-label text-muted" style="font-size:13px;">
                                    Enter 4-digit OTP
                                </label>
                                <input type="text"
                                       id="modal-otp-input"
                                       class="form-control"
                                       placeholder="_ _ _ _"
                                       maxlength="4"
                                       style="border:1px solid #000; height:48px; border-radius:6px; font-size:22px; letter-spacing:8px; text-align:center;">
                            </div>

                            <div class="d-flex gap-2 flex-wrap mt-3">
                                <button id="modal-verify-otp-btn"
                                        class="btn px-4 py-2"
                                        style="background:#11ABB0; color:#fff; border-radius:0; font-size:14px; border:none;">
                                    Verify & Login
                                </button>
                                <button id="modal-back-btn"
                                        class="btn px-4 py-2"
                                        style="background:transparent; color:#000; border:1px solid #000; border-radius:0; font-size:14px;">
                                    ← Back
                                </button>
                            </div>

                            <p class="mt-3 text-muted" style="font-size:12px;">
                                Didn't receive OTP?
                                <a href="javascript:void(0)" id="modal-resend-otp" style="color:#11ABB0;">Resend</a>
                            </p>
                        </div>

                        <!-- Step 3: Success (briefly shown before modal closes) -->
                        <div id="modal-step-success" class="d-none text-center py-4">
                            <div style="font-size:60px; color:#11ABB0;">✓</div>
                            <h5 class="fw-bold mt-2">Logged in!</h5>
                            <p class="text-muted" id="modal-success-name"></p>
                        </div>

                    </div>
                </div>

                <!-- ── Right: Image Side (same as login page) ── -->
                <div class="col-lg-6 d-none d-lg-block position-relative" style="background:#e0f7fa; overflow:hidden; min-height:420px;">
                    <img src="{{ asset('website/assets/img/bg/login.png') }}"
                         alt="Speckart Login"
                         style="width:100%; height:100%; object-fit:cover; object-position:center;">
                </div>

            </div><!-- /.row -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- ══ END LOGIN MODAL ════════════════════════════════════ -->


<!-- ══════════════════════════════════════════════════
     LOGIN MODAL JAVASCRIPT
════════════════════════════════════════════════════ -->
<script>
(function () {
    'use strict';

    var csrfToken = document.querySelector('meta[name="csrf-token"]')
                   ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                   : '';

    var currentLoginId = '';

    // ── Send OTP ──────────────────────────────────────
    document.addEventListener('click', function (e) {
        if (e.target && e.target.id === 'modal-send-otp-btn') {
            sendOtp();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            var step1 = document.getElementById('modal-step-1');
            var step2 = document.getElementById('modal-step-2');
            if (step1 && !step1.classList.contains('d-none')) sendOtp();
            if (step2 && !step2.classList.contains('d-none')) verifyOtp();
        }
    });

    function sendOtp() {
        var input = document.getElementById('modal-login-input');
        var loginId = input ? input.value.trim() : '';
        var errBox = document.getElementById('modal-login-error');

        if (!loginId) {
            showError(errBox, 'Please enter your email or mobile number.');
            return;
        }

        currentLoginId = loginId;

        var btn = document.getElementById('modal-send-otp-btn');
        btn.disabled = true;
        btn.textContent = 'Sending…';
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
            btn.textContent = 'Continue';
            if (data.status === 'success') {
                // Show Step 2
                document.getElementById('modal-step-1').classList.add('d-none');
                document.getElementById('modal-step-2').classList.remove('d-none');
                document.getElementById('modal-otp-sent-to').textContent = loginId;
                var otpInput = document.getElementById('modal-otp-input');
                if (otpInput) otpInput.focus();
            } else {
                showError(errBox, data.message || 'Failed to send OTP. Try again.');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.textContent = 'Continue';
            showError(errBox, 'Network error. Please try again.');
        });
    }

    // ── Verify OTP ───────────────────────────────────
    document.addEventListener('click', function (e) {
        if (e.target && e.target.id === 'modal-verify-otp-btn') verifyOtp();
        if (e.target && e.target.id === 'modal-back-btn') goBackToStep1();
        if (e.target && e.target.id === 'modal-resend-otp') resendOtp();
    });

    function verifyOtp() {
        var otpInput = document.getElementById('modal-otp-input');
        var otp = otpInput ? otpInput.value.trim() : '';
        var errBox = document.getElementById('modal-otp-error');

        if (!otp || otp.length < 4) {
            showError(errBox, 'Please enter the 4-digit OTP.');
            return;
        }

        var btn = document.getElementById('modal-verify-otp-btn');
        btn.disabled = true;
        btn.textContent = 'Verifying…';
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
            btn.textContent = 'Verify & Login';

            if (data.status === 'success') {
                // Show success step briefly
                document.getElementById('modal-step-2').classList.add('d-none');
                document.getElementById('modal-step-success').classList.remove('d-none');
                document.getElementById('modal-success-name').textContent = 'Welcome, ' + (data.userName || 'Customer') + '!';

                // Fire custom event so page JS can react (e.g., save pending wishlist item)
                window.dispatchEvent(new CustomEvent('speckartLoginSuccess', {
                    detail: { userName: data.userName }
                }));

                // Close modal after 1.2s
                setTimeout(function() {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('speckartLoginModal'));
                    if (modal) modal.hide();

                    // Reset modal state for next open
                    resetModal();
                }, 1200);

            } else {
                showError(errBox, data.message || 'Invalid OTP. Try again.');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.textContent = 'Verify & Login';
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
        document.getElementById('modal-step-success').classList.add('d-none');
        var inp = document.getElementById('modal-login-input');
        if (inp) inp.value = '';
        var otp = document.getElementById('modal-otp-input');
        if (otp) otp.value = '';
        hideError(document.getElementById('modal-login-error'));
        hideError(document.getElementById('modal-otp-error'));
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
})();
</script>
