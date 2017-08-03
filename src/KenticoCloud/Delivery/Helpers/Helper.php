<?php

namespace KenticoCloud\Delivery\Helpers;

class Helper
{    
    static protected $instance = null;
    public static function getInstance()
    {
        $class = get_called_class();
        if (!$class::$instance) {
            $class::$instance = new $class;
        }
        return $class::$instance;
    }
}
