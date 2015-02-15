<?php

namespace Outlandish\AcadOowpBundle\PostType;

/**
 * Class Theme
 * @package Outlandish\AcadOowpBundle\PostType
 */
abstract class Theme extends Search
{

    public static $menuIcon = 'dashicons-category';
    public static $theme = true;
    public static $searchFilter = true;

}