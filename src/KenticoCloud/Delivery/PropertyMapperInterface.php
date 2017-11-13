<?php
/**
 * TODO: PS
 */

namespace KenticoCloud\Delivery;

/**
 * Interface PropertyMapperInterface serves for mapping model properties to data in JSON responses.
 * @package KenticoCloud\Delivery
 */
interface PropertyMapperInterface
{
    /**
     * TODO: PS
     * @param $data
     * @param $modelType
     * @param $property
     * @return mixed
     */
    public function getProperty($data, $modelType, $property);
}