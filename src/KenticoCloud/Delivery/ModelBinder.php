<?php

namespace KenticoCloud\Delivery;

use \KenticoCloud\Delivery\Helpers\TextHelper;
use \KenticoCloud\Delivery\ContentTypesMap;
use \KenticoCloud\Delivery\ContentElementTypesMap;

class ModelBinder
{
    public function getContentItems($contentItems, $modularContent = null)
    {
        $arr = array();
        foreach ($contentItems as $item) {
            $class = ContentTypesMap::getTypeClass($item->system->type);
            $arr[$item->system->codename] = $this->bindModel($class, $item, $modularContent);
        }
        return $arr;
    }


    public function bindModel($modelType, $data, $modularContent = null)
    {
        $model = new $modelType();
        $modelProperties = get_object_vars($model);
        

        if (is_string($data)) {
            $data = json_decode($data);
        }
        
        if (is_object($data)) {
            $dataProperties = get_object_vars($data);
        } else {
            // Assume it's an array
            $dataProperties = $data;
        }
        
        foreach ($modelProperties as $modelProperty => $modelPropertyValue) {
                $dataProperty = $dataProperties[TextHelper::getInstance()->decamelize($modelProperty)];
            if ($modelProperty === 'system') {
                $dataProperty = $this->bindModel(\KenticoCloud\Delivery\Models\ContentItemSystem::class, $dataProperty, $modularContent);
            } /*  else if ($modelProperty === 'elements') {

            } */
            else {
                if (is_object($dataProperty)) {
                    $dataProperty = get_object_vars($dataProperty);
                } else {
                    // Assume it's an array
                    $dataProperty = $dataProperty;
                }

                if (is_array($dataProperty)) {
                    foreach ($dataProperty as $item => $itemValue) {
                        if (isset($itemValue->type)) {
                            if ($itemValue->type == 'modular_content') {
                                if ($modularContent != null) {
                                    foreach ($itemValue->value as $key => $modularCodename) {
                                        foreach ($modularContent as $mc) {
                                            if ($mc->system->codename == $modularCodename) {
                                                 $itemValue->value[$key] = $mc;
                                                 //TODO: recursively resolve all levels + prevent infinite recursion
                                            }
                                        }
                                    }
                                }
                            }
                          /*   $class = ContentElementTypesMap::getTypeClass($itemValue->type);
                            if ($class != null) {
                                $dataProperty = $this->bindModel($class, $itemValue);
                            } */
                        }
                    }
                }
            }
            
            $model->$modelProperty = $dataProperty;
        }
        return $model;
    }
}
