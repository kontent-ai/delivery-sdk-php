<?php
/**
 * Represents single taxonomy item.
 */

namespace Kontent\Ai\Delivery\Models\Taxonomies;

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
