<?php

namespace Outlandish\AcadOowpBundle\Repository;

use Outlandish\OowpBundle\PostType\Post as OowpPost;

/**
 * Interface Repository
 * @package Outlandish\AcadOowpBundle\Repositories
 */
interface Repository
{
    /**
     * @param array $ids
     * @return OowpPost[]
     */
    public function fetchMany(array $ids);

    public function fetchOne($id);

    public function fetchAll();

}