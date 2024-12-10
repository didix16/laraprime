<?php

namespace Didix16\LaraPrime\Http\Middleware;

use Closure;
use Didix16\LaraPrime\Events\LaraPrimeServiceProviderRegistered;
use Didix16\LaraPrime\Util;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServeLaraPrime
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Util::isLaraPrimeRequest($request)){
            LaraPrimeServiceProviderRegistered::dispatch();
        }
        return $next($request);
    }
}
