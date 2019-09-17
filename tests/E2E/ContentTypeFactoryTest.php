<?php

namespace Kentico\Kontent\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Kentico\Kontent\Delivery\DeliveryClient;
use Kentico\Kontent\Delivery\QueryParams;

class ContentTypeFactoryTest extends TestCase
{
    public function getClient($previewApiKey = null)
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        if (is_null($previewApiKey)) {
            return new DeliveryClient($projectId);
        } else {
            return new DeliveryClient($projectId, $previewApiKey);
        }
    }

    public function testGetElement()
    {
        $client = $this->getClient();
        $actual = $client->getElement('coffee', 'processing');

        $this->assertEquals('Processing', $actual->name);
    }

    public function testGetNonExistentElement()
    {
        $client = $this->getClient();
        $actual = $client->getElement('non-existent', 'processing');

        $this->assertNull($actual);
    }

    public function testGetNonExistentType()
    {
        $client = $this->getClient();
        $actual = $client->getType('non-existent');

        $this->assertNull($actual);
    }

    public function testGetAllTypes()
    {
        $client = $this->getClient();
        $response = $client->getTypes();
        $this->assertNotEmpty($response->types);
    }

    public function testGetNonExistentTypes()
    {
        $params = (new QueryParams())->equals('system.codename', 'non-existent');
        $client = $this->getClient();
        $response = $client->getTypes($params);
        $this->assertEmpty($response->types);
    }

    public function testCreateTypes_NullResponse_IsEmptyArray()
    {
        $response = null;
        $factory = new \Kentico\Kontent\Delivery\ContentTypeFactory();
        $types = $factory->createTypes($response);

        $this->assertEquals(array(), $types);
    }

    public function testCreateTypes_SingleItemStructure_Matches()
    {
        $client = $this->getClient();
        $params = (new QueryParams())->limit(1);
        $actual = $client->getTypes($params)->types;

        $this->assertEquals('About us', $actual[0]->system->name);
        $this->assertGreaterThan(0, count($actual[0]->elements));
        $this->assertInstanceOf(\Kentico\Kontent\Delivery\Models\Types\ContentTypeElement::class, array_pop($actual[0]->elements));
    }

    public function testCreateTypes_FullTypeResponse_ContainsXObjects()
    {
        $expectedObjects = 13;
        $client = $this->getClient();
        $params = new QueryParams();
        $types = $client->getTypes($params)->types;

        $actualObjects = count($types);

        $this->assertEquals($expectedObjects, $actualObjects);
    }
}
