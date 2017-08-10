<?php

namespace KenticoCloud\Delivery;

class ContentElementTypesMap extends AbstractTypeMapper
{
    public $types = array(
        //'asset' => \KenticoCloud\Delivery\Models\Elements\Assets::class,
        //'taxonomy' => \KenticoCloud\Delivery\Models\Elements\TaxonomyTerm::class,
       // 'multiple_choice' => \KenticoCloud\Delivery\Models\Elements\MultipleChoiceOption::class,
        'date_time' => \DateTime::class,
        'number' => float::class
    );

    protected function getDefaultTypeClass()
    {
        return \KenticoCloud\Delivery\Models\ContentItemElement::class;
    }
}
