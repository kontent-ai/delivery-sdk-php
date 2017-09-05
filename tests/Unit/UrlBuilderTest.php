<?php

namespace KenticoCloud\Tests\Unit;

use KenticoCloud\Delivery\UrlBuilder;
use PHPUnit\Framework\TestCase;

class UrlBuilderTest extends TestCase
{
    public function testGetElement()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId);
        $url = $builder->getContentElementUrl('article', 'author');
        $this->assertEquals('https://deliver.kenticocloud.com/975bf280-fd91-488c-994c-2f04416e5ee3/types/article/elements/author', $url);
    }

    public function testGetPreviewApi()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId, true);
        $url = $builder->getContentElementUrl('article', 'author');
        $this->assertStringStartsWith('https://preview-deliver.kenticocloud.com', $url);
    }

    public function testGetTypes()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId);
        $url = $builder->getTypesUrl();
        $this->assertEquals('https://deliver.kenticocloud.com/975bf280-fd91-488c-994c-2f04416e5ee3/types', $url);
    }
    
    public function testGetType()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId);
        $url = $builder->getTypeUrl('article');
        $this->assertEquals('https://deliver.kenticocloud.com/975bf280-fd91-488c-994c-2f04416e5ee3/types/article', $url);
    }
}
