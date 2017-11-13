<?php
/**
 * Default implementation of the TypeMapperInterface.
 */

namespace KenticoCloud\Delivery;

use \KenticoCloud\Delivery\Helpers\TextHelper;

/**
 * Class DefaultMapper serves for resolving strong types based on provided information.
 * @package KenticoCloud\Delivery
 */
class DefaultMapper implements TypeMapperInterface, PropertyMapperInterface
{
    /**
     * Generic content item model.
     * @var string
     */
    private $ci = \KenticoCloud\Delivery\Models\Items\ContentItem::class;
    
    /**
     * Content item system element model.
     * @var string
     */
    private $cis = \KenticoCloud\Delivery\Models\Items\ContentItemSystem::class;

    /**
     * Gets strong type based on provided information.
     * @param $typeName Name of the type to get (should be a primary source type resolution).
     * @param null $elementName Name of the property whose type should be resolved.
     * @param null $parentModelType Type of class where the $elementName resides.
     * @return null|string
     */
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null)
    {
        if ($elementName === 'system' && $parentModelType == $this->ci) {
            return $this->cis;
        }
        if ($typeName != null) {
            switch ($typeName) {
                case 'asset':
                    return \KenticoCloud\Delivery\Models\Items\Asset::class;
                case 'taxonomy':
                    return \KenticoCloud\Delivery\Models\Items\TaxonomyTerm::class;
                case 'multiple_choice':
                    return \KenticoCloud\Delivery\Models\Items\MultipleChoiceOption::class;
                case 'date_time':
                    return \DateTime::class;
                case 'number':
                    return float::class;
                default:
                    return $this->ci;
            }
        } else {
            return null;
        }
    }

    /**
     * Returns the correct element from $data based on $modelType and $property.
     * @param $data Source data (deserialized JSON).
     * @param $modelType Target model type.
     * @param $property Target property name.
     * @return mixed
     */
    public function getProperty($data, $modelType, $property)
    {
        if ($property == 'elements' && $modelType == $this->ci) {
            return get_object_vars($data['elements']);
        } else {
            $index = TextHelper::getInstance()->decamelize($property);
            if (!array_key_exists($index, $data)) {
                // Custom model, search in elements
                $data = get_object_vars($data['elements']);
            }
            return $data[$index];
        }
    }
}
