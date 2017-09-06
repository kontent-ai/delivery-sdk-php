<?php
namespace KenticoCloud\Tests\Unit;

use KenticoCloud\Delivery;
use PHPUnit\Framework\TestCase;
use KenticoCloud\Delivery\Client;
use KenticoCloud\Delivery\Models;
use KenticoCloud\Delivery\QueryParams;

class ContentTypeFactoryTest extends TestCase
{
    public function getClient($previewApiKey = null)
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        if (is_null($previewApiKey)) {
            return new Client($projectId);
        } else {
            return new Client($projectId, $previewApiKey);
        }
    }

    public function testCreateTypes_NullResponse_IsEmptyArray()
    {
        $response = null;
        $factory = new \KenticoCloud\Delivery\ContentTypeFactory();
        $types = $factory->createTypes($response);

        $this->assertEquals(array(), $types);
    }
    
    public function testCreateTypes_SingleItemStructure_Matches()
    {
        $client = $this->getClient();
        $params = (new QueryParams())->limit(1);
        $actual = $client->getTypes($params);

        $contentSystem = new Models\ContentTypeSystem(
            "b2c14f2c-6467-460b-a70b-bca17972a33a",
            "About us",
            "about_us",
            "2017-08-02T07:33:28.2997578Z"
        );
        $contentElements = array(
            new Models\ContentTypeElement("modular_content", "facts", "Facts"),
            new Models\ContentTypeElement("url_slug", "url_pattern", "URL pattern")
        );

        $dummyType = new Models\ContentType();
        $dummyType->system = $contentSystem;
        $dummyType->elements = $contentElements;
        $expected = array($dummyType);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateTypes_FullTypeResponse_ContainsXObjects()
    {
        $expectedObjects = 13;
        $client = $this->getClient();
        $params = new QueryParams();
        $types = $client->getTypes($params);

        $actualObjects = count($types);

        $this->assertEquals($expectedObjects, $actualObjects);
    }
}