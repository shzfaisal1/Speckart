<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use Spatie\Permission\Models\Role;


Route::get('/', function () {
    $subdomain = explode('.', request()->getHost())[0];
    
    if ($subdomain === 'speckart' || request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1') 
    {
        if (auth()->check()) 
        {
            return app(\App\Http\Controllers\HomeController::class)->index();
        }
        return redirect()->route('login');
    } else {
        if (auth()->check()) {
            return app(\App\Http\Controllers\HomeController::class)->index();
        }
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
