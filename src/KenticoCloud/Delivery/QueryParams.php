<?php

namespace KenticoCloud\Delivery;


class QueryParams implements \ArrayAccess
{
    public $data = array();

    public function depth(int $depth)
    {
        $this->data['depth'] = $depth;
        return $this;
    }

    public function type($type)
    {
        $this->data['system.type'] = $type;
        return $this;
    }

    public function limit(int $limit)
    {
        $this->data['limit'] = $limit;
        return $this;
    }

    public function skip(int $skip)
    {
        $this->data['skip'] = $skip;
        return $this;
    }

    public function codename($codename)
    {
        $this->data['system.codename'] = $codename;
        return $this;
    }

    public function orderAsc($element)
    {
        $this->data['order'] = $codename . '[asc]';
        return $this;
    }

    public function orderDesc($element)
    {
        $this->data['order'] = $codename . '[desc]';
        return $this;
    }

    public function language($language)
    {
        $this->data['language'] = $language;
        return $this;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}