<?php
/**
 * Facilitates binding of JSON responses to defined models.
 */

namespace KenticoCloud\Delivery;

/**
 * Class ModelBinder.
 */
class ModelBinder
{
    /**
     * Serves for resolving strong types based on provided information.
     *
     * @var TypeMapperInterface|null
     */
    protected $typeMapper = null;
    /**
     * Serves for mapping model properties to data in JSON responses.
     *
     * @var PropertyMapperInterface|null
     */
    protected $propertyMapper = null;

    /**
     * ModelBinder constructor.
     *
     * @param TypeMapperInterface     $typeMapper
     * @param PropertyMapperInterface $propertyMapper
     */
    public function __construct(TypeMapperInterface $typeMapper, PropertyMapperInterface $propertyMapper)
    {
        $this->typeMapper = $typeMapper;
        $this->propertyMapper = $propertyMapper;
    }

    /**
     * Instantiates models for given content items and resolves modular content as nested models.
     *
     * @param $contentItems
     * @param null $modularContent
     *
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
     *
     * @param $item
     * @param null $modularContent
     *
     * @return array
     */
    public function getContentItem($item, $modularContent = null)
    {
        $class = $this->typeMapper->getTypeClass($item->system->type);
        $contentItem = $this->bindModel($class, $item, $modularContent);

        return $contentItem;
    }

    /**
     * Binds given data to a predefined model.
     *
     * @param $modelType "strong" type of predefined model to bind the $data to
     * @param $data JSON response containing content items
     * @param null $modularContent JSON response containing nested modular content items
     * @param null $processedItems collection of already processed items (to avoid infinite loops)
     *
     * @return mixed
     */
    public function bindModel($modelType, $data, $modularContent = null, $processedItems = null)
    {
        $processedItems = $processedItems ?? array();
        $model = new $modelType();
        $modelProperties = get_object_vars($model);

        // Add item to processed items collection to prevent recursion
        if (isset($data->system->codename) && !isset($processedItems[$data->system->codename])) {
            $processedItems[$data->system->codename] = $model;
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
                            $modelPropertyValue = $this->bindProperty($modelPropertyValue, $item, $itemValue, $modularContent, $processedItems);
                        }
                    }
                } else {
                    if (isset($dataProperty->value)) {
                        if (isset($dataProperty->type)) {
                            $modelPropertyValue = array();
                            $resolvedProperty = $this->bindProperty($modelPropertyValue, $modelProperty, $dataProperty, $modularContent, $processedItems);
                            $dataProperty = $resolvedProperty[$modelProperty];
                        } else {
                            $dataProperty = $dataProperty->value;
                        }
                    }
                    $modelPropertyValue = $dataProperty;
                }
            }

            $model->$modelProperty = $modelPropertyValue;
        }

        return $model;
    }

    /**
     * //Binds given data to a predefined model.
     *
     * @param $modularContent
     * @param $processedItems
     * @param $itemValue
     * @param $modelPropertyValue
     * @param $item
     *
     * @return mixed
     */
    public function bindProperty($modelPropertyValue, $item, $itemValue, $modularContent, $processedItems)
    {
        switch ($itemValue->type) {
            case 'asset':
            case 'taxonomy':
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
                if ($modularContent != null) {
                    $modelPropertyValue[$item] = $this->bindModularContent($itemValue, $modularContent, $processedItems);
                }
                break;

            default:
                $modelPropertyValue[$item] = $itemValue->value;
                break;
        }

        return $modelPropertyValue;
    }

    public function bindModularContent($itemValue, $modularContent, $processedItems)
    {
        $modelModularItems = array();
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

        return $modelModularItems;
    }
}
