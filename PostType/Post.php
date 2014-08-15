<?php

namespace Outlandish\AcadOowpBundle\PostType;

use Outlandish\OowpBundle\PostType\RoutemasterPost as BasePost;
use Outlandish\SiteBundle\PostType\Person;


abstract class Post extends BasePost {

    public static $resource = false;
    public static $theme = false;

    public static function isResource(){
        return static::$resource;
    }

    public static function isTheme(){
        return static::$theme;
    }

    public function sections()
    {
        return $this->metadata('sections');
    }

    public function subtitle()
    {
        return $this->metadata('subtitle');
    }

    public function authorName()
    {
        return $this->metadata('author_name');
    }

    public function authorDesc()
    {
        return $this->metadata('author_description');
    }

    public function author()
    {
        $authorType = $this->metadata('author_type');

        if(!$authorType) return null;

        switch($authorType){
            case 'acf':
                return array(
                    'name' => $this->authorName(),
                    'description' => $this->authorDesc()
                );
            break;
            case 'post':
                return $this->connected(Person::postType())->posts;
            break;
            default:
                $person = Person::fetchByUser($this->post_author);
                if($person) {
                    return $person;
                } else {
                    return new \WP_User( $this->post_author);
                }
        }
    }

    /**
     *  Returns date as
     *  (1) dd/mm - dd/mm/yyyy for events if end date set
     *  (2) dd/mm/yyyy for everything else
     * @param bool $shortMonth (return 'Sep' instead of 'September')
     * @return int|string
     */
    public function dateString($format = "j F Y", $shortMonth = false) {

        $date = '';

        if ($this->post_type == Event::postType()) {

            if ($this->endDateString()) {
                $date = $shortMonth ? $this->startDateString("j M")." - " . $this->endDateString("j M Y") : $this->startDateString("j F")." - " . $this->endDateString("j F Y");
            } else {
                $date = $this->startDateString($format);
            }
        }

        if (!$date) {
            $date = date($format, strtotime($this->post_date));
        }

        return $date;
    }

	public static function childTypes( $isChild = true ) {
		$types = array();
		$self = get_called_class();
		foreach (self::$postManager->postTypeMapping() as $postType => $class) {

			if ( in_array( $self, class_parents( $class) ) && $isChild ) {
				// if the class is a child of self and we are looking for children
				$types[$postType] = $class;
			} else if (	! in_array( $self, class_parents( $class) ) && ! $isChild) {
				// if class not child of self and we are not looking for children
				$types[$postType] = $class;
			}
		}

		return $types;
	}

	public function connectedTypes( $types = array() ) {
		$post_types = self::connectedPostTypes();

		return array_intersect( $post_types, $types);
	}

}