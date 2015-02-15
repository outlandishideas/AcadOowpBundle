<?php

namespace Outlandish\AcadOowpBundle\PostType;


/**
 * Class News
 * @package Outlandish\AcadOowpBundle\PostType
 */
abstract class News extends Resource
{

    public static $menuIcon = 'dashicons-megaphone';

    public static $connections = array(
        'news' => array('sortable' => 'any', 'cardinality' => 'many-to-many'),
        'person' => array('sortable' => 'any', 'cardinality' => 'many-to-many'),
        'place' => array('sortable' => 'any', 'cardinality' => 'many-to-many'),
        'project' => array('sortable' => 'any', 'cardinality' => 'many-to-many'),
        'theme' => array('sortable' => 'any', 'cardinality' => 'many-to-many'),
    );

    /**
     * @return string
     */
    public static function friendlyNamePlural()
    {
        return "News";
    }

    /**
     * @return string
     */
    public function postTypeIcon()
    {
        return self::$menuIcon;
    }

}