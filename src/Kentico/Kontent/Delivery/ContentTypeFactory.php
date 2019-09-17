<?php
/**
 * Retrieves content types as corresponding content type objects.
 */

namespace Kentico\Kontent\Delivery;

use Kentico\Kontent\Delivery\Models\Types;

/**
 * Class ContentTypeFactory.
 */
class ContentTypeFactory
{
    /**
     * ContentTypeFactory constructor.
     */
    public function __construct()
    {
    }

    /**
     * Crafts an array of content types.
     *
     * Parses response for types and returns mixed array of
     * _MultipleOptionsTypeElement_, _TaxonomyTypeElement_ and
     * _ContentTypeElement_ objects that represent them.
     *
     * @param $typesData types part of the body of the content type response
     *
     * @return mixed array
     */
    public function createTypes($typesData)
    {
        $types = array();
        if (empty($typesData) || is_null($typesData)) {
            return $types;
        }

        foreach ($typesData as $type) {
            $types[] = $this->createType($type);
        }

        return $types;
    }

    /**
     * Crafts a model of content type.
     *
     * @param $typesData type part of the body of the content type response
     *
     * @return mixed array
     */
    public function createType($type)
    {
        if (!isset($type->system)) {
            return null;
        }
        // Acquire data for 'system' property
        $system = new Models\Types\ContentTypeSystem(
            $type->system->id,
            $type->system->name,
            $type->system->codename,
            $type->system->last_modified
            );

        // Iterate over 'elements' and prepare content for 'elements' property
        $elements = array();
        foreach ($type->elements as $codename => $element) {
            $elements[] = $this->createElement($element, $codename);
        }

        // Create content type object with it's values
        $newType = new Models\Types\ContentType();
        $newType->system = $system;
        $newType->elements = $elements;

        return $newType;
    }

    /**
     * Crafts a model of a content type element.
     *
     * @param [type] $element  content type element data
     * @param [type] $codename codename of the content type element
     */
    public function createElement($element, $codename)
    {
        // Create types of ContentTypeElement with different properties
        if (isset($element->options)) {
            $options = $this->loadOptions($element->options);
            $newElement = new Models\Types\MultipleOptionsTypeElement(
                $element->type,
                $codename,
                $element->name,
                $options
            );
        } elseif (isset($element->taxonomy_group)) {
            $newElement = new Models\Types\TaxonomyTypeElement(
                $element->type,
                $codename,
                $element->name,
                $element->taxonomy_group
            );
        } elseif (isset($element->type)) {
            $newElement = new Models\Types\ContentTypeElement(
                $element->type,
                $codename,
                $element->name
            );
        } else {
            $newElement = null;
        }

        return $newElement;
    }

    /**
     * Transforms response option items to MultipleChoiceOption objects.
     *
     * Returned Types\MultipleChoiceOption objects are different from
     * MultipleChoiceOption objects used with ContentItem objects.
     *
     * @param $optionItems
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
