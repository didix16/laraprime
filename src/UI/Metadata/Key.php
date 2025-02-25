<?php

namespace Didix16\LaraPrime\UI\Metadata;

/**
 * Enum Key
 * This class represents the keys that can be used in the metadata of a component
 * @package Didix16\LaraPrime\UI\Metadata
 */
enum Key: string
{
    case ID = 'i';
    case NAME = 'n';
    case PROPS = 'p';
    case LAYOUT = 'l';
    case TYPE = 't';
    case VALUE = 'v';
    case CHILDREN = 'c';
}
