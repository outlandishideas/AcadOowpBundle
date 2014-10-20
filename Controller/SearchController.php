<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\AcadOowpBundle\FacetedSearch\SearchFormHelper;
use Outlandish\OowpBundle\Manager\QueryManager;
use Outlandish\OowpBundle\PostType\Post as BasePost;
use Outlandish\AcadOowpBundle\FacetedSearch\FacetOption\FacetOption;
use Outlandish\AcadOowpBundle\FacetedSearch\FacetOption\FacetOptionPost;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetOrder;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetOrderBy;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostToPost;
use Outlandish\AcadOowpBundle\FacetedSearch\Facets\FacetPostType;
use Outlandish\SiteBundle\PostType\Post;
use Outlandish\OowpBundle\PostType\Post as OowpPost;
use Outlandish\OowpBundle\Manager\PostManager;
use Outlandish\AcadOowpBundle\FacetedSearch\Search;
use Outlandish\SiteBundle\PostType\Page;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends DefaultController {

    public $search = null;

    /**
     * action for displaying front page
     *
     * @param Request $request
     * @return array
     * @Template("OutlandishAcadOowpBundle:Page:pageFront.html.twig")
     */
    public function frontPageAction(Request $request)
    {
        /** @var Page $post */
        $post = $this->querySingle(array('page_id' => get_option('page_on_front')));
        $response = $this->indexResponse($post, $request);
        return $response;
    }

    /**
     * @Template("OutlandishAcadOowpBundle:Search:index.html.twig")
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
     * @Template("OutlandishAcadOowpBundle:Search:ajax.html.twig")
     */
    public function ajaxAction(Request $request)
    {
        $response = $this->searchResponse($request->query->all());
        return $response;
    }

    /**
     * method to generate response for index pages
     * returns either a response with items (if a search has been placed)
     * or a response with sections if sections have been created for a page
     *
     * @param \Outlandish\OowpBundle\PostType\Post $post
     * @param Request $request
     * @param array $postType
     * @return array
     */
    public function indexResponse(BasePost $post, Request $request, $postType = array())
    {
        $response = array();
        $response['post'] = $post;
        //if a search item does not appear in request
        //look for sections on the page
        if(!$request->query->has('s')){
            $sections = $this->sections($post->sections());
            if($sections) $response['sections'] = $sections;
        }
        //if section has not been set get items
        if(!array_key_exists('sections', $response)){
            $response = array_merge($response, $this->searchResponse($request->query->all(), $postType));
        }
        return $response;
    }

    /**
     * takes Request object and array postTypes and returns search object
     *
     * @param Request $request
     * @param array $postTypes
     * @return Search
     */
    protected function items(Request $request, $postTypes = array()){
        if(!$request->query->has('s')) return null;
        $params = $request->query->all();

        /** @var Search $search */
        $search = $this->get('outlandish_acadoowp.faceted_search.search');
        if(!empty($postTypes)){
            $params['post_types'] = $postTypes;
            $facet = $search->addFacetPostType('post_types', "");
            foreach($postTypes as $postType){
                $facet->addOption($postType, "");
            }
            $facet->setHidden(true);
        }

        foreach(Theme::childTypes(true) as $postType => $class) {
            $search->addFacetPostToPost(
                $class::postType(),
                $class::friendlyName(),
                $class::postType(),
                $class::fetchAll()->posts
            );
        }

        $search->setParams($params);
        return $search;
    }

    /**
     * Generates an array of sections that can be display on a page
     * Uses the sections metadata for a page to generate a list of sections and their content
     *
     * @param $sections
     * @return array
     */
    protected function sections($sections)
    {
        if(!$sections || !is_array($sections)){
            return array();
        }
        foreach($sections as $s => $section){
            if(!isset($section['acf_fc_layout'])){
                unset($sections[$s]);
                break;
            }
            switch($section['acf_fc_layout']) {
                //if search_posts layout create faceted search from arguments
                //add results from faceted search to section
                case "search_posts":
                    $params = array();
                    /** @var Search $search */
                    $search = clone $this->get('outlandish_acadoowp.faceted_search.search');

                    $params['s'] = $section['query'];
                    unset($section['query']);

                    if(!empty($section['post_types'])){
                        $params['post_types'] = $section['post_types'];
                        $facet = $search->addFacetPostType('post_types', "");
                        foreach($section['post_types'] as $postType){
                            $facet->addOption(new FacetOption($postType, ""));
                        }
                    }
                    unset($section['post_types']);

                    if(!empty($section['connected_to'])){
                        //get posts from array of post ids in $section['connected_to']
                        $query = Post::fetchAll(array('post_type' => 'any', 'post__in' => $section['connected_to']));
                        if($query->post_count > 0){
                            $postTypes = array();
                            //sort posts from query by post type
                            foreach($query->posts as $post){
                                /** @var Post $post */
                                if(!isset($postTypes[$post->post_type])){
                                    $postTypes[$post->post_type] = array();
                                }
                                $postTypes[$post->post_type][] = $post->ID;
                            }
                            //create facet for each post type
                            foreach($postTypes as $postType => $posts){
                                $params[$postType] = $posts;
                                $facet = $search->addFacetPostToPost($postType, "", $postType);
                                foreach($posts as $postId){
                                    $option = new FacetOption($postId, "");
                                    $facet->addOption($option);
                                }
                            }
                        }
                    }
                    unset($section['connected_to']);

                    if(!empty($section['order'])){
                        $search->addFacetOrder('order', "");
                        $params['order'] = $section['order'];
                    }

                    if(!empty($section['orderby'])){
                        $search->addFacetOrderBy('orderby', "");
                        $params['orderby'] = $section['orderby'];
                    }

                    $search->setParams($params);
                    $sections[$s]['items'] = $search->search();
                    break;
                //if curated posts convert WP_Post items to ooPost items
                case "curated_posts":
                    $ids = array();
                    foreach($section['items'] as $item){
                        if($item instanceof \WP_Post){
                            $ids[] = $item->ID;
                        } else {
                            $ids[] = $item;
                        }
                    }
                    $queryArgs = array('post_type' => 'any', 'post__in' => $ids);
                    if (!$section['order_by_date']) {$queryArgs['orderby'] = 'post__in';}
                    $query = Post::fetchAll($queryArgs);
                    if($query->post_count > 0){
                        $items = $query->posts;
                    } else {
                        $items = array();
                    }
                    $sections[$s]['items'] = $items;
                    break;
                default:
                    unset($sections[$s]);
            }
        }
        return $sections;
    }

    public function renderRelatedPostsAction( OowpPost $post, $s = null) {
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
     * @param $class
     * @return null|FacetPostType
     */
    public function generatePostToPostFacet($postType, $class){
        $posts = $class::fetchAll();
        if($posts->post_count < 1) return null;

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