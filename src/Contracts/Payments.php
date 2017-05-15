<?php

namespace Skylex\Larapay\Contracts;

use Skylex\Larapay\Abstracts\Gateway;

interface Payments
{
    /**
     * Get payment gateway implementation.
     *
     * @param string $gateway
     *
     * @return Gateway
     */
    public function gateway(string $gateway): Gateway;

    /**
     * Extend basic implementations list.
     *
     * @param string $driver
     * @param string $implementation
     *
     * @return self
     */
    public function extend(string $driver, string $implementation): self;
}
