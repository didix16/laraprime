<?php

namespace Didix16\LaraPrime\Http\Controllers;

use Didix16\LaraPrime\Concerns\Auth\AuthenticatesUsers;
use Didix16\LaraPrime\LaraPrime;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('laraprime.guest:'.config('laraprime.guard'))->except('logout');
    }

    /**
     * Show the application's login form.
     */
    public function showLoginForm(): Response|\Symfony\Component\HttpFoundation\Response
    {
        if ($loginPath = config('laraprime.routes.login', false)) {
            return Inertia::location($loginPath);
        }

        return Inertia::render('Auth/Login', []);
    }

    /**
     * The user has been authenticated.
     *
     * @param  mixed  $user
     */
    protected function authenticated(Request $request, $user): JsonResponse|RedirectResponse
    {
        $redirect = redirect()->intended($this->redirectPath());

        return $request->wantsJson()
            ? new JsonResponse([
                'redirect' => $redirect->getTargetUrl(),
            ], 200)
            : $redirect;
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the post register / login redirect path.
     */
    public function redirectPath(): string
    {
        return LaraPrime::url(LaraPrime::$initialPath);
    }

    /**
     * Get the guard to be used during authentication.
     */
    protected function guard(): StatefulGuard
    {
        return Auth::guard(config('laraprime.guard'));
    }
}
