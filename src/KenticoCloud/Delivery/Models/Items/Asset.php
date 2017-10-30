<?php
/**
 * Represents a digital asset, such as a document or image.
 */

namespace KenticoCloud\Delivery\Models\Items;

use KenticoCloud\Delivery\Models\ContentItemElement;

/**
 * Class Asset
 * @package KenticoCloud\Delivery\Models\Items *
 */
class Asset extends ContentItemElement
{
    /**
     * @var string
     * Gets the name of the asset.
     */
    public $name = null;
    /**
     * @var string
     * Gets the description of the asset.
     */
    public $description = null;
    /**
     * @var string
     * Gets the media type of the asset, for example "image/jpeg".
     */
    public $type = null;
    /**
     * @var int
     * Gets the asset size in bytes.
     */
    public $size = null;
    /**
     * @var string
     * Gets the URL of the asset.
     */
    public $url = null;
}
