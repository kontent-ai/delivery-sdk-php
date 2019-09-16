<?php
/**
 * Represents content type element with taxonomy_group property.
 */

namespace Kentico\Kontent\Delivery\Models\Types;

/**
 * Class TaxonomyTypeElement.
 */
class TaxonomyTypeElement extends ContentTypeElement
{
    /**
     * Taxonomy Group.
     *
     * @var string
     */
    public $taxonomyGroup = null;

    /**
     * TaxonomyTypeElement constructor.
     *
     * @param $type type of a content type element
     * @param $codename code name of a content type element
     * @param $name display name of a content type element
     * @param $taxonomy_group corresponding taxonomy group (type)
     */
    public function __construct($type, $codename, $name, $taxonomyGroup)
    {
        $this->taxonomyGroup = $taxonomyGroup;
        parent::__construct($type, $codename, $name);
    }
}
