<?php

namespace KenticoCloud\Tests\E2E;

use KenticoCloud\Delivery\DeliveryClient;
use KenticoCloud\Delivery\QueryParams;
use KenticoCloud\Delivery\AbstractTypeMapper;
use KenticoCloud\Delivery\DefaultMapper;

use PHPUnit\Framework\TestCase;

class TetsMapper extends DefaultMapper
{
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null)
    {
        switch ($typeName) {
            case 'home':
                return \KenticoCloud\Tests\E2E\HomeModel::class;
            case 'article':
                return \KenticoCloud\Tests\E2E\ArticleModel::class;
        }
        return parent::getTypeClass($typeName, $elementName, $parentModelType);
    }
}

class ModelBindingTest extends TestCase
{
    public function getClient()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $client = new DeliveryClient($projectId);
        $client->typeMapper =  new TetsMapper();
        return $client;
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
