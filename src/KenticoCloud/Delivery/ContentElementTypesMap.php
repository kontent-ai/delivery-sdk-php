<?php

namespace KenticoCloud\Delivery;

class ContentElementTypesMap extends TypesMap
{
    public static $types = array(
        'asset' => \KenticoCloud\Delivery\Models\Elements\Asset::class,
        'taxonomy' => \KenticoCloud\Delivery\Models\Elements\TaxonomyTerm::class
    );

    public static $defaultTypeClass = \KenticoCloud\Delivery\Models\ContentItemElement::class;
}
