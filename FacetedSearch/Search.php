<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:43
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch;

use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostToPost;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostType;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetOrder;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetOrderBy;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\Facet;
use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\OowpBundle\Manager\PostManager;

class Search {

    /**
     * array to hold the facets that have been added to this Search object
     * @var array
     */
    protected $facets = array();

    /**
     * these are the params that have been passed through from $_GET
     * these are the options that should be selected when running the search
     * @var array
     */
    protected $params = array();


    /**
     * default arguments for search
     * @var array
     */
    public $defaults = array(
        'post_type' => 'any',
        'post_count' => 10,
        'page' => 1
    );

    function __construct(QueryManager $queryManager, PostManager $postManager, array $params = array())
    {
        $this->queryManager = $queryManager;
        $this->postManager = $postManager;
        $this->params = $params;
    }

    /**
     * method to add pre-existing Facet object to Search
     * @param Facet $facet
     * @return Facet
     */
    public function addFacet(Facet $facet)
    {
        $this->facets[] = $facet;
        usort($this->facets, array($this, 'sortFacets'));

        return $facet;
    }

    /**
     * Shortcut function for adding a FacetPostType object
     * @param $name
     * @param $section
     * @return FacetPostType
     */
    public function addFacetPostType($name, $section)
    {
        $facet = new FacetPostType($name, $section);
        return $this->addFacet($facet);
    }

    /**
     * Shortcut function for adding a FacetPostType object
     * @param $name
     * @param $section
     * @return FacetPostType
     */
    public function addFacetPostToPost($name, $section, $postType)
    {
        $facet = new FacetPostToPost($name, $section, $postType);
        return $this->addFacet($facet);
    }

    /**
     * Shortcut function for adding a FacetOrder object
     * @param $name
     * @param $section
     * @param array $options
     * @return FacetOrder
     */
    public function addFacetOrder($name, $section, $options = array())
    {
        $facet = new FacetOrder($name, $section, $options);
        return $this->addFacet($facet);
    }

    /**
     * Shortcut function for adding a FacetOrderBy object
     * @param $name
     * @param $section
     * @param array $options
     * @return FacetOrderBy
     */
    public function addFacetOrderBy($name, $section, $options = array())
    {
        $facet = new FacetOrderBy($name, $section, $options);
        return $this->addFacet($facet);
    }

    /**
     * custom sorting function to sort facets so that FacetPostType come first
     * need this because when generatingArguments() we need post_type defined
     * @param $a
     * @param $b
     * @return int
     */
    public static function sortFacets(Facet $a, Facet $b)
    {
        if($a == $b) return 0;
        return $a instanceof FacetPostType ? -1 : 1;
    }

    /**
     * method for running an OowpQuery search
     * arguments are generated from the properties of this Search object
     * @return \Outlandish\OowpBundle\Query\OowpQuery
     */
    public function search()
    {
        $args = $this->generateArguments();
        return $this->queryManager->query($args);
    }

    /**
     * method to generate arguments for OowpQuery
     * Loops through Facets and creates a combined array of arguments from each individual facet
     * @return array
     */
    public function generateArguments()
    {
        $args = $this->defaults;

        if(array_key_exists('q', $this->params)){
            $args['s'] = $this->params['q'];
        }

        foreach($this->facets as $facet) {
            /** @var Facet $facet */
            $facet->setSelected($this->params);
            $args = $facet->generateArguments($args);
        }
        return $args;
    }
    /**
     * setter for params property
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

} 