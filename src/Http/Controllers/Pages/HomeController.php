<?php

namespace Didix16\LaraPrime\Http\Controllers\Pages;

use Didix16\LaraPrime\LaraPrime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    /**
     * Show the LaraPrime homepage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect(LaraPrime::url(LaraPrime::$initialPath));
    }
}
