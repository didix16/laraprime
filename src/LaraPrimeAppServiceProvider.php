<?php

namespace Didix16\LaraPrime;

use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Foundation\Vite;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * This class represents the LaraPrime App Service Provider.
 * It is used to register the LaraPrime resources and tools to the application.
 */
abstract class LaraPrimeAppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }
    /**
     * Boot the application events.
     * @return void
     */
    public function boot(): void
    {
        LaraPrime::routes()
            ->withAuthenticationRoutes();

        $this->defineRoutes();
    }

    /**
     * Define routes at service provider boot time.
     * @param Router $router
     * @return void
     */
    protected function routes(Router $router): void
    {

    }

    /**
     * Define routes for the dashboard.
     *
     * @return $this
     */
    private function defineRoutes(): static
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return $this;
        }

        Route::domain((string) config('laraprime.domain'))
            ->prefix(LaraPrime::path())
            ->middleware(config('laraprime.api_middleware'))
            ->group(function (Router $route) {
                $this->routes($route);
            });

        return $this;
    }
}
