<?php
namespace KenticoCloud\Delivery\Models\Types;
use \KenticoCloud\Delivery\Models;

/**
 * TaxonomyTypeElement
 *
 * Represents content type element with taxonomy_group property.
 */
class TaxonomyTypeElement extends Models\ContentTypeElement
{
    public $taxonomy_group = null;

    public function __construct($type, $codename, $name, $taxonomy_group)
    {
        $this->taxonomy_group = $taxonomy_group;
        parent::__construct($type, $codename, $name);
    }
}