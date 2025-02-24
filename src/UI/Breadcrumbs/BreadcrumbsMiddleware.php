<?php

namespace Didix16\LaraPrime\UI\Breadcrumbs;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Laravel\SerializableClosure\SerializableClosure;
use Throwable;

class BreadcrumbsMiddleware
{
    protected Router $router;

    protected Breadcrumbs $breadcrumbs;

    /**
     * BreadcumbsMiddleware constructor
     */
    public function __construct(Router $router, Breadcrumbs $breadcrumbs)
    {
        $this->router = $router;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Handle the breadcrumbs middleware
     *
     *
     * @return mixed
     *
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next)
    {
        collect($this->router->getRoutes())
            ->filter(fn (Route $route) => array_key_exists(self::class, $route->defaults))
            ->filter(fn (Route $route) => ! $this->breadcrumbs->has($route->getName()))
            ->each(function (Route $route) {

                $serializedFn = $route->defaults[self::class];

                /** @var SerializableClosure $callback */
                $callback = unserialize($serializedFn);

                $this->breadcrumbs->register($route->getName(), $callback->getClosure());
            });

        optional($request->route())->forgetParameter(self::class);

        return $next($request);
    }
}
