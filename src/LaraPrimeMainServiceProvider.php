<?php

namespace Didix16\LaraPrime;

use Didix16\LaraPrime\Events\ServingLaraPrime;
use Didix16\LaraPrime\Http\Middleware\ServeLaraPrime;
use Didix16\LaraPrime\Http\Requests\LaraPrimeRequest;
use Didix16\LaraPrime\Listeners\BootLaraPrime;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

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

        Route::middlewareGroup('laraprime', config('laraprime.middleware', []));
        Route::middlewareGroup('laraprime:api', config('laraprime.api_middleware', []));

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
            ->registerJsonVariables();
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
}
