<?php

namespace KenticoCloud\Tests\E2E;

use KenticoCloud\Delivery\DeliveryClient;
use KenticoCloud\Delivery\QueryParams;

use PHPUnit\Framework\TestCase;

class DeliveryClientTest extends TestCase
{
    public function getProjectId()
    {
        return '975bf280-fd91-488c-994c-2f04416e5ee3';
    }
    
    public function testGetArticleItem()
    {
        $client = new DeliveryClient($this->getProjectId());
        $item = $client->getItem('on_roasts');
        $this->assertEquals('f4b3fc05-e988-4dae-9ac1-a94aba566474', $item->system->id);
        $this->assertEquals('On Roasts', $item->elements['title']);
        $this->assertEquals('on-roasts', $item->elements['url_pattern']);
    }

    public function testGetHomeItem()
    {
        $client = new DeliveryClient($this->getProjectId());
        $item = $client->getItem('home');
        $this->assertEquals('1bd6ba00-4bf2-4a2b-8334-917faa686f66', $item->system->id);
        $this->assertInternalType('string', $item->system->lastModified);
        $this->assertInstanceOf(\DateTime::class, $item->system->getLastModifiedDateTime());
        $this->assertEquals('2017-04-04', $item->system->getLastModifiedDateTime('Y-m-d'));
    }

    public function testWebhooks()
    {
        $client = new DeliveryClient($this->getProjectId(), null, true);
        $item = $client->getItem('home');
        $this->assertArrayHasKey('X-KC-Wait-For-Loading-New-Content', $client->lastRequest->headers);
    }

    public function testGetContentTypesLimit_TwoTypes()
    {
        $params = (new QueryParams())->limit(2);
        $client = new DeliveryClient($this->getProjectId());
        $types = $client->getTypes($params);

        $this->assertEquals(2, $types->pagination->count);
        $this->assertCount(2, $types->types);
    }

    public function testGetContentType_TypeNotExist()
    {
        $client = new DeliveryClient($this->getProjectId());
        $type = $client->getType('non-existent-codename');

        $this->assertNull($type);
    }

    public function testGetContentTypes_FirstRecord()
    {
        $params = (new QueryParams())->limit(1);
        $client = new DeliveryClient($this->getProjectId());
        $types = $client->getTypes($params);

        $this->assertEquals("b2c14f2c-6467-460b-a70b-bca17972a33a", $types->types[0]->system->id);
    }
    
    public function testGetContentType()
    {
        $client = new DeliveryClient($this->getProjectId());
        $type = $client->getType('article');
    
        $this->assertEquals("b7aa4a53-d9b1-48cf-b7a6-ed0b182c4b89", $type->system->id);
    }

    public function testGetContentTypesCount()
    {
        $client = new DeliveryClient($this->getProjectId());
        $types = $client->getTypes(null);

        $this->assertGreaterThan(1, $types->pagination->count);
        $this->assertGreaterThan(1, count($types->types));
    }

    public function testGetTaxonomy_CodenameNotExist_IsNull()
    {
        $codename = "XX_manufacturer_XX";
        $client = new DeliveryClient($this->getProjectId());
        $taxonomy = $client->getTaxonomy($codename);

        $this->assertNull($taxonomy, "Taxonomy with codename that does not exist is expected to be null.");
    }

    public function testGetTaxonomy_CodenameExist_IsTaxonomyObject()
    {
        $codename = "manufacturer";
        $client = new DeliveryClient($this->getProjectId());
        $taxonomy = $client->getTaxonomy($codename);

        $this->assertTrue(is_a($taxonomy, \KenticoCloud\Delivery\Models\Taxonomies\Taxonomy::class));
    }

    public function testGetTaxonomy_CodenameManufacturer_HasFourTerms()
    {
        $codename = "manufacturer";
        $client = new DeliveryClient($this->getProjectId());
        $taxonomy = $client->getTaxonomy($codename);

        $actualTerms = count($taxonomy->terms);

        $this->assertEquals(4, $actualTerms, "Four 'manufacturer' terms are expected.");
    }

