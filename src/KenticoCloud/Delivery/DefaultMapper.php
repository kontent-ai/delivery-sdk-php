<?php
/**
 * TODO: PS
 */

namespace KenticoCloud\Delivery;

use \KenticoCloud\Delivery\Helpers\TextHelper;

/**
 * Class DefaultMapper
 * @package KenticoCloud\Delivery
 */
class DefaultMapper implements TypeMapperInterface, PropertyMapperInterface
{
    /**
     * TODO: PS
     * @var string
     */
    private $ci = \KenticoCloud\Delivery\Models\Items\ContentItem::class;
    /**
     * TODO: PS
     * @var string
     */
    private $cis = \KenticoCloud\Delivery\Models\Items\ContentItemSystem::class;

    /**
     * TODO: PS
     * @param $typeName
     * @param null $elementName
     * @param null $parentModelType
     * @return null|string
     */
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null)
    {
        if ($elementName === 'system' && $parentModelType == $this->ci) {
            return $this->cis;
        }
        if ($typeName != null) {
            return $this->ci;


        } else {
            return null;
        }
    }


    /**
     * TODO: PS
     * @param $data
     * @param $modelType
     * @param $property
     * @return array
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
