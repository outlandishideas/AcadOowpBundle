<?php

namespace Outlandish\AcadOowpBundle\Helper;


use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\AcadOowpBundle\Wordpress\Exceptions\WordpressException;
use Outlandish\AcadOowpBundle\Wordpress\WordpressWrapper;

/**
 * Gets the Oowp Post Objects for a given menu name
 *
 * Class WordpressMenuHelper
 * @package Outlandish\AcadOowpBundle\Helper
 */
class WordpressMenuHelper
{
    /**
     * @var QueryManager
     */
    private $queryManager;
    /**
     * @var WordpressWrapper
     */
    private $wordpress;

    /**
     * @param QueryManager $queryManager
     */
    public function __construct(QueryManager $queryManager, WordpressWrapper $wordpress)
    {
        $this->queryManager = $queryManager;
        $this->wordpress = $wordpress;
    }

    /**
     * Fetches the pages for a particular menu as defined in wordpress
     *
     * @param string $menu The name of the menu to be returned
     *
     * @return array
     */
    public function get($menu)
    {
        $menuLocations = $this->wordpress->getNavMenuLocations();

        $menuObject = $this->getMenuObject($menu, $menuLocations);

        $postIDs = $this->getMenuPostIds($menuObject);

        $args = array(
            'post__in' => $postIDs,
            'orderby' => 'post__in'
        );

        return $this->queryManager->query($args)->posts;
    }


    /**
     * Get the term object that represents the wordpress menu
     *
     * @param $menu
     * @param $menuLocations
     * @return mixed|null|\WP_Error
     */
    private function getMenuObject($menu, $menuLocations)
    {
        $this->checkMenuExists($menu, $menuLocations);

        $menuObject = $this->wordpress->getTerm($menuLocations[$menu], 'nav_menu');

        if($this->wordpress->isWPError($menuObject)) {
            throw new WordpressException("Menu Object has errors: $menu");
        }

        return $menuObject;
    }

    /**
     * Get the ids of an array of post objects associated with the menuObject
     *
     * @param $menuObject
     * @throw WordpressException
     * @return array
     */
    private function getMenuPostIds($menuObject)
    {
        $termId = 'term_id';
        $posts = $this->wordpress->getNavMenuItems($menuObject->{$termId});

        if (!is_array($posts) || count($posts) == 0) {
            throw new WordpressException("Menu has no pages");
        }

        return array_map(function ($a) {
            $objectId = 'object_id';

            return $a->{$objectId};
        }, $posts);
    }


    /**
     * Checks to see that the $menu exists in the menuLocations provided by Wordpress
     *
     * @param $menu
     * @param $menuLocations
     * @throws WordpressException
     */
    private function checkMenuExists($menu, $menuLocations)
    {
        if (!array_key_exists($menu, $menuLocations)) {
            throw new WordpressException("Menu missing from menuLocations: $menu");
        }
    }

}