<?php
/**
 * Interface PropertyMapperInterface serves for mapping model properties to data in JSON responses.
 */

namespace Kentico\Kontent\Delivery;

/**
 * Interface PropertyMapperInterface serves for mapping model properties to data in JSON responses.
 */
interface PropertyMapperInterface
{
    /**
     * Returns the correct element from $data based on $modelType and $property.
     *
     * @param $data source data (deserialized JSON)
     * @param $property target property name
     *
     * @return mixed
     */
    public function getProperty($data, $property);

    /**
     * Gets an array of properties of a model that need to be filled with data.
     *
     * @param $model model to examine
     * @param $data  source JSON data
     *
     * @return array
     */
    public function getModelProperties($model, $data);
}
