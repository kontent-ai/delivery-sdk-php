<?php

namespace KenticoCloud\Delivery\Models;

use \KenticoCloud\Delivery\ModelBinder;

class ContentItems
{
    public $items = null;
    public $pagination = null;

    public function __construct($obj)
    {
        $this->populate($obj);
        return $this;
    }

    protected function populate($obj)
    {
        if (is_string($obj)) {
            $obj = json_decode($obj);
        }
        if (is_object($obj)) {
            $properties = get_object_vars($obj);
        } else {
            $properties = $obj; //assume it's an array
        }
        
        $modelBinder = new ModelBinder();
        
        // Modular content
        $modularContent = $modelBinder->getContentItems($properties['modular_content']);

        // Items
        $this->items = $modelBinder->getContentItems($properties['items'], $modularContent);

        // Pagination
        $this->pagination = $modelBinder->bindModel(Pagination::class, $properties['pagination']);

        return $this;
    }
}
