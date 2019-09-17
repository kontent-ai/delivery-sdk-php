<?php
/**
 * Represents a digital asset, such as a document or an image.
 */

namespace Kentico\Kontent\Delivery\Models\Items;

/**
 * Class Asset.
 */
class Asset
{
    /**
     * Gets the name of the asset.
     *
     * @var string
     */
    public $name = null;

    /**
     * Gets the media type of the asset, for example "image/jpeg".
     *
     * @var string
     */
    public $type = null;

    /**
     * Gets the asset size in bytes.
     *
     * @var int
     */
    public $size = null;

    /**
     * Gets the description of the asset.
     *
     * @var string
     */
    public $description = null;

    /**
     * Gets the URL of the asset.
     *
     * @var string
     */
    public $url = null;
}
