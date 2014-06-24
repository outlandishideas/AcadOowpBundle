<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 17/06/14
 * Time: 08:32
 */

namespace Outlandish\AcadOowpBundle\Tests\FacetedSearch;

use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostType;
use Outlandish\AcadOowpBundle\FacetedSearch\Search;
use Outlandish\SiteBundle\PostType\Person;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchTest extends WebTestCase
{
    public $search;
    public $client;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->search = $this->client->getContainer()->get('outlandish_acadoowp.search');
    }

    protected function tearDown()
    {
        $this->search = null;
        $this->client = null;
    }

    public function test_that_service_returns_correct_object()
    {
        $this->assertInstanceOf('Outlandish\AcadOowpBundle\FacetedSearch\Search', $this->search);
    }

    public function test_that_search_returns_standard_args()
    {
        $expected = array(
            'post_type' => 'any',
            'post_count' => 10,
            'page' => 1,
        );
        $this->assertEquals($expected, $this->search->generateArguments());
    }

    public function test_that_search_with_post_type_facet_returns_correct_args()
    {
        $expected = array(
            'post_type' => Person::postType(),
            'post_count' => 10,
            'page' => 1,
        );
        $facet = new FacetPostType('test_name', 'test_section');
        $facet->addOption(Person::friendlyName(), Person::postType());
        $this->search->addFacet($facet);
        $this->assertEquals($expected, $this->search->generateArguments());
    }
}
 