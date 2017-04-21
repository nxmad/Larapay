<?php

namespace Skylex\Larapay\Contracts;

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
}
