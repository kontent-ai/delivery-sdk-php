<?php
/**
 * Interface PropertyMapperInterface serves for mapping model properties to data in JSON responses.
 */

namespace KenticoCloud\Delivery;

/**
 * Interface PropertyMapperInterface serves for mapping model properties to data in JSON responses.
 * @package KenticoCloud\Delivery
 */
interface PropertyMapperInterface
{
    /**
     * Returns the correct element from $data based on $modelType and $property.
     * @param $data Source data (deserialized JSON).
     * @param $modelType Target model type.
     * @param $property Target property name.
     * @return mixed
     */
    public function getProperty($data, $modelType, $property);
}