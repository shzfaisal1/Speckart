<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use Spatie\Permission\Models\Role;


Route::get('/', function () {
    $subdomain = explode('.', request()->getHost())[0];
    
    if ($subdomain === 'speckart') 
    {
        if (auth()->check()) 
        {
            $setting['page_title'] = 'Dashboard';
            $setting['breadcrumbs'] = [
                ['link' => url("/"), 'name' => 'Home'],
                ['name' => 'Dashboard'],
            ];
            $user = auth()->user();
            return view('layouts.index', $setting);
        }
        return redirect()->route('login');
    } else {
        return view('frontend/index');
    }
})->name('index');

Route::get('/clear', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "Cleared!";
});

Route::post('/register-client', [RegisterController::class, 'createUser'])->name('registerClient');

Auth::routes();
