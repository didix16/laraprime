<?php

namespace Didix16\LaraPrime\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static static showBreadcrumbs(bool $show = true)
 * @see \Didix16\LaraPrime\LaraPrime
 */
class LaraPrime extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Didix16\LaraPrime\LaraPrime::class;
    }
}
