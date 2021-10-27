<?php
/**
 * Base metadata shared for all objects.
 */

namespace Kentico\Kontent\Delivery\Models\Languages;

/**
 * Class LanguageSystem.
 */
class LanguageSystem
{
    /**
     * Gets and sets objects unique identifier.
     *
     * @var null
     */
    public $id = null;

    /**
     * Gets and sets object's display name.
     *
     * @var null
     */
    public $name = null;

    /**
     * Gets and sets object's code name.
     *
     * @var null
     */
    public $codename = null;

    /**
     * AbstractSystem constructor.
     *
     * @param $id identifier of a given entity
     * @param $name display name of a given entity
     * @param $codename code name of a given entity
     */
    public function __construct($id, $name, $codename)
    {
        $this->id = $id;
        $this->name = $name;
        $this->codename = $codename;
    }
}
