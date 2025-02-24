<?php

namespace Didix16\LaraPrime;

use Didix16\LaraPrime\Http\Controllers\LoginController;
use Didix16\LaraPrime\Http\Controllers\Pages\DashboardController;
use Didix16\LaraPrime\Http\Controllers\Pages\ErrorController;
use Didix16\LaraPrime\Http\Controllers\Pages\HomeController;
use Didix16\LaraPrime\UI\Breadcrumbs\Breadcrumbs;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class PendingRouteRegistation
{
    /**
     * Indicates if the routes have been registered.
     */
    protected bool $registered = false;

    /**
     * Register the LaraPrime authentication routes.
     *
     * @param  array<int, class-string|string>  $middleware
     * @return $this
     */
    public function withAuthenticationRoutes(array $middleware = ['laraprime']): static
    {
        LaraPrime::withAuthentication();

        Route::namespace('Didix16\LaraPrime\Http\Controllers')
            ->domain(config('laraprime.domain', null))
            ->middleware($middleware)
            ->prefix(LaraPrime::path())
            ->group(function (Router $router) {
                $router->get('/login', [LoginController::class, 'showLoginForm'])->name('laraprime.pages.login');
                $router->post('/login', [LoginController::class, 'login'])->name('laraprime.login');
            });

        Route::namespace('Didix16\LaraPrime\Http\Controllers')
            ->domain(config('laraprime.domain', null))
            ->middleware(config('laraprime.middleware', []))
            ->prefix(LaraPrime::path())
            ->group(function (Router $router) {
                $router->post('/logout', [LoginController::class, 'logout'])->name('laraprime.logout');
            });

        return $this;
    }

    /**
     * Register the LaraPrime routes.
     */
    public function register(): void
    {
        $this->registered = true;

        /**
         * Don't use ->as('larapriem.pages.'). Since this method will be called on a Lazy Service Provider (LaraPrimeProvider) which will only be loaded on demand
         * (by accessing to laraprime routes), the Laravel mechanism to update the lookup name table will be executed before this routes are registered,
         * so the names will not be found in the lookup table by route() helper function.
         * At the moment, we use the action array to define the route name using the key 'as'.
         */
        Route::namespace('Didix16\LaraPrime\Http\Controllers')
            ->domain(config('laraprime.domain', null))
            ->middleware(config('laraprime.middleware', []))
            ->prefix(LaraPrime::path())
            ->group(function (Router $router) {
                $router->get('/403', ['as' => 'laraprime.pages.403', 'uses' => ErrorController::class.'@throw403']);
                $router->get('/404', ['as' => 'laraprime.pages.404', 'uses' => ErrorController::class.'@throw404']);
            });

        Route::namespace('Didix16\LaraPrime\Http\Controllers')
            ->domain(config('laraprime.domain', null))
            ->middleware(config('laraprime.api_middleware', []))
            ->prefix(LaraPrime::path())
            ->group(function (Router $router) {
                $router->get('/', ['as' => 'laraprime.pages.home', 'uses' => HomeController::class.'@index']);
                $router->redirect('dashboard', LaraPrime::url('/'));

                $router
                    ->get('/main', ['as' => 'laraprime.pages.dashboard', 'uses' => DashboardController::class.'@index'])
                    ->breadcrumbs(fn (Breadcrumbs $breadcrumbs) => $breadcrumbs->push('Dashboard', route('laraprime.pages.dashboard')));
                $router->fallback([ErrorController::class, 'throw404'])->name('fallback');
            });
    }

    /**
     * Handle the object's destruction and register the router route.
     *
     * @return void
     */
    public function __destruct()
    {
        if (! $this->registered) {
            $this->register();
        }
    }
}
