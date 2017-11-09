<?php

namespace KenticoCloud\Tests\E2E;

use KenticoCloud\Delivery\DeliveryClient;
use KenticoCloud\Delivery\QueryParams;
use KenticoCloud\Delivery\AbstractTypeMapper;
use KenticoCloud\Delivery\TypeMapperInterface;

use PHPUnit\Framework\TestCase;

class TetsMapper implements TypeMapperInterface
{
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null)
    {
        switch ($typeName) {
            case 'home':
                return \KenticoCloud\Tests\E2E\HomeModel::class;
            case 'article':
                return \KenticoCloud\Tests\E2E\ArticleModel::class;
        }
        return null;
    }
}

class ModelBindingTest extends TestCase
{
    public function getClient()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        return new DeliveryClient($projectId, null, new TetsMapper());
    }

    public function testArticleModel()
    {
        $client = $this->getClient();
        $item = $client->getItem('on_roasts');
        $this->assertEquals('On Roasts', $item->title);
        //$this->assertEquals('1bd6ba00-4bf2-4a2b-8334-917faa686f66', $item->system->id);        
    }

    /*
    public function testHomeModel()
    {
        $params['system.codename'] = 'home';
        $client = $this->getClient();
        $item = $client->getItem($params);
    } */
}
