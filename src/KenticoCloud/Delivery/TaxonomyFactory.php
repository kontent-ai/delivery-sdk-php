<?php
namespace KenticoCloud\Delivery;
use \KenticoCloud\Delivery\Models;

/**
 * TaxonomyFactory
 *
 * Retrieves taxonomy as corresponding taxonomy objects
 */
class TaxonomyFactory
{
    public function __construct(){ }

    /**
     * Crafts an array of taxonomies
     *
     * Parses response for taxonomies and returns array of 
     * _Taxonomy_ objects that represent them.
     * 
     * @param $response object response body for taxonomies request.
     *
     * @return array of Taxonomy objects
     */
    public function createTaxonomies($response)
    {
        $taxonomies = array();
        if ($this->isInvalidResponse($response))
        {
            return $taxonomies;
        }

        $taxonomiesData = get_object_vars($response)["taxonomies"];
        foreach($taxonomiesData as $taxonomy)
        {
            // Acquire data for 'system' property
            $system = new Models\TaxonomySystem(
                $taxonomy->system->id,
                $taxonomy->system->name,
                $taxonomy->system->codename,
                $taxonomy->system->last_modified
            );

            // Iterate over 'terms' and prepare content for 'terms' property
            $terms = array();
            foreach ($taxonomy->terms as $term)
            {
                $termsItem = new Models\Taxonomies\Term(
                    $term->name,
                    $term->codename,
                    $term->terms
                );
                $terms[] = $termsItem;
            }
            
            $newTaxonomy = new Models\Taxonomy();
            $newTaxonomy->system = $system;
            $newTaxonomy->terms = $terms;
            
            $taxonomies[] = $newTaxonomy;

        }

        return $taxonomies;
    }


    /**
     * Checks whether response parameter is invalid.
     *
     * @param $response object response body for taxonomies.
     * @return bool True on invalid response body, false on valid.
     */
    private function isInvalidResponse($response)
    {
        return empty($response) || is_null($response);
    }
}