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
    | List of implementations by slug
    |--------------------------------------------------------------------------
    |
    | 'slug' => Vendor\Package\Gateway::class,
    |
    */

    'gateways' => [

    ],

];
