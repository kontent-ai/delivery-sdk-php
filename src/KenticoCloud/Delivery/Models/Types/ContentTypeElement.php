<?php
namespace KenticoCloud\Delivery\Models\Types;

/**
 * ContentTypeElement
 *
 * Represents basic content type element. More complex content type elements
 * inherit from this class.
 */
class ContentTypeElement
{
    public $type = null;
    public $codename = null;
    public $name = null;

    public function __construct($type, $codename, $name)
    {
        $this->type = $type;
        $this->codename = $codename;
        $this->name = $name;
    }
}