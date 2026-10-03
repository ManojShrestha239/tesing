<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


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

        Blade::if('super', function () {
            return auth()->check() && (auth()->user()->user_type == "super" || Auth::user()->user_type == 'ASM');
        });
        Blade::if('account', function () {
            return auth()->check() && auth()->user()->user_type == "account";
        });
        Blade::if('marketing', function () {
            return auth()->check() && auth()->user()->user_type == "marketing";
        });
        Blade::if('dispatch', function () {
            return auth()->check() && auth()->user()->user_type == "dispatch";
        });
        Blade::if('super_market', function () {
            return auth()->check() && (auth()->user()->user_type == "super" || auth()->user()->user_type == "marketing" || Auth::user()->user_type == 'ASM');
        });
        Blade::if('super_market_dispatch', function () {
            return auth()->check() && (auth()->user()->user_type == "super" || auth()->user()->user_type == "marketing" || auth()->user()->user_type == "dispatch" || Auth::user()->user_type == 'ASM');
        });
        Blade::if('super_market_account', function () {
            return auth()->check() && (auth()->user()->user_type == "super" || auth()->user()->user_type == "marketing" || auth()->user()->user_type == "account" || Auth::user()->user_type == 'ASM');
        });

        Schema::defaultStringLength(191);
    }
}
