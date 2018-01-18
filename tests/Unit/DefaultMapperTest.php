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
        $this->assertEquals($linkUrlSlug, $result);
    }
    
    public function test_ResolveBrokenLinkUrl_ReturnEmptySting()
    {
        $mapper = new DefaultMapper();
        $result = $mapper->resolveBrokenLinkUrl();
        
        $this->assertTrue(is_string($result));
        $this->assertEmpty($result);

    }
}
