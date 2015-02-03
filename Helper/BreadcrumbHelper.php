<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 03/02/2015
 * Time: 12:39
 */

namespace Outlandish\AcadOowpBundle\Helper;


use Outlandish\OowpBundle\Manager\PostManager;
use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\OowpBundle\PostType\Post as OowpPost;

class BreadcrumbHelper {

    /**
     * @var PostManager
     */
    private $postManager;
    /**
     * @var QueryManager
     */
    private $queryManager;

    /**
     * @param PostManager $postManager
     * @param QueryManager $queryManager
     */
    function __construct(PostManager $postManager, QueryManager $queryManager)
    {
        $this->postManager = $postManager;
        $this->queryManager = $queryManager;
    }


    public function make(OowpPost $post)
    {
        $postType = $post->post_type;

        $crumbs = array();

        if ( is_search() ) {
            $crumbs[] = $this->createCrumb('  Search Results');
        } elseif ( is_404() ) {
            $crumbs[] = $this->createCrumb('  404 Not Found');
        } elseif ( is_single() && $postType != 'page' ) {
            $crumbs[] = $this->createCrumbFromId( $this->getParentId($postType) );
            $crumbs[] = $this->createCrumbFromPost($post, false);
        } elseif ( $postType == 'page' ) {
            $parents = $this->getAncestorsAsCrumbs($post);
            $crumbs = array_merge($crumbs, $parents);
            $crumbs[] = $this->createCrumbFromPost($post, false);
        }
        array_unshift($crumbs, $this->createHomeCrumb());

        return array_filter($crumbs);
    }

    /**
     * @return array
     */
    private function createHomeCrumb()
    {
        return $this->createCrumb(
            'Home',
            'Back to Home',
            home_url()
        );
    }

    /**
     * @param int id
     * @param bool $url
     * @return array
     */
    private function createCrumbFromId($id, $url = true)
    {
        $post = $this->queryManager->query([
            'page_id' => $id,
            'post_type' => 'page'
        ])->post;
        if(! $post) return null;
        return $this->createCrumb(
            $post->title(),
            $post->title(),
            $url ? $post->permalink() : null);
    }

    /**
     * @param $post
     * @param bool $url
     * @return array
     */
    private function createCrumbFromPost(OowpPost $post, $url = true)
    {
        return $this->createCrumb(
            $post->title(),
            $post->title(),
            $url ? $post->permalink() : null);
    }

    /**
     * @param $postType
     * @return mixed
     */
    private function getParentId($postType)
    {
        $class = $this->postManager->postTypeClass($postType);
        return $class::postTypeParentId();
    }

    /**
     * @param OowpPost $post
     * @return OowpPost[]
     */
    private function getAncestorsAsCrumbs(OowpPost $post)
    {
        $ids = get_post_ancestors($post->ID);
        if(empty($ids))
            return [];

        $parents = $this->queryManager->query([
            'post_type' => 'page',
            'post__in' => $ids,
            'orderby' => 'post__in'
        ])->posts;
        return array_map([$this, 'createCrumbFromPost'], $parents);
    }

    /**
     * @param string $label
     * @param string|null $title
     * @param string|null $url
     * @return array
     */
    private function createCrumb($label, $title = null, $url = null)
    {
        return [
            'label' => $label,
            'title' => $title,
            'url' => $url
        ];
    }
}