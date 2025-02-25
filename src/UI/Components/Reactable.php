<?php

namespace Didix16\LaraPrime\UI\Components;

use JsonSerializable;

/**
 * Interface Reactable
 * This interface defines the contract that a component must implement to be rendered as a json serializable object
 * that can be used in a React dynamic component.
 */
interface Reactable extends JsonSerializable {}
