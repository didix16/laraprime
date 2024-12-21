<?php

namespace Didix16\LaraPrime;

use Didix16\LaraPrime\Events\ServingLaraPrime;
use Didix16\LaraPrime\Exceptions\LaraPrimeExceptionHandler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * This class represents the LaraPrime App Service Provider.
 * It is used to register the LaraPrime resources and tools to the application.
 */
abstract class LaraPrimeAppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->gate();

        LaraPrime::routes()
            ->withAuthenticationRoutes();

        $this->defineRoutes();

        LaraPrime::serving(function (ServingLaraPrime $event) {
            $this->authorization();
            $this->registerExceptionHandler();
        });
    }

    /**
     * Define routes at service provider boot time.
     */
    protected function routes(Router $router): void {}

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

    /**
     * Configure the LaraPrime authorization services.
     *
     * @return void
     */
    protected function authorization()
    {
        LaraPrime::auth(function ($request) {
            return app()->environment('local') ||
                Gate::check('viewLaraPrime', [LaraPrime::user($request)]);
        });
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

    /**
     * Register LaraPrime's custom exception handler.
     *
     * @return void
     */
    protected function registerExceptionHandler()
    {
        $this->app->bind(ExceptionHandler::class, LaraPrimeExceptionHandler::class);
    }
}
