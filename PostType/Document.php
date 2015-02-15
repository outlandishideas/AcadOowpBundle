<?php

namespace Outlandish\AcadOowpBundle\PostType;

/**
 * Class Document
 * @package Outlandish\AcadOowpBundle\PostType
 */
abstract class Document extends Resource
{

    public static $menuIcon = 'dashicons-format-aside';

    public static $connections = array(
        'document' => ['sortable' => 'any', 'cardinality' => 'many-to-many'],
        'event' => ['sortable' => 'any', 'cardinality' => 'many-to-many'],
        'news' => ['sortable' => 'any', 'cardinality' => 'many-to-many'],
        'person' => ['sortable' => 'any', 'cardinality' => 'many-to-many'],
        'place' => ['sortable' => 'any', 'cardinality' => 'many-to-many'],
        'project' => ['sortable' => 'any', 'cardinality' => 'many-to-many'],
        'theme' => ['sortable' => 'any', 'cardinality' => 'many-to-many'],
    );

    /**
     * Returns additional_information custom field for this post
     *
     * @return mixed
     */
    public function additionalInformation()
    {
        return $this->metadata('additional_information');
    }

    /**
     * @return array
     */
    public function taxonomies()
    {
        $taxonomies = [
            [
                'type' => Theme::postType(),
                'name' => Theme::friendlyNamePlural()
            ],
            [
                'type' => Place::postType(),
                'name' => Place::friendlyNamePlural()
            ],
        ];

        foreach ($taxonomies as &$taxonomy) {
            $taxonomy['terms'] = $this->connected($taxonomy['type']);
        }

        return $taxonomies;
    }

    /**
     * @return mixed
     */
    public function documentText()
    {
        return $this->metadata('document_text');
    }

    /**
     * @return mixed
     */
    public function bibliographicReference()
    {
        return $this->metadata('bibliographic_reference');
    }

    /**
     * @return mixed
     */
    public function documentUrl()
    {
        return $this->metadata('document_url');
    }

    /**
     * @return null|string
     */
    public function attachmentSize()
    {
        $attachment = $this->attachment();
        $filename = get_attached_file($attachment['id']);

        if (!file_exists($filename)) {
            return null;
        }

        return $this->humanReadableFilesize(filesize($filename));
    }

    /**
     * @return mixed
     */
    public function attachment()
    {
        return $this->metadata('attachment');
    }

    //

    /**
     * Adapted from: http://www.php.net/manual/en/function.filesize.php
     *
     * @param int $size
     * @return string
     */
    public function humanReadableFilesize($size)
    {
        $mod = 1024;

        $units = explode(' ', 'B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}