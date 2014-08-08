<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Project extends Theme {

	public static $menu_icon = 'dashicons-media-text';

    //connected to document, event, news, person, place, theme
    public static function onRegistrationComplete() {
        self::registerConnection(Theme::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
    }

}