<?php

namespace KenticoCloud\Delivery;

abstract class AbstractTypeMapper implements TypeMapperInterface
{
    public $types = array();

    abstract protected function getDefaultTypeClass();

    public function getTypeClass($type)
    {
        $self = get_called_class();
        return isset($this->types[$type]) ? $this->types[$type] : $this->getDefaultTypeClass();
    }
}
