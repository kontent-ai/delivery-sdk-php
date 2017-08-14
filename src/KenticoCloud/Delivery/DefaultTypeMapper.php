<?php

namespace KenticoCloud\Delivery;

class DefaultTypeMapper extends AbstractTypeMapper
{
    protected function getDefaultTypeClass()
    {
        return \KenticoCloud\Delivery\Models\ContentItem::class;
    }

    public function getTypeClass($typeName, $elementName = null, $parentModelType = null)
    {
        if ($elementName === 'system' && $parentModelType == \KenticoCloud\Delivery\Models\ContentItem::class) {
            return \KenticoCloud\Delivery\Models\ContentItemSystem::class;
        }
        if ($typeName != null) {
            return parent::getTypeClass($typeName, $elementName, $parentModelType);
        } else {
            return null;
        }
    }
}
