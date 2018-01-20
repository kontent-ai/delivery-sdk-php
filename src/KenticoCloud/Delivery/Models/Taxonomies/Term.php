<?php
/**
 * Represents taxonomy 'terms' element.
 */

namespace KenticoCloud\Delivery\Models\Taxonomies;

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
     * Taxonomy term constructor.
     *
     * @param $name display name of taxonomy term
     * @param $codename string code name of taxonomy term
     * @param $terms Term array Taxonomy terms
     */
    public function __construct($name, $codename, $terms)
    {
        $this->name = $name;
        $this->codename = $codename;
        $this->terms = $terms;
    }
}
