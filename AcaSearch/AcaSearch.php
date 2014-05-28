<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 28/05/14
 * Time: 17:06
 */

namespace Outlandish\AcadOowpBundle\AcaSearch;

use Outlandish\FacetedSearchBundle\DependencyInjection\FacetedSearch;
use Outlandish\AcadOowpBundle\PostType;

class AcaSearch {
    function __construct(FacetedSearch $search)
    {
        $this->search = $search;
        $search->setSearchType(FacetedSearch::SEARCH_TYPE_DATABASE);

        $search->addFacet(
            array(
                'type'      =>  'post_type',
                'title'     =>  'Main Types',
                'public'    =>  true,
                'items'     =>  array(
                    PostType\Document::postType(),
                    PostType\Person::postType() )
            )
        );

    }

    public function search()
    {
        return $this->search->startSearch();
    }


} 