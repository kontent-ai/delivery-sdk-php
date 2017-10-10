<?php

namespace KenticoCloud\Delivery;

/**
 * Interface TypeMapperInterface
 * @package KenticoCloud\Delivery
 */
interface TypeMapperInterface
{
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null);
}