<?php

namespace Skylex\Larapay\Providers;

use Skylex\Larapay\GatewayManager;
use Skylex\Larapay\Contracts\Payments;
use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider
{
    /**
     * Register Service Provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'payments');

        $this->app->singleton(Payments::class, function ($app) {
            return new GatewayManager($app);
        });
    }

    /**
     * Boot Service Provider.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            $this->getConfigPath() => config_path('larapay.php')
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /**
     * Get default config path.
     *
     * @return string
     */
    protected function getConfigPath(): string
    {
        return __DIR__ . '/../../config/larapay.php';
    }
}
