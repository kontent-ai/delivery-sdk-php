<?php

namespace KenticoCloud\Delivery;

use \KenticoCloud\Delivery\Helpers\TextHelper;

class ModelBinder
{
    protected $typeMapper = null;

    public function __construct(TypeMapperInterface $typeMapper)
    {
        $this->typeMapper = $typeMapper;
    }

    public function getContentItems($contentItems, $modularContent = null)
    {
        $arr = array();
        foreach ($contentItems as $item) {
            $class = $this->typeMapper->getTypeClass($item->system->type);
            $arr[$item->system->codename] = $this->bindModel($class, $item, $modularContent);
        }
        return $arr;
    }


    public function bindModel($modelType, $data, $modularContent = null, $processedItems = null)
    {
        $processedItems = $processedItems ?? array();
        $model = new $modelType();
        $modelProperties = get_object_vars($model);

        if (isset($data->system->codename)) {
            // Add item to processed items collection to prevent recursion
            if (!isset($processedItems[$data->system->codename])) {
                $processedItems[$data->system->codename] = $model;
            }
        }

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
            $modelPropertyValue = null;

            $type = $this->typeMapper->getTypeClass(null, $modelProperty, $modelType);
            if ($type != null) {
                $modelPropertyValue = $this->bindModel($type, $dataProperty, $modularContent);
            } else {
                if (is_object($dataProperty)) {
                    $dataProperty = get_object_vars($dataProperty);
                } else {
                    // Assume it's an array
                    $dataProperty = $dataProperty;
                }

                if (is_array($dataProperty)) {
                    $modelPropertyValue = array();
                    foreach ($dataProperty as $item => $itemValue) {
                        if (isset($itemValue->type)) {
                            // Elements
                            switch ($itemValue->type) {
                                case 'modular_content':
                                    $modelModularItems = array();
                                    if ($modularContent != null) {
                                        foreach ($itemValue->value as $key => $modularCodename) {
                                            // Try to load the content item from processed items
                                            if (isset($processedItems[$modularCodename])) {
                                                $subItem = $processedItems[$modularCodename];
                                            } else {
                                                // If not found, recursively load model
                                                if (isset($modularContent->$modularCodename)) {
                                                    $class = $this->typeMapper->getTypeClass($modularContent->$modularCodename->system->type);
                                                    $subItem = $this->bindModel($class, $modularContent->$modularCodename, $modularContent, $processedItems);
                                                    $processedItems[$modularCodename] = $subItem;
                                                } else {
                                                    $subItem = null;
                                                }
                                            }
                                            $modelModularItems[$modularCodename] = $subItem;
                                        }
                                        $modelPropertyValue[$item] = $modelModularItems;
                                    }
                                    break;
                            }
                        }
                    }
                } else {
                    $modelPropertyValue = $dataProperty;
                }
            }
            
            $model->$modelProperty = $modelPropertyValue;
        }
        return $model;
    }
}
