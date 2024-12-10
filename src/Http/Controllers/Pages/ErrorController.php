<?php

namespace Didix16\LaraPrime\Http\Controllers\Pages;

use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    /**
     * Show LaraPrime 403 page using Inertia.
     */
    public function throw403(): void
    {
        abort(Response::HTTP_FORBIDDEN);
    }

    /**
     * Show LaraPrime 404 page using Inertia.
     */
    public function throw404(): void
    {
        abort(Response::HTTP_NOT_FOUND);
    }
}
