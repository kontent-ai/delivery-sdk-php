<?php
namespace Kentico\Kontent\Tests\E2E;

use PHPUnit\Framework\TestCase;
use Kentico\Kontent\Delivery\DeliveryClient;
use Kentico\Kontent\Delivery\Models\Taxonomies;
use Kentico\Kontent\Delivery\QueryParams;

class TaxonomyFactoryTest extends TestCase
{
    public function getClient($previewApiKey = null)
    {
        $projectId = '975bf280-fd91-488c-994c-2f04416e5ee3';
        if (is_null($previewApiKey)) {
            return new DeliveryClient($projectId);
        } else {
            return new DeliveryClient($projectId, $previewApiKey);
        }
    }

    public function testCreateTaxonomies_NullResponse_IsEmptyArray()
    {
        $response = null;
        $factory = new \Kentico\Kontent\Delivery\TaxonomyFactory();
        $taxonomies = $factory->createTaxonomies($response);

        $this->assertEquals(array(), $taxonomies);
    }

    public function testCreateTaxonomies_SingleItemStructure_Matches()
    {
        $client = $this->getClient();
        $params = (new QueryParams())->limit(1);
        $actual = $client->getTaxonomies($params)->taxonomies;

        $taxonomySystem = new Taxonomies\TaxonomySystem(
            "4ce421e9-c403-eee8-fdc2-74f09392a749",
            "Manufacturer",
            "manufacturer",
            "2017-09-07T08:15:22.7210000Z"
        );

        $term1 = new Taxonomies\Term();
        $term1->name = "Aerobie";
        $term1->codename = "aerobie";

        $term2 = new Taxonomies\Term();
        $term2->name = "Chemex";
        $term2->codename = "chemex";

        $term3 = new Taxonomies\Term();
        $term3->name = "Espro";
        $term3->codename = "espro";

        $term4 = new Taxonomies\Term();
        $term4->name = "Hario";
        $term4->codename = "hario";

        $termsArray = array($term1, $term2, $term3, $term4);

        $dummyTaxonomy = new Taxonomies\Taxonomy();
        $dummyTaxonomy->system = $taxonomySystem;
        $dummyTaxonomy->terms = $termsArray;
        $expected = array($dummyTaxonomy);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateTaxonomies_FullTaxonomyResponse_NoRecordIsMissing()
    {
        $expectedCount = 5;
        $client = $this->getClient();
        $params = new QueryParams();
        $taxonomies = $client->getTaxonomies($params);

        $this->assertEquals($expectedCount, $taxonomies->pagination->count);
        $this->assertCount($expectedCount, $taxonomies->taxonomies);
    }

    public function testCreateTaxonomies_SingleTaxonomyResponse_NoTermsRecordIsMissing()
    {
        $client = $this->getClient();
        $params = (new QueryParams())->limit(1);
        $taxonomy = $client->getTaxonomies($params)->taxonomies;

        $expectedCount = 4;
        $actualCount = count($taxonomy[0]->terms);

        $this->assertEquals($expectedCount, $actualCount);
    }

    public function testCreateTaxonomy_TaxonomyExist_TaxonomyObjectReturned()
    {
        // Think about rewriting this test not to use client.
        $client = $this->getClient();
        $codename = "manufacturer";
        $taxonomy = $client->getTaxonomy($codename);

        $this->assertTrue(is_a($taxonomy, \Kentico\Kontent\Delivery\Models\Taxonomies\Taxonomy::class));
    }

    public function testCreateTaxonomy_SingleTaxonomyResponse_NestedTaxonomyStructureIsCreated()
    {
        $client = $this->getClient();
        $codename = "manufacturer";
        $taxonomy = $client->getTaxonomy($codename);

        // Assert that Manufacturer is composed of Aerobie,
        // Chemex, Espro and Hario terms.
        $expectedTaxonomy = new Taxonomies\Taxonomy();
        $expectedTaxonomy->system = new Taxonomies\TaxonomySystem(
            "4ce421e9-c403-eee8-fdc2-74f09392a749",
            "Manufacturer",
            "manufacturer",
            "2017-09-07T08:15:22.7210000Z"
        );

        $term1 = new Taxonomies\Term();
        $term1->name = "Aerobie";
        $term1->codename = "aerobie";

        $term2 = new Taxonomies\Term();
        $term2->name = "Chemex";
        $term2->codename = "chemex";

        $term3 = new Taxonomies\Term();
        $term3->name = "Espro";
        $term3->codename = "espro";

        $term4 = new Taxonomies\Term();
        $term4->name = "Hario";
        $term4->codename = "hario";

        $expectedTaxonomy->terms = array($term1, $term2, $term3, $term4);
        $this->assertEquals($expectedTaxonomy, $taxonomy, "Retrieved nested taxonomy is not the same as the expected one.");
    }
}