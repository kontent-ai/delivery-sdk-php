<?php
/**
 * Represents taxonomy 'terms' element.
 */
namespace KenticoCloud\Delivery\Models\Taxonomies;

/**
 * Class Term
 * @package KenticoCloud\Delivery\Models\Taxonomies
 */
class Term
{
    public $name = null;
    public $codename = null;
    public $terms = null;

    /**
     * Term constructor.
     * @param $name
     * @param $codename
     * @param $terms
     */
    public function __construct($name, $codename, $terms)
    {
        $this->name = $name;
        $this->codename = $codename;
        $this->terms = $terms;
    }
}