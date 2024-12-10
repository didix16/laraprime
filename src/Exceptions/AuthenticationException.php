<?php

namespace Didix16\LaraPrime\Exceptions;

use Didix16\LaraPrime\LaraPrime;
use Illuminate\Auth\AuthenticationException as BaseAuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationException extends BaseAuthenticationException
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): Response|JsonResponse|\Illuminate\Http\Response
    {
        if ($request->expectsJson()) {
            return response()
                ->json([
                    'message' => $this->getMessage(),
                    'redirect' => $this->location(),
                ], Response::HTTP_UNAUTHORIZED);
        } elseif ($request->is('laraprime-api/*') || $request->is('laraprime-vendor/*')) {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        if ($request->inertia() || config('laraprime.routes.login', false) !== false) {
            return $this->redirectForInertia($request);
        }

        return redirect()->guest($this->location());

    }

    /**
     * Determine the location the user should be redirected to.
     *
     * @return string
     */
    protected function location()
    {
        return config('nova.routes.login') ?: LaraPrime::url('login');
    }

    /**
     * Redirect request for Inertia.
     *
     * @param  Request  $request
     */
    protected function redirectForInertia($request): Response
    {
        tap(redirect(), function (Redirector $redirect) use ($request) {
            $url = $redirect->getUrlGenerator();

            $intended = $request->method() === 'GET' && $request->route() && ! $request->expectsJson()
                ? $url->full()
                : $url->previous();

            if ($intended) {
                $redirect->setIntendedUrl($intended);
            }
        });

        return Inertia::location($this->location());
    }
}
