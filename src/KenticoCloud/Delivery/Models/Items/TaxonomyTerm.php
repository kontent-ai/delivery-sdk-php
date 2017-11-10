<?php
/**
 * Represents a taxonomy term assigned to a Taxonomy element of a content item.
 */

namespace KenticoCloud\Delivery\Models\Items;

/**
 * Class TaxonomyTerm
 * @package KenticoCloud\Delivery\Models\Items
 */
class TaxonomyTerm
{
    /**
     * @var string
     * Gets the name of the taxonomy term.
     */
    public $name = null;
    /**
     * @var string
     * Gets the codename of the taxonomy term.
     */
    public $codename = null;
}
