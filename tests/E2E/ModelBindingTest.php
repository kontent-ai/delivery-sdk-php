<?php

namespace KenticoCloud\Tests\E2E;

use KenticoCloud\Delivery\DeliveryClient;
use KenticoCloud\Delivery\DefaultMapper;
use PHPUnit\Framework\TestCase;

class TetsMapper extends DefaultMapper
{
    public function getTypeClass($typeName, $elementName = null, $parentModelType = null)
    {
        switch ($typeName) {
            case 'home':
                return \KenticoCloud\Tests\E2E\HomeModel::class;
            case 'article':
                return \KenticoCloud\Tests\E2E\ArticleModel::class;
        }

        return parent::getTypeClass($typeName, $elementName, $parentModelType);
    }
}

class ModelBindingTest extends TestCase
{
    public function getClient()
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        $client = new DeliveryClient($projectId);
        $client->typeMapper = new TetsMapper();

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
        $this->assertEquals(new \DateTime('2014-11-07T00:00:00Z'), $item->postDate);
        $this->assertCount(2, $item->personas);
        $this->assertInstanceOf(\KenticoCloud\Delivery\Models\Items\TaxonomyTerm::class, $item->personas[0]);
        $this->assertCount(2, $item->relatedArticles);
        $this->assertEquals('Coffee processing techniques', $item->relatedArticles['coffee_processing_techniques']->title);
        $this->assertInstanceOf(\KenticoCloud\Tests\E2E\ArticleModel::class, $item->relatedArticles['coffee_processing_techniques']);
        $this->assertInstanceOf(\KenticoCloud\Delivery\Models\Items\ContentItemSystem::class, $item->system);
    }

    public function testHomeModel()
    {
        // Arrange
        $client = $this->getClient();

        // Act
        $item = $client->getItem('home');

        // Assert
        $this->assertEquals('1bd6ba00-4bf2-4a2b-8334-917faa686f66', $item->system->id);
    }
}
