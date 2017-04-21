<?php

namespace Skylex\Larapay\Facades;

use Illuminate\Support\Facades\Facade;
use Skylex\Larapay\Contracts\Factory;

class Payments extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Factory::class;
    }
}
