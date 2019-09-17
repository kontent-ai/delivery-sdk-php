<?php
/**
 * Represents a generic content link.
 */

namespace Kentico\Kontent\Delivery\Models\Items;

/**
 * Class ContentLink.
 */
class ContentLink
{

    /**
     * Constructor of a class.
     * 
     * @var string $id Id of a link.
     * @var mixed $linkMetadata Link additional information.
     */
    public function __construct($id, $linkMetadata) 
    {
        $this->id = $id;
        $linkMetadataValues = get_object_vars($linkMetadata);
        $this->codeName = $linkMetadataValues['codename'];
        $this->urlSlug = $linkMetadataValues['url_slug'];
        $this->contentTypeCodeName = $linkMetadataValues['type'];
    }

    /**
     * Id of the content link.
     * 
     * @var string
     */
    public $id;

    /**
     * Code name of the content link.
     * 
     * @var string
     */
    public $codeName;

    /**
     * Url slug of the content link.
     * 
     * @var string
     */
    public $urlSlug;

    /**
     * Content type code name.
     * 
     * @var string
     */
    public $contentTypeCodeName;
    
}
