<?php
/**
 * Retrieves content types as corresponding content type objects.
 */

namespace KenticoCloud\Delivery;

use \KenticoCloud\Delivery\Models\Types;

/**
 * Class ContentTypeFactory
 * @package KenticoCloud\Delivery
 */
class ContentTypeFactory
{
    public function __construct()
    {
    }

    /**
     * Crafts an array of content types
     *
     * Parses response for types and returns mixed array of
     * _MultipleOptionsTypeElement_, _TaxonomyTypeElement_ and
     * _ContentTypeElement_ objects that represent them.
     *
     * @param $response object response body for content type request.
     * @return mixed array
     */
    public function createTypes($response)
    {
        $types = array();
        if (empty($response) || is_null($response)) {
            return $types;
        }

        $typesData = get_object_vars($response)['types'];
        foreach ($typesData as $type) {
            // Acquire data for 'system' property
            $system = new Models\Types\ContentTypeSystem(
                $type->system->id,
                $type->system->name,
                $type->system->codename,
                $type->system->last_modified
                );
            
            // Iterate over 'elements' and prepare content for 'elements' property
            $i = 0;
            $elements = array();
            $codenames = array_keys(get_object_vars($type->elements));
            foreach ($type->elements as $element) {
                // Create types of ContentTypeElement with different properties
                if (isset($element->options)) {
                    $options = $this->loadOptions($element->options);
                    $newElement = new Models\Types\MultipleOptionsTypeElement(
                        $element->type,
                        $codenames[$i],
                        $element->name,
                        $options
                    );
                } elseif (isset($element->taxonomy_group)) {
                    $newElement = new Models\Types\TaxonomyTypeElement(
                        $element->type,
                        $codenames[$i],
                        $element->name,
                        $element->taxonomy_group
                    );
                } else {
                    $newElement = new Models\Types\ContentTypeElement(
                        $element->type,
                        $codenames[$i],
                        $element->name
                    );
                }

                $elements[] = $newElement;
                $i++;
            }
            
            // Create content type object with it's values
            $newType = new Models\Types\ContentType();
            $newType->system = $system;
            $newType->elements = $elements;

            $types[] = $newType;
        }
        return $types;
    }


    /**
     * Transforms response option items to MultipleChoiceOption objects.
     *
     * Returned Types\MultipleChoiceOption objects are different from
     * MultipleChoiceOption objects used with ContentItem objects.
     *
     * @return array of Types\MultipleChoiceOption
     */
    private function loadOptions($optionItems)
    {
        $options = array();
        foreach ($optionItems as $option) {
            $options[] = new Models\Shared\MultipleChoiceOption($option->name, $option->codename);
        }

        return $options;
    }
}
