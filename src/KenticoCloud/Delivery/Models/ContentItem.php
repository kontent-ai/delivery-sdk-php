<?php

namespace KenticoCloud\Delivery\Models;

class ContentItem extends Model
{
    public $system = null;
    public $elements = null;

    public function setSystem($system)
    {
        $this->system = ContentItemSystem::create($system);
        return $this;
    }

    public function getModularContent($huh)
    {
    }

    public function getElementValue($name)
    {
        if (!$this->elements) {
            return null;
        }
        foreach ($this->elements as $element) {
        }
        return null;
    }

    public function getString($name)
    {
        $value = $this->getElementValue($name);
        return (string)$value;
    }

    public function getNumber($name)
    {
        $value = $this->getElementValue($name);
        return gettype($value) == "double" ? floatval($value) : intval($value);
    }

    public function getDateTime($name, $format = null)
    {
        $value = $this->getElementValue($name);
        if (is_string($value)) {
            $value = strtotime($value);
        }
        if ($format) {
            $value = date($format, $value);
        }
        return $value;
    }
}
