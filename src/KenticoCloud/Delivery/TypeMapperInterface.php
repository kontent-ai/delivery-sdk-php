<?php

namespace KenticoCloud\Delivery;

interface TypeMapperInterface
{
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null);
}