<?php
/**
 * Represents a content item.
 */

namespace KenticoCloud\Delivery\Models\Types;

/**
 * Class ContentTypesResponse
 * @package KenticoCloud\Delivery\Models\Types
 */
class ContentTypesResponse
{
    /**
     * TODO:RC
     * @var null
     */
    public $types = null;
    
    /**
     * TODO:RC
     * @var null
     */
    public $pagination = null;

    /**
     * ContentTypesResponse constructor.
     * @param ModelBinder $modelBinder
     * @param $obj
     */
    public function __construct($types, $pagination)
    {
        $this->types = $types;
        $this->pagination = $pagination;
        return $this;
    }
}
