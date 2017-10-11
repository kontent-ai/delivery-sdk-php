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
     * Gets the system attributes of the content item.
     * TODO: RC specify type
     * @var null
     */
    public $system = null;
    /**
     * TODO: RC specify type
     * Gets the dynamic view of the JSON response where elements and their properties can be retrieved by name, for example <c>item.Elements.description.value</c>;
     * @var null
     */
    public $elements = null;
}
