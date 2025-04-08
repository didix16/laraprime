<?php

namespace Didix16\LaraPrime\UI\Components;

use Didix16\LaraPrime\UI\Metadata\Key;

/**
 * Class Component
 * This class represents a component that can be rendered in a React dynamic component
 */
abstract class Component implements Reactable
{
    /**
     * The name of the component. This name must match with the name of the React component
     * registered in LaraPrime fornt-end component registry
     */
    protected string $name;

    /**
     * The props map of the component
     */
    protected array $props;

    /**
     * Component constructor.
     */
    public function __construct(string $name, array $props = [])
    {
        $this->name = $name;
        $this->props = $props;
    }

    /**
     * Returns the name of the component
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns the props of the component
     */
    public function props(): array
    {
        return $this->props;
    }

    /**
     * Set a prop to the component
     */
    public function setProp(string $key, mixed $value): static
    {
        $this->props[$key] = $value;

        return $this;
    }

    /**
     * Get a prop from the component. Null if prop does not exists
     */
    public function getProp(string $key): mixed
    {
        return $this->props[$key] ?? null;
    }

    /**
     * Returns the component representation to be json serialized
     * to be used in a React dynamic component
     */
    public function jsonSerialize(): mixed
    {
        return [
            Key::NAME() => $this->name(),
            Key::PROPS() => $this->props(),
        ];
    }
}
