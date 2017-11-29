<?php
/**
 * Represents a taxonomy term assigned to a Taxonomy element of a content item.
 */

namespace KenticoCloud\Delivery\Models\Items;

/**
 * Class TaxonomyTerm.
 */
class TaxonomyTerm
{
    /**
     * Gets the name of the taxonomy term.
     *
     * @var string
     */
    public $name = null;
    /**
     * Gets the codename of the taxonomy term.
     *
     * @var string
     */
    public $codename = null;
}
