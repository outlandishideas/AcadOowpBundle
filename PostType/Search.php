<?php
/**
 * Search Post Type
 *
 * @category PostType
 * @package  Outlandish\AcadOowpBundle\PostType
 * @author   Matthew Kendon <matt@outlandish.com>
 * @license  http://license.com MIT
 * @version  GIT: 1.2
 * @link     http://link.com
 */

namespace Outlandish\AcadOowpBundle\PostType;

/**
 * Class Search
 * @category PostType
 * @package Outlandish\AcadOowpBundle\PostType
 * @author Matthew Kendon <matt@outlandish.com>
 * @license http://license.com MIT
 * @link http://link.com
 */
abstract class Search extends Post
{

    protected static $search = true;

    /**
     * Determines whether this class should appear in a search or not
     *
     * @return boolean
     */
    public static function isSearch()
    {
        return self::$search;
    }

}