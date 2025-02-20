<?php

namespace Didix16\LaraPrime\UI\Breadcrumbs;

use Didix16\LaraPrime\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use JsonSerializable;

class Breadcrumbs implements JsonSerializable
{
    use Makeable;

    protected Collection $items;

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
     * Check if the breadcrumbs collection has any item with the given name
     * If no name is provided, it will use the current route name
     * @param ?string $name
     * @return bool
     */
    public function has(?string $name = null): bool
    {
        $name = $name ?? Route::currentRouteName();

        if ($name === null) {
            return false;
        }

        return $this->items->contains(fn(Breadcrumb $breadcrumb) => $breadcrumb->getLabel() === $name);
    }

    /**
     * Add a new breadcrumb item to the collection.
     * It can be a Breadcrumb instance or a label, url and icon for the new item
     * @param string|Breadcrumb $labelItem
     * @param string|null $url
     * @param string|null $icon
     * @return $this
     */
    public function add(string|Breadcrumb $labelItem, ?string $url = null, ?string $icon = null): self
    {
        if ($labelItem instanceof Breadcrumb) {
            $this->items->push($labelItem);
            return $this;
        }

        $this->items->push(Breadcrumb::make($labelItem, $url, $icon));

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->items;
    }
}
