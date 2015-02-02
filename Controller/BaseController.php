<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 26/01/2015
 * Time: 19:18
 */

namespace Outlandish\AcadOowpBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Outlandish\OowpBundle\PostType\Post;

class BaseController extends \Outlandish\RoutemasterBundle\Controller\BaseController {

    /**
     * @param string $searchQuery search query that will be converted into a post_name
     * @return null|Post
     */
    protected function featuredPost($searchQuery)
    {
        $name = sanitize_title($searchQuery, null);
        $results = $this->query(array("name" => $name, "post_type" => "any", "posts_per_page" => 1));
        return ($results->post_count == 1) ? $results->post : null;
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
        return $this->querySingle(array('name' => $slugBits[count($slugBits) - 1], 'post_type' => $postTypes), $redirectCanonical);
    }

    /**
     * @param int $id ID of the page
     * @param bool $redirectCanonical whether to redirect to the canonical link
     * @return Post|null
     */
    protected function queryPage($id, $redirectCanonical = false)
    {
        return $this->querySingle(array(
            'page_id' => $id,
            'post_type' => 'page'
        ), $redirectCanonical);
    }


}