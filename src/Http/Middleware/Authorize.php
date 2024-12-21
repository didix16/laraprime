<?php

namespace Didix16\LaraPrime\Http\Middleware;

use Closure;
use Didix16\LaraPrime\LaraPrime;
use Illuminate\Http\Request;

class Authorize
{
    /**
     * Handle an incoming request.
     * Check if the request is authorized to access LaraPrime admin panel
     *
     * @param  Closure(Request):mixed  $next
     * @return void
     */
    public function handle(Request $request, Closure $next)
    {
        return LaraPrime::check($request) ? $next($request) : abort(403);
    }
}
