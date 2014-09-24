<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Theme extends Post {

	public static $menuIcon = 'dashicons-category';
    public static $theme = true;
    public static $searchFilter = true;

}