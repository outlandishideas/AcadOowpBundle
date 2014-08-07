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
            $options[''] = array(
                'name' => '',
                'label' => 'All',
                'selected' => false
            );
        }
        $options = array_merge($options, $facet->options);

        $classes = array();
        if($facet->exclusive) $options[] = 'facet-exclusive';

        $classes = implode(' ', $classes);

        $html = "<ul id='{$facet->name}-group' class='{$classes}'>";
        foreach($options as $option) {
            $selected = $option['selected'] ? 'selected' : '';
            $html += "<li>";
            $html += "<label for='{$facet->name}-{$option['name']}'>{$option['label']}</label>";
            $html += "<input type='checkbox'
                id='{$facet->name}-{$option['name']}'
                name='{$facet->name}'
                value='{$option['name']}'
                {$selected} />";
            $html += "</li>";
        }
        return $html;
    }

} 