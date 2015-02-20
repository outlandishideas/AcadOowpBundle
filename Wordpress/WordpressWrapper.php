<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 19/02/2015
 * Time: 21:13
 */

namespace Outlandish\AcadOowpBundle\Wordpress;

/**
 * Wraps Wordpress functions to aid testing and cleaner code
 *
 * Class WordpressWrapper
 * @package Outlandish\AcadOowpBundle\Wordpress
 */
class WordpressWrapper
{
    /**
     * @param $thing
     * @return bool
     */
    public function isWPError($thing)
    {
        return is_wp_error($thing);
    }

    /**
     * @return array
     */
    public function getNavMenuLocations()
    {
        return get_nav_menu_locations();
    }

    /**
     * @param $term
     * @param $taxonomy
     * @param string $output
     * @param string $filter
     * @return mixed|null|\WP_Error
     */
    public function getTerm($term, $taxonomy, $output = 'OBJECT', $filter = 'raw')
    {
        return get_term($term, $taxonomy, $output, $filter);
    }

    /**
     * @param $menu
     * @param array $args
     * @return mixed
     */
    public function getNavMenuItems($menu, $args = array())
    {
        return wp_get_nav_menu_items($menu, $args);
    }
}