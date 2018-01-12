<?php
/**
 * Interface ComplexValueConverterInterface serves for converting complex values to desired types.
 */

namespace KenticoCloud\Delivery;

/**
 * Interface ComplexValueConverterInterface serves for converting complex values to desired types.
 */
interface ComplexValueConverterInterface
{
    /**
     * Converts a given complex value to a specified type.
     *
     * @param $element modular content item element
     * @param null $modularContent JSON response containing nested modular content items
     * @param null $processedItems collection of already processed items (to avoid infinite loops)
     *
     * @return mixed
     */
    public function getComplexValue($element, $modularContent, $processedItems);
}
