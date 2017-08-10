<?php

namespace KenticoCloud\Delivery;

class DefaultTypeMapper extends AbstractTypeMapper
{
    protected function getDefaultTypeClass()
    {
        return \KenticoCloud\Delivery\Models\ContentItem::class;
    }
}
