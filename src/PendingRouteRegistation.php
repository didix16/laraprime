<?php

namespace Didix16\LaraPrime;

use Didix16\LaraPrime\Http\Controllers\LoginController;
use Didix16\LaraPrime\Http\Controllers\Pages\ErrorController;
use Didix16\LaraPrime\Http\Controllers\Pages\HomeController;
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

        Route::namespace('Didix16\LaraPrime\Http\Controllers')
            ->domain(config('laraprime.domain', null))
            ->middleware(config('laraprime.middleware', []))
            ->prefix(LaraPrime::path())
            ->as('laraprime.pages.')
            ->group(function (Router $router) {
                $router->get('/403', [ErrorController::class, 'throw403'])->name('403');
                $router->get('/404', [ErrorController::class, 'throw404'])->name('404');
            });

        Route::namespace('Didix16\LaraPrime\Http\Controllers')
            ->domain(config('laraprime.domain', null))
            ->middleware(config('laraprime.api_middleware', []))
            ->prefix(LaraPrime::path())
            ->as('laraprime.pages.')
            ->group(function (Router $router) {
                $router->get('/', [HomeController::class, 'index'])->name('home');
                $router->redirect('dashboard', LaraPrime::url('/'))->name('dashboard');
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
