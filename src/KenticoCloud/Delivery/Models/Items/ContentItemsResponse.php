<?php
/**
 * Represents a content item.
 */

namespace KenticoCloud\Delivery\Models\Items;

/**
 * Class ContentItemsResponse
 * @package KenticoCloud\Delivery\Models\Items
 */
class ContentItemsResponse
{
    /**
     * TODO:RC
     * @var null
     */
    public $items = null;
    
    /**
     * TODO:RC
     * @var null
     */
    public $pagination = null;

    /**
     * ContentItemsResponse constructor.
     * @param ModelBinder $modelBinder
     * @param $obj
     */
    public function __construct($items, $pagination)
    {
        $this->items = $items;
        $this->pagination = $pagination;
        return $this;
    }
}
