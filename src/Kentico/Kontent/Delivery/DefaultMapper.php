<?php

/**
 * Default implementation of the TypeMapperInterface.
 */

namespace Kentico\Kontent\Delivery;

use Exception;
use Kentico\Kontent\Delivery\Helpers\TextHelper;
use Kentico\Kontent\Delivery\Models\Items\ContentItemSystem;

/**
 * Class DefaultMapper serves for resolving strong types based on provided information.
 */
class DefaultMapper implements TypeMapperInterface, PropertyMapperInterface, ValueConverterInterface, ContentLinkUrlResolverInterface, InlineLinkedItemsResolverInterface
{
    const ELEMENTS_ATTRIBUTE_NAME = 'elements';

    /**
     * Generic content item model.
     *
     * @var string
     */
    protected $ci = \Kentico\Kontent\Delivery\Models\Items\ContentItem::class;

    /**
     * Gets strong type based on provided information.
     *
     * @param $typeName name of the type to get (should be a primary source type resolution)
     *
     * @return null|string
     */
    public function getTypeClass($typeName)
    {
        switch ($typeName) {
            case 'asset':
                $type = \Kentico\Kontent\Delivery\Models\Items\Asset::class;
                break;
            case 'taxonomy':
                $type = \Kentico\Kontent\Delivery\Models\Items\TaxonomyTerm::class;
                break;
            case 'multiple_choice':
                $type = \Kentico\Kontent\Delivery\Models\Items\MultipleChoiceOption::class;
                break;
            case null:
                $type = null;
                break;
            default:
                $type = $this->ci;
                break;
        }

        return $type;
    }

    /**
     * Returns the correct element from $data based on property name.
     *
     * @param $data source data (deserialized JSON)
     * @param $property target property name
     *
     * @return mixed
     */
    public function getProperty($data, $property)
    {
        $result = array_filter(
            (array) $data,
            function ($arrKey) use ($property) {
                // Apply the property transformation to the array key
                $arrKey = TextHelper::getInstance()->camelCase($arrKey);
                // See if the key matches with the searched property
                return $arrKey == $property;
            },
            ARRAY_FILTER_USE_KEY
        );

        switch (count($result)) {
            case 0:
                // Not found even in elements
                if(!isset($data[self::ELEMENTS_ATTRIBUTE_NAME])) {
                    return null;
                }
                // Search within elements
                return $this->getProperty($data[self::ELEMENTS_ATTRIBUTE_NAME], $property);

            case 1:
                // Return the first (and only) item in the array
                return array_pop($result);

            default:
                throw new Exception('More than one property found! Please adjust the property mapper.');
        }
    }

    /**
     * Gets an array of properties of a model that need to be filled with data.
     *
     * @param $model model to examine
     * @param $data  source JSON data
     *
     * @return array
     */
    public function getModelProperties($model, $data)
    {
        if (is_a($model, $this->ci)) {
            // Load all properties from the retrieved data (including the system element)
            $model->system = null;
            $data = get_object_vars($data);
            foreach ($data[self::ELEMENTS_ATTRIBUTE_NAME] as $elementName => $element) {
                $camelElement = TextHelper::getInstance()->camelCase($elementName);
                $model->$camelElement = null;
            }

            return get_object_vars($model);
        } else {
            // Load properties from a strongly-typed model
            return get_object_vars($model);
        }
    }

    /**
     * Converts a given simple value to a specified type.
     *
     * @param $type type to convert the value to
     * @param $value value to convert
     *
     * @return mixed
     */
    public function getValue($type, $value)
    {
        switch ($type) {
            case 'system':
                // Components does not have workflow step set
                $workflowStep = isset($value->workflow_step) ? $value->workflow_step : null;
                $result = new ContentItemSystem($value->id, $value->name, $value->codename, $value->last_modified, $value->type, $value->sitemap_locations, $value->language, $value->collection, $workflowStep);
                break;
            case 'date_time':
                $result = new \DateTime($value);
                break;
            case 'number':
                $result = (float) $value;
                break;
            default:
                $result = $value;
                break;
        }

        return $result;
    }    

    /**
     * Returns a URL of the linked content item.
     *
     * @param Kentico\Kontent\Delivery\Models\Items\ContentLink $link The link to a content item that needs to be resolved.
     *
     * @return string
     */
    public function resolveLinkUrl($link)
    {
        return "/$link->urlSlug";
    }

    /**
     * Returns a URL of the linked content item that is not available.
     *
     * @return string
     */
    public function resolveBrokenLinkUrl()
    {
        return "";
    }

    /**
     * Return resolved inline linked item.
     * 
     * @param string $input input html of inline linked items.
     * @param mixed $item data for inline linked items.
     * @param mixed|null $linkedItems JSON response containing nested linked items
     * 
     * @return string
     */
    public function resolveInlineLinkedItems($input, $item, $linkedItems)
    {
        if(isset($item) && strpos($input, $item->system->codename) !== false){
            return $input;
        }

        return "";
    }
}
   