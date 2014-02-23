<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Person extends Post {

    public static function postTypeParentId()
    {
        return "17";
    }
} 