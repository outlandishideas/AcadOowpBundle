<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Role extends Theme {

	public static $menu_icon = 'dashicons-groups';
    public static $searchFilter = false;

    static function getRegistrationArgs($defaults) {

        $defaults['hierarchical'] = true;

        // Adds menu icon using the $menu_icon property if set
        if ( static::$menu_icon ) {
            $defaults['menu_icon'] = static::$menu_icon;
        }

        return $defaults;
    }

    //connected to person, project
    public static function onRegistrationComplete() {
        self::registerConnection(Person::postType(),  array('sortable' => 'any','cardinality' => 'many-to-one'));
        self::registerConnection(Project::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));

    }

}