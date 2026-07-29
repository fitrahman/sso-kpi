<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Skip authorization prompt by using custom client model
        Passport::useClientModel(\App\Models\PassportClient::class);

        // Token expiration times
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));


        // Define OAuth2 Scopes
        Passport::tokensCan([
            'admin-access' => 'Access admin panel',
            'user-read'    => 'Read user information',
            'user-write'   => 'Create, update, and delete users',
        ]);

        // Default scope
        Passport::setDefaultScope([
            'user-read',
        ]);
    }
}
