<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Place extends Theme {

	public static $menu_icon = 'dashicons-location';

    //connected to document, event, news, person, project
    public static function onRegistrationComplete() {
        self::registerConnection(Project::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
    }

    public function postTypeIcon() {
        return self::$menu_icon;
    }

}