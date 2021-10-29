<?php

namespace Kentico\Kontent\Tests\E2E;

use Kentico\Kontent\Delivery\DeliveryClient;
use Kentico\Kontent\Tests\E2E\Utils\CustomContentLinkUrlResolver;
use Kentico\Kontent\Tests\E2E\Utils\TestMapper;
use PHPUnit\Framework\TestCase;

class ModelBindingTest extends TestCase
{
    public function getClient()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $client = new DeliveryClient($projectId);
        $client->typeMapper = new TestMapper();
        $client->contentLinkUrlResolver = new CustomContentLinkUrlResolver();        

        return $client;
    }

    public function testArticleModel()
    {
        // Arrange
        $client = $this->getClient();

        // Act
        $item = $client->getItem('on_roasts');

        // Assert
        $this->assertEquals('On Roasts', $item->title);
        $this->assertEquals('f4b3fc05-e988-4dae-9ac1-a94aba566474', $item->system->id);
        $this->assertEquals('article', $item->system->type);
        $this->assertEquals('default', $item->system->collection);
        $this->assertEquals('published', $item->system->workflowStep);
        $this->assertEquals(new \DateTime('2014-11-07T00:00:00Z'), $item->postDate);
        $this->assertCount(2, $item->personas);
        $this->assertInstanceOf(\Kentico\Kontent\Delivery\Models\Items\TaxonomyTerm::class, $item->personas[0]);
        $this->assertCount(2, $item->relatedArticles);
        $this->assertEquals('Coffee processing techniques', $item->relatedArticles['coffee_processing_techniques']->title);
        $this->assertInstanceOf(\Kentico\Kontent\Tests\E2E\ArticleModel::class, $item);
        $this->assertInstanceOf(\Kentico\Kontent\Tests\E2E\ArticleModel::class, $item->relatedArticles['coffee_processing_techniques']);
        $this->assertInstanceOf(\Kentico\Kontent\Delivery\Models\Items\ContentItemSystem::class, $item->system);
    }

    public function testGetArticleWithLinks_LinksUrlResolved()
    {
        $client = $this->getClient();

        $item = $client->getItem('coffee_processing_techniques');

        $this->assertEquals('117cdfae-52cf-4885-b271-66aef6825612', $item->system->id);
        $this->assertEquals('Coffee processing techniques', $item->title);
        $this->assertEquals('coffee-processing-techniques', $item->urlPattern);
        $this->assertContains('<a data-item-id="80c7074b-3da1-4e1d-882b-c5716ebb4d25" href="/custom/kenya-gakuyuni-aa">Kenya Gakuyuni AA</a>', $item->bodyCopy);
        $this->assertContains('<a data-item-id="0c9a11bb-6fc3-409c-b3cb-f0b797e15489" href="/custom/brazil-natural-barra-grande">Brazil Natural Barra Grande</a>', $item->bodyCopy);        
    }

    public function testHomeModel()
    {
        // Arrange
        $client = $this->getClient();

        // Act
        $item = $client->getItem('home');

        // Assert
        $this->assertEquals('1bd6ba00-4bf2-4a2b-8334-917faa686f66', $item->system->id);
        $this->assertEquals('home', $item->system->type);
        $this->assertEquals('default', $item->system->collection);
    }
}
