<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Document extends Post {

	public static $menu_icon = 'dashicons-format-aside';

    //connected to event, news, person, place, project, theme
    public static function onRegistrationComplete() {
        self::registerConnection(Event::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(News::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Person::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Place::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Project::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Theme::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
    }

}