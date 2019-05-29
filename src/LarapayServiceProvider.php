<?php

declare(strict_types=1);

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
            $this->getDatabasePath() => database_path(),
            $this->getConfigPath() => config_path('larapay.php'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/../files/views', 'larapay');
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

    /**
     * Get default database path.
     *
     * @return string
     */
    protected function getDatabasePath(): string
    {
        return __DIR__ . '/../files/database';
    }
}
