<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Coordinador;
use App\Observers\CoordinadorObserver;

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
        Coordinador::observe(CoordinadorObserver::class);
    }
}
