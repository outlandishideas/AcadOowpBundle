<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Resource extends Post {

    public static $resource = true;

    public function themes()
    {
        $themes = array();
        foreach(self::$postManager->postTypeMapping() as $postType => $class) {
            if($class::isTheme()) $themes[] = $postType;
        }
        return $this->connected($themes);
    }

    public function hasThemes()
    {
        $themes = $this->themes();
        return $themes->post_count != 0;
    }

    public function themeTitles()
    {
        $themes = $this->themes();
        if($themes->post_count < 1) return array();
        return array_map(function($a){
            return $a->title();
        }, $themes->posts);
    }
}