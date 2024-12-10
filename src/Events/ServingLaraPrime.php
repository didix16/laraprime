<?php

namespace Didix16\LaraPrime\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class ServingLaraPrime
{
    use Dispatchable;

    /**
     * The request instance.
     */
    public Request $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
