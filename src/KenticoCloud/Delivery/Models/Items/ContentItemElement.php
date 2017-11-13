<?php
/**
 * Represents a content element.
 */

namespace KenticoCloud\Delivery\Models\Items;

/**
 * Class ContentItemElement
 * @package KenticoCloud\Delivery\Models\Items
 */
class ContentItemElement
{
    /**
     * @var string
     * Gets the type of the content element, for example "multiple_choice".
     */
    public $type = null;
    /**
     * @var null
     * Gets the value of the content element.
     */
    public $value = null;
    /**
     * @var string
     * Gets the name of the content element.
     */
    public $name = null;
}
