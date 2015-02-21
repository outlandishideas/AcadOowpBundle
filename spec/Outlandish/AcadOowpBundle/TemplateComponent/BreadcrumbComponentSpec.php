<?php

namespace spec\Outlandish\AcadOowpBundle\TemplateComponent;

use Outlandish\AcadOowpBundle\Breadcrumb\Breadcrumb;
use Outlandish\AcadOowpBundle\Breadcrumb\BreadcrumbTrail;
use Outlandish\AcadOowpBundle\Repository\Repository;
use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\AcadOowpBundle\PostType\PostInterface as Post;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbComponentSpec extends ObjectBehavior
{
    public function let(BreadcrumbTrail $helper,
                        RequestStack $requestStack,
                        Request $request,
                        Repository $repository,
                        Post $post)
    {
        $this->beConstructedWith($requestStack, $helper, $repository);

        $requestStack->getCurrentRequest()->willReturn($request);
        $repository->fetchOne(Argument::any())->willReturn($post);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Outlandish\AcadOowpBundle\TemplateComponent\BreadcrumbComponent');
    }

    public function it_gets_the_post_from_the_request(Request $request, Repository $repository, Post $post)
    {
        $request->get('post_id')->shouldBeCalled();
        $request->get('post_id')->willReturn(1);
        $repository->fetchOne(1)->shouldBeCalled();
        $repository->fetchOne(1)->willReturn($post);
        $this->getArguments();
    }

    public function it_returns_the_array_of_posts_with_the_key_breadcrumbs(BreadcrumbTrail $helper, Breadcrumb $breadcrumb, Post $post)
    {
        $helper->make($post)->willReturn([$breadcrumb]);
        $helper->make($post)->shouldBeCalled();
        $this->getArguments()->shouldReturn(['breadcrumbs' => [$breadcrumb]]);
    }

}
