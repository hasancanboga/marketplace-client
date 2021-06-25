<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading(!$this->app->isProduction());

        RateLimiter::for('save_order_details', function ($job) {
            // Limiting this to 14 because this also triggers the UpdateOrderType job
            // which will make it 28 requests, plus the scheduled GET request makes it 29.
            // so it respects the 30 rate limit of the marketplace API.
            return Limit::perMinute(14);
        });
        
    }
}
