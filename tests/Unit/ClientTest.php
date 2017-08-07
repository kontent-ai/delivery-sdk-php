<?php

namespace KenticoCloud\Tests\Unit;

use KenticoCloud\Delivery\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function getClient()
    {
        return new Client('975bf280-fd91-488c-994c-2f04416e5ee3');
    }

    public function testGetContentItem()
    {
        $params['system.codename'] = 'home';
        $client = $this->getClient();
        $item = $client->getItem($params);
        $this->assertEquals('1bd6ba00-4bf2-4a2b-8334-917faa686f66', $item->system->id);
    }

    /* public function testGetModel()
    {
        $params['system.codename'] = 'home';
        $client = $this->getClient();
        \KenticoCloud\Delivery\TypesMap::setTypeClass('home', \KenticoCloud\Tests\Unit\HomeModel::class);

        $item = $client->getItem($params);
        $this->assertEquals('1bd6ba00-4bf2-4a2b-8334-917faa686f66', $item->system->id);
    } */

    
    public function testGetContentItems()
    {
        $params['system.type'] = 'article';
        $params['depth'] = 2;
        $client = $this->getClient();
        $items = $client->getItems($params);
        $this->assertGreaterThan(1, count($items->items));
        $this->assertGreaterThan(1, count($items->modularContent));
    }

    public function testDepth()
    {
        $params['system.type'] = 'article';
        $params['depth'] = 0;
        $client = $this->getClient();
        $items = $client->getItems($params);
        $this->assertEquals(0, count($items->modularContent));
    }
    

    /* public function testAssets()
    {
        $params['system.codename'] = 'home_page_hero_unit';
        $client = $this->getClient();
        $item = $client->getItem($params);
    } */
}
