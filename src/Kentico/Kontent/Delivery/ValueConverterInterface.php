<?php
/**
 * Interface ValueConverterInterface serves for converting simple values to desired types.
 */

namespace Kentico\Kontent\Delivery;

/**
 * Interface ValueConverterInterface serves for converting simple values to desired types.
 */
interface ValueConverterInterface
{
    /**
     * Converts a given simple value to a specified type.
     *
     * @param $type value type
     * @param $value value to convert
     *
     * @return mixed
     */
    public function getValue($type, $value);
}
