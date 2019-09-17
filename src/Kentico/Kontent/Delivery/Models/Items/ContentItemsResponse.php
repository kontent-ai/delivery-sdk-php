<?php
/**
 * Represents a content item.
 */

namespace Kentico\Kontent\Delivery\Models\Items;

/**
 * Class ContentItemsResponse
 * @package Kentico\Kontent\Delivery\Models\Items
 */
class ContentItemsResponse
{
    /**
     * Returns an array of content items.
     * @var null
     */
    public $items = null;
    
    /**
     * Gets data about the page size, current page, etc.
     * @var null
     */
    public $pagination = null;

    /**
     * ContentItemsResponse constructor.
     * @param $items Array of content items.
     * @param $pagination Pagination information (size, offset, etc.)
     */
    public function __construct($items, $pagination)
    {
        $this->items = $items;
        $this->pagination = $pagination;
        return $this;
    }
}
