<?php

namespace KenticoCloud\Delivery;

/**
 * Class ModelBinder
 * @package KenticoCloud\Delivery
 */
class ModelBinder
{
    protected $typeMapper = null;
    protected $propertyMapper = null;

    /**
     * ModelBinder constructor.
     * @param TypeMapperInterface $typeMapper
     * @param PropertyMapperInterface $propertyMapper
     */
    public function __construct(TypeMapperInterface $typeMapper, PropertyMapperInterface $propertyMapper)
    {
        $this->typeMapper = $typeMapper;
        $this->propertyMapper = $propertyMapper;
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
            $dataProperty = $this->propertyMapper->getProperty($dataProperties, $modelType, $modelProperty);
            $modelPropertyValue = null;

            $type = $this->typeMapper->getTypeClass(null, $modelProperty, $modelType);
            if ($type != null) {
                $modelPropertyValue = $this->bindModel($type, $dataProperty, $modularContent);
            } else {
                if (is_array($dataProperty)) {
                    //TODO: only create array if there is more than one item
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

                                default:
                                    $modelPropertyValue[$item] = $itemValue->value;
                                    break;
                            }
                        }
                    }
                } else {
                    if (isset($dataProperty->value)) {
                        $dataProperty = $dataProperty->value;
                    }
                    $modelPropertyValue = $dataProperty;
                }
            }
            
            $model->$modelProperty = $modelPropertyValue;
        }
        return $model;
    }
}
