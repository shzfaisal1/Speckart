<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'client_name' => ['required', 'string', 'max:255'],
            'comapny_name' => ['required', 'string', 'max:255'],
            'mobile_no' => ['required', 'string', 'min:10', 'max:10', 'unique:users,phone'],
            'email_id' => ['required', 'string', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:6', 'same:password'],
        ]);
    }
    public function showRegistrationForm()
    {
        $subdomain = explode('.', request()->getHost())[0];
        if ($subdomain === 'app' || $subdomain === 'ad') {
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->hasRole('Admin')) {
                    return redirect('/');
                } else if ($user->hasRole('Customer')) {
                    return redirect('/');
                }
            }
        }
        return view('frontend.register');
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $input = $data;
        $client_unique_id = 'C' . time() . rand('111', '999');

        $password = Hash::make($input['password']);
        $uu['name'] = $input['client_name'];
        $uu['email'] = $input['email_id'];
        $uu['password'] = $password;
        $uu['company_name'] = $input['comapny_name'];
        $uu['phone'] = $input['mobile_no'];

        $user = User::create($uu);
        $user->assignRole('Customer');


        \DB::table("tbl_client")->insert(
            [
                "user_id" => $user->id,
                "client_type" => 'Customer',
                "client_unique_id" => $client_unique_id,
                "client_name" => $input['client_name'],
                "comapny_name" => $input['comapny_name'],
                "email_id" => $input['email_id'],
                "mobile_no" => $input['mobile_no'],
                "password" => $password,
                "status" => "0",
            ]
        );
        return redirect()->back()->with('success', 'Client added successfully!');
    }

    protected function createUser(Request $request)
    {
        $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'comapny_name' => ['required', 'string', 'max:255'],
            'mobile_no' => ['required', 'string', 'min:10', 'max:10', 'unique:users,phone'],
            'email_id' => ['required', 'string', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:6', 'same:password'],
        ]);
        $input = $request->all();

        $password = Hash::make($input['password']);
        $uu['name'] = $input['client_name'];
        $uu['email'] = $input['email_id'];
        $uu['password'] = $password;
        $uu['company_name'] = $input['comapny_name'];
        $uu['phone'] = $input['mobile_no'];

        $user = User::create($uu);
        $user->assignRole('Customer');

        $client_unique_id = substr('C' . rand(11, 99) . $user->id . time(), 0, 8);
        \DB::table("tbl_client")->insert(
            [
                "user_id" => $user->id,
                "client_type" => 'Customer',
                "client_unique_id" => $client_unique_id,
                "client_name" => $input['client_name'],
                "comapny_name" => $input['comapny_name'],
                "email_id" => $input['email_id'],
                "mobile_no" => $input['mobile_no'],
                "password" => $password,
                "status" => "0",
            ]
        );
        // return redirect('http://'.config('app.vendor_domain').'/')->with('success', 'Hey '.$input['client_name'].',<br>Your registration is done. please login to quickddak panel');
        return redirect()->back()->with('success', 'Hey ' . $input['client_name'] . ',<br>Your registration is done. please login to quickddak panel');
    }
}
