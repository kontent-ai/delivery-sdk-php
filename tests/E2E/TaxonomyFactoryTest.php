<?php
namespace KenticoCloud\Tests\Unit;

use KenticoCloud\Delivery;
use PHPUnit\Framework\TestCase;
use KenticoCloud\Delivery\DeliveryClient;
use KenticoCloud\Delivery\Models;
use KenticoCloud\Delivery\QueryParams;

class TaxnomyFactoryTest extends TestCase
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
        $factory = new \KenticoCloud\Delivery\TaxonomyFactory();
        $taxonomies = $factory->createTaxonomies($response);

        $this->assertEquals(array(), $taxonomies);
    }

    public function testCreateTaxonomies_SingleItemStructure_Matches()
    {
        $client = $this->getClient();
        $params = (new QueryParams())->limit(1);
        $actual = $client->getTaxonomies($params);

        $taxonomySystem = new Models\TaxonomySystem(
            "4ce421e9-c403-eee8-fdc2-74f09392a749",
            "Manufacturer",
            "manufacturer",
            "2017-09-07T08:15:22.7215324Z"
        );

        $termsArray = array(
            new Models\Taxonomies\Term(
                "Aerobie", "aerobie", array()
            ),
            new Models\Taxonomies\Term(
                "Chemex", "chemex", array()
            ),
            new Models\Taxonomies\Term(
                "Espro", "espro", array()
            ),
            new Models\Taxonomies\Term(
                "Hario", "hario", array()
            )
        );

        $dummyTaxonomy = new Models\Taxonomy();
        $dummyTaxonomy->system = $taxonomySystem;
        $dummyTaxonomy->terms = $termsArray;
        $expected = array($dummyTaxonomy);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateTaxonomies_FullTaxonomyResponse_NoRecordIsMissing()
    {
        $expectedCount = 4;
        $client = $this->getClient();
        $params = new QueryParams();
        $taxonomies = $client->getTaxonomies($params);

        $this->assertEquals($expectedCount, count($taxonomies));
    }

    public function testCreateTaxonomies_SingleTaxonomyResponse_NoTermsRecordIsMissing()
    {
        $client = $this->getClient();
        $params = (new QueryParams())->limit(1);
        $taxonomy = $client->getTaxonomies($params);

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

        $this->assertTrue(is_a($taxonomy, \KenticoCloud\Delivery\Models\Taxonomy::class));
    }
}