<?php
/**
 * Represents basic content type element. More complex content type elements
 * inherit from this class.
 */

namespace KenticoCloud\Delivery\Models\Types;

/**
 * Class ContentTypeElement
 * @package KenticoCloud\Delivery\Models\Types
 */
class ContentTypeElement
{
    public $type = null;
    public $codename = null;
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