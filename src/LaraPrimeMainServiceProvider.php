<?php

namespace Didix16\LaraPrime;

use Closure;
use Didix16\LaraPrime\Events\ServingLaraPrime;
use Didix16\LaraPrime\Http\Middleware\ServeLaraPrime;
use Didix16\LaraPrime\Http\Requests\LaraPrimeRequest;
use Didix16\LaraPrime\Listeners\BootLaraPrime;
use Didix16\LaraPrime\Page\Page;
use Didix16\LaraPrime\UI\Breadcrumbs\Breadcrumbs;
use Didix16\LaraPrime\UI\Breadcrumbs\BreadcrumbsMiddleware;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Laravel\SerializableClosure\SerializableClosure;

class LaraPrimeMainServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        LaraPrime::booted(BootLaraPrime::class);

        if ($this->app->runningInConsole()) {
            $this->app->register(LaraPrimeServiceProvider::class);
        }

        \Illuminate\Support\Facades\Route::middlewareGroup('laraprime', config('laraprime.middleware', []));
        \Illuminate\Support\Facades\Route::middlewareGroup('laraprime:api', config('laraprime.api_middleware', []));

        $this->app
            ->make(HttpKernel::class)
            ->pushMiddleware(ServeLaraPrime::class);

        $this->app
            ->afterResolving(LaraPrimeRequest::class, function ($request, Application $app) {
                if (! $app->bound(LaraPrimeRequest::class)) {
                    $app->instance(LaraPrimeRequest::class, $request);
                }
            });

        $this
            ->registerEvents()
            ->registerJsonVariables()
            ->registerPageRouteMacro()
            ->registerBreadcrumbs();
    }

    public function register() {}

    /**
     * Register the package events.
     *
     * @throws BindingResolutionException
     */
    protected function registerEvents(): self
    {
        $this->app->make('events')->listen(RequestHandled::class, function ($event) {
            Container::getInstance()->forgetInstance(LaraPrimeRequest::class);
        });

        return $this;
    }

    /**
     * Register the LaraPrime package json variables to pass through to LaraPrime javascript instance.
     */
    protected function registerJsonVariables(): self
    {
        LaraPrime::serving(function (ServingLaraPrime $event) {

            LaraPrime::provideToScript([
                'appName' => LaraPrime::name(),
                'timezone' => config('app.timezone', 'UTC'),
                'locale' => config('app.locale', 'en'),
                'version' => LaraPrime::version(),
                'theme' => LaraPrime::defaultTheme(),
            ]);
        });

        return $this;
    }

    /**
     * Register the breadcrumbs backend component.
     */
    public function registerBreadcrumbs(): self
    {
        // Register the Breadcrumbs class in the service container
        $this->app->singleton(Breadcrumbs::class);

        // Register the Breadcrumbs middleware
        \Illuminate\Support\Facades\Route::middlewareGroup('breadcrumbs', [
            BreadcrumbsMiddleware::class,
        ]);

        // Add Route breadcrumbs macro if not exists
        if (!Route::hasMacro('breadcrumbs')) {

            Route::macro('breadcrumbs', function (Closure $callback) {

                /**
                 *  @var Route $this
                 */
                $this
                    ->middleware('breadcrumbs')
                    ->defaults(BreadcrumbsMiddleware::class, serialize(new SerializableClosure($callback)));

                return $this;
            });
        }


        return $this;
    }

    public function registerPageRouteMacro(): self
    {
        if (! \Illuminate\Support\Facades\Route::hasMacro('page')) {
            \Illuminate\Support\Facades\Route::macro('page', function (string $url, string|Page $page) {

                /**
                 * @var Router $this
                 */
                $route = $this->match(['GET', 'HEAD', 'POST'], $url . '/{m?}', $page);

                $route->where('m', $page::getAvailableMethods()->implode('|'));

                return $route;
            });
        }

        if (! Route::hasMacro('page')) {
            Route::macro('page', function (string $url, string|Page $page) {

                /**
                 * @var Router $this
                 */
                $route = $this->match(['GET', 'HEAD', 'POST'], $url . '/{m?}', $page);

                $route->where('m', $page::getAvailableMethods()->implode('|'));

                return $route;
            });
        }

        return $this;
    }
}
