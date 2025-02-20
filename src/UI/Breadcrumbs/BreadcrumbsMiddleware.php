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
     *
     * @param Router $router
     * @param Breadcrumbs $breadcrumbs
     */
    public function __construct(Router $router, Breadcrumbs $breadcrumbs)
    {
        $this->router = $router;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Handle the breadcrumbs middleware
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next)
    {
        collect($this->router->getRoutes())
            ->filter(fn(Route $route) => array_key_exists(self::class, $route->defaults))
            ->filter(fn(Route $route) => !$this->breadcrumbs->has($route->getName()))
            ->each(function (Route $route) {

                $serializedFn = $route->defaults[self::class];

                /** @var SerializableClosure $callback */
                $callback = unserialize($serializedFn);

                $callback->getClosure()($this->breadcrumbs, ...$route->parameters());
            });

        return $next($request);
    }
}
