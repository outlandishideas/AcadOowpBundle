<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Role extends Theme {

	public static $menuIcon = 'dashicons-groups';
    public static $searchFilter = false;

    public static $connections = array(
        'person' => array('sortable' => 'any','cardinality' => 'many-to-many'),
    );

    static function getRegistrationArgs($defaults) {

        $defaults['hierarchical'] = true;

        // Adds menu icon using the $menu_icon property if set
        if ( static::$menuIcon ) {
            $defaults['menu_icon'] = static::$menuIcon;
        }

        return $defaults;
    }
}