<?php
/**
 * Created by PhpStorm.
 * User: outlander
 * Date: 08/08/2014
 * Time: 17:54
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\FacetOption;


use Outlandish\OowpBundle\PostType\Post;

class FacetOptionPost extends FacetOption {

    public $post;

    function __construct(Post $post, $selected = false)
    {
        parent::__construct($post->ID, $post->title(), $selected);
        $this->post = $post;
    }

} 