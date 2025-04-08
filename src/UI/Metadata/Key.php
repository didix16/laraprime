<?php

namespace Didix16\LaraPrime\UI\Metadata;

use Didix16\LaraPrime\Traits\InvokableCases;

/**
 * Enum Key
 * This class represents the keys that can be used in the metadata of a component
 * and the layout tree.
 *
 * @method static string ID()
 * @method static string NAME()
 * @method static string PROPS()
 * @method static string LAYOUT()
 * @method static string TYPE()
 * @method static string VALUE()
 * @method static string CHILDREN()
 */
enum Key: string
{
    use InvokableCases;

    case ID = 'i';
    case NAME = 'n';
    case PROPS = 'p';
    case LAYOUT = 'l';
    case TYPE = 't';
    case VALUE = 'v';
    case CHILDREN = 'c';
}
