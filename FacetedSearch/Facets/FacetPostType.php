<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:58
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\Facets;


class FacetPostType extends Facet{

    public function generateArguments($args = array())
    {
        $args = parent::generateArguments($args);

        //if we don't have the post_type in the $args, make it
        if(!$args['post_type'] || !is_array($args['post_type'])) {
            $args['post_type'] = array();
        }

        //foreach option that is selected insert option as post_type
        foreach($this->options as $option){
            if($option['selected']){
                $args['post_type'][] = $option['name'];
            }
        }

        return $args;
    }


} 