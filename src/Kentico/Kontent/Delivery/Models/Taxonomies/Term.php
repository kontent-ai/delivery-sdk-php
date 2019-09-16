<?php
/**
 * Represents taxonomy 'terms' element.
 */

namespace Kentico\Kontent\Delivery\Models\Taxonomies;

/**
 * Class Term.
 */
class Term
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

    /**
     * Gets nested taxonomy terms.
     *
     * @var null
     */
    public $terms = array();

    /**
     * Taxonomy term empty constructor.
     * 
     * Constructor used when Taxonomy term is supposed to be created
     * but not loaded with data.
     */
    public function __construct() {}
}
