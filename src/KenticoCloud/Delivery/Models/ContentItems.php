<?php

namespace KenticoCloud\Delivery\Models;

use \KenticoCloud\Delivery\ContentTypesMap;

class ContentItems extends Model
{
    public $items = null;
    public $pagination = null;

    public function setItems($value)
    {
        $this->setContentItems('items', $value);
        return $this;
    }

    public function setPagination($value)
    {
        $this->pagination = Pagination::create($value);
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
        $this->$name = array();
        foreach ($value as $item) {
            $class = ContentTypesMap::getTypeClass($item->system->type);
            $this->$name[] = $class::create($item);
        }
        return $this;
    }
}
