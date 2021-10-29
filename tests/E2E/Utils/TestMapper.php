<?php

namespace Kentico\Kontent\Tests\E2E\Utils;

use Kentico\Kontent\Delivery\DefaultMapper;

class TestMapper extends DefaultMapper
{
    public function getTypeClass($typeName)
    {
        switch ($typeName) {
            case 'home':
                return \Kentico\Kontent\Tests\E2E\HomeModel::class;
            case 'article':
                return \Kentico\Kontent\Tests\E2E\ArticleModel::class;
        }

        return parent::getTypeClass($typeName);
    }
}