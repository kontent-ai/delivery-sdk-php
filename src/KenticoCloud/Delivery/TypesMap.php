<?php

namespace KenticoCloud\Delivery;

class TypesMap {

	public static $types = array(
		
	);

	public static $defaultTypeClass = null;

	public static function setTypeClass($type, $class){
		$self = get_called_class();
		$self::$types[$type] = $class;
	}

	public static function getTypeClass($type){
		$self = get_called_class();
		return isset($self::$types[$type]) ? $self::$types[$type] : $self::$defaultTypeClass;
	}

}