<?php

namespace App\LaraPrime;

use Didix16\LaraPrime\LaraPrimeAppServiceProvider;
use Illuminate\Routing\Router;

class LaraPrimeProvider extends LaraPrimeAppServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        // Your boot code here
    }

    public function register(): void
    {
        parent::register();

        // Your register code here
    }

    /**
     * Define routes at service provider boot time.
     * @param Router $router
     * @return void
     */
    public function routes(Router $router): void
    {
        // Define routes here
    }
}
