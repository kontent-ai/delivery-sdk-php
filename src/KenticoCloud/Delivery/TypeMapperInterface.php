<?php
/**
 * TODO: PS
 */


namespace KenticoCloud\Delivery;

/**
 * Interface TypeMapperInterface serves for resolving strong types based on provided information.
 * @package KenticoCloud\Delivery
 */
interface TypeMapperInterface
{
    /**
     * Gets strong type based on provided information.
     * @param $typeName Name of the type to get (should be a primary source type resolution).
     * @param null $elementName Name of the property whose type should be resolved.
     * @param null $parentModelType Type of class where the $elementName resides.
     * @return mixed
     */
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null);
}