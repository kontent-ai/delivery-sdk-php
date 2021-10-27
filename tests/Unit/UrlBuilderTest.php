<?php

namespace Kentico\Kontent\Tests\Unit;

use Kentico\Kontent\Delivery\UrlBuilder;
use PHPUnit\Framework\TestCase;
use Kentico\Kontent\Delivery\QueryParams;

class UrlBuilderTest extends TestCase
{
    public function testGetElement()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId);
        $url = $builder->getContentElementUrl('article', 'author');
        $this->assertEquals('https://deliver.kontent.ai/975bf280-fd91-488c-994c-2f04416e5ee3/types/article/elements/author', $url);
    }

    public function testGetPreviewApi()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId, true);
        $url = $builder->getContentElementUrl('article', 'author');
        $this->assertStringStartsWith('https://preview-deliver.kontent.ai', $url);
    }

    public function testGetTypes()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId);
        $url = $builder->getTypesUrl();
        $this->assertEquals('https://deliver.kontent.ai/975bf280-fd91-488c-994c-2f04416e5ee3/types', $url);
    }

    public function testGetType()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId);
        $url = $builder->getTypeUrl('article');
        $this->assertEquals('https://deliver.kontent.ai/975bf280-fd91-488c-994c-2f04416e5ee3/types/article', $url);
    }

    public function testGetItemsQuery()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId);

        $params = (new QueryParams())
        ->skip(2)
        ->orderAsc('title')

        ->all('elements.personas', array('barista', 'coffee_blogger'))
        ->any('elements.description', 'Hello')
        ->contains('elements.title', 'a')
        ->in('elements.margin', array(21,15,19))
        ->notIn('elements.oldMargin', array(42, 666))
        ->equals('elements.isfeatured', 'true')
        ->notEquals('system.type', 'article')
        ->empty('elements.note')
        ->notEmpty('elements.slug')
        ->equals('system.collection', 'default')
        ->greaterThan('elements.price', 6)
        ->greaterThanOrEqual('elements.oldprice', 7)
        ->range('elements.shoesize', 7, 9)
        ->lessThan('elements.tax', 21)
        ->lessThanOrEqual('elements.oldtax', 25)
        ->includeTotalCount();

        $url = $builder->getItemsUrl($params);

        $this->assertContains('skip=2', $url);
        $this->assertContains('order=title%5Basc%5D', $url);

        $this->assertContains('elements.personas%5Ball%5D=barista%2Ccoffee_blogger', $url);
        $this->assertContains('elements.description%5Bany%5D=Hello', $url);
        $this->assertContains('elements.title%5Bcontains%5D=a', $url);
        $this->assertContains('elements.margin%5Bin%5D=21%2C15%2C19', $url);
        $this->assertContains('elements.oldMargin%5Bnin%5D=42%2C666', $url);

        $this->assertContains('elements.isfeatured=true', $url);
        $this->assertContains('system.type%5Bneq%5D=article', $url);

        $this->assertContains('elements.note%5Bempty%5D', $url);
        $this->assertContains('elements.slug%5Bnempty%5D', $url);

        $this->assertContains('system.collection=default', $url);
        $this->assertContains('elements.price%5Bgt%5D=6', $url);
        $this->assertContains('elements.oldprice%5Bgte%5D=7', $url);
        $this->assertContains('elements.shoesize%5Brange%5D=7%2C9', $url);
        $this->assertContains('elements.tax%5Blt%5D=21', $url);
        $this->assertContains('elements.oldtax%5Blte%5D=25', $url);

        $this->assertContains('includeTotalCount=1', $url);
    }

    public function testGetTaxonomy()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId);
        $url = $builder->getTaxonomyUrl('persona');
        $this->assertEquals('https://deliver.kontent.ai/975bf280-fd91-488c-994c-2f04416e5ee3/taxonomies/persona', $url);
    }

    public function testGetTaxonomies()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId);
        $url = $builder->getTaxonomiesUrl();
        $this->assertEquals('https://deliver.kontent.ai/975bf280-fd91-488c-994c-2f04416e5ee3/taxonomies', $url);
    }

    public function testGetLanguages()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $builder = new UrlBuilder($projectId);
        $url = $builder->getLanguagesUrl();
        $this->assertEquals('https://deliver.kontent.ai/975bf280-fd91-488c-994c-2f04416e5ee3/languages', $url);
    }

}
