<?php

namespace Outlandish\AcadOowpBundle\PostType;


abstract class Document extends Post {

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

    public function bibliographicReference() {
        return $this->metadata('bibliographic_reference');
    }

    public function attachment() {
        return $this->metadata('attachment');
    }

    public function tempAttachmentThumb() {
       return 'http://culturehive.co.uk/wp-content/uploads/2013/04/digital_strategy_framework.png';
    }

    public function attachmentSize() {
//        $attachmentId = $this->attachment()['id'];
//        $filename = get_attached_file($attachmentId);
//        if (file_exists($filename)) {
//            return $this->humanReadableFilesize(filesize($filename));
//        } else {
            return null;
//        }

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