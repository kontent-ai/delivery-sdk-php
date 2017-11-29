<?php
/**
 * Represents system attributes of a content item.
 */

namespace KenticoCloud\Delivery\Models\Items;

use KenticoCloud\Delivery\Models\Shared\AbstractSystem;

/**
 * Class ContentItemSystem.
 */
class ContentItemSystem extends AbstractSystem
{
    /**
     * Gets the codename of the content type, for example "article".
     *
     * @var null
     */
    public $type = null;

    /**
     * Gets a list of codenames of sitemap items to which the content item is assigned.
     *
     * @var null
     */
    public $sitemapLocations = null;

    /**
     * Gets the language of the content item.
     *
     * @var null
     */
    public $language = null;

    /**
     * ContentItemSystem constructor.
     *
     * @param null $id               Identifier of a content item
     * @param null $name             Display name of a content item
     * @param null $codename         Code name of a content item
     * @param null $lastModified     Last modified time stamp
     * @param null $type             Content type of a content item
     * @param null $sitemapLocations Array of sitemap nodes  which the content item is assigned to
     * @param null $language         Globalization culture of a content item
     */
    public function __construct($id = null, $name = null, $codename = null, $lastModified = null, $type = null, $sitemapLocations = null, $language = null)
    {
        parent::__construct($id, $name, $codename, $lastModified);
        $this->type = $type;
        $this->sitemapLocations = $sitemapLocations;
        $this->language = $language;
    }
}
