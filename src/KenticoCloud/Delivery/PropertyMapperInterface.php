<?php
/**
 * Interface PropertyMapperInterface serves for mapping model properties to data in JSON responses.
 */

namespace KenticoCloud\Delivery;

/**
 * Interface PropertyMapperInterface serves for mapping model properties to data in JSON responses.
 */
interface PropertyMapperInterface
{
    /**
     * Returns the correct element from $data based on $modelType and $property.
     *
     * @param $data source data (deserialized JSON)
     * @param $modelType target model type
     * @param $property target property name
     *
     * @return mixed
     */
    public function getProperty($data, $property);

    public function getModelProperties($data, $model);
}
