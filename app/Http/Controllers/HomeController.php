<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $setting['page_title'] = 'Dashboard';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => 'Dashboard'],
        ];
        return view('home', $setting);
    }
}
