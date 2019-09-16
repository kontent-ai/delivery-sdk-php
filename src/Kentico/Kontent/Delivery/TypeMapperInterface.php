<?php
/**
 * Interface TypeMapperInterface serves for resolving strong types based on provided information.
 */

namespace Kentico\Kontent\Delivery;

/**
 * Interface TypeMapperInterface serves for resolving strong types based on provided information.
 */
interface TypeMapperInterface
{
    /**
     * Gets strong type based on provided information.
     *
     * @param $typeName name of the type to get (should be a primary source type resolution)
     *
     * @return mixed
     */
    public function getTypeClass($typeName);
}
