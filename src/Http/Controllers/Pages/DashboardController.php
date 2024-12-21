<?php

namespace Didix16\LaraPrime\Http\Controllers\Pages;

use Didix16\LaraPrime\LaraPrime;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Show the LaraPrime homepage.
     */
    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Dashboard', []);
    }
}
