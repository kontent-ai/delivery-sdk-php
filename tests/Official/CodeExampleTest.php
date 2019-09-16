<?php

namespace Kentico\Kontent\Tests\Official;

use Kentico\Kontent\Delivery\DeliveryClient;
use Kentico\Kontent\Delivery\QueryParams;
use PHPUnit\Framework\TestCase;

class CodeExamplesTests extends TestCase
{
    protected function setUp()
    {
        $this->client = new DeliveryClient('975bf280-fd91-488c-994c-2f04416e5ee3');
    }

    public function test_OneContentItem()
    { 

        $item = $this->client->getItem('on_roasts', (new QueryParams())
            ->elements(array('title', 'summary', 'post_date','teaser_image', 'related_articles')));

        $this->assertNotNull($item);        
    }

    public function test_ListContentItems()
    {
        $items = $this->client->getItems((new QueryParams())
            ->equals('system.type', 'article')
            ->elements(array('title', 'summary', 'post_date','teaser_image'))
            ->orderDesc('elements.post_date')
            ->limit(3)
            ->depth(0));
        
        $this->assertNotNull($items);
        $this->assertNotNull($items->items);
        $this->assertInternalType('array', $items->items);        
    }

    public function test_OneType()
    {
        $type = $this->client->getType('coffee');
        $this->assertNotNull($type);               
    }

    public function test_ThreeTypes()
    {
        $types = $this->client->getTypes((new QueryParams())
            ->limit(3));

        $this->assertNotNull($types);  
        $this->assertNotNull($types->types);
        $this->assertInternalType('array', $types->types);  
    }

    public function test_contentTypeElement()
    {
        $element = $this->client->getElement('coffee', 'processing');

        $this->assertNotNull($element);  
    }

    public function test_getTaxonomyGroup()
    {
        $taxonomies = $this->client->getTaxonomies((new QueryParams())
            ->limit(3));

        $this->assertNotNull($taxonomies);  
        $this->assertNotNull($taxonomies->taxonomies);
        $this->assertInternalType('array', $taxonomies->taxonomies);  
    }

    public function test_getFiveLatestArticles()
    {
        $items = $this->client->getItems((new QueryParams())
            ->equals('system.type', 'article')
            ->orderDesc('elements.post_date')
            ->limit(5)
            ->elements(array('title', 'teaser_image')));
        
        $this->assertNotNull($items);
        $this->assertNotNull($items->items);
        $this->assertInternalType('array', $items->items); 
    }

    public function test_getArticlesWithTaxonomyTerm()
    {
        $items = $this->client->getItems((new QueryParams())
            ->contains('persona', 'visitor'));
        
        $this->assertNotNull($items);
        $this->assertNotNull($items->items);
        $this->assertInternalType('array', $items->items); 
    }

    public function test_getContentInSpecificLanguageWithFallback()
    {
        $item = $this->client->getItem('on_roasts', (new QueryParams())
            ->language('es-ES'));

            $this->assertNotNull($item);  
    }

    public function test_getContentInSpecificLanguageNoFallback()
    {
        $item = $this->client->getItem('on_roasts', (new QueryParams())
            ->equals('system.language', 'en-US')
            ->language('en-US'));

            $this->assertNotNull($item);  
    }
}