<?php

namespace Didix16\LaraPrime\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base Controller for LaraPrime
 */
class Controller extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function checkPermission(string $permission): void
    {
        $this->middleware(static function ($request, $next) use ($permission) {
            if (Auth::user()->hasAccess($permission)) {
                return $next($request);
            }
            abort(Response::HTTP_FORBIDDEN);
        });

        $user = Auth::user();
        abort_if($user !== null && ! $user->hasAccess($permission), Response::HTTP_FORBIDDEN);
    }
}
