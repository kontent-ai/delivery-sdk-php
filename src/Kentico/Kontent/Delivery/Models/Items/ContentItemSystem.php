<?php
/**
 * Represents system attributes of a content item.
 */

namespace Kentico\Kontent\Delivery\Models\Items;

use Kentico\Kontent\Delivery\Models\Shared\AbstractSystem;

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
     * Gets the codename of the collection, for example "default".
     *
     * @var null
     */
    public $collection = null;

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
     * Gets the workflow step of the content item.
     *
     * @var string
     */
    public $workflowStep = null;

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
    public function __construct($id = null, $name = null, $codename = null, $lastModified = null, $type = null, $sitemapLocations = null, $language = null, $collection = null, $workflowStep = null)
    {
        parent::__construct($id, $name, $codename, $lastModified);
        $this->type = $type;
        $this->collection = $collection;
        $this->sitemapLocations = $sitemapLocations;
        $this->language = $language;
        $this->workflowStep = $workflowStep;
    }
}
