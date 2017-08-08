<?php

namespace KenticoCloud\Delivery\Models;

class ContentItem
{
    public $system = null;
    public $elements = null;

    public function setSystem($system)
    {
        $this->system = (new ModelBinder())->bindModel(ContentItemSystem::class, $system);
        return $this;
    }
}
