<?php
/**
 * Retrieves taxonomy as corresponding taxonomy objects.
 */

namespace KenticoCloud\Delivery;

use \KenticoCloud\Delivery\Models\Taxonomies;

/**
 * Class TaxonomyFactory
 * @package KenticoCloud\Delivery
 */
class TaxonomyFactory
{
    /**
     * TaxonomyFactory constructor.
     */
    public function __construct()
    {
    }

    /**
     * Crafts an array of taxonomies
     *
     * Parses response for taxonomies and returns array of
     * _Taxonomy_ objects that represent them. Can be also used to
     * retrieve single taxonomy object from single taxonomy request.
     * See call to _createTaxonomy()_ method.
     *
     * @param $response object response body for taxonomies request.
     * @return array of Taxonomy objects, or single Taxonomy object.
     */
    public function createTaxonomies($response)
    {
        $taxonomies = array();
        if ($this->isInvalidResponse($response)) {
            return $taxonomies;
        }
        
        $taxonomiesData = get_object_vars($response)["taxonomies"];
        foreach ($taxonomiesData as $taxonomy) {
            $taxonomies[] =  $this->prepareTaxonomy($taxonomy);
        }

        return $taxonomies;
    }

    
    /**
     * Creates single taxonomy object.
     *
     * @param $response object Response body for single taxonomy request.
     * @return Null on invalid response, Taxonomy object on valid response.
     */
    public function createTaxonomy($response)
    {
        if ($this->isInvalidResponse($response)) {
            return null;
        }
        return $this->prepareTaxonomy($response);
    }


    /**
     * Crafts Taxonomy object.
     *
     * @param $taxonomyItem object representing single taxonomy item.
     * @return Taxonomy object
     */
    private function prepareTaxonomy($taxonomyItem)
    {
        // Acquire data for 'system' property
        $system = new Models\Taxonomies\TaxonomySystem(
            $taxonomyItem->system->id,
            $taxonomyItem->system->name,
            $taxonomyItem->system->codename,
            $taxonomyItem->system->last_modified
        );

        // Iterate over 'terms' and prepare content for 'terms' property
        $terms = array();
        foreach ($taxonomyItem->terms as $term) {
            $termsItem = new Models\Taxonomies\Term(
                $term->name,
                $term->codename,
                $term->terms
            );
            $terms[] = $termsItem;
        }
        
        $newTaxonomy = new Models\Taxonomies\Taxonomy();
        $newTaxonomy->system = $system;
        $newTaxonomy->terms = $terms;
        
        return $newTaxonomy;
    }


    /**
     * Checks whether response parameter is invalid.
     *
     * @param $response object response body for taxonomies.
     * @return bool True on invalid response body, false on valid.
     */
    private function isInvalidResponse($response)
    {
        if (empty($response) || is_null($response)) {
            return true;
        }
    
        $notTaxonomyFormat = !isset(get_object_vars($response)["taxonomies"]) &&
                             !isset(get_object_vars($response)["system"]);
        return $notTaxonomyFormat;
    }
}
