<?php

namespace KenticoCloud\Delivery;

class ContentElementTypesMap extends TypesMap
{
    public static $types = array(
        'asset' => \KenticoCloud\Delivery\Models\Elements\Asset::class
    );

    public static $defaultTypeClass = \KenticoCloud\Delivery\Models\ContentItemElement::class;
}
