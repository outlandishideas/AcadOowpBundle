<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Person extends Post {

    public static $menuIcon = 'dashicons-businessman';
    public static $resource = true;

    public static $connections = array(
        'place' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'project' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'theme' => array('sortable' => 'any','cardinality' => 'many-to-many'),
    );

    static function getRegistrationArgs($defaults) {

        $defaults['hierarchical'] = true;

        // Adds menu icon using the $menu_icon property if set
        if ( static::$menuIcon ) {
            $defaults['menu_icon'] = static::$menuIcon;
        }

        return $defaults;
    }

    public static function friendlyNamePlural(){
        return "People";
    }

    /**
     * @param $userId
     * @return void|Person
     */
    public static function fetchByUser($userId)
    {
        $user = new \WP_User($userId);
        $connectionName = self::getConnectionName('user');
        return self::fetchOne(array(
                'connected_type' => $connectionName,
                'connected_items' => $user
            ));
    }

	/**
	 * Get the role for th person
	 *
	 * @return mixed|string|void
	 */
	public function role() {
		$role = $this->connected( Role::postType(), true );

		return ( $role ) ? $role->title() : '';
	}
    /**
     * todo: get metadata from person object
     * @return string
     */
    public function email()
    {
        return "test@email.com";
    }

    /**
     * todo: get metadata from persnn object
     * @return string
     */
    public function phone()
    {
        return "01234 567 890";
    }

} 