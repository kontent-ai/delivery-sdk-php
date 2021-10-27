<?php

namespace Kentico\Kontent\Tests\E2E;

use Kentico\Kontent\Delivery\DeliveryClient;
use Kentico\Kontent\Delivery\QueryParams;
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
        $this->assertEquals('On Roasts', $item->title);
        $this->assertEquals('on-roasts', $item->urlPattern);
        $this->assertInstanceOf(\Kentico\Kontent\Delivery\Models\Items\ContentItemSystem::class, $item->system);
    }

    public function testGetArticleWithLinksItem()
    {
        $client = new DeliveryClient($this->getProjectId());
        $item = $client->getItem('coffee_processing_techniques');
        $this->assertEquals('117cdfae-52cf-4885-b271-66aef6825612', $item->system->id);
        $this->assertEquals('Coffee processing techniques', $item->title);
        $this->assertEquals('coffee-processing-techniques', $item->urlPattern);
        $this->assertInstanceOf(\Kentico\Kontent\Delivery\Models\Items\ContentItemSystem::class, $item->system);
        $this->assertInstanceOf(\Kentico\Kontent\Delivery\Models\Items\ContentItemSystem::class, $item->system);
        $this->assertContains('<a data-item-id="80c7074b-3da1-4e1d-882b-c5716ebb4d25" href="/kenya-gakuyuni-aa">Kenya Gakuyuni AA</a>', $item->bodyCopy);
        $this->assertContains('<a data-item-id="0c9a11bb-6fc3-409c-b3cb-f0b797e15489" href="/brazil-natural-barra-grande">Brazil Natural Barra Grande</a>', $item->bodyCopy);
    }

    public function testGetHomeItem()
    {
        $client = new DeliveryClient($this->getProjectId());
        $item = $client->getItem('home');
        $this->assertEquals('1bd6ba00-4bf2-4a2b-8334-917faa686f66', $item->system->id);
        $this->assertInstanceOf(\DateTime::class, $item->system->getLastModifiedDateTime());
        $this->assertInstanceOf(\DateTime::class, $item->system->lastModified);
        $this->assertRegExp('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $item->system->getLastModifiedDateTime('Y-m-d'));
    }

    public function testGetNonExistentItem()
    {
        $client = new DeliveryClient($this->getProjectId());
        $item = $client->getItem('non-existent');
        $this->assertNull($item);
    }

    public function testGetNonExistentItems()
    {
        $client = new DeliveryClient($this->getProjectId());
        $response = $client->getItems((new QueryParams())->equals('system.codename', 'non-existent'));
        $this->assertEmpty($response->items);
    }

    public function testGetAllItems()
    {
        $client = new DeliveryClient($this->getProjectId());
        $response = $client->getItems();
        $this->assertNotEmpty($response->items);
    }

    public function testNumber()
    {
        $client = new DeliveryClient($this->getProjectId());
        $item = $client->getItem('brazil_natural_barra_grande');
        $this->assertInternalType('float', $item->price);
        $this->assertEquals(8.5, $item->price);
    }

    public function testMultipleChoice()
    {
        $client = new DeliveryClient($this->getProjectId());
        $item = $client->getItem('the_coffee_story');
        $this->assertInstanceOf(\Kentico\Kontent\Delivery\Models\Items\MultipleChoiceOption::class, $item->videoHost[0]);
        $this->assertEquals('Vimeo', $item->videoHost[0]->name);
    }

    public function testAssets()
    {
        $client = new DeliveryClient($this->getProjectId());
        $item = $client->getItem('home_page_hero_unit');
        $this->assertInstanceOf(\Kentico\Kontent\Delivery\Models\Items\Asset::class, $item->image[0]);
        $this->assertEquals('banner-default.jpg', $item->image[0]->name);
    }

    public function testWebhooks()
    {
        $client = new DeliveryClient($this->getProjectId(), null, null, true);
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

        $this->assertEquals('b2c14f2c-6467-460b-a70b-bca17972a33a', $types->types[0]->system->id);
    }

    public function testGetContentType()
    {
        $client = new DeliveryClient($this->getProjectId());
        $type = $client->getType('article');

        $this->assertEquals('b7aa4a53-d9b1-48cf-b7a6-ed0b182c4b89', $type->system->id);
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
        $codename = 'XX_manufacturer_XX';
        $client = new DeliveryClient($this->getProjectId());
        $taxonomy = $client->getTaxonomy($codename);

        $this->assertNull($taxonomy, 'Taxonomy with codename that does not exist is expected to be null.');
    }

    public function testGetTaxonomy_CodenameExist_IsTaxonomyObject()
    {
        $codename = 'manufacturer';
        $client = new DeliveryClient($this->getProjectId());
        $taxonomy = $client->getTaxonomy($codename);

        $this->assertInstanceOf(\Kentico\Kontent\Delivery\Models\Taxonomies\Taxonomy::class, $taxonomy);
    }

    public function testGetTaxonomy_CodenameManufacturer_HasFourTerms()
    {
        $codename = 'manufacturer';
        $client = new DeliveryClient($this->getProjectId());
        $taxonomy = $client->getTaxonomy($codename);

        $actualTerms = count($taxonomy->terms);

        $this->assertEquals(4, $actualTerms, "Four 'manufacturer' terms are expected.");
    }

    public function testGetLanguages_returnAllLanguages()
    {
        $client = new DeliveryClient($this->getProjectId());
        $response = $client->getLanguages();
        
        $this->assertEquals(2, count($response->languages));
        $firstLanguage = $response->languages[0];
        $secondLanguage = $response->languages[1];
        $this->assertEquals("00000000-0000-0000-0000-000000000000", $firstLanguage->system->id);
        $this->assertEquals("English (United States)", $firstLanguage->system->name);
        $this->assertEquals("en-US", $firstLanguage->system->codename);

        $this->assertEquals("d1f95fde-af02-b3b5-bd9e-f232311ccab8", $secondLanguage->system->id);
        $this->assertEquals("Spanish (Spain)", $secondLanguage->system->name);
        $this->assertEquals("es-ES", $secondLanguage->system->codename);
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
        $params = (new QueryParams())->type('article')->depth(2)->elements(array('personas'));
        $client = new DeliveryClient($this->getProjectId());
        $items = $client->getItems($params);

        $this->assertGreaterThan(1, $items->pagination->count);
        $this->assertGreaterThan(1, count($items->items));
        $this->assertFalse(property_exists($items->items['on_roasts'], 'title'));
        $this->assertTrue($items->items['on_roasts']->personas !== null);
    }

    public function testZeroDepth()
    {
        $params = (new QueryParams())->type('article')->depth(0);
        $client = new DeliveryClient($this->getProjectId());
        $items = $client->getItems($params);
        foreach ($items->items as $item) {
            $relatedArticles = $item->relatedArticles;
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
            $relatedArticles = $item->relatedArticles;
            foreach ($relatedArticles as $article) {
                // All related articles should be resolved
                $this->assertNotNull($article);
            }
        }
    }

    public function testLinkedItemsResolution()
    {
        $params = (new QueryParams())->codename('home');
        $client = new DeliveryClient($this->getProjectId());
        $items = $client->getItems($params);
        $heroUnit = $items->items['home']->heroUnit['home_page_hero_unit'];
        $this->assertEquals('home_page_hero_unit', $heroUnit->system->codename);
        $this->assertEquals('Roasting premium coffee', $heroUnit->title);
    }

    public function testQueryParams()
    {
        $params = (new QueryParams())->type('article', 'home')->depth(0)->language('es-ES')->orderDesc('system.name')->limit(2);
        $client = new DeliveryClient($this->getProjectId());
        $response = $client->getItems($params);
        $this->assertGreaterThan(1, count($response->items));
    }

    public function testIncludeTotalCount()
    {
        $limit = 2;
        $params = (new QueryParams())->type('article', 'home')->depth(0)->limit($limit)->includeTotalCount();
        $client = new DeliveryClient($this->getProjectId());
        $response = $client->getItems($params);
        $this->assertEquals(6, $response->pagination->totalCount);
        $this->assertEquals($limit, $response->pagination->limit);
    }
}
