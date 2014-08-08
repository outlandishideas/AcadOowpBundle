<?php
/**
 * Created by PhpStorm.
 * User: outlander
 * Date: 08/08/2014
 * Time: 17:47
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch\FacetOption;


class FacetOption {

    /**
     * @var null
     */
    public $name = null;

    /**
     * @var null
     */
    public $label = null;

    /**
     * @var bool
     */
    public $selected = false;

    /**
     * @param $name
     * @param $label
     * @param bool $selected
     */
    function __construct($name, $label, $selected = false)
    {
        $this->setName($name);
        $this->setLabel($label);
        $this->setSelected($selected);
    }

    /**
     * @return null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param null $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return boolean
     */
    public function isSelected()
    {
        return $this->selected;
    }

    /**
     * @param boolean $selected
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }

} 