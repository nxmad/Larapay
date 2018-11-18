<?php

if (! function_exists('payments')) {

    /**
     * Return Payments interface.
     *
     * @param string|null $gateway
     *
     * @return \Nxmad\Larapay\Contracts\Payments|\Nxmad\Larapay\Abstracts\Gateway
     */
    function payments(string $gateway = null)
    {
        $payments = app(\Nxmad\Larapay\Contracts\Payments::class);

        if (is_null($gateway)) {
            return $payments;
        }

        return $payments->gateway($gateway);
    }
}
