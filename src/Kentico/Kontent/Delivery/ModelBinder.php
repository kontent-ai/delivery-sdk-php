<?php
/**
 * Facilitates binding of JSON responses to defined content item models.
 */

namespace Kentico\Kontent\Delivery;

use Kentico\Kontent\Delivery\Models\Items\ContentLink;
use KubAT\PhpSimple\HtmlDomParser;

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
     * @var InlineLinkedItemsResolverInterface|null
     */
    protected $inlineLinkedItemsResolver = null;

    /**
     * ModelBinder constructor.
     *
     * @param TypeMapperInterface     $typeMapper
     * @param PropertyMapperInterface $propertyMapper
     * @param ValueConverterInterface $valueConverter
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
    public function __set($property, $value)
    {
        switch ($property) {
            case 'contentLinkUrlResolver':
                $this->contentLinkUrlResolver = $value;
                break;
            case 'inlineLinkedItemsResolver':
                $this->inlineLinkedItemsResolver = $value;
                break;
            default:
                return;
        }
    }

    /**
     * Instantiates models for given content items and resolves linked items as nested models.
     *
     * @param $contentItems
     * @param null $linkedItems
     *
     * @return array
     */
    public function getContentItems($contentItems, $linkedItems = null)
    {
        $arr = array();
        foreach ($contentItems as $item) {
            $arr[$item->system->codename] = $this->getContentItem($item, $linkedItems);
        }

        return $arr;
    }
    
    public function getModularItems($contentItems, $linkedItems = null)
    {
        $arr = array();
        foreach ($linkedItems as $item) {
            $arr[$item->system->codename] = $this->getContentItem($item, $linkedItems);
        }

        return $arr;
    }

    /**
     * Instantiates model for a given content item and resolves linked items as nested models.
     *
     * @param $item
     * @param null $linkedItems
     *
     * @return array
     */
    public function getContentItem($item, $linkedItems = null)
    {
        $class = $this->typeMapper->getTypeClass($item->system->type);
        $contentItem = $this->bindModel($class, $item, $linkedItems);

        return $contentItem;
    }

    /**
     * Binds given data to a predefined model.
     *
     * @param $modelType "strong" type of predefined model to bind the $data to
     * @param $data JSON response containing content items
     * @param null $linkedItems JSON response containing nested linked items
     * @param null $processedItems collection of already processed items (to avoid infinite loops)
     *
     * @return mixed
     */
    public function bindModel($modelType, $data, $linkedItems = null, $processedItems = null)
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
                        $modelPropertyValue = $this->bindElement($dataProperty, $linkedItems, $processedItems);
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
     * @param null $linkedItems JSON response containing nested linked items
     * @param null $processedItems collection of already processed items (to avoid infinite loops)
     *
     * @return mixed
     */
    public function bindElement($element, $linkedItems, $processedItems)
    {
        $result = null;
        switch ($element->type) {
            case 'asset':
            case 'taxonomy':
            case 'multiple_choice':
                // Map well-known types to their models
                $result = $this->bindKnownType($element, $linkedItems, $processedItems);
                break;
            case 'modular_content':
                // Recursively bind the nested models
                $result = $this->bindLinkedItems($element, $linkedItems, $processedItems);
                break;
            case 'rich_text':
                // Resolve nested artifacts in rich-text elements
                $result = $this->getComplexValue($element, $linkedItems, $processedItems);
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
     * @param mixed      $element  linked items element
     * @param mixed|null $linkedItems JSON response containing nested linked items
     * @param mixed|null $processedItems collection of already processed items (to avoid infinite loops)
     *
     * @return mixed
     */
    private function getComplexValue($element, $linkedItems, $processedItems)
    {
        if(empty($element->value)){
            return $element->value;
        }
        $result = $this->resolveLinksUrls($element->value, $element->links);
        $result = $this->resolveInlineLinkedItems($result, $linkedItems, $processedItems);

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
        if (empty($this->contentLinkUrlResolver)) {
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
                $resolvedLink = $this->contentLinkUrlResolver->resolveBrokenLinkUrl();
            }
            $linkElement->href = $resolvedLink;
        }

        return (string) $dom;
    }

    /**
     * Resolve all linked items detected in input html.
     *
     * @var string input html containing linked items
     *
     * @param mixed|null $linkedItemsData JSON response containing nested linked items
     * @param mixed|null $processedItems collection of already processed items (to avoid infinite loops)
     */
    private function resolveInlineLinkedItems($input, $linkedItemsData, $processedItems)
    {
        if (empty($this->inlineLinkedItemsResolver)) {
            return $input;
        }

        $parser = new HtmlDomParser();
        $dom = $parser->str_get_html($input);

        // Not possible to use multiple attribute selectors
        $linkedItems = $dom->find('object[type=application/kenticocloud]');
        foreach ($linkedItems as $linkedItem) {
            $linkedItem->outertext = $this->resolveLinkedItem($linkedItem, $linkedItemsData, $processedItems);
        }

        return (string) $dom;
    }

    /**
     * Resolve linked item in input html.
     *
     * @var string input html containing linked item
     *
     * @param mixed|null $linkedItems JSON response containing nested linked items
     * @param mixed|null $processedItems collection of already processed items (to avoid infinite loops)
     */
    private function resolveLinkedItem($linkedItem, $linkedItems, $processedItems)
    {
        if ($linkedItem->getAttribute('data-type') == 'item') {
            $itemCodeName = $linkedItem->getAttribute('data-codename');
            $linkedItemsArray = get_object_vars($linkedItems);
            $linkedItemData = array_merge($linkedItemsArray, $processedItems);
            if (isset($linkedItemData[$itemCodeName])) {
                $linkedItem->outertext = $this->inlineLinkedItemsResolver->resolveInlineLinkedItems($linkedItem->outertext, $linkedItemData[$itemCodeName], $linkedItems);
            }
        }
        return $linkedItem->outertext;
    }

    /**
     * Uses a well-known type to bind the element's data.
     *
     * @param $element linked items element
     * @param null $linkedItems JSON response containing nested linked items
     * @param null $processedItems collection of already processed items (to avoid infinite loops)
     */
    public function bindKnownType($element, $linkedItems, $processedItems)
    {
        $knownTypes = array();
        foreach ($element->value as $knownType) {
            $knownTypeClass = $this->typeMapper->getTypeClass($element->type);
            $knowTypeModel = $this->bindModel($knownTypeClass, $knownType, $linkedItems, $processedItems);
            $knownTypes[] = $knowTypeModel;
        }

        return $knownTypes;
    }

    /**
     * Binds a linked items element to a "strongly" typed model.
     *
     * @param $element linked items element
     * @param null $linkedItems JSON response containing nested linked items
     * @param null $processedItems collection of already processed items (to avoid infinite loops)
     */
    public function bindLinkedItems($element, $linkedItems, $processedItems)
    {
        $modelLinkedItems = null;
        if ($linkedItems != null) {
            $modelLinkedItems = array();
            foreach ($element->value as $linkedItemCodename) {
                // Try to load the content item from processed items
                if (isset($processedItems[$linkedItemCodename])) {
                    $subItem = $processedItems[$linkedItemCodename];
                } else {
                    // If not found, recursively load model
                    if (isset($linkedItems->$linkedItemCodename)) {
                        $class = $this->typeMapper->getTypeClass($linkedItems->$linkedItemCodename->system->type);
                        $subItem = $this->bindModel($class, $linkedItems->$linkedItemCodename, $linkedItems, $processedItems);
                        $processedItems[$linkedItemCodename] = $subItem;
                    } else {
                        $subItem = null;
                    }
                }
                $modelLinkedItems[$linkedItemCodename] = $subItem;
            }
        }

        return $modelLinkedItems;
    }
}
