<?php

namespace KenticoCloud\Delivery;

use \KenticoCloud\Delivery\Helpers\TextHelper;

class DefaultMapper implements TypeMapperInterface, PropertyMapperInterface
{
    private $ci = \KenticoCloud\Delivery\Models\ContentItem::class;
    private $cis = \KenticoCloud\Delivery\Models\ContentItemSystem::class;

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
