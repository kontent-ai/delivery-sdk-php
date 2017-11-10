<?php
/**
 * Represents a multiple choice option assigned to a Multiple choice element of a content item.
 */

namespace KenticoCloud\Delivery\Models\Items;

/**
 * Class MultipleChoiceOption
 * @package KenticoCloud\Delivery\Models\Items
 */
class MultipleChoiceOption
{
    /**
     * @var string
     * Gets the name of the multiple choice option.
     */
    public $name = null;
    /**
     * @var string
     * Gets the codename of the multiple choice option.
     */
    public $codename = null;
}
