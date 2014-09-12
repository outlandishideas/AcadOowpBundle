<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Theme extends Resource {

	public static $menu_icon = 'dashicons-category';
    public static $theme = true;
    public static $searchFilter = true;

    //connected to document, event, news, person, project

    public function postTypeIcon() {
        return self::$menu_icon;
    }

}