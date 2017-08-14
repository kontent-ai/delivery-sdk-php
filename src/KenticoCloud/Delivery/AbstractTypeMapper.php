<?php

namespace KenticoCloud\Delivery;

abstract class AbstractTypeMapper implements TypeMapperInterface
{
    public $types = array();

    abstract protected function getDefaultTypeClass();

    public function getTypeClass($typeName, $elementName = null, $parentModelType = null)
    {        
        return isset($this->types[$typeName]) ? $this->types[$typeName] : $this->getDefaultTypeClass();
    }
}
