<?php

namespace Didix16\LaraPrime\Concerns;

use Closure;
use Didix16\LaraPrime\Events\LaraPrimeServiceProviderRegistered;
use Didix16\LaraPrime\Events\ServingLaraPrime;
use Illuminate\Support\Facades\Event;

trait InteractsWithEvents
{
    /**
     * Register an event listener for the LaraPrime "booted" event.
     *
     * @param  (Closure(LaraPrimeServiceProviderRegistered):(void))|string  $callback
     */
    public static function booted(Closure|string $callback): void
    {
        Event::listen(LaraPrimeServiceProviderRegistered::class, $callback);
    }

    /**
     * Register an event listener for the LaraPrime "serving" event.
     *
     * @param  string|(Closure(ServingLaraPrime):(void))  $callback
     */
    public static function serving(string|Closure $callback): void
    {
        Event::listen(ServingLaraPrime::class, $callback);
    }
}
