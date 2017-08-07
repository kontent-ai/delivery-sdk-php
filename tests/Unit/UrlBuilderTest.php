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
        $url = $builder->getContentElementUrl('article','author');
        $this->assertEquals('https://deliver.kenticocloud.com/975bf280-fd91-488c-994c-2f04416e5ee3/types/article/elements/author', $url);
    }
}