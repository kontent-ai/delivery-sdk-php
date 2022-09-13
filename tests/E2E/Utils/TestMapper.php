<?php

namespace Kontent\Ai\Tests\E2E\Utils;

use Kontent\Ai\Delivery\DefaultMapper;

class TestMapper extends DefaultMapper
{
    public function getTypeClass($typeName)
    {
        switch ($typeName) {
            case 'home':
                return \Kontent\Ai\Tests\E2E\HomeModel::class;
            case 'article':
                return \Kontent\Ai\Tests\E2E\ArticleModel::class;
        }

        return parent::getTypeClass($typeName);
    }
}