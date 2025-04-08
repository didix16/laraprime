<?php

/**
 * Code borrowed from https://github.com/archtechx/enums/blob/master/src/InvokableCases.php
 * Adapted to work with the LaraPrime package by adding an internal cache for the enum's cases.
 * More info at: https://www.php.net/manual/en/language.variables.scope.php#language.variables.scope.static
 */

declare(strict_types=1);

namespace Didix16\LaraPrime\Traits;

use BackedEnum;
use Exception;

trait InvokableCases
{
    /** Return the enum's value when it's $invoked(). */
    public function __invoke()
    {
        return $this instanceof BackedEnum ? $this->value : $this->name;
    }

    /** Return the enum's value or name when it's called ::STATICALLY(). */
    public static function __callStatic($name, $args)
    {
        // internal cache for the enum's cases
        static $cases = [];

        $cases[static::class] ??= [];
        $cache = &$cases[static::class];

        if (isset($cache[$name])) {
            return $cache[$name];
        } elseif (! empty($cache)) {
            throw new Exception(sprintf('Undefined constant %s::%s', static::class, $name));
        }

        // generate cache for the enum's cases
        foreach (static::cases() as $case) {
            $cache[$case->name] = $case instanceof BackedEnum ? $case->value : $case->name;
        }

        if (isset($cache[$name])) {
            return $cache[$name];
        }

        throw new Exception(sprintf('Undefined constant %s::%s', static::class, $name));
    }
}
