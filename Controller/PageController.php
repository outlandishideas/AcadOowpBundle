<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;

use Outlandish\AcadOowpBundle\PostType\Page;
use Outlandish\AcadOowpBundle\PostType\Post;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PageController extends BaseController {

	/**
	 * @Route("/", name="home")
	 * @Template("OutlandishAcadOowpBundle:Page:pageFront.html.twig")
	 */
	public function frontPageAction()
    {
        /** @var Page $post */
		$post = $this->querySingle(array('page_id' => get_option('page_on_front')));
        $sections = $this->sections($post->sections());
        $returnArgs = array(
            'post' => $post,
            'sections' => $sections
        );
		return $returnArgs;
    }

    /**
     * @Route("about/contact/", name="contact")
     * @Template("OutlandishAcadOowpBundle:Default:contactPost.html.twig")
     */
    public function contactPostAction() {
        /** @var Page $post */
        $post = $this->querySingle(array('page_id' => Page::CONTACT_PAGE_ID));

        $response['post'] = $post;
        $response['map']  = $post->contactMap();
        $response['address'] = get_field('address', 'options');
        $response['contact_people'] = $post->contactPeople();

        return $response;
    }

    public function sections($sections)
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
                    $search = $this->get('outlandish_acadoowp.faceted_search.search');

                    $params['q'] = $section['query'];
                    unset($section['query']);

                    if(!empty($section['post_types'])){
                        $params['post_types'] = $section['post_types'];
                        $facet = $search->addFacetPostType('post_types', "");
                        foreach($section['post_types'] as $postType){
                            $facet->addOption($postType, "");
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
                                    $facet->addOption($postId, "");
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
                    $query = Post::fetchAll(array('post_type' => 'any', 'post__in' => $ids));
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

}