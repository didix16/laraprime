<?php

namespace Didix16\LaraPrime\Concerns;

use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;

trait HandlesRoutes
{
    /**
     * Get url for LaraPrime.
     *
     * @param  string  $url
     * @return string
     */
    public static function url(string $url): string
    {
        return rtrim(static::path(), '/').'/'.ltrim($url, '/');
    }

    /**
     * Get Route Registrar for LaraPrime.
     *
     * @param array<int, class-string|string>|null $middleware
     * @param string|null $prefix
     * @return RouteRegistrar
     */
    public static function router(?array $middleware = null, ?string $prefix = null): RouteRegistrar
    {
        return Route::domain(config('laraprime.domain', null))
            ->prefix(static::url($prefix))
            ->middleware($middleware ?? config('laraprime.middleware', []));
    }
}
