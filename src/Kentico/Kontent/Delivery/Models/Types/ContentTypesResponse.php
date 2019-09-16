<?php
/**
 * Represents a content item.
 */

namespace Kentico\Kontent\Delivery\Models\Types;

/**
 * Class ContentTypesResponse
 * @package Kentico\Kontent\Delivery\Models\Types
 */
class ContentTypesResponse
{
    /**
     * Returns an array of content types.
     * @var null
     */
    public $types = null;
    
    /**
     * Gets data about the page size, current page, etc.
     * @var null
     */
    public $pagination = null;

    /**
     * ContentTypesResponse constructor.
     * @param $types Array of content types.
     * @param $pagination Pagination information (size, offset, etc.)
     */
    public function __construct($types, $pagination)
    {
        $this->types = $types;
        $this->pagination = $pagination;
        return $this;
    }
}
