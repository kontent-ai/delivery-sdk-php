<?php
/**
 * Represents an option of a Multiple choice element.
 */

namespace Kentico\Kontent\Delivery\Models\Shared;

/**
 * Class MultipleChoiceOption.
 */
class MultipleChoiceOption
{
    /**
     * Gets the name of the selected option.
     *
     * @var string
     */
    public $name = null;

    /**
     * Gets the codename of the selected option.
     *
     * @var string
     */
    public $codename = null;

    /**
     * Initializes a new instance of the MultipleChoiceOption class.
     *
     * @param $name
     * @param $codename
     */
    public function __construct($name, $codename)
    {
        $this->name = $name;
        $this->codename = $codename;
    }
}
