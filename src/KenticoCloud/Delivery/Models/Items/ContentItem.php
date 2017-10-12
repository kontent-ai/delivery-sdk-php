<?php
/**
 * Represents a content item.
 */

namespace KenticoCloud\Delivery\Models\Items;

/**
 * Class ContentItem
 * @package KenticoCloud\Delivery\Models\Items
 */
class ContentItem
{
    /**
     * Content item metadata
     * @var ContentItemSystem
     */
    public $system = null;
    /**
     * Gets an array that contains elements of the content item indexed by their codename.
     * @var ContentItemElement[]
     */
    public $elements = null;
}
