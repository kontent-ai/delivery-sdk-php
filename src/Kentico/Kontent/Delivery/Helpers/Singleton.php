<?php
/**
 * Ensures a singleton instance for successors.
 */

namespace Kentico\Kontent\Delivery\Helpers;

/**
 * Class Singleton.
 */
abstract class Singleton
{
    /**
     * Singleton constructor.
     */
    protected function __construct()
    {
    }

    /**
     * Get a singleton instance.
     *
     * @return mixed
     */
    final public static function getInstance()
    {
        static $aoInstance = array();

        $calledClassName = get_called_class();

        if (!isset($aoInstance[$calledClassName])) {
            $aoInstance[$calledClassName] = new $calledClassName();
        }

        return $aoInstance[$calledClassName];
    }

    /**
     * @codeCoverageIgnore
     * Disable cloning (there is no reason to clone).
     */
    private function __clone()
    {
    }
}
