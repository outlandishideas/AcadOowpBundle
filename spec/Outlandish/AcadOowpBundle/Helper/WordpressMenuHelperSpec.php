<?php

namespace spec\Outlandish\AcadOowpBundle\Helper;

use Outlandish\AcadOowpBundle\Wordpress\Exceptions\WordpressException;
use Outlandish\AcadOowpBundle\Wordpress\WordpressWrapper;
use Outlandish\OowpBundle\Helper\WordpressHelper;
use Outlandish\OowpBundle\Manager\QueryManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WordpressMenuHelperSpec extends ObjectBehavior
{
    public function let(QueryManager $queryManager, WordpressWrapper $wordpress)
    {
        $this->beConstructedWith($queryManager, $wordpress);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Outlandish\AcadOowpBundle\Helper\WordpressMenuHelper');
    }

    public function it_gets_an_array_of_post_objects_for_a_given_menu(QueryManager $queryManager, WordpressWrapper $wordpress)
    {
        $menuObject = (object)['term_id' => 1];
        $menuLocations = ['example_menu' => $menuObject];
        $posts = [(object)['object_id' => 1]];
        $query = (object)['posts' => [1]];

        $wordpress->getNavMenuLocations()->willReturn($menuLocations);
        $wordpress->getTerm($menuObject, 'nav_menu')->willReturn($menuObject);
        $wordpress->isWPError($menuObject)->willReturn(false);
        $wordpress->getNavMenuItems(1)->willReturn($posts);
        $queryManager->query(Argument::type('array'))->willReturn($query);

        $this->get('example_menu')->shouldBeArray();
    }

    public function it_throws_a_wordpress_exception_if_menu_doesnt_exist(WordpressWrapper $wordpress)
    {
        $menuObject = (object)['term_id' => 1];
        $menuLocations = ['example_menu' => $menuObject];

        $wordpress->getNavMenuLocations()->willReturn($menuLocations);
        $this->shouldThrow(new WordpressException("Menu missing from menuLocations: fakeMenu"))->duringGet('fakeMenu');
    }

    public function it_throws_a_wordpress_exception_if_the_menu_doesnt_have_any_posts(WordpressWrapper $wordpress)
    {
        $menuObject = (object)['term_id' => 1];
        $menuLocations = ['example_menu' => $menuObject];
        $posts = [];

        $wordpress->getNavMenuLocations()->willReturn($menuLocations);
        $wordpress->getTerm($menuObject, 'nav_menu')->willReturn($menuObject);
        $wordpress->isWPError($menuObject)->willReturn(false);
        $wordpress->getNavMenuItems(1)->willReturn($posts);

        $this->shouldThrow(new WordpressException("Menu has no pages"))->duringGet('example_menu');
    }

    public function it_throws_a_wordpress_exception_if_there_is_a_wp_error(WordpressWrapper $wordpress)
    {
        $menuObject = (object)['term_id' => 1];
        $menuLocations = ['example_menu' => $menuObject];

        $wordpress->getNavMenuLocations()->willReturn($menuLocations);
        $wordpress->getTerm($menuObject, 'nav_menu')->willReturn($menuObject);
        $wordpress->isWPError($menuObject)->willReturn(true);

        $this->shouldThrow(new WordpressException("Menu Object has errors: example_menu"))->duringGet('example_menu');
    }
}
