<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\FacetedSearch\SearchFormHelper;
use Outlandish\AcadOowpBundle\PostType\Theme;
use Outlandish\OowpBundle\PostType\MiscPost;
use Outlandish\AcadOowpBundle\Controller\SearchController as BaseController;
use Outlandish\SiteBundle\PostType\News;
use Symfony\Component\HttpFoundation\Request;
use Outlandish\SiteBundle\PostType\Page;
use Outlandish\SiteBundle\PostType\Post;
use Outlandish\AcadOowpBundle\FacetedSearch\Search;
use Outlandish\SiteBundle\PostType\Person;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends BaseController {

	/**
	 * Route is specified in routing.yml because it needs to come last
	 * @Template("OutlandishAcadOowpBundle:Default:defaultPost.html.twig")
	 */
	public function defaultPostAction($slugs)
    {
		$slugBits = explode('/', trim($slugs, '/'));
        /** @var Post $post */
		$post = $this->querySingle(array('name' => $slugBits[count($slugBits) - 1], 'post_type' => 'any'), true);
        $author = $post->author();

        $bottomItems = array(
            $post->connectedDocuments(),
            $post->connectedNews()
        );

		return array(
            'post' => $post,
            'author' => $author,
            'bottomItems' => $bottomItems
        );
	}

    /**
     * method to generate response for index pages
     * returns either a response with items (if a search has been placed)
     * or a response with sections if sections have been created for a page
     * @param \Outlandish\OowpBundle\PostType\Post $post
     * @param Request $request
     * @param array $postType
     * @return array
     */
    public function indexResponse(\Outlandish\OowpBundle\PostType\Post $post, Request $request, $postType = array())
    {
        $response = array();
        $response['post'] = $post;
        $search = $this->search($request);
        if($search) {
            $query = $search->search();
        }
        if($search && $query->post_count > 0){
            $response['items'] = $query->posts;
            $helper = new SearchFormHelper($search);
            $response['search_form'] = $helper->getSearchFormElements();
        } else {
            $response['sections'] = $this->sections($post->sections());
        }

        return $response;
    }

    /**
     * takes Request object and array postTypes and returns search object
     * @param Request $request
     * @param array $postTypes
     * @return Search
     */
    public function items(Request $request, $postTypes = array()){
        if(!$request->query->has('q')) return false;
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

}