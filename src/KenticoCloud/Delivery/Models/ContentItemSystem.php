<?php

namespace KenticoCloud\Delivery\Models;

class ContentItemSystem
{
    public $id = null;
    public $name = null;
    public $codename = null;
    public $type = null;
    public $sitemapLocations = null;
    public $lastModified = null;

    public function getLastModified($format = null)
    {
        if (!$format) {
            return $this->lastModified;
        }
        return date($format, $this->lastModified);
    }

    public function setLastModified($value)
    {
        if (is_string($value)) {
            $value = strtotime($value);
        }
        $this->lastModified = $value;
        return $this;
    }
}
