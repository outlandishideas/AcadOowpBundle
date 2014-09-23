<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 17/06/14
 * Time: 08:32
 */

namespace Outlandish\AcadOowpBundle\Tests\FacetedSearch;

require_once __DIR__ . '/../../../../../web/wp-config.php';
require_once __DIR__ . '/../../../../../web/wp-settings.php';

use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostType;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostToPost;
use Outlandish\AcadOowpBundle\FacetedSearch\Search;
use Outlandish\SiteBundle\PostType\Document;
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

    public function test_search_with_post_to_post_facet_and_nothing_selected()
    {
        $expected = array(
            'post_type' => array(Person::postType(), News::postType()),
            'post_count' => 10,
            'page' => 1
        );
        $facet = new FacetPostType('test_name', 'test_section');
        $facet->addOption(Person::postType(), Person::friendlyName());
        $facet->addOption(News::postType(), News::friendlyName());
        $this->search->addFacet($facet);

        $facet = new FacetPostToPost('p2p', 'p2p_section', 'person');
        $facet->addOption(44, 'Chris Williams');
        $facet->addOption(7, 'Penny Green');
        $this->search->addFacet($facet);

        $this->assertEquals($expected, $this->search->generateArguments());

    }

    public function test_search_with_post_to_post_facet_with_a_post_selected()
    {
        $expected = array(
            'post_type' => array(News::postType()),
            'post_count' => 10,
            'page' => 1,
            'connected_type' => array('news_person'),
            'connected_items' => array(44)
        );

        $this->search->setParams(
            array(
                'test_name' => array(News::postType()),
                'p2p' => 44
            )
        );

        $facet = new FacetPostType('test_name', 'test_section');
        $facet->addOption(Person::postType(), Person::friendlyName());
        $facet->addOption(News::postType(), News::friendlyName());
        $this->search->addFacet($facet);

        $facet = new FacetPostToPost('p2p', 'p2p_section', Person::postType());
        $facet->addOption(44, 'Chris Williams');
        $facet->addOption(7, 'Penny Green');
        $this->search->addFacet($facet);

        $this->assertEquals($expected, $this->search->generateArguments());
    }

    public function test_search_with_post_to_post_facet_with_two_posts_selected()
    {
        $expected = array(
            'post_type' => array(News::postType()),
            'post_count' => 10,
            'page' => 1,
            'connected_type' => array('news_person'),
            'connected_items' => array(44, 7)
        );

        $this->search->setParams(
            array(
                'test_name' => array(News::postType()),
                'p2p' => array(44, 7)
            )
        );

        $facet = new FacetPostType('test_name', 'test_section');
        $facet->addOption(Person::postType(), Person::friendlyName());
        $facet->addOption(News::postType(), News::friendlyName());
        $this->search->addFacet($facet);

        $facet = new FacetPostToPost('p2p', 'p2p_section', Person::postType());
        $facet->addOption(44, 'Chris Williams');
        $facet->addOption(7, 'Penny Green');
        $this->search->addFacet($facet);

        $this->assertEquals($expected, $this->search->generateArguments());
    }

    public function test_search_with_two_post_to_post_facet()
    {
        $expected = array(
            'post_type' => array(News::postType()),
            'post_count' => 10,
            'page' => 1,
            'connected_type' => array('document_news', 'news_person'),
            'connected_items' => array(1, 2, 44, 7)
        );

        $this->search->setParams(
            array(
                'test_name' => array(News::postType()),
                'p2p1' => array(7, 44),
                'p2p2' => array(1, 2)
            )
        );

        $facet = new FacetPostType('test_name', 'test_section');
        $facet->addOption(Person::postType(), Person::friendlyName());
        $facet->addOption(News::postType(), News::friendlyName());
        $this->search->addFacet($facet);

        $facet = new FacetPostToPost('p2p1', 'p2p_section_1', Person::postType());
        $facet->addOption(44, 'Chris Williams');
        $facet->addOption(7, 'Penny Green');
        $this->search->addFacet($facet);

        $facet = new FacetPostToPost('p2p2', 'p2p_section_2', Document::postType());
        $facet->addOption(1, 'Chris Williams');
        $facet->addOption(2, 'Penny Green');
        $this->search->addFacet($facet);

        $this->assertEquals($expected, $this->search->generateArguments());
    }
}
 