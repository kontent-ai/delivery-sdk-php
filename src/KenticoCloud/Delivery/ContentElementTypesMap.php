<?php

namespace KenticoCloud\Delivery;

class ContentElementTypesMap extends TypesMap
{
    public static $types = array(
        //'asset' => \KenticoCloud\Delivery\Models\Elements\Assets::class,
        //'taxonomy' => \KenticoCloud\Delivery\Models\Elements\TaxonomyTerm::class,
       // 'multiple_choice' => \KenticoCloud\Delivery\Models\Elements\MultipleChoiceOption::class,
        'date_time' => \DateTime::class,
        'number' => float::class
    );

    public static $defaultTypeClass = null;//\KenticoCloud\Delivery\Models\ContentItemElement::class;
}
