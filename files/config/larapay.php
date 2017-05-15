<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Internal Transaction class
    |--------------------------------------------------------------------------
    |
    | Must extend Skylex\Larapay\Models\Transaction
    |
    */

    'transaction' => \Skylex\Larapay\Models\Transaction::class,

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
