<?php

namespace KenticoCloud\Tests\Unit;

use KenticoCloud\Delivery\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function getClient($previewApiKey = null)
    {
        if (is_null($previewApiKey)) {
            return new Client('975bf280-fd91-488c-994c-2f04416e5ee3');
        } else {
            return new Client('975bf280-fd91-488c-994c-2f04416e5ee3', $previewApiKey);
        }
    }

    public function testGetContentItem()
    {
        $params['system.codename'] = 'home';
        $client = $this->getClient();
        $item = $client->getItem($params);
        $this->assertEquals('1bd6ba00-4bf2-4a2b-8334-917faa686f66', $item->system->id);
    }

    public function testGetPreviewApiEmpty()
    {
        $params['system.codename'] = 'amsterdam';
        $client = $this->getClient();
        $item = $client->getItem($params);
        $this->assertNull($item);
    }

    public function testGetPreviewApiPresent()
    {
        $params['system.codename'] = 'amsterdam';
        $client = $this->getClient('ew0KICAiYWxnIjogIkhTMjU2IiwNCiAgInR5cCI6ICJKV1QiDQp9.ew0KICAidWlkIjogInVzcl8wdk4xUTA1bks2YmlyQVQ2TU5wdkkwIiwNCiAgImVtYWlsIjogInBldHIuc3ZpaGxpa0BrZW50aWNvLmNvbSIsDQogICJwcm9qZWN0X2lkIjogIjk3NWJmMjgwLWZkOTEtNDg4Yy05OTRjLTJmMDQ0MTZlNWVlMyIsDQogICJqdGkiOiAibzhUdkc0OHFqX0ZUSWplVCIsDQogICJ2ZXIiOiAiMS4wLjAiLA0KICAiZ2l2ZW5fbmFtZSI6ICJQZXRyIiwNCiAgImZhbWlseV9uYW1lIjogIlN2aWhsaWsiLA0KICAiYXVkIjogInByZXZpZXcuZGVsaXZlci5rZW50aWNvY2xvdWQuY29tIg0KfQ.wd7_nOYInsdsoh9-0R43FnDQuVk_azPaYze7Ghxv43I');
        $item = $client->getItem($params);
        $this->assertEquals('e844a6aa-4dc4-464f-8ae9-f9f66cc6ab61', $item->system->id);
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
    

    public function testAssets()
    {
        $params['system.codename'] = 'home_page_hero_unit';
        $client = $this->getClient();
        $item = $client->getItem($params);
    }
}
