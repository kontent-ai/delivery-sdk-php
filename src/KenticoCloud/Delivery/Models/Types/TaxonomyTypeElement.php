<?php
/**
 * Represents content type element with taxonomy_group property.
 */

namespace KenticoCloud\Delivery\Models\Types;

use \KenticoCloud\Delivery\Models;

/**
 * Class TaxonomyTypeElement
 * @package KenticoCloud\Delivery\Models\Types
 */
class TaxonomyTypeElement extends ContentTypeElement
{
    /**
     * Taxonomy Group
     * @var string
     */
    public $taxonomyGroup = null;

    /**
     * TaxonomyTypeElement constructor.
     * @param $type
     * @param $codename
     * @param $name
     * @param $taxonomy_group
     */
    public function __construct($type, $codename, $name, $taxonomyGroup)
    {
        $this->taxonomyGroup = $taxonomyGroup;
        parent::__construct($type, $codename, $name);
    }
}
