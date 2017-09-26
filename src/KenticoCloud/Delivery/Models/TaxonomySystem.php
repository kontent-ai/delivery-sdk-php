<?php
namespace KenticoCloud\Delivery\Models;

/**
 * TaxonomySystem
 *
 * Represents 'system' property of taxonomy.
 */
class TaxonomySystem
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
        $this->last_modified = $this->setLastModified($lastModified);
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
        $this->lastModified = $value;
        return $this;   // TODO: This is questionable, I think it should be void, but I have it in contentTypeSystem with $this
    }

}