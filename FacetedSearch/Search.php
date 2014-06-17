<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 16/06/14
 * Time: 00:43
 */

namespace Outlandish\AcadOowpBundle\FacetedSearch;

use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostType;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\Facet;
use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\OowpBundle\Manager\PostManager;

class Search {

    protected $facets = array();

    function __construct(QueryManager $queryManager, PostManager $postManager)
    {
        $this->queryManager = $queryManager;
        $this->postManager = $postManager;
    }

    public function addFacet($facet)
    {
        $this->facets[] = $facet;
        return $facet;
    }

    public function addFacetPostType($name, $section)
    {
        $facet = new FacetPostType($name, $section);
        return $this->addFacet($facet);
    }

    public function search()
    {
        $args = $this->generateArguments();
        return $this->queryManager->query($args);
    }

    public function generateArguments()
    {
        $args = $this->defaults;
        foreach($this->facets as $facet) {
            /** @var Facet $facet */
            $args = wp_parse_args($args, $facet->generateArguments());
        }
        return $args;
    }

    public $defaults = array(
        'post_type' => 'any',
        'post_count' => 10,
        'page' => 1,
    );

} 