<?php

namespace App\LaraPrime;

use Didix16\LaraPrime\LaraPrimeAppServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;

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
     */
    public function routes(Router $router): void
    {
        // Define routes here
    }

    /**
     * Register the LaraPrime gate.
     *
     * This gate determines who can access LaraPrime in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewLaraPrime', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }
}
