<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Page extends Post {

    //connected to theme
    public static function onRegistrationComplete() {
        self::registerConnection(Theme::postType(),  array('sortable' => 'any','cardinality' => 'one-to-many'));
    }

}