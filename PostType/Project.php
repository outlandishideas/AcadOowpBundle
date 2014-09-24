<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Project extends Theme {

	public static $menu_icon = 'dashicons-media-text';

    public static $connections = array(
        'theme' => array('sortable' => 'any','cardinality' => 'many-to-many'),
    );

    public function postTypeIcon() {
        return self::$menu_icon;
    }

}