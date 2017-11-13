<?php
/**
 * Represents system attributes of a content item.
 */

namespace KenticoCloud\Delivery\Models\Items;

use KenticoCloud\Delivery\Models\Shared\AbstractSystem;
use \DateTime;

/**
 * Class ContentItemSystem
 * @package KenticoCloud\Delivery\Models\Items
 */
class ContentItemSystem extends AbstractSystem
{
    /**
     * @var null
     * Gets the codename of the content type, for example "article".
     */
    public $type = null;
    /**
     * @var null
     * Gets a list of codenames of sitemap items to which the content item is assigned.
     */
    public $sitemapLocations = null;
    /**
     * @var null
     * Gets the language of the content item.
     */
    public $language = null;

    /**
     * ContentItemSystem constructor.
     * @param null $id
     * @param null $name
     * @param null $codename
     * @param null $lastModified
     * @param null $type
     * @param null $sitemapLocations
     * @param null $language
     */
    public function __construct($id = null, $name = null, $codename = null, $lastModified = null, $type = null, $sitemapLocations = null, $language = null)
    {
        parent::__construct($id, $name, $codename, $lastModified);
        $this->type = $type;
        $this->sitemapLocations = $sitemapLocations;
        $this->language = $language;
    }

    /**
     * Gets strongly typed, formatted last modified time stamp.
     * @param null $format
     * @return DateTime|string
     *
     */
    public function getLastModifiedDateTime($format = null)
    {
        $dateTime = new DateTime($this->lastModified);
        if (!$format) {
            return $dateTime;
        }
        return $dateTime->format($format);
    }
}
