<?php
/**
 * Base metadata shared for all objects.
 */

namespace KenticoCloud\Delivery\Models\Shared;

/**
 * Class SystemBase
 * @package KenticoCloud\Delivery\Models\Shared
 */
class SystemBase
{
    /**
     * TODO:
     * @var null
     */
    public $id = null;
    /**
     * TODO: PS
     * @var null
     */
    public $name = null;
    /**
     * TODO: PS
     * @var null
     */
    public $codename = null;
    /**
     * TODO: PS
     * @var null
     */
    public $lastModified = null;

    /**
     * SystemBase constructor.
     * @param $id
     * @param $name
     * @param $codename
     * @param $lastModified
     */
    public function __construct($id, $name, $codename, $lastModified)
    {
        $this->id = $id;
        $this->name = $name;
        $this->codename = $codename;
        $this->lastModified = $lastModified;
    }


    /**
     * Returns 'lastModified' property in requested format.
     *
     * @param $format string in which 'lastModified' property should
     * be returned.
     *
     * @return string datetime
     */
    public function getLastModified($format = null)
    {
        if (!$format) {
            return $this->lastModified;
        }
        return date($format, $this->lastModified);
    }

    
    /**
     * Sets 'lastModified'.
     *
     * @param $value mixed Value representing time to stored to 'lastModified'
     * property.
     *
     * @return void
     */
    public function setLastModified($value)
    {
        if (is_string($value)) {
            $value = strtotime($value);
        }
        $this->lastModified = $value;
    }
}
