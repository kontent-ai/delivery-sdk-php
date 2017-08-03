<?php

namespace KenticoCloud\Delivery\Models;

use \KenticoCloud\Delivery\ContentTypesMap;

class ContentItems extends Model {

	public $items = null;
	public $pagination = null;

	public function setItems($value){
		$this->items = array();
		foreach($value as $item){
			if(isset($item->system->type)){
				$class = ContentTypesMap::getTypeClass($item->system->type);
			}else{
				$class = ContentTypesMap::$defaultTypeClass;
			}
			$this->items[] = $class::create($item);
		}
		return $this;
	}

	public function setPagination($value){
		$this->pagination = Pagination::create($value);
		return $this;
	}
	
}