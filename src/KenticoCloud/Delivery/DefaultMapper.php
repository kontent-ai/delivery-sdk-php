<?php

/**
 * Default implementation of the TypeMapperInterface.
 */

namespace KenticoCloud\Delivery;

use KenticoCloud\Delivery\Helpers\TextHelper;
use KenticoCloud\Delivery\Models\Items\ContentItemSystem;

/**
 * Class DefaultMapper serves for resolving strong types based on provided information.
 */
class DefaultMapper implements TypeMapperInterface, PropertyMapperInterface, ValueConverterInterface, ContentLinkUrlResolverInterface
{
    const ELEMENTS_ATTRIBUTE_NAME = 'elements';

    /**
     * Generic content item model.
     *
     * @var string
     */
    protected $ci = \KenticoCloud\Delivery\Models\Items\ContentItem::class;

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
                $type = \KenticoCloud\Delivery\Models\Items\Asset::class;
                break;
            case 'taxonomy':
                $type = \KenticoCloud\Delivery\Models\Items\TaxonomyTerm::class;
                break;
            case 'multiple_choice':
                $type = \KenticoCloud\Delivery\Models\Items\MultipleChoiceOption::class;
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
            // Load all properties from the retireved data (including the system element)
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
                $result = new ContentItemSystem($value->id, $value->name, $value->codename, $value->last_modified, $value->type, $value->sitemap_locations, $value->language);
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
     * @param KenticoCloud\Delivery\Models\Items\ContentLink $link The link to a content item that needs to be resolved.
     *
     * @return string
     */
    public function resolveLinkUrl($link)
    {
        return "";
    }
}
