<?php

namespace KenticoCloud\Delivery\Models;

use \KenticoCloud\Delivery\Helpers\TextHelper;

class Model
{

    public function populate($obj)
    {
        if (is_string($obj)) {
            $obj = json_decode($obj);
        }
        if (is_object($obj)) {
            $properties = get_object_vars($obj);
        } else {
            $properties = $obj; //assume it's an array
        }
        
        foreach ($properties as $property => $value) {
            $setMethod = 'set'.TextHelper::getInstance()->pascalCase($property);
            call_user_func_array(array($this, $setMethod), array($value));
        }
        return $this;
    }

    public static function create($obj = null)
    {
        $class = get_called_class();
        $instance = new $class();
        if ($obj) {
            $instance->populate($obj);
        }
        return $instance;
    }

    public function __call($val, $x)
    {
        if (substr($val, 0, 3) == 'get') {
            $varname = substr($val, 3);

            if (property_exists($this, $dcvarname = TextHelper::getInstance()->decamelize($varname))) {
                return $this->$lcfvarname;
            } elseif (property_exists($this, $lcfvarname = lcfirst($varname))) {
                return $this->$lcfvarname;
            } else {
                throw new \Exception('Property does not exist: '.$varname, 500);
            }
        } elseif (substr($val, 0, 3) == 'set') {
            $varname = substr($val, 3);
            $varname = TextHelper::getInstance()->decamelize($varname);
            $this->$varname = reset($x);
            return $this;
        } else {
            throw new \Exception('Bad method.', 500);
        }
    }

    public function __toString()
    {
        return json_encode($this);
    }
}
