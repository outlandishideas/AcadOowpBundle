<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 26/01/2015
 * Time: 19:18
 */

namespace Outlandish\AcadOowpBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class BaseController extends \Outlandish\RoutemasterBundle\Controller\BaseController {

    protected function featuredPost($searchQuery)
    {
        $name = sanitize_title($searchQuery, null);
        $results = $this->query(array("name" => $name, "post_type" => "any", "posts_per_page" => 1));
        return ($results->post_count == 1) ? $results->post : null;
    }

    protected function queryPage($id)
    {
        return $this->querySingle(array(
            'page_id' => $id,
            'post_type' => 'page'
        ));
    }


}