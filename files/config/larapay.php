<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Internal Transaction class
    |--------------------------------------------------------------------------
    |
    | Must extend Nxmad\Larapay\Models\Transaction
    |
    */

    'transaction' => \Nxmad\Larapay\Models\Transaction::class,

    /*
    |--------------------------------------------------------------------------
    | Specified extensions settings
    |--------------------------------------------------------------------------
    |
    | Vendor\Package\Gateway::class => [
    |   'key' => 'value',
    | ],
    |
    */

    'gateways' => [

    ],

];
