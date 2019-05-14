<?php

namespace Mtownsend\SnipcartApi\Providers;

use Illuminate\Support\ServiceProvider;
use Mtownsend\SnipcartApi\SnipcartApi;

class SnipcartApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/snipcart.php' => config_path('snipcart.php')
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('snipcart', function () {
            return new SnipcartApi(config('snipcart.api_key'));
        });
    }
}
