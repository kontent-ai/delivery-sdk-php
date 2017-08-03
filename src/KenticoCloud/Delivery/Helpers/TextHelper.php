<?php

namespace KenticoCloud\Delivery\Helpers;

class TextHelper extends Helper
{    
    //http://syframework.alwaysdata.net/decamelize
    function decamelize($input, $separator = '_')
    {
        return strtolower(preg_replace(array('/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'), '$1'. $separator .'$2', $input));
    }

    //http://stackoverflow.com/a/33122760/3363709
    function camelize($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }
}
