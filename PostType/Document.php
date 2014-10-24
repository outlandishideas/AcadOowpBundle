<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Document extends Resource {

	public static $menuIcon = 'dashicons-format-aside';

    public static $connections = array(
        'document' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'event' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'news' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'person' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'place' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'project' => array('sortable' => 'any','cardinality' => 'many-to-many'),
        'theme' => array('sortable' => 'any','cardinality' => 'many-to-many'),
    );

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

    public function documentText() {
        return $this->metadata('document_text');
    }

    public function bibliographicReference() {
        return $this->metadata('bibliographic_reference');
    }

    public function documentUrl() {
        return $this->metadata('document_url');
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

//    public static function fetchDocuments($queryArgs = array()) {
//        $defaults = array (
//            'posts_per_page' => -1,
//            'meta_query'=>array(
//                array(
//                    'key'=>'publication_date',
//                    'value'=> date('Y/m/d'),
//                    'compare'=>'>',
//                    'type'=>'DATE'
//                )
//            ),
//            'orderby' => 'meta_value',
//            'meta_key' => 'start_date',
//            'order' => 'asc'
//        );
//
//        $queryArgs = wp_parse_args($queryArgs, $defaults);
//        $futureEvents = self::fetchAll($queryArgs);
//
//        return $futureEvents;
//    }


}