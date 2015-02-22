<?php

namespace Outlandish\AcadOowpBundle\Repository;

use Outlandish\AcadOowpBundle\Repository\Repository;
use Outlandish\AcadOowpBundle\PostType\PostInterface as Post;
use Outlandish\OowpBundle\Manager\QueryManager;

class PostRepository implements Repository
{
    /**
     * @var QueryManager
     */
    private $queryManager;

    public function __construct(QueryManager $queryManager)
    {
        $this->queryManager = $queryManager;
    }

    /**
     * @return Post[]
     */
    public function fetchAll()
    {
        return $this->queryManager->query(['post_type' => 'any'])->posts;
    }

    /**
     * @param array $ids
     *
     * @return Post[]
     */
    public function fetchMany(array $ids)
    {
        return $this->queryManager->query(['post__in' => $ids])->posts;
    }


    /**
     * @param $id
     *
     * @return Post
     */
    public function fetchOne($id)
    {
        return $this->queryManager->query(['p' => $id])->post;
    }
}
