<?php

namespace Outlandish\AcadOowpBundle\Breadcrumb;

use Outlandish\AcadOowpBundle\Repository\Repository;
use Outlandish\AcadOowpBundle\Wordpress\WordpressWrapper;
use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\AcadOowpBundle\PostType\PostInterface as Post;
use Outlandish\AcadOowpBundle\Breadcrumb\Breadcrumb;

class BreadcrumbFactory
{
    /**
     * @var WordpressWrapper
     */
    private $wordpress;
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(WordpressWrapper $wordpress, Repository $repository)
    {
        $this->wordpress = $wordpress;
        $this->repository = $repository;
    }

    /**
     * @param string $label
     * @param string $url
     * @param string $hover
     *
     * @return Breadcrumb
     */
    public function make($label, $url = null, $hover = null)
    {
        return new Breadcrumb($label, $url, $hover);
    }

    /**
     * @return Breadcrumb[]
     */
    public function make404()
    {
        return [$this->make('404')];
    }

    /**
     * @return Breadcrumb[]
     */
    public function makeHome()
    {
        return [$this->make('Home', $this->wordpress->homeUrl(), 'Home')];
    }

    /**
     * @return Breadcrumb[]
     */
    public function makeSearch()
    {
        return [$this->make('Search')];
    }

    /**
     * @param OowpPost $post
     * @param bool     $withUrl
     *
     * @return Breadcrumb[]
     */
    public function makeFromPost(Post $post, $withUrl = true)
    {
        $title = $post->title();
        $url = $withUrl ? $post->permalink() : null;
        $hover = $withUrl ? $title :  null;

        return [$this->make($post->title(), $url, $hover)];
    }

    /**
     * @param Post $post
     * @return Breadcrumb[]
     */
    public function makeFromAncestors(Post $post)
    {
        $crumbs = [];

        $ids = $this->wordpress->getPostAncestors($post);

        if (count($ids) < 1) {
            return $crumbs;
        }

        $posts = $this->repository->fetchMany($ids);

        foreach($posts as $post) {
            $crumbs = array_merge($crumbs, $this->makeFromPost($post));
        }

        return $crumbs;
    }

    /**
     * @param Post $post
     *
     * @return Breadcrumb[]
     */
    public function makeFromParent(Post $post)
    {
        $parent = $post->parent();

        if (!$parent) {
            return [];
        }

        return $this->makeFromPost($parent);
    }
}