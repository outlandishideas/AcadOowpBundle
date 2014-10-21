<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Search extends Post {

    protected static $search = true;

    /**
     * @return boolean
     */
    public static function isSearch()
    {
        return self::$search;
    }

}