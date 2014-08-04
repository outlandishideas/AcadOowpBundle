<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Role extends Post {

	public static $menu_icon = 'dashicons-groups';

    //connected to person, project
    public static function onRegistrationComplete() {
        self::registerConnection(Person::postType(),  array('sortable' => 'any','cardinality' => 'many-to-one'));
        self::registerConnection(Project::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));

    }

}