<?php
/**
 * Represents a content item.
 */

namespace KenticoCloud\Delivery\Models\Taxonomies;

/**
 * Class TaxonomiesResponse
 * @package KenticoCloud\Delivery\Models\Taxonomies
 */
class TaxonomiesResponse
{
    /**
     * TODO:RC
     * @var null
     */
    public $taxonomies = null;
    
    /**
     * TODO:RC
     * @var null
     */
    public $pagination = null;

    /**
     * TaxonomiesResponse constructor.
     * @param ModelBinder $modelBinder
     * @param $obj
     */
    public function __construct($taxonomies, $pagination)
    {
        $this->taxonomies = $taxonomies;
        $this->pagination = $pagination;
        return $this;
    }
}
