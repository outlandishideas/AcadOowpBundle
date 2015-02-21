<?php

namespace Outlandish\AcadOowpBundle\Breadcrumb;

use Outlandish\AcadOowpBundle\Breadcrumb\BreadcrumbFactory;
use Outlandish\AcadOowpBundle\Repository\Repository;
use Outlandish\AcadOowpBundle\Wordpress\WordpressWrapper;
use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\AcadOowpBundle\PostType\PostInterface as Post;

/**
 * Class BreadcrumbHelper
 * @package Outlandish\AcadOowpBundle\Helper
 */
class BreadcrumbTrail
{
    /**
     * @var WordpressWrapper
     */
    private $wordpress;
    /**
     * @var BreadcrumbFactory
     */
    private $breadcrumb;

    /**
     * @param WordpressWrapper  $wordpress
     * @param BreadcrumbFactory $breadcrumb
     */
    public function __construct(WordpressWrapper $wordpress, BreadcrumbFactory $breadcrumb)
    {
        $this->wordpress = $wordpress;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @param Post $post
     *
     * @return array
     */
    public function make(Post $post)
    {
        $crumbs = [];

        $crumbs[] = $this->breadcrumb->makeHome();

        if ($this->wordpress->is404()) {
            $crumbs[] = $this->breadcrumb->make404();

            return $crumbs;
        }

        if ($this->wordpress->isSearch()) {
            $crumbs[] = $this->breadcrumb->makeSearch();

            return $crumbs;
        }

        if ($this->wordpress->isSingle() and $post->postType() != 'page') {
            $crumbs = array_merge($crumbs, $this->breadcrumb->makeFromParent($post), $this->breadcrumb->makeFromPost($post, false));
        } else if (!$this->wordpress->isHome()) {
            $crumbs = array_merge($crumbs, $this->breadcrumb->makeFromAncestors($post), $this->breadcrumb->makeFromPost($post, false));
        }

        return $crumbs;
    }
}