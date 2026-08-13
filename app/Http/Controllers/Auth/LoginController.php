<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    // protected function redirectTo()
    // {
    //     $user = Auth::user();
    //     if ($user->hasRole('Admin')) {
    //         return '/home';
    //     } else if ($user->hasRole('Customer')) {
    //         return '/dashboard';
    //     }

    //     return '/login'; // Default redirect
    // }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string|email',
        ]);
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check login throttle
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // Retrieve user by email
        $user = \App\Models\User::where($this->username(), $request->input($this->username()))->first();

        if ($user) 
        {
            // Login user directly without checking password
            Auth::login($user, $request->filled('remember'));

            $role = $user->roles[0]->name ?? null;
            $host = $request->getHost();
            $allowed = false;

            $subdomain = explode('.', $request->getHost())[0];

            if ($subdomain === 'franchise') {
                /*$checkStatus = \DB::table('tbl_client')->where('user_id', $user->id)->first()->status;
                if ($checkStatus != 0) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'User is inactive.please contact admin for further details.',
                    ]);
                }*/
                $allowed = true;
            } 
            elseif ($subdomain === 'speckart' && $role !== 'Customer') 
            {
                $allowed = true;
            }
            else
            {
                // Allow direct/localhost domains
                $allowed = true;
            }

            if (!$allowed) {
                Auth::logout(); // Logout user immediately
                return back()->withErrors([
                    'email' => 'Please enter valid email address',
                ]);
            }

            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        // Login failed
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }


    public function logout(Request $request)
    {
        if (session()->has('impersonate')) {
            $adminId = session('impersonate');
            session()->forget('impersonate');
    
            Auth::loginUsingId($adminId);
    
            return redirect()->back();
        }else{
            Auth::logout(); // Default logout method
            $request->session()->invalidate();
            $request->session()->regenerateToken();
    
            return redirect('/login'); // Redirect to login page
        }
    }
}
