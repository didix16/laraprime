<?php

namespace Didix16\LaraPrime\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'laraprime::app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     *
     * @return string|null
     */
    public function version(Request $request){
        return sprintf('%s:%s', $this->rootView, parent::version($request));
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'primeConfig' => function () use ($request) {
                return [];
            },
//            'currentUser' => function () use ($request) {
//                return with(LaraPrime::user($request), function ($user) use ($request) {
//                    return ! is_null($user) ? UserResource::make($user)->toArray($request) : null;
//                });
//            },
        ]);
    }

}
