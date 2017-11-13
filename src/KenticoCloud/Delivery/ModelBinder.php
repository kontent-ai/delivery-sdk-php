<?php
/**
 * TODO: PS
 */

namespace KenticoCloud\Delivery;

/**
 * Class ModelBinder
 * @package KenticoCloud\Delivery
 */
class ModelBinder
{
    /**
     * TODO: PS
     * @var TypeMapperInterface|null
     */
    protected $typeMapper = null;
    /**
     * TODO: PS
     * @var PropertyMapperInterface|null
     */
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

    /**
     * Instantiates models for given content items and resolves modular content as nested models.
     * @param $contentItems
     * @param null $modularContent
     * @return array
     */
    public function getContentItems($contentItems, $modularContent = null)
    {
        $arr = array();
        foreach ($contentItems as $item) {
            $arr[$item->system->codename] = $this->getContentItem($item, $modularContent);
        }
        return $arr;
    }

    /**
     * Instantiates model for a given content item and resolves modular content as nested models.
     * @param $item
     * @param null $modularContent
     * @return array
     */
    public function getContentItem($item, $modularContent = null)
    {
        $class = $this->typeMapper->getTypeClass($item->system->type);
        $contentItem = $this->bindModel($class, $item, $modularContent);
        return $contentItem;
    }

    /**
     * TODO: PS
     * @param $modelType
     * @param $data
     * @param null $modularContent
     * @param null $processedItems
     * @return mixed
     */
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
                    $modelPropertyValue = array();
                    foreach ($dataProperty as $item => $itemValue) {
                        if (isset($itemValue->type)) {
                            // Elements
                            switch ($itemValue->type) {
                                case 'asset':
                                case 'taxonomy:':
                                case 'multiple_choice':
                                    $knownTypes = array();
                                    foreach ($itemValue->value as $knownType) {
                                        $knownTypeClass = $this->typeMapper->getTypeClass($itemValue->type);        
                                        $knowTypeModel = $this->bindModel($knownTypeClass, $knownType, $modularContent, $processedItems);                                    
                                        $knownTypes[] = $knowTypeModel;
                                    }
                                    $modelPropertyValue[$item] = $knownTypes;
                                    break;
                                    
                                case 'modular_content':
                                    $modelModularItems = array();
                                    if ($modularContent != null) {
                                        foreach ($itemValue->value as $modularCodename) {
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
                        /* if (isset($dataProperty->type)) {
                            $class = $this->typeMapper->getTypeClass($dataProperty->type);
                            //TODO: check class for  null

                            $subItem = $this->bindModel($class, $dataProperty->value, $modularContent, $processedItems);
                            
                        } */
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
