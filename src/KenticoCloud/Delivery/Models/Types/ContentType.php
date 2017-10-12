<?php
/**
 * Represents specific content type.
 */

namespace KenticoCloud\Delivery\Models\Types;

/**
 * Class ContentType
 * @package KenticoCloud\Delivery\Models\Types
 */
class ContentType
{
    /**
     * Content type metadata
     * @var ContentTypeSystem
     */
    public $system;
    /**
     * Gets an array that contains elements of the content type indexed by their codename.
     * @var ContentTypeElement[]
     */
    public $elements;
}