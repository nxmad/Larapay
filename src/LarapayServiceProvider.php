<?php

namespace Nxmad\Larapay;

use Nxmad\Larapay\Contracts\Payments;
use Illuminate\Support\ServiceProvider;

class LarapayServiceProvider extends ServiceProvider
{
    /**
     * Register Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'larapay');

        $this->app->singleton(Payments::class, function ($app) {
            return new GatewayManager($app['config']);
        });
    }

    /**
     * Boot Service Provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->getConfigPath() => config_path('larapay.php'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/../files/views', 'larapay');

        $this->loadMigrationsFrom(__DIR__ . '/../files/database/migrations');
    }

    /**
     * Get default config path.
     *
     * @return string
     */
    protected function getConfigPath(): string
    {
        return __DIR__ . '/../files/config/larapay.php';
    }
}
