<?php

namespace Didix16\LaraPrime;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Util
{
    /**
     * Determine if the given request is intended for LaraPrime.
     *
     * @param  Request  $request
     * @return bool
     */
    public static function isLaraPrimeRequest(Request $request): bool
    {
        $domain = config('laraprime.domain');
        $path = trim(LaraPrime::path(), '/') ?: '/';

        if (! is_null($domain) && $domain !== config('app.url') && $path === '/') {
            if (! Str::startsWith($domain, ['http://', 'https://', '://'])) {
                $domain = $request->getScheme().'://'.$domain;
            }

            if (! in_array($port = $request->getPort(), [443, 80]) && ! Str::endsWith($domain, ":{$port}")) {
                $domain = $domain.':'.$port;
            }

            $uri = parse_url($domain);

            return isset($uri['port'])
                ? rtrim($request->getHttpHost(), '/') === $uri['host'].':'.$uri['port']
                : rtrim($request->getHttpHost(), '/') === $uri['host'];
        }

        return $request->is($path) ||
            $request->is(trim($path.'/*', '/')) ||
            $request->is('prime-api/*') ||
            $request->is('prime-vendor/*');
    }
}
