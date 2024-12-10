<?php

namespace Didix16\LaraPrime\Concerns\Auth;

/**
 * The old legacy RedirectsUsers trait from laravel/ui => Illuminate\Foundation\Auth
 * For more info see: https://github.com/laravel/ui/blob/4.x/README.md#introduction
 */
trait RedirectsUsers
{
    /**
     * Get the post register / login redirect path.
     */
    public function redirectPath(): string
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}
