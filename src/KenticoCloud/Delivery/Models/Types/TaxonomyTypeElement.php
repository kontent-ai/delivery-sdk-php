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
    public $taxonomy_group = null;

    /**
     * TaxonomyTypeElement constructor.
     * @param $type
     * @param $codename
     * @param $name
     * @param $taxonomy_group
     */
    public function __construct($type, $codename, $name, $taxonomy_group)
    {
        $this->taxonomy_group = $taxonomy_group;
        parent::__construct($type, $codename, $name);
    }
}
