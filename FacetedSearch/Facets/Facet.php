<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:55
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\Facets;


class Facet {
    function __construct($name, $section)
    {
        $this->name = $name;
        $this->section = $section;
    }

    public function generateArguments()
    {
        $args = array();
        return $args;
    }
}