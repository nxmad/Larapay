<?php

namespace Skylex\Larapay;

use RuntimeException;
use InvalidArgumentException;
use Skylex\Larapay\Abstracts\Gateway;
use Illuminate\Contracts\Config\Repository;

class GatewayManager implements Contracts\Payments
{
    /**
     * The global GatewayManager config.
     *
     * @var Repository
     */
    protected $config;

    /**
     * Create a new manager instance.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * The list of default implementations.
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
     * @return Gateway
     */
    public function gateway(string $gateway): Gateway
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
     * @return Gateway
     *
     * @throws InvalidArgumentException
     */
    protected function createGateway(string $gateway): Gateway
    {
        if (! isset($this->gateways[$gateway])) {
            throw new InvalidArgumentException("Gateway [{$gateway}] was not found.");
        }

        $implementation = $this->gateways[$gateway];

        if (! class_exists($implementation)) {
            throw new RuntimeException("Class [{$implementation}] was not found.");
        }

        if (! $this->config->has("larapay.gateways.{$implementation}")) {
            throw new RuntimeException("No config found for {$implementation}.");
        }

        $config = $this->config->get("larapay.gateways.{$implementation}");

        return new $implementation($config);
    }
}
