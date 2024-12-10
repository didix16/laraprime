<?php

namespace Didix16\LaraPrime\Decorators;

use Illuminate\Foundation\Vite;

class ViteDecorator
{
    private Vite $vite;

    public function __construct(Vite $vite)
    {
        $this->vite = $vite;
        $this->vite->useHotFile(public_path('vendor/laraprime/hot'));
    }

    public function __get($name)
    {
        return $this->vite->$name;
    }

    public function __call($name, $arguments)
    {
        return $this->vite->$name(...$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return Vite::$name(...$arguments);
    }

    /**
     * @throws \Exception
     */
    public function __invoke(...$arguments)
    {
        return ($this->vite)(...$arguments);
    }
}
