<?php

namespace Outlandish\AcadOowpBundle\PostType;

use Outlandish\OowpBundle\PostType\RoutemasterPost as BasePost;
use Outlandish\SiteBundle\PostType\Person;


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

    public function subtitle()
    {
        return $this->metadata('subtitle');
    }

    public function authorName()
    {
        return $this->metadata('author_name');
    }

    public function authorDesc()
    {
        return $this->metadata('author_description');
    }

    public function author()
    {
        $authorType = $this->metadata('author_type');

        if(!$authorType) return null;

        switch($authorType){
            case 'acf':
                return array(
                    'name' => $this->authorName(),
                    'description' => $this->authorDesc()
                );
            break;
            case 'post':
                return $this->connected(Person::postType())->posts;
            break;
            default:
                $person = Person::fetchByUser($this->post_author);
                if($person) {
                    return $person;
                } else {
                    return new \WP_User( $this->post_author);
                }
        }
    }
} 