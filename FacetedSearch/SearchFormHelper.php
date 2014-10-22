<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 06/08/2014
 * Time: 15:18
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch;


use Outlandish\AcadOowpBundle\FacetedSearch\Facets\Facet;
use Outlandish\AcadOowpBundle\FacetedSearch\FacetOption\FacetOption;

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
        if($facet->isDefaultAll()){
//            $options[] = new FacetOption\FacetOption('', 'All', false);
        }
        /** @var FacetOption[] $options */
        $options = array_merge($options, $facet->options);

        $classes = array();
        if($facet->isExclusive())
        {
            $classes[] = 'facet-exclusive';
        }

        $classes = implode(' ', $classes);

        $html = "<dl id='{$facet->getName()}-group' class='search-facet {$classes}' role='menu'>";

        $html .= "<dt>{$facet->getSection()}:</dt>";

        //create an all button
        $selectedOptions = $facet->getSelectedOptions();
        if(!$facet->isExclusive()){
            $type = "radio";
            $optionNames = implode(',', array_map(function($a){
                return $a->getName();
            }, $options));
            $liClass = (count($selectedOptions) == count($options)) ? 'class="active"': null;
            $selected = (count($selectedOptions) == count($options)) ? 'checked' : '';
            $li = "<dd {$liClass}>";
            $li .= "<label for='{$facet->getName()}-all'>All</label>";
            $li .= "<input type='{$type}'
                id='{$facet->getName()}-all'
                name='{$facet->getName()}'
                value='{$optionNames}'
                {$selected} />";
            $li .= "</dd>";
            $html .= $li;
        } else {
            $type = "radio";
        }

        /** @var FacetOption $option */
        foreach($options as $option) {
            $selected = $option->selected && (count($selectedOptions) != count($options)) ? 'checked' : '';
            $liClass = $option->selected && (count($selectedOptions) != count($options)) ? 'class="active"': null;
            $html .= "<dd {$liClass}>";
            $html .= "<label for='{$facet->getName()}-{$option->getName()}'>{$option->getLabel()}</label>";
            $html .= "<input type='{$type}'
                id='{$facet->getName()}-{$option->getName()}'
                name='{$facet->getName()}'
                value='{$option->getName()}'
                {$selected} />";
            $html .= "</dd>";
        }
        $html .= "</dl>";
        return $html;
    }

} 