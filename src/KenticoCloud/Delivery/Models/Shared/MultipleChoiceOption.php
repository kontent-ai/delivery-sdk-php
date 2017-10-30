<?php
/**
 * Represents an option of a Multiple choice element.
 */

namespace KenticoCloud\Delivery\Models\Shared;

/**
 * Class MultipleChoiceOption
 * @package KenticoCloud\Delivery\Models\Shared
 */
class MultipleChoiceOption
{
    /**
     * @var string
     * Gets the name of the selected option.
     */
    public $name = null;
    /**
     * @var string
     * Gets the codename of the selected option.
     */
    public $codename = null;

    /**
     * MultipleChoiceOption constructor.
     * @param $name
     * @param $codename
     * Initializes a new instance of the MultipleChoiceOption class.
     */
    public function __construct($name, $codename)
    {
        $this->name = $name;
        $this->codename = $codename;
    }
}