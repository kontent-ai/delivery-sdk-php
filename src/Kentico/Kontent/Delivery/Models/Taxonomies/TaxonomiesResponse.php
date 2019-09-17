<?php
/**
 * Represents a content item.
 */

namespace Kentico\Kontent\Delivery\Models\Taxonomies;

/**
 * Class TaxonomiesResponse
 * @package Kentico\Kontent\Delivery\Models\Taxonomies
 */
class TaxonomiesResponse
{
    /**
     * Returns an array of taxonomy terms.
     * @var null
     */
    public $taxonomies = null;
    
    /**
     * Gets data about the page size, current page, etc.
     * @var null
     */
    public $pagination = null;

    /**
     * TaxonomiesResponse constructor.
     * @param $taxonomies Array of taxonomy terms.
     * @param $pagination Pagination information (size, offset, etc.)
     */
    public function __construct($taxonomies, $pagination)
    {
        $this->taxonomies = $taxonomies;
        $this->pagination = $pagination;
        return $this;
    }
}
