<?php
/**
 * Interface TypeMapperInterface serves for resolving strong types based on provided information.
 */

namespace KenticoCloud\Delivery;

/**
 * Interface TypeMapperInterface serves for resolving strong types based on provided information.
 */
interface TypeMapperInterface
{
    /**
     * Gets strong type based on provided information.
     *
     * @param $typeName name of the type to get (should be a primary source type resolution)
     * @param null $elementName     name of the property whose type should be resolved
     * @param null $parentModelType type of class where the $elementName resides
     *
     * @return mixed
     */
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null);
}
