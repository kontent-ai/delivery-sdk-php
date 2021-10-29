<?php

namespace Kentico\Kontent\Tests\Unit;

use Kentico\Kontent\Delivery\DefaultMapper;
use Kentico\Kontent\Delivery\Models\Items\ContentLink;
use PHPUnit\Framework\TestCase;

class DefaultMapperTest extends TestCase
{
     function testGetTypeClassNull()
    {
        $mapper = new DefaultMapper();

        $type = $mapper->getTypeClass(null);

        $this->assertNull($type);
    }

    public function test_getProperty_forNonExistingProperty_returnNull()
    {
        $mapper = new DefaultMapper();
        $data = json_decode('{
            "skip": 0,
            "limit": 2,
            "count": 2,
            "next_page": "https://deliver.kontent.ai/975bf280-fd91-488c-994c-2f04416e5ee3/items?system.type%5bin%5d=article&depth=0&language=es-ES&order=system.name%5bdesc%5d&limit=2&skip=2"
        }');
        $property = "totalCount";

        $result = $mapper->getProperty(get_object_vars($data), $property);
        $this->assertNull($result);
    }

    public function test_ResolveLinkUrl_ReturnsUrlSlug()
    {
        $mapper = new DefaultMapper();
        $linkUrlSlug = "link-1";
        $linkData = json_decode('{
            "url_slug" : "'.$linkUrlSlug.'",
            "codename" : "link-1",
            "type" : "fakeType"
        }');
        $link = new ContentLink("00000000-0000-0000-0000-000000000001", $linkData);
        
        $result = $mapper->resolveLinkUrl($link);
        
        $this->assertTrue(is_string($result));
        $this->assertEquals('/'.$linkUrlSlug, $result);
    }
    
    public function test_ResolveBrokenLinkUrl_ReturnEmptySting()
    {
        $mapper = new DefaultMapper();
        $result = $mapper->resolveBrokenLinkUrl();
        
        $this->assertTrue(is_string($result));
        $this->assertEmpty($result);
    }

    public function test_ResolveInlineLinkedItems_ItemNull_returnEmptyString()
    {
        $mapper = new DefaultMapper();
        $input = '<object type=\"application/kenticocloud\" data-type=\"item\" data-codename=\"modular_item_1\"></object>';
        $result = $mapper->resolveInlineLinkedItems($input, null, null); 
        
        $this->assertTrue(is_string($result));
        $this->assertEmpty($result);
    }

    public function test_ResolveInlineLinkedItems_ItemValid_returnInput()
    {
        $mapper = new DefaultMapper();
        $input = '<object type=\"application/kenticocloud\" data-type=\"item\" data-codename=\"modular_item_1\"></object>';
        $itemJson = file_get_contents('./tests/Unit/Data/SimpleItem.json');
        $item = json_decode($itemJson);
        $result = $mapper->resolveInlineLinkedItems($input, $item, null); 
        
        $this->assertTrue(is_string($result));
        $this->assertEquals($input, $result);
    }


}
