<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class News extends Resource {

	public static $menu_icon = 'dashicons-megaphone';

    //connected to document, event, person, place, project, theme
    public static function onRegistrationComplete() {
        self::registerConnection(Person::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Place::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Project::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Theme::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
    }

    public static function friendlyNamePlural(){
        return "News";
    }

}