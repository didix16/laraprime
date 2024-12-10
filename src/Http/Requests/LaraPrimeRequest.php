<?php

namespace Didix16\LaraPrime\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class LaraPrimeRequest extends FormRequest
{
    /**
     * Create an Illuminate request from a Symfony instance.
     *
     * @param SymfonyRequest $request
     * @return static
     */
    public static function createFromBase(SymfonyRequest $request): static
    {
        $newRequest = parent::createFromBase($request);

        if ($request instanceof Request) {
            $newRequest->setUserResolver($request->getUserResolver());
        }

        return $newRequest;
    }
}
