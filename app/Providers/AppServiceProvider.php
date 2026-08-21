<?php

namespace App\Providers;

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('role', function ($app) {
            return new RoleMiddleware(); // pastikan Role ada di namespace yang benar
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('userLogin', Auth::guard('web')->user());
        });
        Schema::defaultStringLength(191);
    }
}
