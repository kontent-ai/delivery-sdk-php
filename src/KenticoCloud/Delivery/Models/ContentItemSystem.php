<?php

namespace KenticoCloud\Delivery\Models;

use \DateTime;

class ContentItemSystem
{
    public $id = null;
    public $name = null;
    public $codename = null;
    public $type = null;
    public $sitemapLocations = null;
    public $lastModified = null;

    public function getLastModifiedDateTime($format = null)
    {
        $dt = new DateTime($this->lastModified);
        if (!$format) {
            return $dt;
        }
        return $dt->format($format);
    }
}
