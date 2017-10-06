<?php
namespace KenticoCloud\Delivery\Models\Shared;

/**
 * SystemBase
 *
 * Base class 'system' property.
 */
class SystemBase
{
    public $id = null;
    public $name = null;
    public $codename = null;
    public $lastModified = null;

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
