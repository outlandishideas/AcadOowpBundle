<?php

namespace Outlandish\AcadOowpBundle\PostType;

use Outlandish\OowpBundle\PostType\RoutemasterPost as BasePost;


abstract class Post extends BasePost {

    /**
     * This is a new method
     * Created By: Hanna
     * Created On: 28/02/2014
     * Modified By: Hanna
     * Modified On: 29/02/2014
     * @return string
     */
    public function newMethod(){
        return "This is new";
    }

    //todo: this is a possible route to go for the faceted search
    //I can't be bothered to write it here, but come and ask me if questions
    /**
     * Create search args for this post type
     * To be passed through when adding this as a filter to FacetedSearch
     * @return array
     */
    public function getSearchArgs()
    {
        //todo: construct search args
        return array();
    }

    public function sections()
    {
        $sections = $this->metadata('sections');
        if(!$sections){
            return array();
        }
        foreach($sections as $s => $section){
            $items = $section['items'];
            $ids = array();
            foreach($items as $item){
                if($item instanceof \WP_Post){
                    $ids[] = $item->ID;
                }
            }
            $query = Post::fetchAll(array('post_type' => 'any', 'post__in' => $ids));
            if(count($query->posts) > 0){
                $items = $query->posts;
            } else {
                $items = array();
            }
            $sections[$s]['items'] = $items;
        }
        return $sections;
    }
} 