<?php
/**
 * Facilitates binding of JSON responses to defined content item models.
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
     * Serves for converting simple values to desired types.
     *
     * @var ValueConverterInterface|null
     */
    protected $valueConverter = null;

    /**
     *  Serves for converting complex values to desired types.
     * 
     * @var ComplexValueConverterInterface|null
     */
    protected $complexValueConverter = null;

    /**
     * ModelBinder constructor.
     *
     * @param TypeMapperInterface     $typeMapper
     * @param PropertyMapperInterface $propertyMapper
     */
    public function __construct(TypeMapperInterface $typeMapper, PropertyMapperInterface $propertyMapper, ValueConverterInterface $valueConverter, ComplexValueConverterInterface $complexValueConverter)
    {
        $this->typeMapper = $typeMapper;
        $this->propertyMapper = $propertyMapper;
        $this->valueConverter = $valueConverter;
        $this->complexValueConverter = $complexValueConverter;
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
        $modelProperties = $this->propertyMapper->getModelProperties($model, $data);

        // Add item to processed items collection to prevent recursion
        if (isset($data->system->codename) && !isset($processedItems[$data->system->codename])) {
            $processedItems[$data->system->codename] = $model;
        }

        if (is_object($data)) {
            $dataProperties = get_object_vars($data);
        }

        foreach ($modelProperties as $modelProperty => $modelPropertyValue) {
            $dataProperty = $this->propertyMapper->getProperty($dataProperties, $modelProperty);
            $modelPropertyValue = null;

            if ($modelProperty == 'system') {
                $modelPropertyValue = $this->valueConverter->getValue($modelProperty, $dataProperty);
            } else {
                if (isset($dataProperty->value)) {
                    // The item contains a value element
                    if (isset($dataProperty->type)) {
                        // The item is an element (complex type that contains a type information)
                        $modelPropertyValue = $this->bindElement($dataProperty, $modularContent, $processedItems);
                    } else {
                        // Bind the nested value element
                        $modelPropertyValue = $dataProperty->value;
                    }
                } else {
                    // There is no object hierarchy, bind it directly
                    $modelPropertyValue = $dataProperty;
                }
            }

            $model->$modelProperty = $modelPropertyValue;
        }

        return $model;
    }

    /**
     * Binds an element to an appropriate model based on the element's type.
     *
     * @param $item Content item element to bind
     * @param null $modularContent JSON response containing nested modular content items
     * @param null $processedItems collection of already processed items (to avoid infinite loops)
     *
     * @return mixed
     */
    public function bindElement($element, $modularContent, $processedItems)
    {
        $result = null;
        switch ($element->type) {
            case 'asset':
            case 'taxonomy':
            case 'multiple_choice':
                // Map well-known types to their models
                $result = $this->bindKnownType($element, $modularContent, $processedItems);
                break;

            case 'modular_content':
                // Recursively bind the nested models
                $result = $this->bindModularContent($element, $modularContent, $processedItems);
                break;
            case 'rich_text':
                $result = $this->complexValueConverter->getComplexValue($element, $modularContent, $processedItems);
                // Recursively bind the nested models                
                break;
            default:                         
                // Use a value converter to get the value in a proper format/type
                $result = $this->valueConverter->getValue($element->type, $element->value);
                break;
        }

        return $result;
    }

    /**
     * Uses a well-known type to bind the element's data.
     *
     * @param $element modular content item element
     * @param null $modularContent JSON response containing nested modular content items
     * @param null $processedItems collection of already processed items (to avoid infinite loops)
     */
    public function bindKnownType($element, $modularContent, $processedItems)
    {
        $knownTypes = array();
        foreach ($element->value as $knownType) {
            $knownTypeClass = $this->typeMapper->getTypeClass($element->type);
            $knowTypeModel = $this->bindModel($knownTypeClass, $knownType, $modularContent, $processedItems);
            $knownTypes[] = $knowTypeModel;
        }

        return $knownTypes;
    }

    /**
     * Binds a modular content element to a "strongly" typed model.
     *
     * @param $element modular content item element
     * @param null $modularContent JSON response containing nested modular content items
     * @param null $processedItems collection of already processed items (to avoid infinite loops)
     */
    public function bindModularContent($element, $modularContent, $processedItems)
    {
        $modelModularItems = null;
        if ($modularContent != null) {
            $modelModularItems = array();
            foreach ($element->value as $modularCodename) {
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
        }

        return $modelModularItems;
    }
}
