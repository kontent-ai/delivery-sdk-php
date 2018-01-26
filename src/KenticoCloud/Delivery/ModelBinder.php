<?php
/**
 * Facilitates binding of JSON responses to defined content item models.
 */

namespace KenticoCloud\Delivery;

use KenticoCloud\Delivery\Models\Items\ContentLink;
use Sunra\PhpSimple\HtmlDomParser;

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
     *  Serves for converting links to desired types.
     *
     * @var ContentLinkUrlResolverInterface|null
     */
    protected $contentLinkUrlResolver = null;

    /**
     *  Serves for converting links to desired types.
     *
     * @var InlineModularContentResolverInterface|null
     */
    protected $inlineModularContentResolver = null;

    /**
     * ModelBinder constructor.
     *
     * @param TypeMapperInterface                   $typeMapper
     * @param PropertyMapperInterface               $propertyMapper
     * @param ValueConverterInterface               $valueConverter
     * 
     */
    public function __construct(TypeMapperInterface $typeMapper, PropertyMapperInterface $propertyMapper, ValueConverterInterface $valueConverter)
    {
        $this->typeMapper = $typeMapper;
        $this->propertyMapper = $propertyMapper;
        $this->valueConverter = $valueConverter;
    }

    /**
     * Setter used for rich text resolvers.
     */
    public function __set($property, $value) {
        switch ($property) {
            case 'contentLinkUrlResolver':
                $this->contentLinkUrlResolver = $value;
                break;
            case 'inlineModularContentResolver':
                $this->inlineModularContentResolver = $value;
                break;
            default:
                return;
        }
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
                // Resolve nested artifacts in rich-text elements
                $result = $this->getComplexValue($element, $modularContent, $processedItems);
                break;
            default:
                // Use a value converter to get the value in a proper format/type
                $result = $this->valueConverter->getValue($element->type, $element->value);
                break;
        }

        return $result;
    }

    /**
     * Converts a given complex value to a specified type.
     *
     * @param mixed $element modular content item element
     * @param mixed|null $modularContent JSON response containing nested modular content items
     * @param mixed|null $processedItems collection of already processed items (to avoid infinite loops)
     *
     * @return mixed
     */
    private function getComplexValue($element, $modularContent, $processedItems)
    {
        $result = $this->resolveLinksUrls($element->value, $element->links);
        $result = $this->resolveInlineModularContent($result, $modularContent, $processedItems);

        return $result;
    }

    /**
     * Resolve all link urls detected in input html.
     *
     * @var string input html containing links
     * @var mixed  $links link contexts using for link resolution
     */
    private function resolveLinksUrls($input, $links)
    {
        if(empty($this->contentLinkUrlResolver)){
            return $input;
        }

        $parser = new HtmlDomParser();
        $dom = $parser->str_get_html($input);

        $linksElements = $dom->find('a[data-item-id]');
        $elementLinksMetadata = get_object_vars($links);

        foreach ($linksElements as $linkElement) {
            $elementId = $linkElement->getAttribute('data-item-id');            
            if (array_key_exists($elementId, $elementLinksMetadata)) {
                $contentLink = new ContentLink($elementId, $elementLinksMetadata[$elementId]);
                $resolvedLink = $this->contentLinkUrlResolver->resolveLinkUrl($contentLink);
            } else {
                $resolvedLink = $this->contentLinkUrlResolver->resolveBrokenLinkUrl($contentLink);
            }
            $linkElement->href = $resolvedLink;
        }

        return (string) $dom;
    }

    /**
     * Resolve all modular items detected in input html.
     *
     * @var string input html containing modular items
     * @param mixed|null $modularContent JSON response containing nested modular content items
     * @param mixed|null $processedItems collection of already processed items (to avoid infinite loops)
     */
    private function resolveInlineModularContent($input, $modularContent, $processedItems)
    {
        if(empty($this->inlineModularContentResolver)){
            return $input;
        }

        $parser = new HtmlDomParser();
        $dom = $parser->str_get_html($input);

        // Not possible to use multiple attribute selectors
        $modularItems = $dom->find('object[type=application/kenticocloud]');
        foreach ($modularItems as $modularItem) {
            $modularItem->outertext = $this->resolveModularItem($modularItem, $modularContent, $processedItems);
        }

        return (string)$dom;
    }

    /**
     * Resolve modular item in input html.
     *
     * @var string input html containing modular item
     * @param mixed|null $modularContent JSON response containing nested modular content items
     * @param mixed|null $processedItems collection of already processed items (to avoid infinite loops)
     */
    private function resolveModularItem($modularItem, $modularContent, $processedItems)
    {
        if($modularItem->getAttribute('data-type') != 'item'){
            return $modularItem->outertext;
        }    

        $itemCodeName = $modularItem->getAttribute('data-codename');
        $modularContentArray = get_object_vars($modularContent);
        $modularData = array_merge($modularContentArray, $processedItems);
        if(isset($modularData[$itemCodeName])){
            $modularItem->outertext = $this->inlineModularContentResolver->resolveInlineModularContent($modularItem->outertext, $modularData[$itemCodeName]);
        }

        return $modularItem->outertext;
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
