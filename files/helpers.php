<?php

if (! function_exists('payments')) {

    /**
     * Return Payments interface.
     *
     * @param string|null $gateway
     *
     * @return \Illuminate\Foundation\Application|mixed
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