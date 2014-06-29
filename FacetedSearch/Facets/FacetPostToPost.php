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

        $args['connections'] = array();

        foreach($args['post_type'] as $postType) {
            $connectionName = $this->getConnectionName($postType);
            foreach($this->options as $option){
                if($option['selected']){
                    $argument = array(
                        'connection' => $connectionName,
                        'post' => $option['name']
                    );
                    $args['connections'][] = $argument;
                }
            }
        }

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