<?php
namespace KenticoCloud\Delivery\Models\Types;

/**
 * MultipleChoiceOption
 *
 * Represents single option for MultipleOptionsTypeElement.
 */
class MultipleChoiceOption
{
    public $name = null;
    public $codename = null;

    public function __construct($name, $codename)
    {
        $this->name = $name;
        $this->codename = $codename;
    }
}