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
use Outlandish\SiteBundle\PostType\News;
use Outlandish\SiteBundle\PostType\Person;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class SearchTest extends WebTestCase
{
    /**
     * @var Search
     */
    public $search;
    /**
     * @var Client
     */
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

    public function test_search_with_post_type_facet_with_one_option_and_none_selected()
    {
        $expected = array(
            'post_type' => array(Person::postType()),
            'post_count' => 10,
            'page' => 1,
        );
        $facet = new FacetPostType('test_name', 'test_section');
        $facet->addOption(Person::postType(), Person::friendlyName());
        $this->search->addFacet($facet);
        $this->assertEquals($expected, $this->search->generateArguments());
    }

    public function test_search_with_post_type_facet_with_two_options_and_none_selected()
    {
        $expected = array(
            'post_type' => array(Person::postType(), News::postType()),
            'post_count' => 10,
            'page' => 1,
        );
        $facet = new FacetPostType('test_name', 'test_section');
        $facet->addOption(Person::postType(), Person::friendlyName());
        $facet->addOption(News::postType(), News::friendlyName());
        $this->search->addFacet($facet);
        $this->assertEquals($expected, $this->search->generateArguments());
    }

    public function test_search_with_post_type_facet_with_two_options_and_one_selected()
    {
        $expected = array(
            'post_type' => array(Person::postType()),
            'post_count' => 10,
            'page' => 1,
        );
        $this->search->setParams(array('test_name' => array(Person::postType())));
        $facet = new FacetPostType('test_name', 'test_section');
        $facet->addOption(Person::postType(), Person::friendlyName());
        $facet->addOption(News::postType(), News::friendlyName());
        $this->search->addFacet($facet);
        $this->assertEquals($expected, $this->search->generateArguments());
    }

    public function test_search_with_wrong_param_values()
    {
        $expected = array(
            'post_type' => array(Person::postType(), News::postType()),
            'post_count' => 10,
            'page' => 1,
        );
        $this->search->setParams(array('test_name' => array('test')));
        $facet = new FacetPostType('test_name', 'test_section');
        $facet->addOption(Person::postType(), Person::friendlyName());
        $facet->addOption(News::postType(), News::friendlyName());
        $this->search->addFacet($facet);
        $this->assertEquals($expected, $this->search->generateArguments());
    }

    public function test_search_with_wrong_param_name()
    {
        $expected = array(
            'post_type' => array(Person::postType(), News::postType()),
            'post_count' => 10,
            'page' => 1,
        );
        $this->search->setParams(array('wrong_test_name' => array(Person::postType())));
        $facet = new FacetPostType('test_name', 'test_section');
        $facet->addOption(Person::postType(), Person::friendlyName());
        $facet->addOption(News::postType(), News::friendlyName());
        $this->search->addFacet($facet);
        $this->assertEquals($expected, $this->search->generateArguments());
    }

    public function test_search_with_post_to_post_facet()
    {
        $expected = array(
            'post_type' => array(Person::postType(), News::postType()),
            'post_count' => 10,
            'page' => 1,
            ''
        );
    }
}
 