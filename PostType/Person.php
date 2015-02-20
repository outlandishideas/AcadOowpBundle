<?php

namespace Outlandish\AcadOowpBundle\PostType;

/**
 * Class Person
 * @package Outlandish\AcadOowpBundle\PostType
 */
abstract class Person extends Resource
{

    public static $menuIcon = 'dashicons-businessman';
    public static $theme = true;
    public static $searchFilter = false;

    public static $connections = array(
        'place' => array('sortable' => 'any', 'cardinality' => 'many-to-many'),
        'project' => array('sortable' => 'any', 'cardinality' => 'many-to-many'),
        'theme' => array('sortable' => 'any', 'cardinality' => 'many-to-many'),
    );

    /**
     * @param array $defaults
     * @return mixed
     */
    public static function getRegistrationArgs($defaults)
    {

        $defaults['hierarchical'] = true;

        // Adds menu icon using the $menu_icon property if set
        if (static::$menuIcon) {
            $defaults['menu_icon'] = static::$menuIcon;
        }

        return $defaults;
    }

    /**
     * @return string
     */
    public static function friendlyNamePlural()
    {
        return "People";
    }

    /**
     * @param int $userId
     * @return null|Person
     */
    public static function fetchByUser($userId)
    {
        $queryArguments = array(
            'connected_type' => self::getConnectionName('user'),
            'connected_items' => new \WP_User($userId)
        );

        return self::fetchOne($queryArguments);
    }

    /**
     * Get the role for the person
     *
     * @return string
     */
    public function role()
    {
        $role = $this->connected(Role::postType(), true);

        return ($role) ? $role->title() : '';
    }

    /**
     * @return string
     */
    public function email()
    {
        return $this->metadata('email');
    }

    /**
     * @return string
     */
    public function phone()
    {
        return $this->metadata('tel');
    }

} 