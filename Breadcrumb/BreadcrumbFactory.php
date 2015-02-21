<?php

namespace Outlandish\AcadOowpBundle\Breadcrumb;

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
     * @var QueryManager
     */
    private $queryManager;

    public function __construct(WordpressWrapper $wordpress, QueryManager $queryManager)
    {
        $this->wordpress = $wordpress;
        $this->queryManager = $queryManager;
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
        $ids = $this->wordpress->getPostAncestors($post);

        $args = [
            'post__in' => $ids,
            'orderby' => 'post__in',
        ];

        $posts = $this->queryManager->query($args)->posts;

        $crumbs = [];

        foreach($posts as $post) {
            $crumbs[] = $this->makeFromPost($post);
        }

        return $crumbs;
    }

    /**
     * @param OowpPost $post
     *
     * @return Breadcrumb[]
     */
    public function makeFromParent(Post $post)
    {
        $parentId = $post->postTypeParentId();

        $post = $this->queryManager->query(['page_id' => $parentId])->post;

        if (!$post) {
            return array();
        }

        return [$this->makeFromPost($post)];
    }
}