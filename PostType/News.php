<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class News extends Resource {

	public static $menu_icon = 'dashicons-megaphone';

    public static $connections = array(
        'person' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'place' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'project' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'theme' => array('sortable' => 'any','cardinality' => 'many-to-many'),
    );

    public static function friendlyNamePlural(){
        return "News";
    }

    public function postTypeIcon() {
        return self::$menu_icon;
    }

}