<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Place extends Theme {

	public static $menu_icon = 'dashicons-location';

    public static $connections = array(
        'project' => array('sortable' => 'any','cardinality' => 'many-to-many'),
    );

    public function postTypeIcon() {
        return self::$menu_icon;
    }

}