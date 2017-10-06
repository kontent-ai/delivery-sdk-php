<?php

namespace KenticoCloud\Delivery\Models\Items;

use KenticoCloud\Delivery\Models\Shared\SystemBase;
use \DateTime;

class ContentItemSystem extends SystemBase
{
    public $type = null;
    public $sitemapLocations = null;
    public $language = null;

    public function __construct($id = null, $name = null, $codename = null, $lastModified = null, $type = null, $sitemapLocations = null, $language = null)
    {
        parent::__construct($id, $name, $codename, $lastModified);
        $this->type = $type;
        $this->sitemapLocations = $sitemapLocations;
        $this->language = $language;
    }

    public function getLastModifiedDateTime($format = null)
    {
        $dateTime = new DateTime($this->lastModified);
        if (!$format) {
            return $dateTime;
        }
        return $dateTime->format($format);
    }
}
