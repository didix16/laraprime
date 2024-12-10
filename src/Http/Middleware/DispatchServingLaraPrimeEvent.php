<?php

namespace Didix16\LaraPrime\Http\Middleware;

use Closure;
use Didix16\LaraPrime\Events\ServingLaraPrime;
use Didix16\LaraPrime\Http\Requests\LaraPrimeRequest;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DispatchServingLaraPrimeEvent
{
    /**
     * Handle the incoming request.
     *
     * @param  Closure(Request):mixed  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $preventsAccessingMissingAttributes = method_exists(Model::class, 'preventsAccessingMissingAttributes')
            ? Model::preventsAccessingMissingAttributes()
            : null;

        if ($preventsAccessingMissingAttributes === true) {
            Model::preventAccessingMissingAttributes(false);
        }

        ServingLaraPrime::dispatch($request);

        Container::getInstance()->forgetInstance(LaraPrimeRequest::class);

        $response = $next($request);

        if ($preventsAccessingMissingAttributes === true) {
            Model::preventAccessingMissingAttributes(true);
        }

        return $response;
    }
}
