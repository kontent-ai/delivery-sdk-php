<?php
/**
 * Base metadata shared for all objects.
 */

namespace KenticoCloud\Delivery\Models\Shared;

use DateTime;

/**
 * Class AbstractSystem.
 */
abstract class AbstractSystem
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
     * Gets and sets object's last modified timestamp.
     *
     * @var null
     */
    public $lastModified = null;

    /**
     * AbstractSystem constructor.
     *
     * @param $id
     * @param $name
     * @param $codename
     * @param $lastModified
     */
    public function __construct($id, $name, $codename, $lastModified)
    {
        $this->id = $id;
        $this->name = $name;
        $this->codename = $codename;
        $this->lastModified = new DateTime($lastModified);
    }

    /**
     * Gets strongly typed last modified time stamp in a requested format.
     *
     * @param null $format
     *
     * @return DateTime|string
     */
    public function getLastModifiedDateTime($format = null)
    {
        $dateTime = $this->lastModified;
        if (!$format) {
            return $dateTime;
        }

        return $dateTime->format($format);
    }
}
