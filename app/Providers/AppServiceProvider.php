<?php

namespace App\Providers;
use App\Observers\CartObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\Cart;

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
        Cart::observe(CartObserver::class);
    }
}
