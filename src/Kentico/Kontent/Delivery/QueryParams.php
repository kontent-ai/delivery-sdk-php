<?php

namespace Kentico\Kontent\Delivery;

/**
 * Class QueryParams.
 */
class QueryParams implements \ArrayAccess
{
    /**
     * Filter key-value pairs.
     *
     * @var array
     */
    public $data = array();

    /**
     * Specifies the maximum level of recursion when retrieving linked items. If not specified, the default depth is one level.
     *
     * @param int $depth the maximum level of recursion to use when retrieving linked items
     *
     * @return $this
     */
    public function depth(int $depth)
    {
        $this->data['depth'] = $depth;

        return $this;
    }

    /**
     * Specifies the type(s) of content items that should be returned.
     *
     * @param $types type(s) of content items that should be returned
     *
     * @return QueryParams
     */
    public function type($types)
    {
        return $this->in('system.type', $types);
    }

    /**
     * Specifies the maximum number of content items to return.
     *
     * @param int $limit the maximum number of content items to return
     *
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->data['limit'] = $limit;

        return $this;
    }

    /**
     * Specifies the number of content items to skip.
     *
     * @param int $skip the number of content items to skip
     *
     * @return $this
     */
    public function skip(int $skip)
    {
        $this->data['skip'] = $skip;

        return $this;
    }

    /**
     * Specifies the content item by its codename.
     *
     * @param $codename codename of content item that should be returned
     *
     * @return $this
     */
    public function codename($codename)
    {
        $this->data['system.codename'] = $codename;

        return $this;
    }

    /**
     * Specifies the content elements to retrieve.
     *
     * @param $elementCodenames content elements to be retrieved
     */
    public function elements($elementCodenames)
    {
        $this->data['elements'] = implode(',', is_array($elementCodenames) ? $elementCodenames : array($elementCodenames));

        return $this;
    }

    /**
     * Specifies that content items should be sorted ascendingly by $element.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     *
     * @return $this
     */
    public function orderAsc($element)
    {
        $this->data['order'] = $element.'[asc]';

        return $this;
    }

    /**
     * Specifies that content items should be sorted descendingly by $element.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     *
     * @return $this
     */
    public function orderDesc($element)
    {
        $this->data['order'] = $element.'[desc]';

        return $this;
    }

    /**
     * Specifies the language of content items to be requested.
     *
     * @param $language the language of items to be returned
     *
     * @return $this
     */
    public function language($language)
    {
        $this->data['language'] = $language;

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute has a value that contains all the specified values.
     * This filter is applicable to array values only, such as sitemap location or value of Linked Items, Taxonomy and Multiple choice content elements.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     * @param $values the filter values
     *
     * @return $this
     */
    public function all($element, $values)
    {
        $this->data[$element.'[all]'] = implode(',', is_array($values) ? $values : array($values));

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute has a value that contains any of the specified values.
     * This filter is applicable to array values only, such as sitemap location or value of Linked Items, Taxonomy and Multiple choice content elements.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     * @param $values the filter values
     *
     * @return $this
     */
    public function any($element, $values)
    {
        $this->data[$element.'[any]'] = implode(',', is_array($values) ? $values : array($values));

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute has a value that matches a value in the specified list.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     * @param $values the filter values
     *
     * @return $this
     */
    public function in($element, $values)
    {
        $this->data[$element.'[in]'] = implode(',', is_array($values) ? $values : array($values));

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute has not a value that matches a value in the specified list.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     * @param $values the filter values
     *
     * @return $this
     */
    public function notIn($element, $values)
    {
        $this->data[$element.'[nin]'] = implode(',', is_array($values) ? $values : array($values));

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute has a value that contains the specified value.
     * This filter is applicable to array values only, such as sitemap location or value of Linked Items, Taxonomy and Multiple choice content elements.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     * @param $value the filter value
     *
     * @return $this
     */
    public function contains($element, $value)
    {
        $this->data[$element.'[contains]'] = $value;

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute has the specified value.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     * @param $value the filter value
     *
     * @return $this
     */
    public function equals($element, $value)
    {
        $this->data[$element] = $value;

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute is not equal to the specified value.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.type.
     * @param $value the filter value
     *
     * @return $this
     */
    public function notEquals($element, $value)
    {
        $this->data[$element.'[neq]'] = $value;

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element is empty.
     * For rich text, use the {@see QueryParams::equals()} operator with value "\<p\>\<br\>\</p\>".
     *
     * @param $element The codename of a content element, for example elements.title.
     *
     * @return $this
     */
    public function empty($element)
    {
        array_push($this->data, $element.'[empty]');

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element is not empty.
     * For rich text, use the {@see QueryParams::notEquals()} operator with value "\<p\>\<br\>\</p\>".
     *
     * @param $element The codename of a content element, for example elements.title.
     *
     * @return $this
     */
    public function notEmpty($element)
    {
        array_push($this->data, $element.'[nempty]');

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute has a value that is greater than the specified value.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     * @param $value the filter value
     *
     * @return $this
     */
    public function greaterThan($element, $value)
    {
        $this->data[$element.'[gt]'] = $value;

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute has a value that is greater than or equal to the specified value.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     * @param $value the filter value
     *
     * @return $this
     */
    public function greaterThanOrEqual($element, $value)
    {
        $this->data[$element.'[gte]'] = $value;

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute has a value that is less than the specified value.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     * @param $value the filter value
     *
     * @return $this
     */
    public function lessThan($element, $value)
    {
        $this->data[$element.'[lt]'] = $value;

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute has a value that is less than or equal to the specified value.
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     * @param $value the filter value
     *
     * @return $this
     */
    public function lessThanOrEqual($element, $value)
    {
        $this->data[$element.'[lte]'] = $value;

        return $this;
    }

    /**
     * Represents a filter that matches a content item if the specified content element or system attribute has a value that falls within the specified range of values (both inclusive).
     *
     * @param $element The codename of a content element or system attribute, for example elements.title or system.name.
     * @param $lowerLimit the lower limit of the filter range
     * @param $upperLimit the upper limit of the filter range
     *
     * @return $this
     */
    public function range($element, $lowerLimit, $upperLimit)
    {
        $this->data[$element.'[range]'] = $lowerLimit.','.$upperLimit;

        return $this;
    }

    /**
     * Adds the information about the total number of content items matching your query.
     * The number doesn't include linked items returned as part of the modular_content property. For the total number of items returned within the response, see the X-Request-Charge header.
     *
     * @return $this
     */
    public function includeTotalCount()
    {
        $this->data['includeTotalCount'] = true;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * ArrayAccess implementation (http://php.net/manual/en/class.arrayaccess.php)
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * @codeCoverageIgnore
     * ArrayAccess implementation (http://php.net/manual/en/class.arrayaccess.php)
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @codeCoverageIgnore
     * ArrayAccess implementation (http://php.net/manual/en/class.arrayaccess.php)
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * @codeCoverageIgnore
     * ArrayAccess implementation (http://php.net/manual/en/class.arrayaccess.php)
     *
     * @param mixed $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}
