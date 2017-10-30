<?php
/**
 * TODO: RC
 */


namespace KenticoCloud\Delivery;

/**
 * Interface TypeMapperInterface
 * @package KenticoCloud\Delivery
 */
interface TypeMapperInterface
{
    /**
     * TODO: RC
     * @param $typeName
     * @param null $elementName
     * @param null $parentModelType
     * @return mixed
     */
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null);
}