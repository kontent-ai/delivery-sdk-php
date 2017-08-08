<?php

namespace KenticoCloud\Delivery\Models;

use \KenticoCloud\Delivery\ContentTypesMap;
use \KenticoCloud\Delivery\ModelBinder;
use \KenticoCloud\Delivery\Helpers\TextHelper;

class ContentItems
{
    public $items = null;
    public $pagination = null;

    private $modelBinder = null;

    public function __construct($obj)
    {
        $this->modelBinder = new ModelBinder();
        $this->populate($obj);
        return $this;
    }

    public function populate($obj)
    {
        if (is_string($obj)) {
            $obj = json_decode($obj);
        }
        if (is_object($obj)) {
            $properties = get_object_vars($obj);
        } else {
            $properties = $obj; //assume it's an array
        }
        
        foreach ($properties as $property => $value) {
            $setMethod = 'set'.TextHelper::getInstance()->pascalCase($property);
            call_user_func_array(array($this, $setMethod), array($value));
        }
        return $this;
    }


    public function setItems($value)
    {
        $this->setContentItems('items', $value);
        return $this;
    }

    public function setPagination($value)
    {
        $this->pagination = $this->modelBinder->bindModel(Pagination::class, $value);
        return $this;
    }

    public function setModularContent($value)
    {
        $this->setContentItems('modularContent', $value);
        $this->resolveModularContent();
        return $this;
    }

    public function resolveModularContent()
    {
        foreach ($this->items as $item) {
            $this->resolveModularContentInContentItem($item, $this->modularContent);
        }
    }
    
    public function resolveModularContentInContentItem($item, $modularContent)
    {
        foreach ($item->elements as $element) {
            if ($element->type == 'modular_content') {
                foreach ($element->value as $key => $modularCodename) {
                    foreach ($modularContent as $mc) {
                        if ($mc->system->codename == $modularCodename) {
                             $element->value[$key] = $mc;
                             //TODO: recursively resolve all levels + prevent infinite recursion
                        }
                    }
                }
            }
            if ($element->type == 'asset') {
                //TODO
            }
        }
    }
    
    protected function setContentItems($name, $value)
    {
        $arr = array();
        foreach ($value as $item) {
            $class = ContentTypesMap::getTypeClass($item->system->type);
            $arr[$item->system->codename] = $this->modelBinder->bindModel($class, $item);
        }
        $this->$name = $arr;
        return $this;
    }
}
