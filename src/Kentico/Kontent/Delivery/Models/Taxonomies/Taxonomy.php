<?php
/**
 * Represents single taxonomy item.
 */

namespace Kentico\Kontent\Delivery\Models\Taxonomies;

/**
 * Class Taxonomy.
 */
class Taxonomy
{
    /**
     * Taxonomy metadata.
     *
     * @var
     */
    public $system;

    /**
     * Array that content taxonomy terms in the taxonomy group.
     *
     * @var mixed[]
     */
    public $terms;
}
