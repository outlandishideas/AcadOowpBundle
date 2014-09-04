<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Person extends Post {

    public static $menu_icon = 'dashicons-businessman';
    public static $resource = true;

    static function getRegistrationArgs($defaults) {

        $defaults['hierarchical'] = true;

        // Adds menu icon using the $menu_icon property if set
        if ( static::$menu_icon ) {
            $defaults['menu_icon'] = static::$menu_icon;
        }

        return $defaults;
    }

    //connected to document, event, news, place, project, role, theme
    public static function onRegistrationComplete() {
        self::registerConnection(Place::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Project::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Theme::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection('user',  array('sortable' => 'any','cardinality' => 'one-to-one'));
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