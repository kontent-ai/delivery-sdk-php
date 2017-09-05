<?php
namespace KenticoCloud\Delivery\Models;

/**
 * ContentTypeSystem
 *
 * Represents 'system' property of any content type.
 */
class ContentTypeSystem
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
     * @param string $format Format in which 'lastModified' property should
     * be returned.
     *
     * @return string
     */
    public function getLastModified($format = null)
    {
        if (!$format)
        {
            return $this->lastModified;
        }
        return date($format, $this->lastModified);
    }

    
    /**
     * Sets 'lastModified'.
     *
     * @param mixed $value Value representing time to stored to 'lastModified'
     * property.
     *
     * @return void
     */
    public function setLastModified($value)
    {
        if (is_string($value))
        {
             $value = strtotime($value);
        }
        $this->lastModified  = $value;
        return $this;
    }
}