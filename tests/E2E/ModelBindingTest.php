<?php

namespace KenticoCloud\Tests\E2E;

use KenticoCloud\Delivery\Client;
use KenticoCloud\Delivery\QueryParams;
use KenticoCloud\Delivery\AbstractTypeMapper;
use KenticoCloud\Delivery\TypeMapperInterface;

use PHPUnit\Framework\TestCase;

class TetsTypeMapper implements TypeMapperInterface
{
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null)
    {
        switch($typeName)
        {
            case 'home':
                return \KenticoCloud\Tests\E2E\HomeModel::class;
        }
        return null;
    }
}

class ModelBindingTest extends TestCase
{
    public function getClient($previewApiKey = null)
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        return new Client($projectId, null, new TetsTypeMapper());
    }    
/*
    public function testGetModel()
    {
        $params['system.codename'] = 'home';
        $client = $this->getClient();
        $item = $client->getItem($params);
        //$this->assertEquals('1bd6ba00-4bf2-4a2b-8334-917faa686f66', $item->system->id);
    } */
}