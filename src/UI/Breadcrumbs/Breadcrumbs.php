<?php

namespace Didix16\LaraPrime\UI\Breadcrumbs;

use Didix16\LaraPrime\Traits\Makeable;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use JsonSerializable;

class Breadcrumbs implements JsonSerializable
{
    use Makeable;

    protected Collection $items;

    /**
     * The registrar of the breadcrumbs
     * It is an array of callbacks that will be executed when the breadcrumbs middleware is executed
     *
     * @var array<string, callable>
     */
    protected array $registrar = [];

    /**
     * @template TKey of array-key
     * @template TValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>|null  $items
     */
    public function __construct(Arrayable|iterable|null $items = [])
    {

        $this->items = collect($items);
    }

    /**
     * Check if the breadcrumbs registrar has a callback for the given route name
     * Return true if the registrar has a callback for the given route name
     *
     * @param  string  $name  The route name to check
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->registrar);
    }

    /**
     * Get the callback for the given route name
     * Return the callback for the given route name or null if it does not exist
     *
     * @param  string  $name  The route name
     *
     * @throws \Throwable
     */
    public function get(string $name): callable
    {
        throw_unless(
            $this->has($name),
            Exception::class,
            "No breadcrumbs defined for route [{$name}]."
        );

        return $this->registrar[$name];
    }

    /**
     * Add a new breadcrumb item to the collection.
     * It can be a Breadcrumb instance or a label, url and icon for the new item
     *
     * @return $this
     */
    public function push(string|Breadcrumb $labelItem, ?string $url = null, ?string $icon = null): self
    {
        if ($labelItem instanceof Breadcrumb) {
            $this->items->push($labelItem);

            return $this;
        }

        $this->items->push(Breadcrumb::make($labelItem, $url, $icon));

        return $this;
    }

    /**
     * Register a callback to be executed when the breadcrumbs middleware is executed for the given route name
     * NOTE: This is a setter method, any previous callback for the given route name will be replaced
     *
     * @param  string  $name  The route name
     * @param  callable  $callback  The callback to be executed
     * @return $this
     */
    public function register(string $name, callable $callback): self
    {
        $this->registrar[$name] = $callback;

        return $this;
    }

    /**
     * Call a parent route callback with the given parameters.
     *
     * @param  mixed  $parameters
     * @return $this
     *
     * @throws \Throwable
     */
    public function parent(string $name, ...$parameters): self
    {
        return $this->call($name, $parameters);
    }

    /**
     * Returns the json representation of the breadcrumbs
     */
    public function jsonSerialize(): mixed
    {
        if ($this->items->isEmpty()) {
            $currentRoute = Route::current();
            $routeName = $currentRoute->getName();
            $routeParams = $currentRoute ? $currentRoute->parameters() : [];
            $this->has($routeName) && $this->call($routeName, $routeParams);
        }

        return $this->items;
    }

    /**
     * Call the breadcrumb callback for the given route name with the given parameters.
     *
     *
     * @return $this
     *
     * @throws \Throwable
     */
    protected function call(string $name, array $parameters): self
    {
        $callback = $this->get($name);

        $parameters = Arr::prepend(array_values($parameters), $this);

        call_user_func_array($callback, $parameters);

        return $this;
    }
}
