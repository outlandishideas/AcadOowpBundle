<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Document extends Resource {

	public static $menu_icon = 'dashicons-format-aside';

    //connected to event, news, person, place, project, theme
    public static function onRegistrationComplete() {
        self::registerConnection(Event::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(News::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Person::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Place::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Project::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
        self::registerConnection(Theme::postType(),  array('sortable' => 'any','cardinality' => 'many-to-many'));
    }

    public function additionalInformation() {
        return $this->metadata('additional_information');
    }

	public function taxonomies() {
		$taxonomies = array(
			array(
				'type' => Theme::postType(),
				'name' => Theme::friendlyNamePlural()
			),
			array(
				'type' => Place::postType(),
				'name' => Place::friendlyNamePlural()
			),
		);

		foreach ( $taxonomies as &$taxonomy ) {
			$taxonomy['terms'] = $this->connected( $taxonomy['type'] );
		}

		return $taxonomies;
	}

    public function journal() {
        return $this->metadata('journal');
    }

    public function publication() {
        return $this->metadata('publication');
    }

    public function publisher() {
        return $this->metadata('publisher');
    }

    public function yearPublished() {
        return $this->metadata('year_published');
    }

    public function pageNumbers() {
        return $this->metadata('page_numbers');
    }

    public function attachment() {
        return $this->metadata('attachment');
    }

    public function attachmentSize() {
        $attachment = $this->attachment();
        $filename = get_attached_file($attachment['id']);
        if (file_exists($filename)) {
            return $this->humanReadableFilesize(filesize($filename));
        } else {
            return null;
        }
    }

    // Adapted from: http://www.php.net/manual/en/function.filesize.php
    public function humanReadableFilesize($size) {
        $mod = 1024;

        $units = explode(' ','B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }

        return round($size, 2) . ' ' . $units[$i];
    }


}