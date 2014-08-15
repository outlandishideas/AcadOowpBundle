<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:58
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\Facets;


use Outlandish\AcadOowpBundle\FacetedSearch\FacetOption\FacetOption;

class FacetOrder extends Facet {

    const SORT_DESC = 'DESC';
    const SORT_DESC_NAME = 'Descending';
    const SORT_ASC = 'ASC';
    const SORT_ASC_NAME = 'Ascending';

    public $defaultAll = false;
    public $exclusive = true;

    public $defaultOptions = array(
        self::SORT_DESC => self::SORT_DESC_NAME,
        self::SORT_ASC => self::SORT_ASC_NAME,
    );

    function __construct($name, $section, $options = array())
    {
        parent::__construct($name, $section, $options);
        if(empty($this->options)){
            foreach($this->defaultOptions as $name => $label){
                $option = new FacetOption($name, $label);
                $this->addOption($option);
            }
        }
    }

    /**
     * @param array $args
     * @return array
     */
    public function generateArguments($args = array())
    {
        $args = parent::generateArguments($args);

        //foreach option that is selected insert option as post_type
        $option = array_values($this->getSelectedOptions());
        $args['order'] = $option[0]->name;

        return $args;
    }

    public function setSelected(array $params)
    {
        $affected = parent::setSelected($params);
        if ($affected == 0) {
            $optionKeys = array_keys($this->options);
            $this->options[$optionKeys[0]]->selected = true;
            $affected++;
        }
        return $affected;
    }


} 