<?php

/**
 * Default implementation of the TypeMapperInterface.
 */

namespace KenticoCloud\Delivery;

use KenticoCloud\Delivery\Helpers\TextHelper;

/**
 * Class DefaultMapper serves for resolving strong types based on provided information.
 */
class DefaultMapper implements TypeMapperInterface, PropertyMapperInterface
{
    const ELEMENTS_ATTRIBUTE_NAME = 'elements';

    /**
     * Generic content item model.
     *
     * @var string
     */
    private $ci = \KenticoCloud\Delivery\Models\Items\ContentItem::class;

    /**
     * Content item system element model.
     *
     * @var string
     */
    private $cis = \KenticoCloud\Delivery\Models\Items\ContentItemSystem::class;

    /**
     * Gets strong type based on provided information.
     *
     * @param $typeName name of the type to get (should be a primary source type resolution)
     * @param null $elementName     name of the property whose type should be resolved
     * @param null $parentModelType type of class where the $elementName resides
     *
     * @return null|string
     */
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null)
    {
        if ($elementName === 'system' && $parentModelType == $this->ci) {
            return $this->cis;
        }
        $type = null;
        if ($typeName != null) {
            switch ($typeName) {
                case 'asset':
                    $type = \KenticoCloud\Delivery\Models\Items\Asset::class;
                    break;
                case 'taxonomy':
                    $type = \KenticoCloud\Delivery\Models\Items\TaxonomyTerm::class;
                    break;
                case 'multiple_choice':
                    $type = \KenticoCloud\Delivery\Models\Items\MultipleChoiceOption::class;
                    break;
                case 'date_time':
                    $type = \DateTime::class;
                    break;
                case 'number':
                    $type = float::class;
                    break;
                default:
                    $type = $this->ci;
            }
        }

        return $type;
    }

    /**
     * Returns the correct element from $data based on $modelType and $property.
     *
     * @param $data source data (deserialized JSON)
     * @param $modelType target model type
     * @param $property target property name
     *
     * @return mixed
     */
    public function getProperty($data, $modelType, $property)
    {
        if ($property == self::ELEMENTS_ATTRIBUTE_NAME && $modelType == $this->ci) {
            return get_object_vars($data[self::ELEMENTS_ATTRIBUTE_NAME]);
        } else {
            $index = TextHelper::getInstance()->decamelize($property);
            if (!array_key_exists($index, $data)) {
                // Custom model, search in elements
                $data = get_object_vars($data[self::ELEMENTS_ATTRIBUTE_NAME]);
            }

            return $data[$index];
        }
    }
}
