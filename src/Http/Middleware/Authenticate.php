<?php

namespace Didix16\LaraPrime\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticationMiddleware;
use Illuminate\Http\Request;
use Didix16\LaraPrime\Exceptions\AuthenticationException as LaraPrimeAuthenticationException;
class Authenticate extends BaseAuthenticationMiddleware
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure(Request):mixed $next
     * @param string ...$guards
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards): mixed
    {
        try {
            $guard = config('laraprime.guard');

            if(!empty($guard)){
                $guards[] = $guard;
            }
            return parent::handle($request, $next, ...$guards);
        }catch(AuthenticationException $e) {
            throw new LaraPrimeAuthenticationException('Unauthenticated.', $e->guards());
        }
    }
}
