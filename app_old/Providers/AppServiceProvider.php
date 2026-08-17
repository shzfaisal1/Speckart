<?php

namespace App\Providers;

//use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }


    public function boot()
    {
        $host = request()->getHost(); // get current host like admin.apnashyam.com
        $uri = request()->path();     // get the URI path like 'quickdaak/...'
        $subdomain = explode('.',  request()->getHost())[0];
        Route::middleware('web')->group(base_path('routes/auth.php'));
        $isLocal = ($host === '127.0.0.1' || $host === 'localhost');
        if ($subdomain === 'franchise' || $subdomain === 'speckart' || $isLocal) {
            $vendorRoute = Route::middleware('web');
            $adminRoute = Route::middleware('web');

            if (!$isLocal) {
                Route::middleware('api')->domain(config('app.vendor_domain'))->prefix('api')->group(base_path('routes/api.php'));
                $vendorRoute = $vendorRoute->domain(config('app.vendor_domain'));
                $adminRoute = $adminRoute->domain(config('app.admin_domain'));
                $vendorRoute->group(base_path('routes/client.php'));
            } else {
                // Commented out on local to prevent crashes from missing API and Client controllers
                // Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
            }

            $adminRoute->group(base_path('routes/web.php'));
            $adminRoute->group(base_path('routes/purchases.php'));
            $adminRoute->group(base_path('routes/setting.php'));
            $adminRoute->group(base_path('routes/inventory.php'));
            $adminRoute->group(base_path('routes/customer.php'));
            $adminRoute->group(base_path('routes/sales.php'));
            $adminRoute->group(base_path('routes/account.php'));

            foreach (glob(base_path('routes/admin*.php')) as $routeFile) {
                $adminRoute->group($routeFile);
            }
        } 
    }
}
