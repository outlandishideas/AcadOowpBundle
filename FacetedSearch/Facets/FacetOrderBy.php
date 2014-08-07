<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:58
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\Facets;


class FacetOrderBy extends Facet {

//    const SORT_RELEVANCE = 'rel';
//    const SORT_RELEVANCE_LABEL = 'Relevance';
    const SORT_DATE = 'date';
    const SORT_DATE_LABEL = 'Date';
    const SORT_TITLE = 'title';
    const SORT_TITLE_LABEL = 'Title';
//    const SORT_POPULARITY = 'pop';
//    const SORT_POPULARITY_LABEL = 'Popularity';

    public $defaultAll = false;
    public $exclusive = true;

    public $defaultOptions = array(
//        self::SORT_RELEVANCE => self::SORT_RELEVANCE_LABEL,
        self::SORT_DATE => self::SORT_DATE_LABEL,
        self::SORT_TITLE => self::SORT_TITLE_LABEL,
//        self::SORT_POPULARITY => self::SORT_POPULARITY_LABEL,
    );

    function __construct($name, $section, $options = array())
    {
        parent::__construct($name, $section, $options);
        if(empty($this->options)){
            foreach($this->defaultOptions as $name => $label){
                $this->addOption($name, $label);
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
        $args['orderby'] = $option[0]['name'];

        return $args;
    }

    public function setSelected(array $params)
    {
        $affected = parent::setSelected($params);
        if ($affected == 0) {
            $optionKeys = array_keys($this->options);
            $this->options[$optionKeys[0]]['selected'] = true;
            $affected++;
        }
        return $affected;
    }

}