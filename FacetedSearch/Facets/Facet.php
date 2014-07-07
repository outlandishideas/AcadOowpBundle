<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:55
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\Facets;


abstract class Facet {

    public $options = array();
    public $defaultAll = true;
    public $exclusive = false;

    function __construct($name, $section, $options = array())
    {
        $this->name = $name;
        $this->section = $section;
        $this->options = $options;
    }

    public function addOption($name, $label)
    {
        $this->options[$name] = array(
            'name' => $name,
            'label' => $label,
            'selected' => false
        );
        return $this;
    }

    public function getSelectedOptions()
    {
        return array_filter($this->options, function($a){
            return $a['selected'];
        });
    }

    public function setSelected(array $params)
    {
        $setOne = false;

        if(array_key_exists($this->name, $params)){
            $selectedValues = $params[$this->name];
            if(!is_array($selectedValues)) $selectedValues = explode(',', $selectedValues);

            foreach($selectedValues as $value){
                if(array_key_exists($value, $this->options)){
                    $setOne = true;
                    $this->options[$value]['selected'] = true;

                    if($this->exclusive) break;
                }
            }
        }
        if(!$setOne && $this->defaultAll){
            foreach($this->options as $p => $option){
                $this->options[$p]['selected'] = true;
            }
        }
        return $this;
    }

    public function selectOption($name = null){
        foreach($this->options as $p => $option){
            if(!$name || ( $name && $option['name'] == $name)){
                $this->options[$p]['selected'] = true;
            }
        }
        return $this;
    }

    public function generateArguments($args = array())
    {
        $args = array_merge($args, array());
        return $args;
    }
}