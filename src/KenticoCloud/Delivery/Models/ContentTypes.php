<?php 

namespace KenticoCloud\Delivery\Models;

use \KenticoCloud\Delivery\ModelBinder;

class ContentTypes
{
    public $types = null;
    // public $pagination = null;  // Is this neccessary for content types?

    public function __construct($obj)
    {
        $this->populate($obj);
        return $this;
    }

    protected function populate($obj)
    {
        $properties = get_object_vars($obj);
        $modelBuilder = new ModelBinder();

        $this->types = $modelBuilder->getContentTypes($properties['types'], null);

        return $this;
    }
}