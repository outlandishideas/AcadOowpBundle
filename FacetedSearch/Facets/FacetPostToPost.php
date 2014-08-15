<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:58
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\Facets;


use Outlandish\OowpBundle\Query\OowpQuery;

class FacetPostToPost extends Facet{

    public $defaultAll = false;
    public $postType;

    function __construct($name, $section, $postType, $options = array())
    {
        parent::__construct($name, $section, $options);
        $this->postType = $postType;
    }

    /**
     * This method generates the arguments for this facet to be passed through to
     * @param array $args
     * @return array
     */
    public function generateArguments($args = array())
    {
        $args = parent::generateArguments($args);

        $options = $this->getSelectedOptions();

        //if no connected items do not add any new content to the arguments
        if($options){
            $connectionNames = array();

            if(isset($args['post_type']) && is_array($args['post_type'])) {
                foreach ($args['post_type'] as $postType) {
                    $connectionNames[] = $this->getConnectionName($postType);
                }
            } else {
                //todo: if any post_type then get all post_types and connections between them
            }

            $connectedIds = array();
            foreach($options as $option){
                $connectedIds[]  = $option->name;
            }

            if(!isset($args['connected_type'])) {
                $args['connected_type'] = array();
                $args['connected_items'] = array();
            }

            $args['connected_type'] = array_merge($connectionNames, $args['connected_type']);
            $args['connected_items'] = array_merge($connectedIds, $args['connected_items']);
        }

        return $args;
    }

    /**
     * This is a copy of a function in the Post class in Outlandish\OowpBundle
     * todo: turn that method into a static one on Post Class
     * @param $postType
     * @return string
     */
    public function getConnectionName($postType)
    {
        $connection = array($postType, $this->postType);
        sort($connection);
        return implode('_', $connection);
    }
} 