<?php

namespace KenticoCloud\Delivery;

class ContentElementTypesMap extends TypesMap {

	public static $types = array(
		'asset' => '\KenticoCloud\Delivery\Models\Asset'
	);

	public static $defaultTypeClass = '\KenticoCloud\Delivery\Models\ContentItemElement';

}