<?php
/**
 * Retrieves taxonomy as corresponding taxonomy objects.
 */

namespace Kentico\Kontent\Delivery;

use \Kentico\Kontent\Delivery\Models\Taxonomies;

/**
 * Class TaxonomyFactory
 * @package Kentico\Kontent\Delivery
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
     * @return Taxonomy array of Taxonomy objects, or single Taxonomy object.
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
        $terms = $this->prepareTerms($taxonomyItem->terms);
        $newTaxonomy = new Models\Taxonomies\Taxonomy();
        $newTaxonomy->system = $system;
        $newTaxonomy->terms = $terms;
        
        return $newTaxonomy;
    }


    /**
     * Prepares possibly recursive Taxonomy structure.
     *
     * @param $terms Terms array of terms.
     * @return Term array of Term objects.
     */
    private function prepareTerms($terms)
    {
        $compositeTerms = array();

        foreach ($terms as $term) {

            if (!empty($term->terms) && $term->terms != null) {
                // Recursively prepare terms
                $craftedTerms = $this->prepareTerms($term->terms);
            }
            else {
                $craftedTerms = $term->terms;
            }

            $termsItem = new Models\Taxonomies\Term();
            $termsItem->name = $term->name;
            $termsItem->codename = $term->codename;
            $termsItem->terms = $craftedTerms;

            $compositeTerms[] = $termsItem;
        }

        return $compositeTerms;
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
