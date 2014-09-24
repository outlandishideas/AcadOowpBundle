<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Role extends Theme {

	public static $menu_icon = 'dashicons-groups';
    public static $searchFilter = false;

    public static $connections = array(
        'person' => array('sortable' => 'any','cardinality' => 'many-to-many'),
    );

    static function getRegistrationArgs($defaults) {

        $defaults['hierarchical'] = true;

        // Adds menu icon using the $menu_icon property if set
        if ( static::$menu_icon ) {
            $defaults['menu_icon'] = static::$menu_icon;
        }

        return $defaults;
    }

    public function postTypeIcon() {
        return self::$menu_icon;
    }

}