<?php

namespace Didix16\LaraPrime\Exceptions;

use Closure;
use Didix16\LaraPrime\LaraPrime;
use Didix16\LaraPrime\Util;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class LaraPrimeExceptionHandler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     *
     *
     * @return void
     */
    public function register()
    {
        with(LaraPrime::$reportCallback, function ($handler) {
            /** @var (callable(\Throwable):(void))|(\Closure(\Throwable):(void))|null $handler */
            if ($handler instanceof Closure || is_callable($handler)) {
                $this->reportable(function (Throwable $e) use ($handler) {
                    call_user_func($handler, $e);
                })->stop();
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if (Util::isLaraPrimeRequest($request)) {
            return $this->renderInertiaException($request, $this->prepareException($e));
        }

        return parent::render($request, $e);
    }

    /**
     * Render Inertia Exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface|\Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderInertiaException($request, $e)
    {
        $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        Inertia::setRootView('laraprime::app');

        if (in_array($statusCode, [Response::HTTP_FORBIDDEN, Response::HTTP_NOT_FOUND])) {
            return Inertia::render(sprintf('Error%d', $statusCode))->toResponse($request)->setStatusCode($statusCode);
        }

        if ($request->inertia()) {
            return Inertia::render('Error')->toResponse($request)->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $e);
    }
}
