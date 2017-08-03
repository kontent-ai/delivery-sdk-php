<?php

namespace KenticoCloud\Delivery\Models;

class ContentItemSystem extends Model
{
    public $id = null;
    public $name = null;
    public $codename = null;
    public $type = null;
    public $sitemap_locations = null;
    public $last_modified = null;

    public function getLastModified($format = null)
    {
        if (!$format) {
            return $this->last_modified;
        }
        return date($format, $this->last_modified);
    }

    public function setLastModified($value)
    {
        if (is_string($value)) {
            $value = strtotime($value);
        }
        $this->last_modified = $value;
        return $this;
    }
}
