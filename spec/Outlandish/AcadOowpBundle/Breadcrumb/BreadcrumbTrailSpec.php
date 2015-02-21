<?php

namespace spec\Outlandish\AcadOowpBundle\Breadcrumb;

use Outlandish\AcadOowpBundle\Wordpress\WordpressWrapper;
use Outlandish\AcadOowpBundle\Breadcrumb\BreadcrumbFactory;
use Outlandish\AcadOowpBundle\Breadcrumb\Breadcrumb;
use Outlandish\AcadOowpBundle\PostType\PostInterface as Post;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BreadcrumbTrailSpec extends ObjectBehavior
{
    public function let(WordpressWrapper $wordpress, BreadcrumbFactory $breadcrumb)
    {
        $this->beConstructedWith($wordpress, $breadcrumb);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Outlandish\AcadOowpBundle\Breadcrumb\BreadcrumbTrail');
    }

    public function it_returns_an_array_of_breadcrumbs_with_home_first_if_on_home_page(WordpressWrapper $wordpress, Breadcrumb $home, BreadcrumbFactory $breadcrumb, Post $post)
    {
        $breadcrumb->makeHome()->shouldBeCalled();
        $wordpress->is404()->willReturn(false);
        $wordpress->isSearch()->willReturn(false);
        $wordpress->isSingle()->willReturn(false);
        $wordpress->isHome()->willReturn(true);

        $this->make($post);
    }

    public function it_returns_a_404_breadcrumb_trail_if_on_a_404_page(WordpressWrapper $wordpress, BreadcrumbFactory $breadcrumb, Post $post)
    {
        $wordpress->is404()->willReturn(true);
        $breadcrumb->makeHome()->shouldBeCalled();
        $breadcrumb->make404()->shouldBeCalled();
        $this->make($post);
    }

    public function it_returns_a_search_breadcrumb_trail_if_on_a_search_page(WordpressWrapper $wordpress, BreadcrumbFactory $breadcrumb, Post $post)
    {
        $wordpress->is404()->willReturn(false);
        $wordpress->isSearch()->willReturn(true);
        $breadcrumb->makeHome()->shouldBeCalled();
        $breadcrumb->makeSearch()->shouldBeCalled();
        $this->make($post);
    }

    public function it_makes_breadcrumbs_from_the_posts_parents_if_on_a_single_post(WordpressWrapper $wordpress, BreadcrumbFactory $breadcrumb, Post $post)
    {
        $wordpress->is404()->willReturn(false);
        $wordpress->isSearch()->willReturn(false);
        $wordpress->isSingle()->willReturn(true);
        $wordpress->isHome()->willReturn(false);

        $post->postType()->willReturn('not_a_page');

        $breadcrumb->makeHome()->shouldBeCalled();
        $breadcrumb->makeFromParent($post)->shouldBeCalled();
        $breadcrumb->makeFromParent($post)->willReturn(array());
        $breadcrumb->makeFromPost($post, false)->shouldBeCalled();
        $breadcrumb->makeFromPost($post, false)->willReturn(array());

        $this->make($post);
    }

    public function it_makes_breadcrumbs_for_the_posts_ancestors_if_its_a_page(WordpressWrapper $wordpress, BreadcrumbFactory $breadcrumb, Post $post)
    {
        $wordpress->is404()->willReturn(false);
        $wordpress->isSearch()->willReturn(false);
        $wordpress->isSingle()->willReturn(false);
        $wordpress->isHome()->willReturn(false);

        $post->postType()->willReturn('page');

        $breadcrumb->makeHome()->shouldBeCalled();
        $breadcrumb->makeFromAncestors($post)->shouldBeCalled();
        $breadcrumb->makeFromAncestors($post)->willReturn([]);
        $breadcrumb->makeFromPost($post, false)->shouldBeCalled();
        $breadcrumb->makeFromPost($post, false)->willReturn([]);

        $this->make($post);
    }

}
