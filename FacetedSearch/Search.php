<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:43
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch;

use Outlandish\AcadOowpBundle\FacetedSearch\FacetOption\FacetOption;
use Outlandish\AcadOowpBundle\FacetedSearch\FacetOption\FacetOptionPost;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostToPost;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostType;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetOrder;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetOrderBy;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\Facet;
use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\OowpBundle\Manager\PostManager;
use Outlandish\OowpBundle\PostType\Post;

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
        's' => null,
        'posts_per_page' => 10,
        'paged' => 1,
        'post__not_in' => array()
    );

    function __construct(QueryManager $queryManager, PostManager $postManager)
    {
        $this->queryManager = $queryManager;
        $this->postManager = $postManager;
        $this->populateFacets();
    }

    function populateFacets()
    {
        $postMap = $this->postManager->postTypeMapping();
        $resources = $this->getResourcePostTypes();
        $themes = $this->getThemePostTypes();

        $facet = new FacetPostType('post_type', 'Post Type');
        /** @var PostManager $postManager */
        $postClasses = array_intersect_key($this->postManager->postTypeMapping(), array_flip($resources));
        foreach($postClasses as $postType => $class){
            $option  = new FacetOption($postType, $class::friendlyName());
            $facet->addOption($option);
        }
        $this->addFacet($facet);

        foreach($themes as $postType){
            if(!array_key_exists($postType, $postMap)) continue;
            $class = $postMap[$postType];

            $posts = $class::fetchAll();
            if($posts->post_count < 1) continue;
            $facet = new FacetPostToPost($postType, $class::friendlyNamePlural(), $postType);
            foreach($posts as $post){
                $option = new FacetOptionPost($post);
                $facet->addOption($option);
            }
            $this->addFacet($facet);
        }

        $this->addFacet(new FacetOrder('order', 'Order'));
        $this->addFacet(new FacetOrderBy('orderby', 'Order By'));
    }

    /**
     * returns only the post types that we consider to be resources
     * These will be returned in search results, whereas others will used as categories
     * @return array
     */
    public function getResourcePostTypes()
    {
        /** @var PostManager $this->postManager */
        $postTypes = array();
        foreach($this->postManager->postTypeMapping() as $postType => $class) {
            if($class::isResource()) $postTypes[] = $postType;
        }
        return $postTypes;
    }

    /**
     * returns only the post types that we consider to be themes
     * These will be used as categories
     * @return array
     */
    public function getThemePostTypes()
    {
        /** @var PostManager $this->postManager */
        $postTypes = array();
        foreach($this->postManager->postTypeMapping() as $postType => $class) {
            if($class::isTheme()) $postTypes[] = $postType;
        }
        return $postTypes;
    }

    /**
     * method to add pre-existing Facet object to Search
     * @param Facet $facet
     * @return Facet
     */
    public function addFacet(Facet $facet)
    {
        $facetExists = False;
        foreach($this->facets as $existingFacet){
            if($facet->name == $existingFacet->name)
                $facetExists = True;
        }
        if(!$facetExists) {
            $this->facets[] = $facet;
            usort($this->facets, array($this, 'sortFacets'));
        }

        return $facet;
    }

    /**
     * remove a specific facet
     * @param $facetName
     */
    public function removeFacet($facetName)
    {
        if(array_key_exists($facetName, $this->facets)){
            unset($this->facets[$facetName]);
        }
    }

    /**
     * remove all facets
     */
    public function resetFacets()
    {
        $this->facets = array();
    }

    /**
     * @return Facet[]
     */
    public function getFacets()
    {
        return $this->facets;
    }

    public function getPostToPostFacets()
    {
        $facets = $this->getFacets();
        return array_filter($facets, function($a){
            return is_a($a, 'Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostToPost');
        });

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
    public function addFacetPostToPost($name, $section, $postType, $options = array())
    {
        $facet = new FacetPostToPost($name, $section, $postType, $options);
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
        return $this->queryManager->query(array_filter($args));
    }

    /**
     * method to generate arguments for OowpQuery
     * Loops through Facets and creates a combined array of arguments from each individual facet
     * @return array
     */
    public function generateArguments()
    {
        $args = $this->getDefaults();

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
        if(array_key_exists('post__not_in', $params)){
            if(!is_array($params['post__not_in'])){
                $params['post__not_in'] = array($params['post__not_in']);
            }
        }
        $this->params = $params;
    }

    public function getDefaults()
    {
        $defaultParams = array_intersect_key($this->params, $this->defaults);
        $defaultParams = array_merge($this->defaults, $defaultParams);

        return $defaultParams;

    }

    public function queryString($page = 0)
    {
        $query = $this->getDefaults();
        /** @var Facet $facet */
        foreach($this->facets as $facet) {
            $options = $facet->getSelectedOptions();
            if(!empty($options)){
                $value = array_map(function($a){
                    return $a->getName();
                }, $options);
                $query[$facet->getName()] = $value;
            }
        }

        //change page by value of $page
        if(array_key_exists('paged', $query)){
            $query['paged'] += $page;
        } else {
            $query['paged'] = $page;
        }

        foreach($query as $key => &$value){
            if(is_array($value)) $value = implode(',', $value);
        }

        return http_build_query($query);
    }

} 