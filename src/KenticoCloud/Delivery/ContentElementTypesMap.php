<?php

namespace KenticoCloud\Delivery;

class ContentElementTypesMap
{
    public $types = array(
        //'asset' => \KenticoCloud\Delivery\Models\Elements\Assets::class,
        //'taxonomy' => \KenticoCloud\Delivery\Models\Elements\TaxonomyTerm::class,
       // 'multiple_choice' => \KenticoCloud\Delivery\Models\Shared\MultipleChoiceOption::class,
        'date_time' => \DateTime::class,
        'number' => float::class
    );

    protected function getDefaultTypeClass()
    {
        return \KenticoCloud\Delivery\Models\Items\ContentItemElement::class;
    }
}
