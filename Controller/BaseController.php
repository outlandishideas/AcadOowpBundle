<?php

namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\OowpBundle\PostType\Post;
use Symfony\Component\HttpFoundation\Request;
use Outlandish\RoutemasterBundle\Controller\BaseController as ParentController;

/**
 * Class BaseController
 * @package Outlandish\AcadOowpBundle\Controller
 */
class BaseController extends ParentController
{

    /**
     * returns a featured post if that post name was passed through
     * @param string $searchQuery search query that will be converted into a post_name
     * @return null|Post
     */
    protected function featuredPost($searchQuery)
    {
        $name = sanitize_title($searchQuery, null);
        $results = $this->query(array("name" => $name, "post_type" => "any", "posts_per_page" => 1));
        $postCount = 'post_count';

        return ($results->{$postCount} == 1) ? $results->post : null;
    }

    /**
     * @param $name - slug of the post to get
     * @param string|array $postTypes array or string or post types to search in - defaults to any
     * @param bool $redirectCanonical whether to redirect to the canonical link
     * @return Post|null
     */
    protected function queryPost($name, $postTypes = 'any', $redirectCanonical = false)
    {
        $slugBits = explode('/', trim($name, '/'));
        $queryArguments = array(
            'name' => $slugBits[count($slugBits) - 1],
            'post_type' => $postTypes
        );

        return $this->querySingle($queryArguments, $redirectCanonical);
    }

    /**
     * @param int $id ID of the page
     * @param bool $redirectCanonical whether to redirect to the canonical link
     * @return Post|null
     */
    protected function queryPage($id, $redirectCanonical = false)
    {
        $queryArguments = array(
            'page_id' => $id,
            'post_type' => 'page'
        );

        return $this->querySingle($queryArguments, $redirectCanonical);
    }
}