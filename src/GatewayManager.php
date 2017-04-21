<?php

namespace Skylex\Larapay;

use InvalidArgumentException;
use Illuminate\Foundation\Application;
use Skylex\Larapay\Contracts\Gateway as GatewayContract;

class GatewayManager implements Contracts\Payments
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * Create a new manager instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * The list of driver implementations.
     *
     * @var array
     */
    protected $gateways = [
        'unitpay' => \Skylex\Larapay\Gateways\Unitpay::class,
    ];

    /**
     * The list of created drivers.
     *
     * @var array
     */
    protected $created = [];

    /**
     * Get gateway module instance.
     *
     * @param string $gateway
     *
     * @return GatewayContract
     */
    public function gateway(string $gateway): GatewayContract
    {
        if (! isset($this->created[$gateway])) {
            $this->created[$gateway] = $this->createGateway($gateway);
        }

        return $this->created[$gateway];
    }

    /**
     * Extend basic implementations list.
     *
     * @param string $driver
     * @param string $implementation
     *
     * @return self
     */
    public function extend(string $driver, string $implementation): self
    {
        $this->gateways[$driver] = $implementation;

        return $this;
    }

    /**
     * Create and cache driver instance to fast access in future.
     *
     * @param string $gateway
     *
     * @return GatewayContract
     *
     * @throws InvalidArgumentException
     */
    protected function createGateway(string $gateway): GatewayContract
    {
        if (! isset($this->gateways[$gateway])) {
            throw new InvalidArgumentException("Gateway [{$gateway}] not found.");
        }

        $implementation = $this->gateways[$gateway];

        if (! class_exists($implementation)) {
            throw new InvalidArgumentException("Implementation for [{$gateway}] isn't installed.");
        }

        $config   = $this->app['config']->get('larapay.gateways')[$gateway];
        $instance = new $implementation($config);

        if (! $instance instanceof GatewayContract) {
            throw new InvalidArgumentException("{$implementation} is not valid gateway implementation.");
        }

        return $instance;
    }
}
