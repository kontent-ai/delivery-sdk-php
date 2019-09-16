<?php
/**
 * Provides helper methods for operations upon texts and strings.
 */

namespace Kentico\Kontent\Delivery\Helpers;

/**
 * Class TextHelper.
 */
class TextHelper extends Singleton
{
    /**
     * Converts "PascalCase" or "camelCase" text to text where words are delimited by a provided separator.
     * The algorithm is based on http://syframework.alwaysdata.net/decamelize.
     *
     * @param $input text to convert
     * @param string $separator word delimiter
     *
     * @return string
     */
    public function decamelize($input, $separator = '_')
    {
        return strtolower(preg_replace(array('/([a-z\d])([A-Z])/', '/([^'.$separator.'])([A-Z][a-z])/'), '$1'.$separator.'$2', $input));
    }

    /**
     * Converts the given text to "PascalCase".
     * The algorithm is based on http://stackoverflow.com/a/33122760/3363709.
     *
     * @param $input text to convert
     * @param string $separator word delimiter
     *
     * @return string
     */
    public function pascalCase($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    /**
     * Converts the given text to "camelCase".
     *
     * @param $input text to convert
     * @param string $separator word delimiter
     *
     * @return string
     */
    public function camelCase($input, $separator = '_')
    {
        return str_replace($separator, '', lcfirst(ucwords($input, $separator)));
    }
}
