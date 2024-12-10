<?php

namespace Didix16\LaraPrime\Listeners;

use Didix16\LaraPrime\Events\LaraPrimeServiceProviderRegistered;
use Didix16\LaraPrime\LaraPrimeServiceProvider;

class BootLaraPrime
{
    /**
     * Handle the event.
     *
     * @param  LaraPrimeServiceProviderRegistered  $event
     * @return void
     */
    public function handle(LaraPrimeServiceProviderRegistered $event)
    {
        return $this($event);
    }

    /**
     * Handle the event.
     *
     * @param  LaraPrimeServiceProviderRegistered  $event
     * @return void
     */
    public function __invoke(LaraPrimeServiceProviderRegistered $event): void
    {
        if (!app()->providerIsLoaded(LaraPrimeServiceProvider::class)) {
            app()->register(LaraPrimeServiceProvider::class);
        }
        // register here LaraPrime resources and tools
    }
}
