<?php
/**
 * TODO: PS
 */


namespace KenticoCloud\Delivery;

/**
 * Interface TypeMapperInterface
 * @package KenticoCloud\Delivery
 */
interface TypeMapperInterface
{
    /**
     * TODO: PS
     * @param $typeName
     * @param null $elementName
     * @param null $parentModelType
     * @return mixed
     */
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null);
}