<?php

namespace Mtownsend\SnipcartApi\Facades;

use Illuminate\Support\Facades\Facade;

class SnipcartApi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'snipcart';
    }
}
