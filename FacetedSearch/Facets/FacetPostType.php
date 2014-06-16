<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:58
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\Facets;


class FacetPostType extends Facet{
    function __construct($name, $section, array $postTypes = array())
    {
        parent::__construct($name, $section);
        $this->postTypes = $postTypes;
    }

    public function addPostType($name, $label)
    {
        $this->postTypes[] = array(
            'name' => $name,
            'label' => $label
        );
        return $this;
    }

    public function generateArguments()
    {
        $parentArgs = parent::generateArguments();
        $args = array();
        //todo: generate arguments
        return $args;
    }


} 