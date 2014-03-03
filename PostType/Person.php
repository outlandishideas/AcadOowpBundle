<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Person extends Post {

    //connected to document, event, news, place, project, role, theme
    public static function onRegistrationComplete() {
        self::registerConnection(Place::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Project::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Theme::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
    }

    public static function friendlyNamePlural(){
        return "People";
    }

} 