<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Register Passport routes
        // if (!app()->runningInConsole()) {
        //     Passport::routes();
        // }

        // Set token expiration times
        Passport::tokensExpireIn(now()->addHours(5)); // Access token expires in 2 hours
        Passport::refreshTokensExpireIn(now()->addDays(30)); // Refresh token expires in 30 days
        Passport::personalAccessTokensExpireIn(now()->addDays(15)); // Personal access token expires in 15 days
    }
}
