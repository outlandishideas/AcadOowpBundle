<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\AcadOowpBundle\FacetedSearch\FacetOption\FacetOption;
use Outlandish\AcadOowpBundle\FacetedSearch\FacetOption\FacetOptionPost;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetOrder;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetOrderBy;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostToPost;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostType;
use Outlandish\OowpBundle\Manager\PostManager;
use Outlandish\AcadOowpBundle\FacetedSearch\Search;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;
use Outlandish\RoutemasterBundle\Controller\BaseController;

class SearchController extends BaseController {

    /**
     * @Route("/search/", name="search")
     * @Template("OutlandishAcadOowpBundle:Search:search.html.twig")
     */
    public function indexAction(Request $request)
    {
        $post = $this->querySingle(array('name' => 'search', 'post_type' => 'page'), true);
        $search = $this->search($request);
        $search->setParams($request->query->all());
        $query = $search->search();
        if($query->post_count > 0){
            $response['items'] = $query->posts;
            $response['moreResultsUrl'] = $this->generateUrl('search') . "?" . $search->queryString(1);
        } else {
            $response['items'] = null;
            $response['moreResultsUrl'] = null;
        }
        return $response;
    }

    /**
     * @Route("/search/ajax/", name="searchAjax")
     * @Template("OutlandishAcadOowpBundle:Search:searchAjax.html.twig")
     */
    public function ajaxAction(Request $request)
    {
        $search = $this->search($request);
        $search->setParams($request->query->all());
        $query = $search->search();
        if($query->post_count > 0){
            $response['items'] = $query->posts;
            $response['moreResultsUrl'] = $this->generateUrl('searchAjax') . "?" . $search->queryString(1);
            $response['paged'] = $query->query_vars['paged'];
        } else {
            $response['items'] = null;
            $response['moreResultsUrl'] = null;
            $response['paged'] = 0;
        }
        return $response;
    }

    public function search(Request $request)
    {
        /** @var PostManager $postManager */
        $postManager = $this->get('outlandish_oowp.post_manager');
        $postMap = $postManager->postTypeMapping();


        /** @var Search $search */
        $search = $this->get('outlandish_acadoowp.faceted_search.search');
        $resources = $this->getResourcePostTypes();
        $themes = $this->getThemePostTypes();

        // adding FacetPostType to search
        $search->addFacet($this->generatePostTypeFacet($resources));

        foreach($themes as $postType){
            if(!array_key_exists($postType, $postMap)) continue;
            $class = $postMap[$postType];
            $facet = $this->generatePostToPostFacet($postType, $class);
            if(!$facet) continue;
            $search->addFacet($facet);
        }

        $search->addFacet(new FacetOrder('order', 'Order'));
        $search->addFacet(new FacetOrderBy('orderby', 'Order By'));
        return $search;
    }

    public function searchSingle(Request $request)
    {
        if(!$request->query->has('q')) return null;

        /** @var QueryManager $queryManager */
        $queryManager = $this->get('outlandish_oowp.query_manager');

        $args = array('post' => $request->query->get('q'));

        $results = $queryManager->query($args);
        if($results->post_count == 1){
            return $results->post;
        } else {
            return null;
        }
    }

    /**
     * generates post to post facet using post class
     * @param $postType
     * @return bool|FacetPostType
     */
    public function generatePostToPostFacet($postType, $class){
        $posts = $class::fetchAll();
        if(!$posts) return false;

        $facet = new FacetPostToPost($postType, $class::friendlyNamePlural(), $postType);
        foreach($posts as $post){
            $option = new FacetOptionPost($post);
            $facet->addOption($option);
        }
        return $facet;
    }

    /**
     * generates post type facet from list of post types
     * @param $postTypes
     * @return FacetPostType
     */
    public function generatePostTypeFacet($postTypes)
    {
        $facet = new FacetPostType('post_type', 'Post Type');

        /** @var PostManager $postManager */
        $postManager = $this->get('outlandish_oowp.post_manager');
        $postClasses = array_intersect_key($postManager->postTypeMapping(), array_flip($postTypes));
        foreach($postClasses as $postType => $class){
            $option  = new FacetOption($postType, $class::friendlyName());
            $facet->addOption($option);
        }
        return $facet;
    }

    /**
     * returns only the post types that we consider to be resources
     * These will be returned in search results, whereas others will used as categories
     * @return array
     */
    public function getResourcePostTypes()
    {
        /** @var PostManager $postManager */
        $postManager = $this->get('outlandish_oowp.post_manager');

        $postTypes = array();
        foreach($postManager->postTypeMapping() as $postType => $class) {
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
        /** @var PostManager $postManager */
        $postManager = $this->get('outlandish_oowp.post_manager');

        $postTypes = array();
        foreach($postManager->postTypeMapping() as $postType => $class) {
            if($class::isTheme()) $postTypes[] = $postType;
        }
        return $postTypes;
    }

}