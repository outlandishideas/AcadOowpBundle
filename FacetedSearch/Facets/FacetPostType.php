<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:58
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\Facets;


class FacetPostType extends Facet{

    public function generateArguments()
    {
        $parentArgs = parent::generateArguments();
        $args = array(
            'post_type' => array()
        );

        foreach($this->options as $option){
            if($option['selected']){
                $args['post_type'][] = $option['selected'];
            }
        }

        return wp_parse_args($args, $parentArgs);
    }


} 