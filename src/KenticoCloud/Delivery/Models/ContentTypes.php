<?php 

namespace KenticoCloud\Delivery\Models;

use \KenticoCloud\Delivery\ModelBinder;

class ContentTypes
{
    public $types = null;
    public $pagination = null;  // TODO: Is this neccessary for content types?

    public function __construct($obj)
    {
        $this->populate($obj);
        return $this;
    }

    protected function populate($obj)
    {
        $properties = get_object_vars($obj);
        $mb = new ModelBinder();

        $this->types = $mb->getContentTypes($properties['types'], null);

        return $this;
    }
}