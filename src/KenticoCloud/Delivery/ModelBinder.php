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


    public function bindModel($modelType, $data, $modularContent = null)
    {
        static $processedItems = array();

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
            if ($modelProperty === 'system') {
                $dataProperty = $this->bindModel(\KenticoCloud\Delivery\Models\ContentItemSystem::class, $dataProperty, $modularContent);
            } else {
                if (is_object($dataProperty)) {
                    $dataProperty = get_object_vars($dataProperty);
                } else {
                    // Assume it's an array
                    $dataProperty = $dataProperty;
                }

                if (is_array($dataProperty)) {
                    foreach ($dataProperty as $item => $itemValue) {
                        if (isset($itemValue->type)) {
                            // Elements
                            switch ($itemValue->type) {
                                case 'modular_content':
                                    if ($modularContent != null) {
                                        foreach ($itemValue->value as $key => $modularCodename) {
                                            // Try to load the content item from processed items
                                            if (isset($processedItems[$modularCodename])) {
                                                $ci = $processedItems[$modularCodename];
                                            } else {
                                                // If not found, recursively load model
                                                if (isset($modularContent->$modularCodename)) {
                                                    $class = $this->typeMapper->getTypeClass($modularContent->$modularCodename->system->type);
                                                    $ci = $this->bindModel($class, $modularContent->$modularCodename, $modularContent);
                                                    $processedItems[$modularCodename] = $ci;
                                                    // Remove placeholders holding references to modular content items
                                                    unset($itemValue->value[$key]);
                                                }
                                            }
                                            $itemValue->value[$modularCodename] = $ci;
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
            
            $model->$modelProperty = $dataProperty;
        }
        return $model;
    }
}
