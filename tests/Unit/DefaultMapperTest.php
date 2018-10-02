<?php

namespace KenticoCloud\Tests\Unit;

use KenticoCloud\Delivery\DefaultMapper;
use KenticoCloud\Delivery\Models\Items\ContentLink;
use PHPUnit\Framework\TestCase;

class DefaultMapperTest extends TestCase
{
     function testGetTypeClassNull()
    {
        $mapper = new DefaultMapper();

        $type = $mapper->getTypeClass(null);

        $this->assertNull($type);
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
        $result = $mapper->resolveInlineLinkedItems($input, null); 
        
        $this->assertTrue(is_string($result));
        $this->assertEmpty($result);
    }

    public function test_ResolveInlineLinkedItems_ItemValid_returnInput()
    {
        $mapper = new DefaultMapper();
        $input = '<object type=\"application/kenticocloud\" data-type=\"item\" data-codename=\"modular_item_1\"></object>';
        $itemJson = file_get_contents('./tests/Unit/Data/SimpleItem.json');
        $item = json_decode($itemJson);
        $result = $mapper->resolveInlineLinkedItems($input, $item); 
        
        $this->assertTrue(is_string($result));
        $this->assertEquals($input, $result);
    }


}
