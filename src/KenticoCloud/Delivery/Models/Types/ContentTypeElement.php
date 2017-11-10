<?php
/**
 * Represents basic content type element. More complex content type elements inherit from this class.
 */

namespace KenticoCloud\Delivery\Models\Types;

/**
 * Class ContentTypeElement
 * @package KenticoCloud\Delivery\Models\Types
 */
class ContentTypeElement
{
    /**
     * Gets the type of the content element, for example "multiple_choice".
     * @var string
     */
    public $type = null;
    /**
     * Gets the codename of the content element.
     * @var string
     */
    public $codename = null;
    /**
     * Gets the name of the content element.
     * @var string
     */
    public $name = null;

    /**
     * ContentTypeElement constructor.
     * @param $type
     * @param $codename
     * @param $name
     */
    public function __construct($type, $codename, $name)
    {
        $this->type = $type;
        $this->codename = $codename;
        $this->name = $name;
    }
}