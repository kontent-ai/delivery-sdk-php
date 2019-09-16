<?php
/**
 * Represents a content element.
 */

namespace Kentico\Kontent\Delivery\Models\Items;

/**
 * Class ContentItemElement.
 */
class ContentItemElement
{
    /**
     *  Gets the type of the content element, for example "multiple_choice".
     *
     * @var string
     */
    public $type = null;
    /**
     * Gets the value of the content element.
     *
     * @var null
     */
    public $value = null;
    /**
     * Gets the name of the content element.
     *
     * @var string
     */
    public $name = null;
}