    public function testGetItemPreviewApiEmpty()
    {
        $client = new DeliveryClient($this->getProjectId());
        $item = $client->getItem('amsterdam');
        $this->assertNull($item);
    }

    public function testGetPreviewApiPresent()
    {
        $client = new DeliveryClient($this->getProjectId(), 'ew0KICAiYWxnIjogIkhTMjU2IiwNCiAgInR5cCI6ICJKV1QiDQp9.ew0KICAidWlkIjogInVzcl8wdk4xUTA1bks2YmlyQVQ2TU5wdkkwIiwNCiAgImVtYWlsIjogInBldHIuc3ZpaGxpa0BrZW50aWNvLmNvbSIsDQogICJwcm9qZWN0X2lkIjogIjk3NWJmMjgwLWZkOTEtNDg4Yy05OTRjLTJmMDQ0MTZlNWVlMyIsDQogICJqdGkiOiAibzhUdkc0OHFqX0ZUSWplVCIsDQogICJ2ZXIiOiAiMS4wLjAiLA0KICAiZ2l2ZW5fbmFtZSI6ICJQZXRyIiwNCiAgImZhbWlseV9uYW1lIjogIlN2aWhsaWsiLA0KICAiYXVkIjogInByZXZpZXcuZGVsaXZlci5rZW50aWNvY2xvdWQuY29tIg0KfQ.wd7_nOYInsdsoh9-0R43FnDQuVk_azPaYze7Ghxv43I');
        $item = $client->getItem('amsterdam');
        $this->assertEquals('e844a6aa-4dc4-464f-8ae9-f9f66cc6ab61', $item->system->id);
    }
    
    public function testGetContentItems()
    {
        $params = (new QueryParams())->type('article')->depth(2);
        $client = new DeliveryClient($this->getProjectId());
        $items = $client->getItems($params);

        $this->assertGreaterThan(1, $items->pagination->count);
        $this->assertGreaterThan(1, count($items->items));
    }

    public function testZeroDepth()
    {
        $params = (new QueryParams())->type('article')->depth(0);
        $client = new DeliveryClient($this->getProjectId());
        $items = $client->getItems($params);
        foreach ($items->items as $item) {
            $relatedArticles = $item->elements['related_articles'];
            foreach ($relatedArticles as $article) {
                $this->assertNull($article);
            }
        }
    }
    
    public function testNonZeroDepth()
    {
        $params = (new QueryParams())->type('article')->depth(99);
        $client = new DeliveryClient($this->getProjectId());
        $items = $client->getItems($params);
        foreach ($items->items as $item) {
            $relatedArticles = $item->elements['related_articles'];
            foreach ($relatedArticles as $article) {
                // All related articles should be resolved
                $this->assertNotNull($article);
            }
        }
    }

    public function testModularContentResolution()
    {
        $params = (new QueryParams())->codename('home');
        $client = new DeliveryClient($this->getProjectId());
        $items = $client->getItems($params);
        $heroUnit = $items->items['home']->elements['hero_unit']['home_page_hero_unit'];
        $this->assertEquals('home_page_hero_unit', $heroUnit->system->codename);
        $this->assertEquals('Roasting premium coffee', $heroUnit->elements['title']);
    }

    /*public function testAssets()
    {
        $client = new DeliveryClient($this->getProjectId());
        $item = $client->getItem('home_page_hero_unit');
    }*/
 
    public function testQueryParams()
    {
        $params = (new QueryParams())->type('article', 'home')->depth(0)->language('es-ES')->orderDesc('system.name')->limit(2);
        $client = new DeliveryClient($this->getProjectId());
        $items = $client->getItems($params);
        $this->assertGreaterThan(1, count($items->items));
    }
}
