<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Produccion;
use App\Observers\ProduccionObserver;
use App\Services\BcvService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BcvService::class, function ($app) {
            return new BcvService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Produccion::observe(ProduccionObserver::class);
    }
}
