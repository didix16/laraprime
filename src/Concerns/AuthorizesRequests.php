<?php

namespace Didix16\LaraPrime\Concerns;

use Closure;
use Illuminate\Http\Request;

trait AuthorizesRequests
{
    /**
     * The callback that should be used to authenticate Nova users.
     *
     * @var (Closure(Request):(bool))|null
     */
    public static ?Closure $authUsing;

    /**
     * Register the Nova authentication callback.
     *
     * @param  Closure(Request):bool  $callback
     */
    public static function auth(Closure $callback): static
    {
        static::$authUsing = $callback;

        return new static;
    }

    /**
     * Determine if the given request can access the Nova dashboard.
     *
     * @return bool
     */
    public static function check(Request $request)
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })($request);
    }
}
