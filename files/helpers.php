<?php

if (! function_exists('payments')) {

    /**
     * Return Payments interface.
     *
     * @param string|null $gateway
     *
     * @return \Skylex\Larapay\Contracts\Payments|\Skylex\Larapay\Abstracts\Gateway
     */
    function payments(string $gateway = null)
    {
        $payments = app(\Skylex\Larapay\Contracts\Payments::class);

        if (is_null($gateway)) {
            return $payments;
        }

        return $payments->gateway($gateway);
    }
}
