<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Role extends Post {

    public static function onRegistrationComplete() {
        self::registerConnection(Person::postType(),  array('sortable' => 'any','cardinality' => 'many-to-one'));
    }
} 