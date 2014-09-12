<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 06/08/2014
 * Time: 15:18
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch;


use Outlandish\AcadOowpBundle\FacetedSearch\Facets\Facet;

class SearchFormHelper {

    function __construct(Search $search)
    {
        $this->search = $search;
    }

    public function getSearchFormElements()
    {
        $elements = array();
        foreach($this->search->getFacets() as $facet){
            $elements[$facet->name] = $this->generateHTML($facet);
        }
        return $elements;
    }


    public function getFacet($name)
    {
        foreach($this->search->getFacets() as $facet){
            if($name == $facet->name){
                return $facet;
            }
        }
        return false;
    }

    public function generateHTML(Facet $facet)
    {
        $options = array();
        if($facet->defaultAll){
//            $options[] = new FacetOption\FacetOption('', 'All', false);
        }
        $options = array_merge($options, $facet->options);

        $classes = array();
        if($facet->exclusive) $classes[] = 'facet-exclusive';

        $classes = implode(' ', $classes);

        $html = "<ul id='{$facet->name}-group' class='search-facet inline-list {$classes}'>";
        foreach($options as $option) {
            $selected = $option->selected ? 'checked' : '';
            $liClass = $option->selected ? 'class="active"': null;
            $html .= "<li {$liClass}>";
            $html .= "<label for='{$facet->name}-{$option->name}'>{$option->label}</label>";
            $html .= "<input type='checkbox'
                id='{$facet->name}-{$option->name}'
                name='{$facet->name}'
                value='{$option->name}'
                {$selected} />";
            $html .= "</li>";
        }
        $html .= "</ul>";
        return $html;
    }

} 