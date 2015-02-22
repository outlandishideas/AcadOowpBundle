<?php

namespace spec\Outlandish\AcadOowpBundle\Repository;

use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\OowpBundle\Query\OowpQuery;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostRepositorySpec extends ObjectBehavior
{
    public function let(QueryManager $queryManager)
    {
        $this->beConstructedWith($queryManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Outlandish\AcadOowpBundle\Repository\PostRepository');
    }

    public function it_fetches_all_posts_for_a_given_array_of_ids(QueryManager $queryManager)
    {
        $queryManager->query(['post__in' => [1,2]])->shouldBeCalled();
        $queryManager->query(['post__in' => [1,2]])->willReturn((object) ['posts' => []]);
        $this->fetchMany([1,2]);
    }

    public function it_fetches_a_single_post_for_a_given_id(QueryManager $queryManager)
    {
        $queryManager->query(['p' => 1])->shouldBeCalled();
        $queryManager->query(['p' => 1])->willReturn((object) ['post' => null]);
        $this->fetchOne(1);
    }

    public function it_fetches_all_posts(QueryManager $queryManager)
    {
        $queryManager->query(['post_type' => 'any'])->shouldBeCalled();
        $queryManager->query(['post_type' => 'any'])->willReturn((object) ['posts' => []]);
        $this->fetchAll();
    }
}
