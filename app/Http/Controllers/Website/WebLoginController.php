<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WebLoginController extends Controller
{
    /**
     * Show the login page (standalone full page).
     */
    public function login_web()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('website.auth.loginweb');
    }

    /**
     * Send OTP — find or create a web user by phone/email.
     * OTP is stored in session. For production, replace with SMS/Email API.
     */
    public function send_otp(Request $request)
    {
        if ($request->isMethod('get')) {
            if (session()->has('web_otp') && session()->has('web_login_id')) {
                return $this->otp_web($request);
            }
            return redirect()->route('login.web');
        }

        $request->validate([
            'email' => 'required|string',
        ]);

        $login_id   = trim($request->email);
        $login_type = filter_var($login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $otp        = '1234';

        // Store in session for verification
        session([
            'web_login_id'   => $login_id,
            'web_login_type' => $login_type,
            'web_otp'        => $otp,
            'web_otp_expiry' => now()->addMinutes(10)->timestamp,
        ]);

        $success = 'OTP Sent! For testing, use: 1234';

        return view('website.auth.otp-web', compact('login_type', 'login_id', 'otp', 'success'));
    }

    /**
     * Show OTP page directly (GET).
     */
    public function otp_web(Request $request)
    {
        $login_type = session('web_login_type', $request->input('login_type', ''));
        $login_id   = session('web_login_id',   $request->input('login_id', ''));
        $otp        = session('web_otp',         $request->input('generated_otp', ''));

        return view('website.auth.otp-web', compact('login_type', 'login_id', 'otp'));
    }

    /**
     * Verify OTP → find/create user → log them in → redirect.
     */
    public function verify_otp(Request $request)
    {
        $rawOtp = $request->input('otp');
        $submittedOtp = is_array($rawOtp) ? implode('', $rawOtp) : strval($rawOtp);

        $sessionOtp    = session('web_otp', '1234');
        $sessionLogin  = session('web_login_id', $request->input('login_id'));
        $sessionExpiry = session('web_otp_expiry');
        $loginType     = session('web_login_type', 'phone');

        // Check expiry
        if ($sessionExpiry && now()->timestamp > $sessionExpiry) {
            return back()->with('error', 'OTP has expired. Please request a new one.');
        }

        // Validate OTP
        if ($submittedOtp !== $sessionOtp && $submittedOtp !== '1234') {
            return back()->with('error', 'Invalid OTP. For testing, use 1234.');
        }

        // Clear OTP session
        session()->forget(['web_otp', 'web_otp_expiry']);

        // Find or create user
        $loginId = $sessionLogin;

        if ($loginType === 'email') {
            $user = User::where('email', $loginId)->first();
            if (!$user) {
                $user = User::create([
                    'staff_id'  => (string) rand(10000000, 99999999),
                    'name'      => explode('@', $loginId)[0],
                    'email'     => $loginId,
                    'password'  => Hash::make(uniqid()),
                    'user_type' => 'B2C',
                    'status'    => 1,
                ]);
            }
        } else {
            // Phone login
            $user = User::where('phone', $loginId)->first();

            if (!$user) {
                $user = User::create([
                    'staff_id'  => (string) rand(10000000, 99999999),
                    'name'      => 'Customer',
                    'phone'     => $loginId,
                    'email'     => $loginId . '@speckart.local',
                    'password'  => Hash::make(uniqid()),
                    'user_type' => 'B2C',
                    'status'    => 1,
                ]);
            }
        }

        // Log the user in via the web guard
        Auth::login($user, $request->boolean('remember'));

        // Redirect to intended URL or home
        $redirectTo = session()->pull('url.intended', route('home'));

        return redirect($redirectTo)->with('success', 'Welcome back, ' . ($user->name ?: 'Customer') . '!');
    }

    /**
     * Handle AJAX OTP verify from the Login Modal Popup.
     * Returns JSON instead of redirect.
     */
    public function verify_otp_ajax(Request $request)
    {
        $request->validate([
            'otp'      => 'required|string',
            'login_id' => 'required|string',
        ]);

        $sessionOtp    = session('web_otp');
        $sessionLogin  = session('web_login_id');
        $sessionExpiry = session('web_otp_expiry');
        $loginType     = session('web_login_type', 'phone');

        if ($sessionExpiry && now()->timestamp > $sessionExpiry) {
            return response()->json(['status' => 'error', 'message' => 'OTP expired. Please resend.'], 422);
        }

        if ($request->otp !== $sessionOtp || $request->login_id !== $sessionLogin) {
            return response()->json(['status' => 'error', 'message' => 'Invalid OTP. Try again.'], 422);
        }

        session()->forget(['web_otp', 'web_otp_expiry']);

        $loginId = $sessionLogin;

        if ($loginType === 'email') {
            $user = User::where('email', $loginId)->first();
            if (!$user) {
                $user = User::create([
                    'staff_id'  => (string) rand(10000000, 99999999),
                    'name'      => explode('@', $loginId)[0],
                    'email'     => $loginId,
                    'password'  => Hash::make(uniqid()),
                    'user_type' => 'B2C',
                    'status'    => 1,
                ]);
            }
        } else {
            $user = User::where('phone', $loginId)->first();
            if (!$user) {
                $user = User::create([
                    'staff_id'  => (string) rand(10000000, 99999999),
                    'name'      => 'Customer',
                    'phone'     => $loginId,
                    'email'     => $loginId . '@speckart.local',
                    'password'  => Hash::make(uniqid()),
                    'user_type' => 'B2C',
                    'status'    => 1,
                ]);
            }
        }

        Auth::login($user, false);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Logged in successfully!',
            'userName' => $user->name ?: 'Customer',
        ]);
    }

    /**
     * Send OTP via AJAX (for the Login Modal).
     */
    public function send_otp_ajax(Request $request)
    {
        $request->validate(['email' => 'required|string']);

        $loginId   = trim($request->email);
        $loginType = filter_var($loginId, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $otp       = '1234'; // Replace with real OTP logic

        session([
            'web_login_id'   => $loginId,
            'web_login_type' => $loginType,
            'web_otp'        => $otp,
            'web_otp_expiry' => now()->addMinutes(10)->timestamp,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'OTP sent! Use 1234 for testing.',
        ]);
    }

    /**
     * Logout the web user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully!');
    }
}
