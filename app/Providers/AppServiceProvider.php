<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UserService; // Import the UserService


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService();
        });
    }
    

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
