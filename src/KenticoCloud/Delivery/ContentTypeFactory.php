<?php
namespace KenticoCloud\Delivery;
use \KenticoCloud\Delivery\Models;
use \KenticoCloud\Delivery\Models\Types;

/**
 * ContentTypeFactory
 *
 * Retrieves content types as corresponding content type objects.
 */
class ContentTypeFactory
{
    public function __construct(){ }

    /**
     * Crafts an array of content types 
     *
     * Parses response for types and returns mixed array of 
     * _MultipleOptionsTypeElement_, _TaxonomyTypeElement_ and 
     * _ContentTypeElement_ objects that represent them.
     *
     * @param object $response HttpFull response body for content type request.
     *
     * @return mixed array 
     */
    public function createTypes($response)
    {
        $types = array();
        if (empty($response) || is_null($response))
        {
            return $types;
        }

        $typesData = get_object_vars($response)['types'];
        foreach ($typesData as $type)
        {
            // Acquire data for 'system' property
            $system = new Models\ContentTypeSystem(
                $type->system->id,
                $type->system->name,
                $type->system->codename,
                $type->system->last_modified
                );
            
            // Iterate over 'elements' and prepare content for 'elements' property
            $i = 0;
            $elements = array();
            $codenames = array_keys(get_object_vars($type->elements));
            foreach ($type->elements as $element)
            {
                // Create types of ContentTypeElement with different properties
                if (isset($element->options))
                {
                    $options = $this->loadOptions($element->options);
                    $newElement = new Models\Types\MultipleOptionsTypeElement(
                        $element->type,
                        $codenames[$i],
                        $element->name,
                        $options
                    );
                }
                elseif (isset($element->taxonomy_group))
                {
                    $newElement = new Models\Types\TaxonomyTypeElement(
                        $element->type,
                        $codenames[$i], 
                        $element->name, 
                        $element->taxonomy_group
                    );
                }
                else
                {
                    $newElement = new Models\ContentTypeElement(
                        $element->type,
                        $codenames[$i],
                        $element->name
                    );
                }

                #$this->debug_TypeElement($newElement);
                $elements[] = $newElement;
                $i++;
            }
            
            // Create content type object with it's values
            $newType = new Models\ContentType();
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
     * @return MultipleChoiceOption array 
     */
    private function loadOptions($optionItems)
    {
        $options = array();
        foreach ($optionItems as $option)
        {
            $options[] = new Models\Types\MultipleChoiceOption($option->name, $option->codename);
        }

        return $options;
    }

    #region "Debug functions, to be removed"
    /*
    private function debug_PrintTypeSystem($typeSystem)
    {
        echo "\n";
        echo "Printing out Type System:\n";
        echo "\tID: " . $typeSystem->id . "\n";
        echo "\tName: " . $typeSystem->name . "\n";
        echo "\tCodename: " . $typeSystem->codename . "\n";
        echo "\tLastModified: " . $typeSystem->lastModified . "\n";
        echo "\n";
    }

    private function debug_TypeElement($typeElement)
    {
        echo "\n";
        echo "Printing out Content Type Element:\n";
        echo "\tType: " . $typeElement->type . "\n";
        echo "\tCodename: " . $typeElement->codename . "\n";
        echo "\tName: " . $typeElement->name . "\n";
        
        if (is_a($typeElement, \KenticoCloud\Delivery\Models\Types\MultipleOptionsTypeElement::class))
        {
            echo "\tOptions(" . count($typeElement->options) . "):\n";
            if (isset($typeElement->options))
            {
                $i = 0;
                foreach($typeElement->options as $option)
                {
                    echo "\t\t[$i](" . $option->name . ", ". $option->codename  .")\n";
                    $i++;
                }
            }
            echo "\n";
        }
        elseif (is_a($typeElement, \KenticoCloud\Delivery\Models\Types\TaxonomyTypeElement::class))
        {
            echo "\tTaxonomy group: " . $typeElement->taxonomy_group . "\n";
        }
    }
    */
    #endregion
}