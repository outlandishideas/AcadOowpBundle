<?php

namespace Outlandish\AcadOowpBundle\PostType;

/**
 * Class Project
 * @package Outlandish\AcadOowpBundle\PostType
 */
abstract class Project extends Theme
{

    public static $menuIcon = 'dashicons-media-text';

    public static $connections = array(
        'theme' => array('sortable' => 'any', 'cardinality' => 'many-to-many'),
    );
}