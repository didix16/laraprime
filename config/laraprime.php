<?php

use Didix16\LaraPrime\Http\Middleware\Authenticate;
use Didix16\LaraPrime\Http\Middleware\Authorize;
use Didix16\LaraPrime\Http\Middleware\DispatchServingLaraPrimeEvent;
use Didix16\LaraPrime\Http\Middleware\HandleInertiaRequests;

return [

    /*
    |--------------------------------------------------------------------------
    | LaraPrime App Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to display the name of the application within the UI
    | or in other locations. Of course, you're free to change the value.
    |
    */

    'name' => env('LARAPRIME_APP_NAME', env('APP_NAME')),

    /*
    |--------------------------------------------------------------------------
    | LaraPrime Domain Name
    |--------------------------------------------------------------------------
    |
    | This value is the "domain name" associated with your application. This
    | can be used to prevent LaraPrime's internal routes from being registered
    | on subdomains which do not need access to your admin application.
    |
    */

    'domain' => env('LARAPRIME_DOMAIN_NAME', null),

    /*
    |--------------------------------------------------------------------------
    | LaraPrime Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where LaraPrime will be accessible from. Feel free to
    | change this path to anything you like. Note that this URI will not
    | affect LaraPrime's internal API routes which aren't exposed to users.
    |
    */

    'path' => '/admin',

    /*
    |--------------------------------------------------------------------------
    | LaraPrime Authentication Guard
    |--------------------------------------------------------------------------
    |
    | This configuration option defines the authentication guard that will
    | be used to protect your LaraPrime routes. This option should match one
    | of the authentication guards defined in the "auth" config file.
    |
    */

    'guard' => env('LARAPRIME_GUARD', null),

    /*
    |--------------------------------------------------------------------------
    | LaraPrime Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be assigned to every Nova route, giving you the
    | chance to add your own middleware to this stack or override any of
    | the existing middleware. Or, you can just stick with this stack.
    |
    */

    'middleware' => [
        'web',
        HandleInertiaRequests::class,
        DispatchServingLaraPrimeEvent::class,
    ],

    'api_middleware' => [
        'laraprime',
        Authenticate::class,
        Authorize::class,
    ],

    /*
     |--------------------------------------------------------------------------
     | Service Provider
     |--------------------------------------------------------------------------
     |
     | This value is a class namespace of the platform's service provider. You
     | can override it to define a custom namespace. This may be useful if you
     | want to place LaraPrime's service provider in a location different from
     | "app/LaraPrime".
     |
     */
    'provider' => \App\LaraPrime\LaraPrimeProvider::class,

    /*
     |--------------------------------------------------------------------------
     | LaraPrime default theme
     |--------------------------------------------------------------------------
     |
     | This is the default theme that will be used by LaraPrime. You can change
     | this value to any of the themes provided by PrimeReact
     | The name should be the same as the folder name in the public/themes directory
     | To see the available themes visit: https://primereact.org/theming/#builtinthemes
     | You can also create your own theme and place it in the public/themes directory
     | and set the name here.
     */
    'theme' => 'mira',
];
