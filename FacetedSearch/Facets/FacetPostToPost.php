<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:58
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\Facets;


class FacetPostToPost extends Facet{

    public function generateArguments()
    {
        $parentArgs = parent::generateArguments();
        $args = array();

        $connectionNames = array();

        foreach($args['post_type'] as $postType) {
            $connectionNames[] = $this->getConnectionName($postType);
        }

        $connectedItems = $this->getSelectedOptions();

        if(!isset($args['connected_type']) || !is_array($args[''])) {
            $args['connected_type'] = array();
            $args[''] = array();
        }

        $args['connected_type'] = array_merge($connectionNames, $args['']);
        $args[''] = array_merge($connectedItems, $args['']);

        return array_merge($args, $parentArgs);
    }

    /**
     * This is a copy of a function in the Post class in Outlandish\OowpBundle
     * Best way to use that instead??
     *
     * @param $postType
     * @return string
     */
    public function getConnectionName($postType)
    {
        $connection = array($postType, $this->name);
        sort($connection);
        return implode('_', $connection);
    }


} 