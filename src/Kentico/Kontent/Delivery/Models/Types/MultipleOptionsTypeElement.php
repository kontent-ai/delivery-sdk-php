<?php
/**
 * Represents content type element with possibility to select from multiple choices.
 */

namespace Kentico\Kontent\Delivery\Models\Types;

use Kentico\Kontent\Delivery\Models;

/**
 * Class MultipleOptionsTypeElement.
 */
class MultipleOptionsTypeElement extends ContentTypeElement
{
    /**
     * Represents an option of a Multiple choice element.
     *
     * @var Models\Shared\MultipleChoiceOption
     */
    public $options = null;

    /**
     * MultipleOptionsTypeElement constructor.
     *
     * @param $type type of a content type element
     * @param $codename code name of the element
     * @param $name display name of the element
     * @param $options array with multiple options
     */
    public function __construct($type, $codename, $name, $options)
    {
        $this->options = $options;
        parent::__construct($type, $codename, $name);
    }
}
