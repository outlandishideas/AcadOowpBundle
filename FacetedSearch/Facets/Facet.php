<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:55
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\Facets;


abstract class Facet {

    /**
     * internal name for this facet
     * used when passing the options through the $_GET
     * @var string
     */
    public $name = "";
    /**
     * external name for this facet
     * todo: figure out what this does
     * @var string
     */
    public $section = "";
    /**
     * the options for this facet
     * used for constructing the arguments
     * @var array
     */
    public $options = array();

    /**
     * general parameter
     * This determines whether the default should be all selected or not
     * @var bool
     */
    public $defaultAll = true;
    /**
     * general parameter
     * This determines whether you can only select one option or not
     * will affect how facet displays on front end (eg. checkbox / radio button)
     * @var bool
     */
    public $exclusive = false;

    /**
     * @param $name
     * @param $section
     * @param array $options
     */
    function __construct($name, $section, $options = array())
    {
        $this->name = $name;
        $this->section = $section;
        $this->options = $options;
    }

    /**
     * add an option to the facet
     * eg. add a post type to the FacetPostType or FacetPostToPost
     * @param $name
     * @param $label
     * @return $this
     */
    public function addOption($name, $label)
    {
        $this->options[$name] = array(
            'name' => $name,
            'label' => $label,
            'selected' => false
        );
        return $this;
    }

    /**
     * get only the options that have been selected
     * if no options selected, and defaultAll is true, return all options
     * @return array
     */
    public function getSelectedOptions()
    {
        $options = array_filter($this->options, function($a){
            return $a['selected'];
        });
        //if no options have been selected and defaultAll is true
        if(count($options) == 0 && $this->defaultAll){
            $options = $this->options;
        }
        return $options;
    }

    /**
     * set the selected options as selected so we can generate the correct arguments
     * @param array $params | these will normally be the $_GET params
     * @return int $optionsSet | return the number of options that were set
     */
    public function setSelected(array $params)
    {
        $optionsSet = 0;
        if(array_key_exists($this->name, $params)){
            $selectedValues = $params[$this->name];
            if(!is_array($selectedValues)) $selectedValues = explode(',', $selectedValues);

            foreach($selectedValues as $value){
                if(array_key_exists($value, $this->options)){
                    $this->options[$value]['selected'] = true;
                    $optionsSet++;
                    if($this->exclusive) break;
                }
            }
        }
        return $optionsSet;
    }

    /**
     * this method parses the passed in args with
     * overwritten in child classes, but they may call this as their may be generic things to do
     * @param array $args
     * @return array
     */
    public function generateArguments($args = array())
    {
        $args = wp_parse_args(array(), $args);
        return $args;
    }
}