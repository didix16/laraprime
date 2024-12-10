<?php

namespace Didix16\LaraPrime\Http\Middleware;

use Closure;
use Didix16\LaraPrime\LaraPrime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect(LaraPrime::path());
        }

        return $next($request);
    }
}
