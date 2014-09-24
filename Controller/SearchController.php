<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\AcadOowpBundle\FacetedSearch\SearchFormHelper;
use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\AcadOowpBundle\FacetedSearch\FacetOption\FacetOption;
use Outlandish\AcadOowpBundle\FacetedSearch\FacetOption\FacetOptionPost;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetOrder;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetOrderBy;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostToPost;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostType;
use Outlandish\OowpBundle\PostType\Post;
use Outlandish\OowpBundle\Manager\PostManager;
use Outlandish\AcadOowpBundle\FacetedSearch\Search;
use Outlandish\SiteBundle\PostType\Page;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Outlandish\RoutemasterBundle\Controller\BaseController;

class SearchController extends BaseController {

    public $search = null;

    /**
     * @Route("/search/", name="search")
     * @Template("OutlandishAcadOowpBundle:Search:search.html.twig")
     */
    public function indexAction(Request $request)
    {
        $single = $this->searchSingle($request);
        //see if query term matches post title redirect to post page
        if($single) return $this->redirect($single->permalink());
        $response = $this->searchResponse($request);
        $response['post'] = $this->querySingle(array('name' => 'search', 'post_type' => 'page'), true);
        return $response;
    }

    /**
     * @Route("/search/ajax/", name="searchAjax")
     * @Template("OutlandishAcadOowpBundle:Search:searchAjax.html.twig")
     */
    public function ajaxAction(Request $request)
    {
        $response = $this->searchResponse($request->query->all());
        return $response;
    }

    public function renderRelatedPostsAction( Post $post, $s = null) {
        if($post->postType() == Page::postType()) return new Response();
        $args = array(
            's' => $s
        );
        if($post::isResource()){
            $themes = $post->connected($this->getThemePostTypes());
            if($themes->post_count > 0){
                foreach($themes->posts as $theme){
                    if(!array_key_exists($theme->postType(), $args)){
                        $args[$theme->postType()] = array();
                    }
                    $args[$theme->postType()][] = $theme->ID;
                }
                $args['post__not_in'] = array($post->ID);
            }
        } else {
            $args[$post->postType()] = array($post->ID);
        }
        $response = $this->searchResponse($args);
        return $this->render(
            'OutlandishAcadOowpBundle:Search:relatedSection.html.twig',
            $response
        );
    }

    public function slugify($string)
    {
        $string = strtolower($string);
        $string = str_replace(' ', '-', $string);
        return $string;

    }

    public function search(array $postType = array())
    {
        /** @var PostManager $postManager */
        $postManager = $this->get('outlandish_oowp.post_manager');
        $postMap = $postManager->postTypeMapping();


        /** @var Search $search */
        $search = $this->get('outlandish_acadoowp.faceted_search.search');
        //if post types have been passed through, use them
        //otherwise use all the resources
//        $resources = array_keys(array_intersect_key($postMap, array_flip($postType)));
//        if(empty($resources)) $resources = $this->getResourcePostTypes();
        $resources = $this->getResourcePostTypes();
        $themes = $this->getFilterPostTypes();

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

    /**
     * @param array $params
     * @return null|Post
     */
    public function searchSingle(array $params)
    {
        if(!array_key_exists('s', $params)) return null;

        /** @var QueryManager $queryManager */
        $queryManager = $this->get('outlandish_oowp.query_manager');

        $args = array(
            'name' => sanitize_title($params['s']),
            'posts_per_page' => 1
        );

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
        if($posts->post_count < 1) return false;

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

    /**
     * returns only the post types that we consider to be themes
     * These will be used as categories
     * @return array
     */
    public function getFilterPostTypes()
    {
        /** @var PostManager $postManager */
        $postManager = $this->get('outlandish_oowp.post_manager');

        $postTypes = array();
        foreach($postManager->postTypeMapping() as $postType => $class) {
            if($class::isTheme() && $class::isFilter()) $postTypes[] = $postType;
        }
        return $postTypes;
    }

    /**
     * @param array $params
     * @param array $postType
     * @return mixed
     */
    public function searchResponse(array $params, array $postType = array())
    {
        $response = array(
            'featuredItem' => $this->searchSingle($params),
            'items' => null,
            'moreResultsUrl' => null
        );
        $search = $this->search($postType);
        if(!empty($postType)) $params['post_type'] = $postType;
        $search->setParams($params);
        $query = $search->search();
        $response['search'] = $query;
        if ($query->post_count > 0) {
            $response['items'] = $query->posts;
//            $uriParts = explode("?", $params->getUri());
            $response['moreResultsUrl'] = "?" . $search->queryString(1);
        }
        $response['helper'] = new SearchFormHelper($search);
        $response['formElements'] = $response['helper']->getSearchFormElements();
        return $response;
    }

    /**
     * returns specific post types for search results on index pages
     * eg. on News Index, have this return an array(News::postType())
     * @return array
     */
    public function postTypes()
    {
        return array();
    }
}