<?php

namespace Didix16\LaraPrime\UI\Breadcrumbs;

use Didix16\LaraPrime\Traits\Makeable;
use Illuminate\Support\Facades\Route;
use JsonSerializable;

class Breadcrumb implements JsonSerializable
{
    use Makeable;

    /**
     * The label of the breadcrumb
     */
    protected string $label;

    /**
     * Show the label of the breadcrumb
     */
    protected bool $showLabel;

    /**
     * The url or path of the breadcrumb
     */
    protected ?string $url;

    /**
     * The icon of the breadcrumb.
     * It should be in the format of react-icons package name (react-icons/<package/IconName>): package/IconName where
     * - package is hi2 (heroicons 2). In the future we will add more packages
     * - IconName is the name of the icon component in the package
     */
    protected ?string $icon;

    /**
     * Build a new Breadcrumb instance
     */
    public function __construct(string $label, ?string $url = null, ?string $icon = null, bool $showLabel = true)
    {
        $this->label = $label;
        $this->url = $url;
        $this->icon = $icon;
        $this->showLabel = $showLabel;
    }

    /**
     * Get the label of the breadcrumb
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the url of the breadcrumb if any
     */
    public function getUrl(): ?string
    {
        return Route::has($this->url) ? route($this->url) : $this->url;
    }

    /**
     * Get the icon of the breadcrumb if any
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Set the label of the breadcrumb
     *
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the url of the breadcrumb
     *
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set the icon of the breadcrumb
     *
     * @return $this
     */
    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Prepare the breadcrumb to be serialized to JSON for menu rendering
     *
     * @return array{name: string, url: string|null, icon: string|null}
     */
    public function jsonSerialize(): mixed
    {
        return [
            'label'     =>  $this->label,
            'showLabel' =>  $this->showLabel,
            'url'       =>  $this->getUrl(),
            'icon'      =>  $this->icon,
        ];
    }
}
