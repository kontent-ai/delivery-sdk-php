<?php
/**
 * Represents specific content type.
 */

namespace Kentico\Kontent\Delivery\Models\Types;

/**
 * Class ContentType.
 */
class ContentType
{
    /**
     * Content type metadata.
     *
     * @var ContentTypeSystem
     */
    public $system;

    /**
     * Gets an array that contains elements of the content type indexed by their codename.
     *
     * @var ContentTypeElement[]
     */
    public $elements;
}
