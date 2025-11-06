<?php

namespace Mortezaa97\Factors;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mortezaa97\Factors\Skeleton\SkeletonClass
 */
class FactorsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'factors';
    }
}
