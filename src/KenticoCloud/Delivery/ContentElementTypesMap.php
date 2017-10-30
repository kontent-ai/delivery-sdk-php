<?php
/**
 * TODO: RC
 */

namespace KenticoCloud\Delivery;

/**
 * Class ContentElementTypesMap
 * @package KenticoCloud\Delivery
 */
class ContentElementTypesMap
{
    /**
     * TODO: RC
     * @var array
     */
    public $types = array(
        //'asset' => \KenticoCloud\Delivery\Models\Elements\Assets::class,
        //'taxonomy' => \KenticoCloud\Delivery\Models\Elements\TaxonomyTerm::class,
       // 'multiple_choice' => \KenticoCloud\Delivery\Models\Shared\MultipleChoiceOption::class,
        'date_time' => \DateTime::class,
        'number' => float::class
    );

    /**
     * TODO: RC
     * @return string
     */
    protected function getDefaultTypeClass()
    {
        return \KenticoCloud\Delivery\Models\Items\ContentItemElement::class;
    }
}
